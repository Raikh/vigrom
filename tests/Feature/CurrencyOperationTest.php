<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Currency;
use App\Operations\Currency\CurrencyOperation;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Config;
use Psr\SimpleCache\CacheInterface;
use Tests\FeatureTestCase;

class CurrencyOperationTest extends FeatureTestCase
{
    private CacheInterface $cache;
    private CurrencyOperation $currencyOperation;

    public function setUp(): void
    {
        parent::setUp();

        $this->cache = \Mockery::mock(CacheInterface::class);

        $this->currencyOperation = new CurrencyOperation($this->cache);
    }

    public function testGetByCode(): void
    {
        $result = $this->currencyOperation->getByCode('TST');

        $this->assertNull($result);

        $currency = new Currency();
        $currency->iso_code = 'TST';
        $currency->description = 'Test description';
        $currency->save();

        $result = $this->currencyOperation->getByCode('TST');

        $this->assertEquals($currency->id, $result->id);
        $this->assertEquals($currency->iso_code, $result->iso_code);
        $this->assertEquals($currency->description, $result->description);
    }

    public function testGetDefaultCurrency(): void
    {
        Config::set('vigrom.application.default_currency', 'TST');
        $currency = new Currency();
        $currency->iso_code = 'TST';
        $currency->description = 'Test description';
        $currency->save();

        $this->cache->shouldReceive('get')
            ->once()
            ->with($this->currencyOperation::DEFAULT_CURRENCY_CACHE_KEY)
            ->andReturnNull();
        $this->cache->shouldReceive('set')
            ->once()
            ->with(
                $this->currencyOperation::DEFAULT_CURRENCY_CACHE_KEY,
                \Mockery::type(Currency::class),
                \Mockery::type(CarbonInterval::class)
            )->andReturnTrue();

        $result = $this->currencyOperation->getDefaultCurrency();

        $this->assertEquals($currency->id, $result->id);
        $this->assertEquals($currency->iso_code, $result->iso_code);
        $this->assertEquals($currency->description, $result->description);
    }
}
