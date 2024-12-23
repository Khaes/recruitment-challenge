<?php

namespace App\Command;

use App\Worker\Capital;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'worker:capital:consume')]
class InvokeCapital extends Command
{
    public function __construct(private readonly Capital $capital) {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->capital->consume();

        return Command::SUCCESS;
    }
}