<?php

namespace App\Handler;

use App\Message\CapitalMessage;
use App\Message\CountryMessage;
use App\Service\HttpService;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * handle message in rabbitMQ country consumer
 */
#[AsMessageHandler]
readonly class CountryMessageHandler
{

    public function __construct(private HttpService $httpService, private MessageBusInterface $messageBus)
    {
    }
    public function __invoke(CountryMessage $message, Acknowledger $acknowledge): void
    {
        $url = sprintf('/v3.1/alpha/%s', $message->getCode());
        try {
            $json = $this->httpService->request($url);
            dump($json);
        } catch (RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            $acknowledge->nack($e);
            return;
        }

        if ($json && count($json) > 0 && array_key_exists('capital', $json[0]) && count($json[0]['capital']) > 0) {
            //TODO: parse it with DTO
            $capital = strtolower($json[0]['capital'][0]);
            try {
                $this->messageBus->dispatch(new CapitalMessage($capital));
            } catch (ExceptionInterface $e) {

            }
        }

        $acknowledge->ack();
    }
}