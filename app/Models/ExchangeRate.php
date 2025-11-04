<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Exchange Rate Model
 * 
 * TCMB (Türkiye Cumhuriyet Merkez Bankası) döviz kurları
 * Context7: Real-time currency rates with history
 */
class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency_code',
        'rate_date',
        'rate_to_try',
        'buying_rate',
        'selling_rate',
        'source',
        // Legacy fields
        'base_currency',
        'currency',
        'buy_rate',
        'sell_rate',
        'mid_rate',
        'provider',
        'fetched_at',
        'status',
    ];

    protected $casts = [
        'rate_to_try' => 'float',
        'buying_rate' => 'float',
        'selling_rate' => 'float',
        'rate_date' => 'date',
        // Legacy
        'buy_rate' => 'float',
        'sell_rate' => 'float',
        'mid_rate' => 'float',
        'status' => 'boolean',
        'fetched_at' => 'datetime',
    ];
    
    /**
     * Get latest rate for a currency
     * 
     * @param string $currencyCode
     * @return float|null
     */
    public static function getLatestRate($currencyCode)
    {
        $rate = static::where('currency_code', $currencyCode)
            ->latest('rate_date')
            ->first();
        
        return $rate ? $rate->selling_rate : null;
    }
    
    /**
     * Get today's rate
     * 
     * @param string $currencyCode
     * @return static|null
     */
    public static function getTodayRate($currencyCode)
    {
        return static::where('currency_code', $currencyCode)
            ->whereDate('rate_date', today())
            ->first();
    }
    
    /**
     * Scope: Latest rates
     */
    public function scopeLatest($query)
    {
        return $query->whereDate('rate_date', today());
    }
    
    /**
     * Scope: By currency
     */
    public function scopeCurrency($query, $code)
    {
        return $query->where('currency_code', $code);
    }
}
