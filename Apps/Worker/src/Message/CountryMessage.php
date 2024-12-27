<?php

namespace App\Message;

readonly class CountryMessage
{
    public function __construct(private string $code)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }
}