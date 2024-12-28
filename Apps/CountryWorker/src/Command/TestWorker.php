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
#[AsCommand(name: 'worker:test:one')]
class TestWorker extends Command
{
    public function __construct(private readonly MessageBusInterface $messageBus) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('code', InputArgument::REQUIRED, 'Country code, example : fr');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->messageBus->dispatch(new CountryMessage($input->getArgument('code')));
        } catch (ExceptionInterface $e) {
            dump($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}