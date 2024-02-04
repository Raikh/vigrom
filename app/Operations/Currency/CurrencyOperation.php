<?php

namespace App\Operations\Currency;

use App\Models\Currency;
use App\Models\Rate;
use Carbon\CarbonInterval;
use Illuminate\Support\Collection;
use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Exchange\FixedExchange;
use Money\Money;
use Psr\SimpleCache\CacheInterface;

class CurrencyOperation
{
    public const DEFAULT_CURRENCY_CACHE_KEY = 'defaultCurrency';
    public const CURRENCY_RATES_KEY = 'currencyRates';
    private CacheInterface $cache;

    public function __construct(
        CacheInterface $cache
    ) {
        $this->cache = $cache;
    }

    public function getByCode(string $isoCode, array $withRelations = []): ?Currency
    {
        return Currency::query()
            ->where('iso_code', '=', $isoCode)
            ->with($withRelations)
            ->first();
    }

    public function getDefaultCurrency(): ?Currency
    {
        $currency = $this->cache->get(static::DEFAULT_CURRENCY_CACHE_KEY);
        if ($currency !== null) {
            return $currency;
        }

        $currency = $this->getByCode(\config('vigrom.application.default_currency'));

        if ($currency === null) {
            throw new \Exception('not_found');
        }

        $this->cache->set(static::DEFAULT_CURRENCY_CACHE_KEY, $currency, CarbonInterval::minutes(10));

        return $currency;
    }

    public function getCurrencyRates(Currency $currency): Collection
    {
        $cacheKey = static::CURRENCY_RATES_KEY . '_' . $currency->id;
        $rates = $this->cache->get($cacheKey);
        if ($rates !== null) {
            return $rates;
        }

        $rates = $currency->currentRates();

        $this->cache->set($cacheKey, $rates, CarbonInterval::minutes(10));

        return $rates;
    }

    public function getDefaultToCurrencyRate(Currency $currency): ?Rate
    {
        return $this->getCurrencyRates($this->getDefaultCurrency())
            ->where('to_currency_id', '=', $currency->id)->first();
    }

    public function convertToCurrencyRate(Money $fromMoney, \Money\Currency $toCurrency, string $rate): Money
    {
        $exchange = new FixedExchange([
            $fromMoney->getCurrency()->getCode() => [
                $toCurrency->getCode() => $rate,
            ],
        ]);
        $converter = new Converter(
            new ISOCurrencies(),
            $exchange
        );
        return $converter->convert(
            $fromMoney,
            $toCurrency
        );
    }
}
