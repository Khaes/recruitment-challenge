<?php

namespace App\Message;


use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
readonly class CapitalMessage
{
    public function __construct(private string $code)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }
}