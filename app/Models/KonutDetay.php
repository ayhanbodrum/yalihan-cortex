<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonutDetay extends Model
{
    protected $table = 'konut_detaylari';

    protected $fillable = [
        'ilan_id',
        'oda_sayisi',
        'banyo_sayisi',
        'metrekare',
        'isitma_tipi',
    ];
}
