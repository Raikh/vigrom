<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accountings', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('wallet_id');
            $table->foreign('wallet_id')
                ->references('id')->on('wallets');
            $table->bigInteger('amount')->default(0);
            $table->string('type', 64);
            $table->string('reason', 64);
            $table->json('payload')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(['type', 'reason']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accountings');
    }
};
