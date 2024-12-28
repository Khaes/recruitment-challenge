<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpService
{
    private string $endPoint;
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        ParameterBagInterface $parameterBag)
    {
        $this->endPoint = $parameterBag->get('endpoint'); //only one endpoint here, move it to another logic if multiples
    }

    /**
     * @throws TransportExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function request(string $path, string $method = 'GET', array $options = [], string $responseFormat = 'JSON')
    {
        //TODO: implements cache system here
        try {
            $response = $this->httpClient->request($method, $this->endPoint.$path, $options);
            return match ($responseFormat) {
                'JSON' => json_decode($response->getContent(), true),
                default => $response->getContent(),
            };
        } catch (ClientExceptionInterface $e) {
            $this->logger->warning(sprintf('[HttpService] Error %s for url %s : %s', $e->getCode(), $this->endPoint.$path, $e->getMessage()));
        }

        return null;
    }
}