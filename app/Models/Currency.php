<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $iso_code
 * @property string $description
 * @property-read Collection|Rate[] $fromRates {@see Currency::fromRates()}
 * @property-read Collection|Rate[] $toRates {@see Currency::toRates()}
 */
class Currency extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function fromRates(): HasMany
    {
        return $this->hasMany(Rate::class, 'from_currency_id', 'id');
    }

    public function toRates(): HasMany
    {
        return $this->hasMany(Rate::class, 'to_currency_id', 'id');
    }

    public function currentRates(): Collection
    {
        return $this->fromRates()
            ->whereIn('id', function (QueryBuilder $builder) {
                return $builder
                    ->select(DB::raw('MAX(id)'))
                    ->from('rates')
                    ->groupBy('to_currency_id');
            })->get();
    }
}
