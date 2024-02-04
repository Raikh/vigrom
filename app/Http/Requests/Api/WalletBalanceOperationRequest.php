<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Accounting;
use App\Rules\IsoCurrencyRule;
use Illuminate\Foundation\Http\FormRequest;

class WalletBalanceOperationRequest extends FormRequest
{
    public function rules(): array
    {
        $allowedReasons = \implode(',', Accounting::REASONS);
        $allowedTypes = \implode(',', Accounting::TYPES);

        return [
            'wallet_id' => 'required|int|min:1',
            'type' => "required|string|in:{$allowedTypes}",
            'reason' => "required|string|in:{$allowedReasons}",
            'amount' => 'required|integer|min:1',
            'iso_currency_code' => [
                'required',
                'string',
                'size:3',
                new IsoCurrencyRule()
            ],
        ];
    }

    public function getWalletId(): int
    {
        return (int) $this->validated()['wallet_id'];
    }

    public function getType(): string
    {
        return $this->validated()['type'];
    }

    public function getReason(): string
    {
        return $this->validated()['reason'];
    }

    public function getAmount(): string
    {
        return (string) $this->validated()['amount'];
    }

    public function getCurrencyCode(): string
    {
        return $this->validated()['iso_currency_code'];
    }
}
