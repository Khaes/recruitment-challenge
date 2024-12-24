<?php

namespace App\Handler;

use PhpAmqpLib\Message\AMQPMessage;

interface MessageHandlerInterface
{
    public function handleMessage(AMQPMessage $message): void;
}