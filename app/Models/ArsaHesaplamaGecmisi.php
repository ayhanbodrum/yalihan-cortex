<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsaHesaplamaGecmisi extends Model
{
    protected $table = 'arsa_hesaplama_gecmisi';

    protected $fillable = [
        'arsa_id',
        'tarih',
        'hesaplanan_deger',
        'hesaplama_tipi',
    ];
}
