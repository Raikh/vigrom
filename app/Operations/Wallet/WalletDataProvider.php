<?php

declare(strict_types=1);

namespace App\Operations\Wallet;

use App\Models\Wallet;

class WalletDataProvider
{
    public function getById(int $walletId, array $withRelations = []): ?Wallet
    {
        return Wallet::query()
            ->where('id', '=', $walletId)
            ->with($withRelations)
            ->first();
    }
}
