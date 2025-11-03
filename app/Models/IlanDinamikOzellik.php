<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IlanDinamikOzellik extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ilan_dinamik_ozellikler';

    protected $fillable = [
        'ilan_id',
        'ozellik_kategori_id',
        'deger',
        'birim',
        'notlar',
        'status', // Context7: status → status
    ];

    protected $casts = [
        'status' => 'boolean', // Context7: status → status
    ];

    /**
     * İlan ilişkisi
     */
    public function ilan()
    {
        return $this->belongsTo(\App\Modules\Emlak\Models\Ilan::class);
    }

    /**
     * Özellik kategorisi ilişkisi
     */
    public function ozellikKategori()
    {
        return $this->belongsTo(OzellikKategori::class);
    }

    /**
     * Aktif özellikler
     */
    public function scopeActive($query)
    {
        return $query->where('status', true); // Context7: status → status
    }
}
