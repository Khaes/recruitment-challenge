<?php

namespace App\Message;

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