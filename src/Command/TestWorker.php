<?php

namespace App\Command;

use App\Worker\Capital;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'worker:test')]
class TestWorker extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $msg = new AMQPMessage('fr');
        $channel->basic_publish($msg, 'router', 'country');

        return Command::SUCCESS;
    }
}