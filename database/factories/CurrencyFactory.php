<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'iso_code' => fake()->unique()->currencyCode(),
            'description' => fake()->unique()->text,
        ];
    }
}
