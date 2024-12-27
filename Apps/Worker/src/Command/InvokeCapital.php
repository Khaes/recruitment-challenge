<?php

namespace App\Command;

use App\Handler\CapitalMessageHandler;
use App\Service\WorkerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'worker:capital:consume')]
class InvokeCapital extends Command
{
    public function __construct(
        private readonly WorkerService $workerService,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly CapitalMessageHandler $messageHandler) {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->eventDispatcher->addListener(
            CapitalMessageHandler::EVENT_NAME,
            function (GenericEvent $event) use ($output) {
                $output->writeln('<info>' . $event->getSubject() . '</info>');
            });
        $this->workerService->listen('capital', $this->messageHandler);
        return Command::FAILURE;
    }
}