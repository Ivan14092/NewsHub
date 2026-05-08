<?php

namespace App\Twig\Extension;

use App\Repository\CategoryRepository;
use App\Service\CurrencyService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly CurrencyService $currencyService,
    ){}
    public function getGlobals(): array
    {
        return [
            'categories' => $this->categoryRepository->findAllOrdered(),
            'usd_rate' => $this->currencyService->getUSDRate(),
            'btc_price' => $this->currencyService->getBTCPrice(),
        ];
    }
}
