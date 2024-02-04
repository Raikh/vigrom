<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Operations\Currency\CurrencyOperation;
use Money\Currency;
use Money\Money;
use Psr\SimpleCache\CacheInterface;
use PHPUnit\Framework\TestCase;

class CurrencyOperationTest extends TestCase
{
    /** @dataProvider dataProviderConvertToCurrencyRate */
    public function testConvertToCurrencyRate(
        Money $money,
        Currency $currency,
        string $rate,
        string $expectedResult
    ): void {
        $cache = \Mockery::mock(CacheInterface::class);
        $operation = new CurrencyOperation($cache);

        $result = $operation->convertToCurrencyRate($money, $currency, $rate);

        $this->assertEquals($expectedResult, $result->getAmount());
    }

    public static function dataProviderConvertToCurrencyRate(): array
    {
        return [
            [
                new Money(100, new Currency('RUB')),
                new Currency('USD'),
                \bcdiv('1', '100', 12),
                '1',
            ],
            [
                new Money(0, new Currency('RUB')),
                new Currency('USD'),
                \bcdiv('1', '100', 12),
                '0',
            ],
            [
                new Money(100, new Currency('RUB')),
                new Currency('USD'),
                \bcdiv('100', '1', 12),
                '10000',
            ],
        ];
    }
}
