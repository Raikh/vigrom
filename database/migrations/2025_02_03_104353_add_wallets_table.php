<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedInteger('currency_id');
            $table->foreign('currency_id')
                ->references('id')->on('currencies');
            $table->bigInteger('balance')->default(0);
//            $table->decimal('balance', 18, 9)->default(0);
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
