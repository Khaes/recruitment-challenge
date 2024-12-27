<?php

namespace App\Service;

use App\Handler\MessageHandlerInterface;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Worker service related to rabbitMQ
 * Configurable in parameters
 */
class WorkerService
{
    const EXCHANGE = 'router';
    const CONSUMER_TAG = 'consumer';
    private array $configuration;
    private array $connections = [];
    public function __construct(
        ParameterBagInterface $parameterBag,
        readonly private LoggerInterface $logger)
    {
        $this->configuration = $parameterBag->get('rabbitmq');
        register_shutdown_function([$this, 'destroy']);
    }

    public function __destruct()
    {
        $this->destroy();
    }

    /**
     * basic consumer
     * @param string $queue
     * @param MessageHandlerInterface $handler
     * @return void
     */
    public function listen(string $queue, MessageHandlerInterface $handler) :void
    {
        try {
            $connection = $this->createConnection();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return;
        }
        $channel = $connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare(self::EXCHANGE, AMQPExchangeType::DIRECT, false, true, false);
        $channel->queue_bind($queue, self::EXCHANGE, $queue);
        $channel->basic_consume($queue, self::CONSUMER_TAG, false, false, false, false, [$handler, 'handleMessage']);
        $this->connections[$queue] = $connection;
        try {
            $channel->consume();
        } catch (\ErrorException $e) {

        }
    }

    /**
     * @return void
     */
    public function destroy() :void
    {
        // doesnt work when scripts is halted, enabling pcntl might be the solution
        // https://dev.to/kakisoft/php-docker-how-to-enable-pcntlprocess-control-extensions-1afk
        // https://stackoverflow.com/questions/3909798/phps-register-shutdown-function-to-fire-when-a-script-is-killed-from-the-command
        // OR send a rabbitMQ event for consumer and return false in handle
        foreach ($this->connections as $queue => $connection) {
            try {
                $connection->close(); //channel closed already here
                unset($this->connections[$queue]);
            } catch (\Exception $e) {

            }
        }
    }

    /**
     * basic event send
     * @param string $message
     * @param string $routingKey
     * @return void
     */
    public function send(string $message, string $routingKey): void
    {
        $publishMessage = new AMQPMessage(
            $message,
            array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
        );
        try {
            $connection = $this->createConnection();
        } catch (\Exception $e) {
            return;
        }
        $channel = $connection->channel();
        $channel->basic_publish($publishMessage, self::EXCHANGE, $routingKey);
        $channel->close();
        try {
            $connection->close();
        } catch (\Exception $e) {

        }
    }

    /**
     * @throws \Exception
     */
    private function createConnection() :AbstractConnection
    {
        return new AMQPStreamConnection($this->configuration['host'], $this->configuration['port'], $this->configuration['user'], $this->configuration['password'], $this->configuration['vhost']);
    }
}