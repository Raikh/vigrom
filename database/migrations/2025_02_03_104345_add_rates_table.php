<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rates', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedInteger('from_currency_id');
            $table->unsignedInteger('to_currency_id');
            $table->foreign('from_currency_id')
                ->references('id')->on('currencies');
            $table->foreign('to_currency_id')
                ->references('id')->on('currencies');
            $table->unsignedDecimal('rate', 18, 9);
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
