<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsaOzellik extends Model
{
    protected $table = 'arsa_ozellikleri';
    protected $fillable = [
        'arsa_id',
        'ozellik_adi',
        'ozellik_degeri',
    ];
}
