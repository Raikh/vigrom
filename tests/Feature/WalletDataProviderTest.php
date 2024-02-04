<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Wallet;
use App\Operations\Wallet\WalletDataProvider;
use Tests\FeatureTestCase;

class WalletDataProviderTest extends FeatureTestCase
{
    public function testGetById(): void
    {
        $currency = Currency::factory(state: ['iso_code' => 'TST'])->create();
        $wallet = new Wallet();
        $wallet->currency_id = $currency->id;
        $wallet->balance = 100;
        $wallet->save();

        $dataProvider = new WalletDataProvider();

        $result = $dataProvider->getById($wallet->id);

        $this->assertEquals($wallet->id, $result->id);
        $this->assertEquals($wallet->currency_id, $result->currency_id);
        $this->assertEquals($wallet->balance, $result->balance);
    }
}
