<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Accounting;
use App\Models\Currency;
use App\Models\Wallet;
use App\Operations\AccountingOperation;
use Tests\FeatureTestCase;

class AccountingOperationTest extends FeatureTestCase
{
    public function testCreate(): void
    {
        $currency = Currency::factory(state: ['iso_code' => 'TST'])->create();
        $wallet = new Wallet();
        $wallet->currency_id = $currency->id;
        $wallet->balance = 100;
        $wallet->save();

        $operation = new AccountingOperation();

        $result = $operation->create(
            $wallet->id,
            '1000',
            Accounting::TYPE_CREDIT,
            Accounting::REASON_STOCK,
            ['payload']
        );

        $json = \json_encode(['payload']);
        $this->assertDatabaseHas('accountings', [
            'wallet_id' => $wallet->id,
            'amount' => 1000,
            'type' => Accounting::TYPE_CREDIT,
            'reason' => Accounting::REASON_STOCK,
            'payload' => \DB::raw("CAST('{$json}' AS JSON)"),
        ]);
    }
}
