<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\MoneyFormatter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(MoneyFormatter::class, static function (): MoneyFormatter {
            return new DecimalMoneyFormatter(new ISOCurrencies());
        });
    }
}
