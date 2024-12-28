<?php

namespace App\Handler;

use App\Message\CapitalMessage;
use App\Message\CountryMessage;
use App\Service\HttpService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Handler\BatchHandlerInterface;
use Symfony\Component\Messenger\Handler\BatchHandlerTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * handle message in rabbitMQ country consumer
 */
#[AsMessageHandler]
class CountryMessageHandler implements BatchHandlerInterface
{
    use BatchHandlerTrait;
    public function __construct(readonly private HttpService $httpService, readonly private MessageBusInterface $messageBus)
    {
    }
    public function __invoke(CountryMessage $message, Acknowledger $acknowledge = null): mixed
    {
        return $this->handle($message, $acknowledge);
    }

    private function process(array $jobs): void
    {
        foreach ($jobs as [$job, $ack]) {
            $url = sprintf('/v3.1/alpha/%s', $job->getCode());
            try {
                $json = $this->httpService->request($url);
            } catch (RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
                $ack->nack($e);
                return;
            }

            if ($json && count($json) > 0 && array_key_exists('capital', $json[0]) && count($json[0]['capital']) > 0) {
                //TODO: parse it with DTO
                $capital = strtolower($json[0]['capital'][0]);
                try {
                    $this->messageBus->dispatch(new CapitalMessage($capital));
                } catch (ExceptionInterface $e) {
                    $ack->nack($e);
                    return;
                }
            }

            $ack->ack();
        }
    }
}