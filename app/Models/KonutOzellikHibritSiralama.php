<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Konut Özellik Hibrit Sıralama Modeli
 *
 * Context7 Compliant
 */
class KonutOzellikHibritSiralama extends Model
{
    use HasFactory;

    protected $table = 'konut_ozellik_hibrit_siralama';

    protected $fillable = [
        'status', // Context7: aktif → status (migration: 2025_11_11_103353)
        'siralama',
    ];

    protected $casts = [
        'status' => 'boolean', // Context7: aktif → status
        'siralama' => 'integer',
    ];

    public $timestamps = true;

    /**
     * Aktif kayıtlar
     * Context7: aktif → status
     */
    public function scopeActive($query)
    {
        return $query->where('status', true); // Context7: aktif → status
    }

    /**
     * Sıralı kayıtlar
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('siralama', 'asc');
    }
}
