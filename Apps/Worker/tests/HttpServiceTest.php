<?php

namespace App\Tests;

use App\Service\HttpService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class HttpServiceTest extends TestCase
{
    public function testJsonFormat() {
        $mockHttpClient = new MockHttpClient(new MockResponse ('{"mock": true}'));
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMockForAbstractClass();
        $httpService = new HttpService($mockHttpClient, $logger, new ParameterBag(['endpoint'=>'nothing']));
        $response = $httpService->request('test');
        $this->assertTrue($response['mock']);
    }

    public function testClientExceptionThrown()
    {
        $mockHttpClient = new MockHttpClient(function () {
            throw new ClientException(new MockResponse('{"error": true}'));
        });
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMockForAbstractClass();
        $httpService = new HttpService($mockHttpClient, $logger, new ParameterBag(['endpoint'=>'nothing']));
        $this->assertNull($httpService->request('test'));
    }
}