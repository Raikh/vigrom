<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Accounting;
use App\Models\Currency;
use App\Models\Rate;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $usd = Currency::factory()->create(['iso_code' => 'USD']);
        $rub = Currency::factory()->create(['iso_code' => 'RUB']);

        $usdRate = new Rate();
        $usdRate->from_currency_id = $usd->id;
        $usdRate->to_currency_id = $usd->id;
        $usdRate->rate = 1;
        $usdRate->save();

        $rubRate = new Rate();
        $rubRate->from_currency_id = $usd->id;
        $rubRate->to_currency_id = $rub->id;
        $rubRate->rate = \mt_rand(10, 100) / \mt_rand(1, 10);
        $rubRate->save();

        for ($i = 0; $i < 20; $i++) {
            $wallet = $this->generateWallet(Arr::random([$usd->id, $rub->id]));

            for ($j = 0; $j < 5; $j++) {
                $accounting = new Accounting();
                $accounting->wallet_id = $wallet->id;
                $accounting->type = Arr::random(Accounting::TYPES);
                $accounting->reason = Arr::random(Accounting::REASONS);
                $accounting->amount = \mt_rand(1, 999999);
                $accounting->save();
            }
        }
    }

    private function generateWallet(int $currencyId): Wallet
    {
        $wallet = new Wallet();
        $wallet->currency_id = $currencyId;
        $wallet->balance = \mt_rand(1, 99999999);
        $wallet->save();

        return $wallet;
    }
}
