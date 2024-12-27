<?php

namespace App\Handler;

use App\Message\CapitalMessage;
use App\Service\HttpService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * handle message in rabbitMQ capital consumer
 */
readonly class CapitalMessageHandler
{
    const EVENT_NAME = 'countryDisplay';

    public function __construct(private HttpService $httpService, private EventDispatcherInterface $dispatcher)
    {
    }

    public function __invoke(CapitalMessage $message, Acknowledger $acknowledge): void
    {
        $url = sprintf('/v3.1/capital/%s', $message->getCode());
        try {
            $json = $this->httpService->request($url);
        } catch (RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            $acknowledge->nack($e);
            return;
        }
        if ($json && count($json) > 0) {
            $this->dispatcher->dispatch(new GenericEvent(var_export($json[0], true)), self::EVENT_NAME);
        }
        $acknowledge->ack();
    }
}