<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonutHesaplamaGecmisi extends Model
{
    protected $table = 'konut_hesaplama_gecmisi';
    protected $fillable = [
        'konut_id',
        'tarih',
        'hesaplanan_deger',
        'hesaplama_tipi',
    ];
}
