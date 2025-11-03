<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahalle extends Model
{
    use HasFactory;

    protected $table = 'mahalleler';

    protected $fillable = [
        'ilce_id',
        'mahalle_kodu',
        'mahalle_adi',
        'enlem',
        'boylam',
        'posta_kodu',
        'status',
    ];

    /**
     * İlçe ilişkisi
     */
    public function ilce()
    {
        return $this->belongsTo(Ilce::class, 'ilce_id');
    }

    /**
     * İl ilişkisi (ilçe üzerinden)
     */
    public function il()
    {
        return $this->hasOneThrough(Il::class, Ilce::class, 'id', 'id', 'ilce_id', 'il_id');
    }
}
