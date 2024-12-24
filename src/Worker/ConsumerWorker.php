<?php

namespace App\Worker;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;

class ConsumerWorker
{
    private AMQPChannel $channel;
    const EXCHANGE = 'router';
    const CONSUMER_TAG = 'consumer';
    private string $queue;

    public function __construct(readonly private AbstractConnection $connection) {
    }

    public function __destruct()
    {
        $this->channel->close();
        try {
            $this->connection->close();
        } catch (\Exception $e) {

        }
    }

    public function createChannel(string $queue): void
    {
        $this->queue = $queue;
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->queue, false, true, false, false);
        $this->channel->exchange_declare(self::EXCHANGE, AMQPExchangeType::DIRECT, false, true, false);
        $this->channel->queue_bind($this->queue, self::EXCHANGE, $queue);
    }

    public function consume(callable $callback): void
    {
        $this->channel->basic_consume($this->queue, self::CONSUMER_TAG, false, false, false, false, $callback);
        try {
            $this->channel->consume();
        } catch (\ErrorException $e) {

        }
    }
}