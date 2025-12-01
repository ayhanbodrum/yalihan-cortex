<?php

namespace App\Models;

use App\Modules\Emlak\Models\Ilan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Talep extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'talepler';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'talep_tipi',
        'aciklama',
        'status',
        'kisi_id', // Context7: musteri_id → kisi_id (reform 2025-11-24)
        'danisman_id',
        'alt_kategori_id', // Context7: category_id yerine alt_kategori_id
        'il_id',
        'ilce_id',
        'mahalle_id',
        'min_fiyat',
        'max_fiyat',
        'min_metrekare', // Context7: Talep reformu - 2025-11-24
        'max_metrekare', // Context7: Talep reformu - 2025-11-24
        'metadata',
        'baslik', // Context7: Başlık alanı eklendi
        'oda_sayisi', // Context7: Özellik alanları
        'yas',
        'kat',
        'manzara',
        'ozel_tercihler',
        'ozel_ozellikler',
        'aranan_ozellikler_json', // Context7: Talep reformu - 2025-11-24
        'notlar',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'min_fiyat' => 'decimal:2',
        'max_fiyat' => 'decimal:2',
        'min_metrekare' => 'integer',
        'max_metrekare' => 'integer',
        'aranan_ozellikler_json' => 'array',
        'metadata' => 'array',
        'deleted_at' => 'datetime',
    ];

    // --- İLİŞKİLER ---

    /**
     * Talebi oluşturan kişi (müşteri).
     */
    public function kisi()
    {
        return $this->belongsTo(Kisi::class, 'kisi_id');
    }

    /**
     * Talebi sisteme ekleyen kullanıcı (danışman).
     */
    public function kullanici()
    {
        return $this->belongsTo(User::class, 'kullanici_id');
    }

    /**
     * Talebin danışmanı (alias for danisman_id).
     * Context7: danisman relationship
     */
    public function danisman()
    {
        return $this->belongsTo(User::class, 'danisman_id');
    }

    /**
     * Talebin ait olduğu ilan kategorisi.
     * Context7: category_id yerine alt_kategori_id kullanılmalı
     */
    public function kategori()
    {
        return $this->belongsTo(IlanKategori::class, 'alt_kategori_id');
    }

    /**
     * Alt kategori (alias)
     * Context7 uyumlu naming
     */
    public function altKategori()
    {
        return $this->belongsTo(IlanKategori::class, 'alt_kategori_id');
    }

    // --- GLOBAL ADRES İLİŞKİLERİ ---

    public function ulke()
    {
        return $this->belongsTo(Ulke::class, 'ulke_id');
    }

    // Context7 kuralı: il() relationship kullanımı

    public function il()
    {
        return $this->belongsTo(Il::class, 'il_id');
    }

    public function ilce()
    {
        return $this->belongsTo(Ilce::class, 'ilce_id');
    }

    public function mahalle()
    {
        return $this->belongsTo(Mahalle::class, 'mahalle_id');
    }

    /**
     * Talebin eşleşmeleri (eslesmeler tablosu üzerinden)
     */
    public function eslesme()
    {
        return $this->hasMany(Eslesme::class, 'talep_id');
    }

    /**
     * Talebin eşleşmeleri (plural form - alias)
     */
    public function eslesmeler()
    {
        return $this->hasMany(Eslesme::class, 'talep_id');
    }

    /**
     * Talebin eşleştiği ilanlar
     */
    public function eslesenIlanlar()
    {
        return $this->belongsToMany(Ilan::class, 'eslesmeler', 'talep_id', 'ilan_id')
            ->withPivot('one_cikan', 'status', 'notlar')
            ->withTimestamps();
    }

    // --- ACCESSORS & MUTATORS ---

    /**
     * Tam adres bilgisini döndürür.
     */
    public function getTamAdresAttribute()
    {
        $adresParcalari = [
            $this->mahalle->name ?? null,
            $this->ilce->name ?? null,
            $this->il->il_adi ?? null,
            $this->ulke->name ?? null,
        ];

        return implode(', ', array_filter($adresParcalari));
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Aktif', 1, true]);
    }
}
