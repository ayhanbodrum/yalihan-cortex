<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonutIstatistik extends Model
{
    protected $table = 'konut_istatistikleri';

    protected $fillable = [
        'konut_id',
        'gosterim_sayisi',
        'favori_sayisi',
    ];
}
