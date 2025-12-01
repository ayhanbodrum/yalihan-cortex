<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsaDetay extends Model
{
    protected $table = 'arsa_detaylari';

    protected $fillable = [
        'ilan_id',
        'parsel_no',
        'yuzolcumu',
        'imar_statusu',
        'tapu_statusu',
    ];
}
