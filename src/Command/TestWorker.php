<?php

namespace App\Command;

use App\Service\WorkerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'worker:test')]
class TestWorker extends Command
{
    public function __construct(private readonly WorkerService $workerService) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('code', InputArgument::REQUIRED, 'Country code, example : fr');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($code = $input->getArgument('code')) {
            $this->workerService->send($code, 'country');

            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }
}