<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Currency;
use App\Models\Rate;
use App\Models\Wallet;
use Tests\FeatureTestCase;

class WalletControllerTest extends FeatureTestCase
{
    public function testGetWallet(): void
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

        $response = $this->getJson("/api/wallet/{$wallet->id}");

        $response->assertOk()
            ->assertJson([
                'wallet_currency' => $currency->iso_code,
                'wallet_balance' => 1.00,
                'default_currency_code' => 'USD',
                'default_currency_balance' => 1.25,
                'rate' => 0.8,
            ]);
    }
}
