<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $wallet_id
 * @property string $amount
 * @property string $type
 * @property string $reason
 * @property array|null $payload
 * @property CarbonInterface $created_at
 * @property CarbonInterface|null $updated_at
 * @property CarbonInterface|null $deleted_at
 * @property-read Wallet|null $wallet
 */
class Accounting extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_DEBIT = 'debit';
    public const TYPE_CREDIT = 'credit';
    public const REASON_STOCK = 'stock';
    public const REASON_REFUND = 'refund';

    public const TYPES = [
        self::TYPE_CREDIT,
        self::TYPE_DEBIT,
    ];

    public const REASONS = [
        self::REASON_REFUND,
        self::REASON_STOCK,
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_id', 'id');
    }
}
