<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;

class CurrencyService
{
    public function __construct(
        private readonly CacheInterface $cache,
    )
    {
    }

    public function getUsdRate(): float
    {
        try {
            $value = $this->cache->getItem('usd_rate');
            return $value->isHit() ? (float)$value->get() : 0.0;
        } catch (\Throwable) {
            return 0.0;
        }
    }

    public function getBtcPrice(): float
    {
        try {
            $value = $this->cache->getItem('btc_price');
            return $value->isHit() ? (float)$value->get() : 0.0;
        } catch (\Throwable) {
            return 0.0;
        }
    }
}