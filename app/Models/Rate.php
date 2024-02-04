<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $from_currency_id
 * @property int $to_currency_id
 * @property string $rate
 * @property CarbonInterface $created_at
 * @property CarbonInterface|null $updated_at
 */
class Rate extends Model
{
    use HasFactory;

    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency_id', 'id');
    }

    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_currency_id', 'id');
    }
}
