<?php

namespace App\Command;

use App\Message\CountryMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'worker:test:big')]
class BigTestWorker extends Command
{
    public function __construct(private readonly MessageBusInterface $messageBus) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $codes = ['fr', 'zim', 'es', 'ger', 'ph'];
        try {
            for($i = 0; $i < 100; $i++) {
                $code = $codes[rand(0, count($codes) - 1)];
                $output->writeln($code);
                usleep(rand(1, 5)*100000);
                $this->messageBus->dispatch(new CountryMessage($code));
            }
        } catch (ExceptionInterface $e) {
            dump($e->getMessage());
        }
    }
}