<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface      $cache,
    )
    {
    }


    public function getUsdRate(): float
    {
        return $this->cache->get('usd_rate', function (ItemInterface $item) {
            $item->expiresAfter(3600); // кеш на 1 годину

            $response = $this->httpClient->request('GET',
                'https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=5'
            );

            $data = $response->toArray();

            foreach ($data as $currency) {
                if ($currency['ccy'] === 'USD') {
                    return (float)$currency['sale'];
                }
            }

            return 0.0;
        });
    }

    public function getBtcPrice(): float
    {
        return $this->cache->get('btc_price', function (ItemInterface $item) {
            $item->expiresAfter(300);

            $response = $this->httpClient->request('GET',
                'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd'
            );

            $data = $response->toArray();

            return (float)($data['bitcoin']['usd'] ?? 0.0);
        });
    }
}
