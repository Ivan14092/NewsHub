<?php

namespace App\MessageHandler;

use App\Message\UpdatePricesMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler]
final class UpdatePricesHandler
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface      $cache,
        private readonly LoggerInterface     $logger,
    )
    {
    }

    public function __invoke(UpdatePricesMessage $message): void
    {
        match ($message->symbol) {
            'BTC' => $this->updateBtcPrice(),
            'USD' => $this->updateUsdRate(),
            default => $this->logger->warning('Unknown symbol', ['symbol' => $message->symbol]),
        };
    }

    private function updateUsdRate(): void
    {
        try {
            $response = $this->httpClient->request('GET',
                'https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=5'
            );

            foreach ($response->toArray() as $currency) {
                if ($currency['ccy'] === 'USD') {
                    $rate = (float)$currency['sale'];
                    $this->saveToCache('usd_rate', $rate, 3600);
                    $this->logger->info('USD rate updated', ['rate' => $rate]);
                    return;
                }
            }
        } catch (\Throwable $e) {
            $this->logger->error('Failed to update USD', ['error' => $e->getMessage()]);
        }
    }

    private function updateBtcPrice(): void
    {
        try {
            $response = $this->httpClient->request('GET',
                'https://min-api.cryptocompare.com/data/price?fsym=BTC&tsyms=USD',
                ['timeout' => 5]
            );

            $data = $response->toArray();
            $price = (float)($data['USD'] ?? 0.0);
            $this->saveToCache('btc_price', $price, 300);
            $this->logger->info('BTC price updated', ['price' => $price]);

        } catch (\Throwable $e) {
            $this->logger->error('Failed to update BTC', [
                'error' => $e->getMessage(),
                'class' => get_class($e),
            ]);
        }
    }

    private function saveToCache(string $key, ?float $value, int $ttl): void
    {
        $this->cache->delete($key);

        if ($value === null) {
            return; // не зберігаємо null
        }

        $this->cache->get($key, function (ItemInterface $item) use ($value, $ttl) {
            $item->expiresAfter($ttl);
            return $value;
        });
    }
}