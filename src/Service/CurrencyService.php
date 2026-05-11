<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

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
            return $this->cache->get('usd_rate', function (ItemInterface $item) {
                $item->expiresAfter(3600);
                return 0.0;
            });
        } catch (\Throwable) {
            return 0.0;
        }
    }

    public function getBtcPrice(): float
    {
        try {
            return $this->cache->get('btc_price', function (ItemInterface $item) {
                $item->expiresAfter(300);
                return 0.0;
            });
        } catch (\Throwable) {
            return 0.0;
        }
    }
}