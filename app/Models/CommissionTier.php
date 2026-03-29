<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionTier extends Model
{
    protected $fillable = [
        'type',
        'min_threshold',
        'max_threshold',
        'rate',
        'is_active',
    ];

    protected $casts = [
        'min_threshold' => 'decimal:2',
        'max_threshold' => 'decimal:2',
        'rate'          => 'decimal:2',
        'is_active'     => 'boolean',
    ];

    public static function getApplicableTier(string $type, float $value): ?self
    {
        return self::where('type', $type)
            ->where('is_active', true)
            ->where('min_threshold', '<=', $value)
            ->where(function ($query) use ($value) {
                $query->whereNull('max_threshold')
                      ->orWhere('max_threshold', '>=', $value);
            })
            ->orderByDesc('min_threshold')
            ->first();
    }
}