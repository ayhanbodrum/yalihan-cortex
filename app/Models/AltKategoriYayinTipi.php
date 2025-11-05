<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Alt Kategori - Yayın Tipi Pivot Tablo Modeli
 * 
 * Context7 Compliant
 */
class AltKategoriYayinTipi extends Model
{
    use HasFactory;

    protected $table = 'alt_kategori_yayin_tipi';

    protected $fillable = [
        'alt_kategori_id',
        'yayin_tipi_id',
        'enabled',
        'order',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'order' => 'integer',
    ];

    public $timestamps = true;

    /**
     * Alt kategori ilişkisi
     */
    public function altKategori()
    {
        return $this->belongsTo(IlanKategori::class, 'alt_kategori_id');
    }

    /**
     * Yayın tipi ilişkisi
     */
    public function yayinTipi()
    {
        return $this->belongsTo(IlanKategoriYayinTipi::class, 'yayin_tipi_id');
    }

    /**
     * Aktif pivot kayıtları
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }
}
