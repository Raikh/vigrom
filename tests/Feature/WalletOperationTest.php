<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Rate;
use App\Models\Wallet;
use App\Operations\Currency\CurrencyOperation;
use App\Operations\Wallet\WalletDataProvider;
use App\Operations\Wallet\WalletOperation;
use Mockery\MockInterface;
use Money\Money;
use Tests\FeatureTestCase;

class WalletOperationTest extends FeatureTestCase
{
    public function testGetWalletInfo(): void
    {
        $usdCurrency = Currency::firstWhere('iso_code', '=', 'USD');
        if ($usdCurrency === null) {
            $usdCurrency = Currency::factory(state: ['iso_code' => 'USD'])->create();
        }

        $currency = Currency::factory(state: ['iso_code' => 'CAD'])->create();
        $rate = new Rate();
        $rate->from_currency_id = $usdCurrency->id;
        $rate->to_currency_id = $currency->id;
        $rate->rate = 0.8;
        $rate->save();
        $wallet = new Wallet();
        $wallet->currency_id = $currency->id;
        $wallet->balance = 100;
        $wallet->save();

        $this->mock(WalletDataProvider::class, static function (MockInterface $mock) use ($wallet): void {
            $mock->shouldReceive('getById')
                ->once()
                ->with($wallet->id, ['currency'])
                ->andReturn($wallet);
        });
        $this->mock(CurrencyOperation::class, static function (MockInterface $mock) use ($usdCurrency, $wallet, $rate): void {
            $walletMoney = new Money($wallet->balance, new \Money\Currency($wallet->currency->iso_code));
            $defaultCurrency = new \Money\Currency($usdCurrency->iso_code);

            $mock->shouldReceive('getDefaultCurrency')
                ->once()
                ->andReturn($usdCurrency);
            $mock->shouldReceive('getDefaultToCurrencyRate')
                ->once()
                ->with($wallet->currency)
                ->andReturn($rate);
            $mock->shouldReceive('convertToCurrencyRate')
                ->once()
                ->withArgs(static function (Money $money, \Money\Currency $currency, string $rate) use ($walletMoney, $defaultCurrency) {
                    return $walletMoney->getCurrency()->equals($money->getCurrency())
                        && $currency->equals($defaultCurrency)
                        && $rate === '1.250000000000';
                })
                ->andReturn($walletMoney->multiply('1.250000000000'));
        });

        /** @var WalletOperation $operation */
        $operation = $this->app->make(WalletOperation::class);

        $result = $operation->getWalletInfo($wallet->id);
        $this->assertEquals(
            [
                'wallet_currency' => 'CAD',
                'wallet_balance' => 1.00,
                'default_currency_code' => 'USD',
                'default_currency_balance' => 1.25,
                'rate' => 0.8,
            ],
            $result->jsonSerialize()
        );
    }
}
