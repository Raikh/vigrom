<?php

declare(strict_types=1);

namespace App\Operations;

use App\Models\Accounting;

class AccountingOperation
{
    public function create(
        int $walletId,
        string $amount,
        string $type,
        string $reason,
        array $payload = [],
    ): Accounting {
        $record = new Accounting();
        $record->wallet_id = $walletId;
        $record->amount = $amount;
        $record->type = $type;
        $record->reason = $reason;

        if (!empty($payload)) {
            $record->payload = $payload;
        }

        $record->save();

        return $record;
    }
}
