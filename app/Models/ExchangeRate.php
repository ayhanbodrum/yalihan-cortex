<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'base_currency',
        'currency',
        'buy_rate',
        'sell_rate',
        'mid_rate',
        'provider',
        'source',
        'fetched_at',
        'status',
    ];

    protected $casts = [
        'buy_rate' => 'float',
        'sell_rate' => 'float',
        'mid_rate' => 'float',
        'status' => 'boolean',
        'fetched_at' => 'datetime',
    ];
}
