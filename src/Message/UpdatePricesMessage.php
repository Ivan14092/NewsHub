<?php

namespace App\Message;

final class UpdatePricesMessage
{
    public function __construct(
        public readonly string $symbol,
    )
    {
    }
}