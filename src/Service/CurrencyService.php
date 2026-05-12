<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CurrencyService
{
    public function __construct(
        private readonly CacheInterface $cache,
    ) {}

    public function getUsdRate(): ?float
    {
        $value = $this->cache->get('usd_rate', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            return null;
        });

        return $value !== null ? (float) $value : null;
    }

    public function getBtcPrice(): ?float
    {
        $value = $this->cache->get('btc_price', function (ItemInterface $item) {
            $item->expiresAfter(300);
            return null;
        });

        return $value !== null ? (float) $value : null;
    }
}