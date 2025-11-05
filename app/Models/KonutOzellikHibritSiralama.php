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
        'active',
        'siralama',
    ];

    protected $casts = [
        'active' => 'boolean',
        'siralama' => 'integer',
    ];

    public $timestamps = true;

    /**
     * Aktif kayıtlar
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Sıralı kayıtlar
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('siralama', 'asc');
    }
}
