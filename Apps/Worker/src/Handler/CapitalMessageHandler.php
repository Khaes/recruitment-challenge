<?php

namespace App\Handler;

use App\Message\CapitalMessage;
use App\Service\HttpService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Handler\BatchHandlerInterface;
use Symfony\Component\Messenger\Handler\BatchHandlerTrait;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * handle message in rabbitMQ capital consumer
 */
#[AsMessageHandler]
class CapitalMessageHandler implements BatchHandlerInterface
{
    use BatchHandlerTrait;

    public function __construct(readonly private HttpService $httpService)
    {
    }

    public function __invoke(CapitalMessage $message, Acknowledger $acknowledge = null): mixed
    {
        return $this->handle($message, $acknowledge);
    }

    private function process(array $jobs): void
    {
        foreach ($jobs as [$message, $ack]) {
            $url = sprintf('/v3.1/capital/%s', $message->getCode());
            try {
                $json = $this->httpService->request($url);
            } catch (RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
                $ack->nack($e);
                return;
            }
            if ($json && count($json) > 0) {
                $ack->ack();
                return;
            }
            $ack->nack(new \Exception('NO JSON'));
        }
    }
}