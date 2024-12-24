<?php

namespace App\Handler;

use App\Service\HttpService;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * handle message in rabbitMQ capital consumer
 */
readonly class CapitalMessageHandler implements MessageHandlerInterface
{
    const EVENT_NAME = 'countryDisplay';

    public function __construct(private HttpService $httpService, private EventDispatcherInterface $dispatcher)
    {
    }

    /**
     * @param AMQPMessage $message
     * @return void
     */
    public function handleMessage(AMQPMessage $message): void
    {
        $url = sprintf('/v3.1/capital/%s', $message->getBody());
        $json = $this->httpService->request($url);
        $this->dispatcher->dispatch(new GenericEvent(var_export($json[0], true)), 'countryDisplay');
        $message->ack();
    }
}