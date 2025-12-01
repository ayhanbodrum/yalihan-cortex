<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Özellik Kategorileri Modeli
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property bool $status
 * @property int $siralama
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class OzellikKategori extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Tablo adı tanımlama
     */
    protected $table = 'ozellik_kategorileri';

    /**
     * Toplu atama için izin verilen alanlar
     */
    protected $fillable = [
        'name', // Context7: ad → name
        'aciklama',
        'icon',
        'renk_kodu',
        'veri_tipi',
        'veri_secenekleri',
        'birim',
        'zorunlu',
        'arama_filtresi',
        'ilan_kartinda_goster',
        'detay_sayfasinda_goster',
        'uyumlu_emlak_turleri',
        'uyumlu_kategoriler',
        'validasyon_kurallari',
        'varsayilan_deger',
        'meta_title',
        'meta_description',
        'kullanim_sayisi',
        'son_kullanim_tarih',
        'parent_id', // Context7: parent relationship
        'display_order', // Context7: order → display_order
        'status', // Context7 kuralı: status → status
        'slug',
    ];

    /**
     * Tip dönüşümleri
     * Context7: display_order kolonu kullanılır
     */
    protected $casts = [
        'status' => 'boolean', // Context7 kuralı: status → status
        'display_order' => 'integer', // Context7: order → display_order
        'zorunlu' => 'boolean',
        'arama_filtresi' => 'boolean',
        'ilan_kartinda_goster' => 'boolean',
        'detay_sayfasinda_goster' => 'boolean',
        'veri_secenekleri' => 'array',
        'uyumlu_emlak_turleri' => 'array',
        'uyumlu_kategoriler' => 'array',
        'validasyon_kurallari' => 'array',
        'son_kullanim_tarih' => 'datetime',
    ];

    /**
     * Model kayıt edilmeden önce slug oluşturma
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kategori) {
            if (empty($kategori->slug)) {
                $kategori->slug = Str::slug($kategori->name);
            }
            // Context7: display_order varsayılan değer atama
            if (is_null($kategori->display_order)) {
                $kategori->display_order = (int) (static::max('display_order') + 1);
            }
        });

        static::updating(function ($kategori) {
            if ($kategori->isDirty('name') && empty($kategori->slug)) {
                $kategori->slug = Str::slug($kategori->name);
            }
        });

        static::saved(fn () => Cache::forget('ozellik_kategorileri_full'));
        static::deleted(fn () => Cache::forget('ozellik_kategorileri_full'));
    }

    /**
     * Bu kategoriye ait özellikleri getiren ilişki
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ozellikler()
    {
        return $this->hasMany(Ozellik::class, 'kategori_id');
    }

    /**
     * Aktif kategorileri filtreleme
     */
    public function scopeActive($query)
    {
        return $query->where('status', true); // Context7: status → status
    }

    /**
     * Arama filtresinde gösterilecek kategoriler
     */
    public function scopeAramaFiltresi($query)
    {
        return $query->where('arama_filtresi', true);
    }

    /**
     * İlan kartında gösterilecek kategoriler
     */
    public function scopeIlanKartindaGoster($query)
    {
        return $query->where('ilan_kartinda_goster', true);
    }

    /**
     * Detay sayfasında gösterilecek kategoriler
     */
    public function scopeDetaySayfasindaGoster($query)
    {
        return $query->where('detay_sayfasinda_goster', true);
    }

    /**
     * Zorunlu özellik kategorileri
     */
    public function scopeZorunlu($query)
    {
        return $query->where('zorunlu', true);
    }

    /**
     * Belirli veri tipindeki kategoriler
     */
    public function scopeVeriTipi($query, $tip)
    {
        return $query->where('veri_tipi', $tip);
    }

    /**
     * Belirli emlak türü için uyumlu kategoriler
     */
    public function scopeEmlakTuruIcin($query, $emlakTuru)
    {
        return $query->where(function ($q) use ($emlakTuru) {
            $q->whereJsonContains('uyumlu_emlak_turleri', $emlakTuru)
                ->orWhereNull('uyumlu_emlak_turleri');
        });
    }

    /**
     * Belirli kategori için uyumlu özellik kategorileri
     */
    public function scopeKategoriIcin($query, $kategoriId)
    {
        return $query->where(function ($q) use ($kategoriId) {
            $q->whereJsonContains('uyumlu_kategoriler', $kategoriId)
                ->orWhereNull('uyumlu_kategoriler');
        });
    }

    /**
     * Sıralı kategoriler
     */
    public function scopeSiralı($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * En çok kullanılan kategoriler
     */
    public function scopeEnCokKullanilan($query, $limit = 10)
    {
        return $query->orderBy('kullanim_sayisi', 'desc')->limit($limit);
    }

    /**
     * İlan kategorileri ile bağlantı
     */
    public function ilanKategoriBaglantilari()
    {
        return $this->hasMany(\App\Models\IlanKategoriOzellikBaglanti::class, 'ozellik_kategori_id');
    }

    /**
     * Veri tipi etiketini getir
     */
    public function getVeriTipiEtiketiAttribute()
    {
        $etiketler = [
            'text' => 'Metin',
            'number' => 'Sayı',
            'boolean' => 'Evet/Hayır',
            'select' => 'Seçim',
            'multiselect' => 'Çoklu Seçim',
            'date' => 'Tarih',
            'file' => 'Dosya',
        ];

        return $etiketler[$this->veri_tipi] ?? $this->veri_tipi;
    }

    /**
     * Kategori rengini getir
     */
    public function getRenkAttribute()
    {
        return $this->renk_kodu ?: $this->getVarsayilanRenk();
    }

    /**
     * Varsayılan kategori rengi
     */
    protected function getVarsayilanRenk()
    {
        $renkler = [
            'text' => '#6B7280',
            'number' => '#3B82F6',
            'boolean' => '#10B981',
            'select' => '#F59E0B',
            'multiselect' => '#8B5CF6',
            'date' => '#EF4444',
            'file' => '#06B6D4',
        ];

        return $renkler[$this->veri_tipi] ?? '#6B7280';
    }
}

if (! function_exists('cachedOzellikKategorileri')) {
    function cachedOzellikKategorileri()
    {
        // Context7: Database column is 'display_order'
        return Cache::remember('ozellik_kategorileri_full', 300, fn () => OzellikKategori::withCount('ozellikler')->orderBy('display_order')->get());
    }
}
