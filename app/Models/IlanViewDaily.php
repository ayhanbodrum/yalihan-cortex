<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IlanViewDaily extends Model
{
    use HasFactory;

    protected $table = 'ilan_goruntulenme_gunluk';

    protected $fillable = [
        'ilan_id',
        'tarih',
        'cihaz',
        'adet',
    ];

    protected $casts = [
        'tarih' => 'date',
        'adet' => 'integer',
    ];

    public function ilan()
    {
        return $this->belongsTo(Ilan::class, 'ilan_id');
    }
}