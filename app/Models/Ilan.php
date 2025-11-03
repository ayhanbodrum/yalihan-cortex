<?php

namespace App\Models;

use App\Traits\HasFeatures;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Ilan
 *
 * @property int $id
 * @property string $baslik
 * @property string|null $aciklama
 * @property float $fiyat
 * @property string $para_birimi
 * @property Carbon|null $ilan_tarihi
 * @property bool $enabled
 * @property string $status
 * @property bool $is_published
 * @property int|null $proje_id
 *
 * // İlişkisel Alanlar
 * @property int|null $ilan_sahibi_id
 * @property int|null $ilgili_kisi_id
 * @property int|null $danisman_id
 * @property int|null $ulke_id
 * @property int|null $il_id
 * @property int|null $ilce_id
 * @property int|null $mahalle_id
 * @property int|null $ana_kategori_id
 * @property int|null $alt_kategori_id
 *
 * // Analitik, SEO ve CRM Alanları
 * @property string|null $slug
 * @property int $view_count
 * @property int $favorite_count
 * @property Carbon|null $son_islem_tarihi
 * @property float|null $son_islem_fiyati
 * @property string|null $islem_tipi // 'satis', 'kiralama'
 *
 * // Diğer Alanlar
 * @property string|null $youtube_video_url
 * @property string|null $sanal_tur_url
 * @property string|null $ada_no
 * @property string|null $parsel_no
 * @property float|null $latitude
 * @property float|null $longitude
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * // İlişkiler (Relationships)
 * @property-read Kisi|null $ilanSahibi
 * @property-read Kisi|null $ilgiliKisi
 * @property-read User|null $danisman
 * @property-read Ulke|null $ulke
 * @property-read Il|null $il
 * @property-read Ilce|null $ilce
 * @property-read Mahalle|null $mahalle
 * @property-read IlanKategori|null $anaKategori
 * @property-read IlanKategori|null $altKategori
 * @property-read \Illuminate\Database\Eloquent\Collection|IlanPriceHistory[] $fiyatGecmisi
 * @property-read int|null $fiyat_gecmisi_count
 * @property-read \Illuminate\Database\Eloquent\Collection|IlanFotografi[] $fotograflar
 * @property-read int|null $fotograflar_count
 * @property-read mixed $kapak_fotografi
 */
class Ilan extends Model
{
    use HasFactory, SoftDeletes, HasFeatures;

    protected $table = 'ilanlar';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'baslik',
        'ilan_basligi', // İlan başlığı için
        'aciklama',
        'ilan_aciklamasi', // İlan açıklaması için
        'fiyat',
        'fiyat_orijinal',
        'fiyat_try_cached',
        'para_birimi',
        'para_birimi_orijinal',
        'kur_orani',
        'kur_tarihi',
        'ilan_tarihi',
        'enabled',
        'status',
        'is_published',
        'proje_id',

        // İlişkisel Alanlar
        'ilan_sahibi_id',
        'ilgili_kisi_id',
        'danisman_id',
        'ulke_id',
        'il_id',
        'ilce_id',
        'mahalle_id',
        'ana_kategori_id',
        'alt_kategori_id',
        // Yayın tipi - Foreign key (yeni güvenli sistem)
        'yayin_tipi_id',
        // Legacy string field (deprecated - sadece geriye uyumluluk için)
        'yayinlama_tipi',

        // Analitik, SEO ve CRM Alanları
        'slug',
        'view_count',
        'favorite_count',
        'son_islem_tarihi',
        'son_islem_fiyati',
        'islem_tipi',

        // Diğer Alanlar
        'youtube_video_url',
        'sanal_tur_url',
        'ada_no',
        'parsel_no',
        'latitude',
        'longitude',

