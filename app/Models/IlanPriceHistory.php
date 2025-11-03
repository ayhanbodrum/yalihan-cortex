<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IlanPriceHistory extends Model
{
    public $timestamps = false;

    protected $table = 'ilan_price_history';

    protected $fillable = [
        'ilan_id',
        'old_price',
        'new_price',
        'currency',
        'change_reason',
        'changed_by',
        'additional_data',
        'created_at',
    ];

    protected $casts = [
        'old_price' => 'float',
        'new_price' => 'float',
        'additional_data' => 'array',
        'created_at' => 'datetime',
    ];

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
