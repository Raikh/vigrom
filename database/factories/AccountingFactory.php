<?php

namespace Database\Factories;

use App\Models\Accounting;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class AccountingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'wallet_id' => Wallet::factory()->create()->id,
            'type' => Arr::random(Accounting::TYPES),
            'reason' => Arr::random(Accounting::REASONS),
            'amount' => \mt_rand(1, 999999),
        ];
    }
}
