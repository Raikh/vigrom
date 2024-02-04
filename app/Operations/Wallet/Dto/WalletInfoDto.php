<?php

declare(strict_types=1);

namespace App\Operations\Wallet\Dto;

class WalletInfoDto implements \JsonSerializable
{

    private string $walletCurrencyCode;
    private string $walletCurrencyBalance;
    private ?string $defaultCurrencyCode;
    private ?string $walletInDefaultCurrencyBalance;
    private ?string $exchangeRate;

    public function __construct(
        string $walletCurrencyCode,
        string $walletCurrencyBalance,
        ?string $defaultCurrencyCode = null,
        ?string $walletInDefaultCurrencyBalance = null,
        ?string $exchangeRate = null
    ) {
        $this->walletCurrencyCode = $walletCurrencyCode;
        $this->walletCurrencyBalance = $walletCurrencyBalance;
        $this->defaultCurrencyCode = $defaultCurrencyCode;
        $this->walletInDefaultCurrencyBalance = $walletInDefaultCurrencyBalance;
        $this->exchangeRate = $exchangeRate;
    }

    public function jsonSerialize(): mixed
    {
        $data = [
            'wallet_currency' => $this->walletCurrencyCode,
            'wallet_balance' => $this->walletCurrencyBalance,
        ];

        if ($this->defaultCurrencyCode !== null) {
            $data['default_currency_code'] = $this->defaultCurrencyCode;
            $data['default_currency_balance'] = $this->walletInDefaultCurrencyBalance;
            $data['rate'] = $this->exchangeRate;
        }

        return $data;
    }
}
