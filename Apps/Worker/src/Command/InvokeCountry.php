<?php

namespace App\Command;

use App\Handler\CountryMessageHandler;
use App\Service\WorkerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'worker:country:consume')]
class InvokeCountry extends Command
{
    public function __construct(
        private readonly WorkerService $workerService,
        private readonly CountryMessageHandler $messageHandler)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->workerService->listen('country', $this->messageHandler);

        return Command::FAILURE;
    }
}