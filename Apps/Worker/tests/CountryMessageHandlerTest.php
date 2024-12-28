<?php

namespace App\tests;

use App\Handler\CountryMessageHandler;
use App\Message\CapitalMessage;
use App\Message\CountryMessage;
use App\Service\HttpService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\MessageBusInterface;

class CountryMessageHandlerTest extends TestCase
{
    protected function setUp(): void
    {
    }

    public function testCountryDispatchToCapital()
    {
        $json = [['capital' => ['Paris']]];
        $httpService = $this->getMockBuilder(HttpService::class)->disableOriginalConstructor()->onlyMethods(['request'])->getMockForAbstractClass();
        $httpService->expects($this->once())->method('request')->willReturn($json);
        $dispatcher = $this->getMockBuilder(MessageBusInterface::class)->disableOriginalConstructor()->onlyMethods(['dispatch'])->getMockForAbstractClass();
        $dispatcher->expects($this->once())->method('dispatch')->willReturn(new Envelope(new CapitalMessage('paris')));;
        $capitalMessageHandler = new CountryMessageHandler($httpService, $dispatcher);
        $capitalMessageHandler->__invoke(new CountryMessage('fr'));
    }
}