        // İlan tipine özel alanlar
        'oda_sayisi',
        'banyo_sayisi',
        'net_metrekare',
        'brut_metrekare',
        // legacy isimler
        'balkon_sayisi',
        'brut_alan',
        'net_alan',
        'yas',
        'kat',
        'toplam_kat',
        'isitma_tipi', // legacy
        'isitma',
        'yakit_tipi',
        'esyali',
        'esya_statusu',
        'site_icerisinde',
        'kredi_uygun',
        'takas_uygun',
        'tapu_statusu',
        'hisse_orani',
        'alan_m2',
        'imar_statusu',
        'kaks',
        'taks',
        'gabari',
        'cephe_sayisi',
        'ifraz_durumu',
        'tapu_durumu',
        'yol_durumu',
        'ifrazsiz',
        'kat_karsiligi',
        'tapu_tipi',
        'krediye_uygun',
        'dynamic_fields', // JSON formatında kategori özel alanları

        // Adres detayları
        'adres_mahalle',
        'adres_detay',
        
        // 🆕 PHASE 1: Address Components (Structured Address - 2025-10-31)
        'sokak',
        'cadde',
        'bulvar',
        'bina_no',
        'daire_no',
        'posta_kodu',
        
        // 🆕 PHASE 2: Distance Data (2025-10-31)
        'nearby_distances',
        
        // 🆕 PHASE 3: Property Boundary (2025-10-31)
        'boundary_geojson',
        'boundary_area',

        // Arsa özellikleri
        'ada_parsel',
        'yola_cephe',
        'altyapi_elektrik',
        'altyapi_su',
        'altyapi_dogalgaz',
        // legacy isimler
        'yola_cephesi',
        'elektrik_altyapisi',
        'su_altyapisi',
        'dogalgaz_altyapisi',

        // Yazlık kiralama özellikleri
        'min_konaklama',
        'temizlik_ucreti',
        'havuz',
        // legacy
        'havuz_var',
        'max_misafir',
        'gunluk_fiyat',
        'haftalik_fiyat',
        'aylik_fiyat',
        'sezonluk_fiyat',

        // Havuz detayları
        'havuz_turu',
        'havuz_boyut',
        'havuz_derinlik',

        // Sezonluk alanlar
        'sezon_baslangic',
        'sezon_bitis',
        'elektrik_dahil',
        'su_dahil',

        // Özel notlar ve indirim bilgileri (sadece admin panelinde görünür)
        'ozel_notlar',
        'musteri_notlari',
        'indirimli_fiyat',
        'indirim_notlari',
        'anahtar_kimde',
        'anahtar_notlari',
        'sahip_ozel_notlari',
        'sahip_iletisim_tercihi',

        // EİDS Onay Durumu
        'eids_onayli',
        'eids_onay_tarihi',
        'eids_belge_no',

        // Villa/Daire Eksik Alanları (YENİ)
        'isinma_tipi',
        'site_ozellikleri',

        // İşyeri Alanları (YENİ)
        'isyeri_tipi',
        'kira_bilgisi',
        'ciro_bilgisi',
        'ruhsat_durumu',
        'personel_kapasitesi',
        'isyeri_cephesi',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ilan_tarihi' => 'datetime',
        'son_islem_tarihi' => 'datetime',
        'enabled' => 'boolean',
        'is_published' => 'boolean',
        'fiyat' => 'float',
        'fiyat_orijinal' => 'float',
        'fiyat_try_cached' => 'float',
        'kur_orani' => 'float',
        'kur_tarihi' => 'date',
        'son_islem_fiyati' => 'float',
        'latitude' => 'float',
        'longitude' => 'float',
        'esyali' => 'boolean',
        'site_icerisinde' => 'boolean',
        'kredi_uygun' => 'boolean',
        'takas_uygun' => 'boolean',

        // Arsa boolean alanları
        'yola_cephe' => 'boolean',
        'altyapi_elektrik' => 'boolean',
        'altyapi_su' => 'boolean',
        'altyapi_dogalgaz' => 'boolean',
        // legacy
        'elektrik_altyapisi' => 'boolean',
        'su_altyapisi' => 'boolean',
        'dogalgaz_altyapisi' => 'boolean',

        // Yazlık kiralama boolean alanları
        'havuz' => 'boolean',
        'havuz_var' => 'boolean',
        'elektrik_dahil' => 'boolean',
        'su_dahil' => 'boolean',

