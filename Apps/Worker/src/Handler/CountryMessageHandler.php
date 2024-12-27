<?php

namespace App\Handler;

use App\Service\HttpService;
use App\Service\WorkerService;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * handle message in rabbitMQ country consumer
 */
readonly class CountryMessageHandler implements MessageHandlerInterface
{

    public function __construct(private HttpService $httpService, private WorkerService $workerService)
    {
    }

    public function handleMessage(AMQPMessage $message): void
    {
        $url = sprintf('/v3.1/alpha/%s', $message->getBody());
        $json = $this->httpService->request($url);

        if ($json) {
            //TODO: parse it with DTO
            $capital = strtolower($json[0]['capital'][0]);
            $this->workerService->send($capital, 'capital');
        }

        $message->ack();
    }
}