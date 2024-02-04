<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class RateFactory extends Factory
{

    public function definition(): array
    {
        return [
            'from_currency_id' => Currency::factory()->create([
                'iso_code' => fake()->unique()->currencyCode()
            ])->id,
            'to_currency_id' => Currency::factory()->create([
                'iso_code' => fake()->unique()->currencyCode()
            ])->id,
            'rate' => \mt_rand(10, 100) / \mt_rand(1, 10),
        ];
    }
}
