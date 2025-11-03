<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bolge extends Model
{
    use HasFactory;

    /**
     * Model için kullanılacak tablo
     */
    protected $table = 'bolgeler';

    /**
     * Toplu atama yapılabilecek özellikler
     */
    protected $fillable = [
        'ulke_id',
        'bolge_adi',
    ];

    /**
     * Bir bölgenin ait olduğu ülke
     */
    public function ulke()
    {
        return $this->belongsTo(Ulke::class, 'ulke_id');
    }

    /**
     * Bir bölgenin birden çok ili olabilir
     * Context7: // Context7: // Context7: // Context7: // Context7: // Context7: region_id kaldırıldı kaldırıldı kaldırıldı kaldırıldı kaldırıldı kaldırıldı, bu relationship kullanılmamalı
     */
    // public function iller()
    // {
    //     return $this->hasMany(Il::class, '// Context7: // Context7: // Context7: // Context7: // Context7: region_id kaldırıldı kaldırıldı kaldırıldı kaldırıldı kaldırıldı');
    // }
}
