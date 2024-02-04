<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletFactory extends Factory
{

    public function definition(): array
    {
        return [
            'currency_id' => Currency::factory()->create([
                'iso_code' => fake()->unique()->currencyCode()
            ])->id,
            'balance' => \mt_rand(1, 99999999),
        ];
    }
}
