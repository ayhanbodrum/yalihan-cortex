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
        'status', // Context7: enabled → status
        'display_order', // ✅ Context7: order → display_order
    ];

    protected $casts = [
        'status' => 'boolean', // Context7: enabled → status
        'display_order' => 'integer', // ✅ Context7: order → display_order
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
        return $query->where('status', true); // Context7: enabled → status
    }
}
