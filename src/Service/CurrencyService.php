<?php

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;

class CurrencyService
{
    public function __construct(
        private readonly CacheItemPoolInterface $cache,
    ) {}

    public function getUsdRate(): ?float
    {
        try {
            $item = $this->cache->getItem('usd_rate');
            return $item->isHit() ? (float) $item->get() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function getBtcPrice(): ?float
    {
        try {
            $item = $this->cache->getItem('btc_price');
            return $item->isHit() ? (float) $item->get() : null;
        } catch (\Throwable) {
            return null;
        }
    }
}