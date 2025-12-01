<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsaIstatistik extends Model
{
    protected $table = 'arsa_istatistikleri';

    protected $fillable = [
        'arsa_id',
        'gosterim_sayisi',
        'favori_sayisi',
    ];
}
