<?php

namespace App\Models;

use App\Traits\HasFeatures;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IlanKategoriYayinTipi extends Model
{
    use HasFactory, HasFeatures, SoftDeletes;

    protected $table = 'ilan_kategori_yayin_tipleri';

    // ✅ Context7: Sadece tabloda OLAN kolonlar (DESC ilan_kategori_yayin_tipleri)
    protected $fillable = [
        'kategori_id',      // ✅ Tabloda var
        'yayin_tipi',       // ✅ Tabloda var (VARCHAR)
        'status',           // ✅ Tabloda var (TINYINT(1) - FIXED!)
        'display_order',    // ✅ Context7: order → display_order
        // alt_kategori_id  // ❌ Tabloda yok!
        // name             // ❌ Tabloda yok!
        // slug             // ❌ Tabloda yok!
        // icon             // ❌ Tabloda yok!
        // description      // ❌ Tabloda yok!
        // restrictions     // ❌ Tabloda yok!
        // required_features// ❌ Tabloda yok!
    ];

    // ✅ Context7: status is now TINYINT(1) - boolean (FIXED VIO-2025-01-24-002)
    protected $casts = [
        'status' => 'boolean',  // ✅ FIXED: TINYINT(1) → boolean
        'display_order' => 'integer', // ✅ Context7: order → display_order
    ];

    /**
     * Kategori ile ilişki (Context7: kategori_id kullan)
     */
    public function kategori()
    {
        return $this->belongsTo(IlanKategori::class, 'kategori_id');
    }

    /**
     * Bu yayın tipine ait ilanlar
     */
    public function ilanlar()
    {
        return $this->hasMany(Ilan::class, 'yayin_tipi_id');
    }

    /**
     * Gerekli özellikler
     */
    public function gerekliOzellikler()
    {
        return $this->belongsToMany(OzellikKategori::class, 'ilan_kategori_ozellik_baglanti', 'yayin_tipi_id', 'ozellik_kategori_id')
            ->wherePivot('baglanti_tipi', 'yayin')
            ->wherePivot('zorunlu', true);
    }

    /**
     * Aktif yayın tiplerini getir (Context7: status boolean)
     */
    public function scopeActive($query)
    {
        return $query->where('status', true); // ✅ Context7: boolean (FIXED!)
    }

    /**
     * Belirli kategori için yayın tiplerini getir (Context7: kategori_id)
     */
    public function scopeKategoriIcin($query, $kategoriId)
    {
        return $query->where('kategori_id', $kategoriId);
    }

    /**
     * Sıralı yayın tiplerini getir
     * Context7: display_order kullan
     */
    public function scopeSiralı($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Name accessor (Context7: yayin_tipi -> name)
     */
    public function getNameAttribute()
    {
        return $this->yayin_tipi;
    }
}
