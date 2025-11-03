<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyHistory extends Model
{
    protected $table = 'currencies_history';

    protected $fillable = [
        'base_currency',
        'target_currency',
        'rate',
        'source',
        'recorded_at',
    ];

    protected $casts = [
        'rate' => 'decimal:6',
        'recorded_at' => 'datetime',
    ];

    public static function getLatestRate($baseCurrency, $targetCurrency)
    {
        return static::where('base_currency', $baseCurrency)
            ->where('target_currency', $targetCurrency)
            ->latest('recorded_at')
            ->first();
    }

    public static function getRateHistory($baseCurrency, $targetCurrency, $days = 30)
    {
        return static::where('base_currency', $baseCurrency)
            ->where('target_currency', $targetCurrency)
            ->where('recorded_at', '>=', now()->subDays($days))
            ->orderBy('recorded_at')
            ->get();
    }
}