        // Sezonluk tarih alanları
        'sezon_baslangic' => 'date',
        'sezon_bitis' => 'date',

        // Numeric alanları
        'net_metrekare' => 'float',
        'brut_metrekare' => 'float',
        'yola_cephesi' => 'float', // legacy
        'min_konaklama' => 'integer',
        'max_misafir' => 'integer',
        'temizlik_ucreti' => 'float',
        'gunluk_fiyat' => 'float',
        'haftalik_fiyat' => 'float',
        'aylik_fiyat' => 'float',
        'sezonluk_fiyat' => 'float',

        // Villa/Daire & İşyeri Casts (YENİ)
        'site_ozellikleri' => 'array',
        'ciro_bilgisi' => 'float',
        'personel_kapasitesi' => 'integer',
        'isyeri_cephesi' => 'integer',
    ];

    // ======================================================================
    // İLİŞKİLER (RELATIONSHIPS)
    // ======================================================================

    /**
     * İlanın sahibini (Mülk Sahibi) döndürür.
     */
    public function ilanSahibi(): BelongsTo
    {
        return $this->belongsTo(Kisi::class, 'ilan_sahibi_id');
    }

    /**
     * İlanla ilgilenen kişiyi (Emlakçı, Kiracı adayı vb.) döndürür.
     */
    public function ilgiliKisi(): BelongsTo
    {
        return $this->belongsTo(Kisi::class, 'ilgili_kisi_id');
    }

    /**
     * İlanın danışmanı ilişkisi
     */
    public function danisman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'danisman_id');
    }

    /**
     * User modeli ile danışman ilişkisi (Eloquent için)
     */
    public function userDanisman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'danisman_id');
    }

    // --- Adres İlişkileri ---

    public function ulke(): BelongsTo
    {
        return $this->belongsTo(Ulke::class, 'ulke_id');
    }

    public function il(): BelongsTo
    {
        return $this->belongsTo(Il::class, 'il_id');
    }

    public function ilce(): BelongsTo
    {
        return $this->belongsTo(Ilce::class, 'ilce_id');
    }

    public function mahalle(): BelongsTo
    {
        return $this->belongsTo(Mahalle::class, 'mahalle_id');
    }

    // --- Kategori İlişkileri ---

    public function anaKategori(): BelongsTo
    {
        return $this->belongsTo(IlanKategori::class, 'ana_kategori_id');
    }

    public function altKategori(): BelongsTo
    {
        return $this->belongsTo(IlanKategori::class, 'alt_kategori_id');
    }

    /**
     * Legacy parentKategori ilişkisi (geriye uyumluluk için)
     */
    public function parentKategori(): BelongsTo
    {
        return $this->belongsTo(IlanKategori::class, 'parent_kategori_id');
    }

    /**
     * Yayın tipi ilişkisi (Foreign Key - Güvenli Sistem)
     */
    public function yayinTipi(): BelongsTo
    {
        return $this->belongsTo(IlanKategori::class, 'yayin_tipi_id')
            ->where('seviye', 2); // Sadece yayın tiplerini getir
    }

    // --- Diğer İlişkiler ---

    /**
     * İlanın fiyat geçmişini döndürür.
     */
    public function fiyatGecmisi(): HasMany
    {
        return $this->hasMany(IlanPriceHistory::class, 'ilan_id')->orderBy('created_at', 'desc');
    }

    /**
     * İlanın fotoğraflarını döndürür.
     */
    public function fotograflar(): HasMany
    {
        return $this->hasMany(IlanFotografi::class, 'ilan_id');
    }

    /**
     * Photo Model ile ilişki (Yeni Photo System)
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class)->ordered();
    }

    /**
     * Öne çıkan fotoğraf (Photo Model)
     */
    public function featuredPhoto()
    {
        return $this->hasOne(Photo::class)->where('is_featured', true);
    }

    /**
     * Events (Rezervasyonlar/Etkinlikler)
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Aktif rezervasyonlar
     */
    public function activeEvents()
    {
        return $this->hasMany(Event::class)->active();
    }

    /**
     * Sezonlar (Fiyatlandırma)
     */
    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    /**
     * Aktif sezonlar
     */
    public function activeSeasons()
    {
        return $this->hasMany(Season::class)->active();
    }

    /**
     * İlanın çevirilerini döndürür.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(IlanTranslation::class);
    }

    /**
     * İlanın kategorisini döndürür (Alt Kategori).
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(IlanKategori::class, 'alt_kategori_id');
    }

    /**
     * İlanın kullanıcısını döndürür.
     * Not: Bu danisman() ile aynı ilişki, tutarlılık için danisman() kullanın
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'danisman_id');
    }

    /**
     * İlanla ilişkili kişiyi döndürür.
     */
    public function kisi(): BelongsTo
    {
        return $this->belongsTo(Kisi::class, 'kisi_id');
    }

    /**
     * İlanın özelliklerini (features) döndürür.
     */
    public function ozellikler(): BelongsToMany
    {
        // TODO: listing_feature ile ilan_feature tablolarını tekilleştir.
        return $this->belongsToMany(Feature::class, 'ilan_feature', 'ilan_id', 'feature_id')
            ->withPivot('value')
            ->withTimestamps();
    }

    /**
     * Features relationship (English alias for ozellikler)
     * Context7: English naming standard
     */
    public function features(): BelongsToMany
    {
        return $this->ozellikler();
    }

    /**
     * Geçiş süreci: eski tablonun kullanıldığı kayıtlar için alternatif ilişki.
     */
    public function ozelliklerLegacy(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'listing_feature', 'ilan_id', 'feature_id');
    }

    /**
     * İlanın etiketlerini döndürür.
     */
    public function etiketler(): BelongsToMany
    {
        return $this->belongsToMany(Etiket::class, 'ilan_etiketler')
                    ->withPivot(['display_order', 'is_featured'])
                    ->orderByPivot('display_order')
                    ->withTimestamps();
    }

    /**
     * İlanın takvim senkronizasyonlarını döndürür.
     */
    public function takvimSync()
    {
        return $this->hasMany(IlanTakvimSync::class, 'ilan_id');
    }

    /**
     * İlanın doluluk durumlarını döndürür (Yazlık için).
     */
    public function dolulukDurumlari()
    {
        return $this->hasMany(YazlikDolulukDurumu::class, 'ilan_id');
    }

    /**
     * İlanın yazlık detaylarını döndürür.
     */
    public function yazlikDetail()
    {
        return $this->hasOne(YazlikDetail::class, 'ilan_id');
    }

    // ======================================================================
    // ERİŞİMCİLER & DEĞİŞTİRİCİLER (ACCESSORS & MUTATORS)
    // ======================================================================

    /**
     * Kapak fotoğrafını döndürür.
     */
    public function getKapakFotografiAttribute()
    {
        return $this->fotograflar()->where('kapak_fotografi', true)->first() ?? $this->fotograflar()->first();
    }

    /**
     * Tam adres metnini oluşturur.
     */
    public function getTamAdresAttribute(): string
    {
        $adresParcalari = [
            $this->mahalle->mahalle_adi ?? null,
            $this->ilce->ilce_adi ?? null,
            $this->il->il_adi ?? null,
            $this->ulke->ulke_adi ?? null,
        ];

        return implode(', ', array_filter($adresParcalari));
    }

    // ======================================================================
    // KAPSAMLAR (SCOPES)
    // ======================================================================

    /**
     * Sadece status olan ilanları getiren scope.
     */
    public function scopeActive($query)
    {
        return $query->where('enabled', true)->where('status', 'yayinda');
    }

    /**
     * Belirli bir kategoriye ait ilanları getiren scope.
     */
    public function scopeKategoriyeGore($query, $kategoriId)
    {
        return $query->where('ana_kategori_id', $kategoriId)
            ->orWhere('alt_kategori_id', $kategoriId);
    }
}
