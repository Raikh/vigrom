<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\Rate;
use App\Operations\Currency\CurrencyOperation;
use Illuminate\Console\Command;
use Psr\SimpleCache\CacheInterface;

class RefreshRatesCommand extends Command
{
    protected $signature = 'refresh_rates';
    protected $description = 'Refresh Rates';

    public function handle(CurrencyOperation $currencyOperation, CacheInterface $cache)
    {
        $defaultCurrency = $currencyOperation->getDefaultCurrency();

        Currency::query()->each(static function (Currency $currency) use ($defaultCurrency) {
            $rate = new Rate();
            $rate->from_currency_id = $defaultCurrency->id;
            $rate->to_currency_id = $currency->id;

            $rate->rate = $currency->id === $defaultCurrency->id
                ? 1
                : \mt_rand(50, 100) / \mt_rand(1, 10);
            $rate->save();
        });

        $cache->clear();
    }
}
