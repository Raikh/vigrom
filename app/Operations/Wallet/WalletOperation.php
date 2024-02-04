<?php

namespace App\Operations\Wallet;

use App\Models\Accounting;
use App\Models\Rate;
use App\Models\Wallet;
use App\Operations\AccountingOperation;
use App\Operations\Currency\CurrencyOperation;
use App\Operations\Wallet\Dto\WalletInfoDto;
use Illuminate\Support\Facades\DB;
use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exchange\FixedExchange;
use Money\Money;
use Money\MoneyFormatter;

class WalletOperation
{
    private WalletDataProvider $walletDataProvider;
    private CurrencyOperation $currencyOperation;
    private AccountingOperation $accountingOperation;
    private MoneyFormatter $moneyFormatter;

    public function __construct(
        WalletDataProvider $walletDataProvider,
        CurrencyOperation $currencyOperation,
        AccountingOperation $accountingOperation,
        MoneyFormatter $moneyFormatter
    ) {
        $this->walletDataProvider = $walletDataProvider;
        $this->currencyOperation = $currencyOperation;
        $this->accountingOperation = $accountingOperation;
        $this->moneyFormatter = $moneyFormatter;
    }

    public function getWalletInfo(int $walletId): WalletInfoDto
    {
        $wallet = $this->walletDataProvider->getById($walletId, ['currency']);

        if ($wallet === null) {
            throw new \Exception('not_found');
        }

        $money = $this->buildWalletMoney($wallet);
        $defaultCurrency = $this->currencyOperation->getDefaultCurrency();

        if ($defaultCurrency->id === $wallet->currency_id) {
            return new WalletInfoDto(
                $money->getCurrency()->getCode(),
                $this->moneyFormatter->format($money)
            );
        }

        $defaultMoneyCurrency = new Currency($defaultCurrency->iso_code);
        $rateForWalletCurrency = $this->currencyOperation->getDefaultToCurrencyRate($wallet->currency);

        $walletMoneyInDefaultCurrency = $this->currencyOperation->convertToCurrencyRate(
            $money,
            $defaultMoneyCurrency,
            \bcdiv('1', $rateForWalletCurrency->rate, 12)
        );

        return new WalletInfoDto(
            $money->getCurrency()->getCode(),
            $this->moneyFormatter->format($money),
            $defaultMoneyCurrency->getCode(),
            $this->moneyFormatter->format($walletMoneyInDefaultCurrency),
            $rateForWalletCurrency->rate
        );
    }

    public function makeTransaction(
        int $walletId,
        string $currencyCode,
        string $value,
        string $operationType,
        string $reason
    ): void {
        $wallet = $this->walletDataProvider->getById($walletId, ['currency']);
        $payload = [];

        if ($wallet === null) {
            throw new \Exception('not found');
        }

        Wallet::lockForUpdate()->find($wallet->id);
        $walletMoney = $this->buildWalletMoney($wallet);

        $operationMoney = new Money($value, new Currency($currencyCode));

        if ($operationMoney->getCurrency()->equals($walletMoney->getCurrency()) === false) {
            $defaultCurrencyRates = $this->currencyOperation->getCurrencyRates(
                $this->currencyOperation->getDefaultCurrency()
            );

            $operationMoneyCurrency = $this->currencyOperation->getByCode($currencyCode);
            /** @var Rate $operationMoneyRate */
            $operationMoneyRate = $defaultCurrencyRates
                ->where('to_currency_id', $operationMoneyCurrency->id)->first();
            $rateForWalletCurrency = $this->currencyOperation->getDefaultToCurrencyRate($wallet->currency);
            $conversionRate = \bcmul(
                $rateForWalletCurrency->rate,
                \bcdiv(
                    '1',
                    $operationMoneyRate->rate ?? '1',
                    12
                ),
                12
            );
            $operationMoney = $this->currencyOperation->convertToCurrencyRate(
                $operationMoney,
                $walletMoney->getCurrency(),
                $conversionRate
            );

            $payload = [
                'wallet_currency_code' =>$walletMoney->getCurrency()->getCode(),
                'currency_code' => $currencyCode,
                'amount' => $value,
                'conversion_rate' => $conversionRate,
                'converted_amount' => $operationMoney->getAmount(),
            ];
        }

        try {
            DB::beginTransaction();

            $walletMoney = $this->calculateMoneyTransfer(
                $walletMoney,
                $operationMoney,
                $operationType
            );

            $this->accountingOperation->create(
                $walletId,
                $this->moneyFormatter->format($operationMoney),
                $operationType,
                $reason,
                $payload
            );

            $wallet->balance = $walletMoney->getAmount();
            $wallet->save();

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();

            throw $throwable;
        }
    }

    private function calculateMoneyTransfer(Money $currentMoney, Money $operationMoney, string $type): Money
    {
        return match ($type) {
            Accounting::TYPE_DEBIT => $currentMoney->add($operationMoney),
            Accounting::TYPE_CREDIT => $currentMoney->subtract($operationMoney),
            default => throw new \Exception('unknown_type'),
        };
    }

    private function buildWalletMoney(Wallet $wallet): Money
    {
        return new Money($wallet->balance, new Currency($wallet->currency->iso_code));
    }
}
