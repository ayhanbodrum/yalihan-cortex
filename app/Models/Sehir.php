<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated Bu model Context7 kurallarına aykırıdır. Il model'ini kullanın.
 * @see Il
 */
class Sehir extends Model
{
    use HasFactory;

    protected $table = 'iller'; // Context7 uyumlu - iller yerine iller

    protected $fillable = [
        'il_adi', // Context7 uyumlu - il_adi yerine il_adi
        'ulke_id',
        'status', // Context7 uyumlu - status yerine status
        'plaka_kodu',
        'telefon_kodu',
    ];

    protected $casts = [
        'status' => 'boolean', // Context7 uyumlu
    ];

    public function ulke()
    {
        return $this->belongsTo(Ulke::class, 'ulke_id');
    }

    public function ilceler()
    {
        return $this->hasMany(Ilce::class, 'il_id');
    }
}
