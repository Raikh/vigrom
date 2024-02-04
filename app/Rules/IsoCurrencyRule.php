<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Money\Currencies\ISOCurrencies;
use Money\Currency;

class IsoCurrencyRule implements ValidationRule
{

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isoCurrencies = new ISOCurrencies();

        $currency = new Currency($value);

        if ($isoCurrencies->contains($currency) === false) {
            $fail('The :attribute must be valid ISO Currency code.');
        }
    }
}
