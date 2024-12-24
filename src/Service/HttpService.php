<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class HttpService
{
    private string $endPoint;
    public function __construct(
        private HttpClientInterface $httpClient,
        ParameterBagInterface $parameterBag)
    {
        $this->endPoint = $parameterBag->get('endpoint');
    }

    public function request(string $path, string $method = 'GET', array $options = [], string $responseFormat = 'JSON')
    {
        //TODO: implements cache system here
        try {
            $response = $this->httpClient->request($method, $this->endPoint.$path, $options);
            if ($responseFormat === 'JSON') {
                return json_decode($response->getContent(), true);
            }
        } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {

        }

        return null;
    }
}