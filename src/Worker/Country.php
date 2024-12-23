<?php

namespace App\Worker;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class Country
{
    private AbstractConnection $connection;
    private AMQPChannel $channel;
    private string $exchange = 'router';
    private string $queue = 'country';
    private string $consumerTag = 'consumer';

    public function __construct()
    {
        try {
            $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest', '/');
            $this->createChannel();
        } catch (\Exception $e) {

        }
    }

    public function __destruct()
    {
        $this->channel->close();
        try {
            $this->connection->close();
        } catch (\Exception $e) {

        }
    }

    protected function createChannel(): void
    {
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->queue, false, true, false, false);
        $this->channel->exchange_declare($this->exchange, AMQPExchangeType::DIRECT, false, true, false);
        $this->channel->queue_bind($this->queue, $this->exchange, 'country');
    }

    public function consume(): void
    {
        $this->channel->basic_consume($this->queue, $this->consumerTag, false, false, false, false, [$this, 'handleMessage']);
        try {
            $this->channel->consume();
        } catch (\ErrorException $e) {

        }
    }

    public function handleMessage(AMQPMessage $message)
    {
        echo "\n--------\n";
        echo $message->getBody();
        echo "\n--------\n";

        // Call API by curl to get country information
        $url = sprintf('https://restcountries.com/v3.1/alpha/%s', $message->getBody());

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        $json = json_decode($response, true);

        $capital = $json[0]['capital'][0];

        // Publish the capital to the exchange
        $publishMessage = new AMQPMessage(
            $capital,
            array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
        );

        $message->getChannel()->basic_publish($publishMessage, $this->exchange, 'capital');

        $message->ack();
    }
}
