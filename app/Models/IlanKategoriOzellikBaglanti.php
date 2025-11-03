<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IlanKategoriOzellikBaglanti extends Model
{
    use HasFactory;

    protected $table = 'ilan_kategori_ozellik_baglanti';

    protected $fillable = [
        'category_id',
        'ozellik_kategori_id',
        'ozellik_id',
        'baglanti_tipi',
        'zorunlu',
        'filtrelemede_goster',
        'ilan_kartinda_goster',
        'siralama',
        'varsayilan_deger',
        'validasyon_kurallari',
    ];

    protected $casts = [
        'zorunlu' => 'boolean',
        'filtrelemede_goster' => 'boolean',
        'ilan_kartinda_goster' => 'boolean',
        'varsayilan_deger' => 'array',
        'validasyon_kurallari' => 'array',
    ];

    /**
     * İlan kategorisi ile ilişki
     */
    public function ilanKategori()
    {
        return $this->belongsTo(IlanKategori::class, 'category_id');
    }

    /**
     * Özellik kategorisi ile ilişki
     */
    public function ozellikKategori()
    {
        return $this->belongsTo(OzellikKategori::class, 'ozellik_kategori_id');
    }

    /**
     * Spesifik özellik ile ilişki
     */
    public function ozellik()
    {
        return $this->belongsTo(Feature::class, 'ozellik_id');
    }

    /**
     * Ana kategori bağlantıları
     */
    public function scopeAnaKategori($query)
    {
        return $query->where('baglanti_tipi', 'ana');
    }

    /**
     * Alt kategori bağlantıları
     */
    public function scopeAltKategori($query)
    {
        return $query->where('baglanti_tipi', 'alt');
    }

    /**
     * Yayın tipi bağlantıları
     */
    public function scopeYayinTipi($query)
    {
        return $query->where('baglanti_tipi', 'yayin');
    }

    /**
     * Zorunlu özellikler
     */
    public function scopeZorunlu($query)
    {
        return $query->where('zorunlu', true);
    }

    /**
     * Filtrelemede gösterilecek özellikler
     */
    public function scopeFiltrelemedeGoster($query)
    {
        return $query->where('filtrelemede_goster', true);
    }

    /**
     * İlan kartında gösterilecek özellikler
     */
    public function scopeIlanKartindaGoster($query)
    {
        return $query->where('ilan_kartinda_goster', true);
    }

    /**
     * Sıralı özellikler
     */
    public function scopeSiralı($query)
    {
        return $query->orderBy('siralama');
    }
}
