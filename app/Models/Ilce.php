<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ilce extends Model
{
    use HasFactory;

    protected $table = 'ilceler';

    protected $fillable = [
        'il_id',
        'ilce_adi',
        'ilce_kodu',
    ];

    /**
     * Bir ilçenin ait olduğu il
     */
    public function il()
    {
        return $this->belongsTo(Il::class, 'il_id');
    }

    /**
     * Bir ilçenin birden çok mahallesi olabilir
     */
    public function mahalleler()
    {
        return $this->hasMany(Mahalle::class, 'ilce_id');
    }
}
