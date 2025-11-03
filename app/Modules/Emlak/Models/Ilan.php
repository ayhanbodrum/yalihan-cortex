<?php

namespace App\Modules\Emlak\Models;

use App\Modules\Auth\Models\User;
use App\Modules\BaseModule\Models\BaseModel;
use App\Models\IlanKategori;
use App\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @deprecated Bu model deprecated edilmiştir.
 *
 * KULLANIM ÖNERİSİ:
 * Bu model kullanılmamalıdır. Bunun yerine App\Models\Ilan kullanılmalıdır.
 *
 * Geçiş:
 * use App\Modules\Emlak\Models\Ilan;  → use App\Models\Ilan;
 *
 * TARİH: 27 Ekim 2025
 * DURUM: Deprecated - Yeni model kullanılmalıdır
 */

trigger_error(
    "DEPRECATED: App\\Modules\\Emlak\\Models\\Ilan kullanılıyor. " .
    "Lütfen App\\Models\\Ilan kullanın. " .
    "Kaynak: " . debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file'] ?? 'unknown',
    E_USER_DEPRECATED
);

/**
 * İlan Ekleme 2.0 - Stage Enum
 */
enum IlanStage: string
{
    case OWNER = 'owner';
    case CATEGORY = 'category';
    case CORE = 'core';
    case LOCATION = 'location';
    case ARSA = 'arsa';
    case AI = 'ai';
    case MEDIA = 'media';
    case REVIEW = 'review';
    case PUBLISHED = 'published';
}

/**
 * @property int $id
 * @property int|null $proje_id
 * @property int $danisman_id
 * @property string $ilan_turu
 * @property string $yayinlama_tipi
 * @property float $fiyat
 * @property string $para_birimi
 * @property string $adres_il
 * @property string $adres_ilce
 * @property string|null $adres_mahalle
 * @property string|null $adres_detay
 * @property string|null $ada_no
 * @property string|null $parsel_no
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string $status
 * @property int $one_cikan
 * @property int|null $kalite_puani
 * @property string|null $kalite_seviyesi
 * @property string|null $youtube_video_url
 * @property string|null $sanal_tur_url
 * @property int $view_count
 * @property string|null $yayin_tarihi
 * @property string|null $nearby_poi_counts
 * @property int|null $distance_to_beach_meters
 * @property int|null $distance_to_airport_km
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read User|null $danisman
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Modules\Emlak\Models\IlanFotografi> $fotograflar
 * @property-read int|null $fotograflar_count
 * @property-read mixed $kapak_fotografi
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Modules\Emlak\Models\IlanOzellik> $ozellikler
 * @property-read int|null $ozellikler_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Modules\Emlak\Models\IlanTranslation> $translations
 * @property-read int|null $translations_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan status()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan il($il)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan ofType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereAdaNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereAdresDetay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereAdresIl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereAdresIlce($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereAdresMahalle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereDanismanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereDistanceToAirportKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereDistanceToBeachMeters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereDurum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereFiyat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereIlanTuru($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereKalitePuani($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereKaliteSeviyesi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereNearbyPoiCounts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereOneCikan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereParaBirimi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereParselNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereProjeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereSanalTurUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereViewCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereYayinTarihi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereYayinlamaTipi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan whereYoutubeVideoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ilan withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Ilan extends BaseModel
{
    use HasFactory, SearchableTrait, SoftDeletes;

    /**
     * İlişkilendirilmiş tablo adı
     *
     * @var string
     */
    protected $table = 'ilanlar';

    /**
     * Toplu atanabilir alanlar
     *
     * @var array
     */
    protected $fillable = [
        'danisman_id',
        'ilan_turu',
        'emlak_turu',
        'yayinlama_tipi',
        'fiyat',
        'para_birimi',
        'prices',
        'is_per_sqm',
        // Çoklu para birimi desteği - orijinal ve TRY cache alanları
        'fiyat_orijinal',
        'para_birimi_orijinal',
        'fiyat_try_cached',
        'kur_orani',
        'kur_tarihi',
        'adres_il',
        'adres_ilce',
        'adres_mahalle',
        'adres_detay',
        'status',
        'youtube_video_url',
        'sanal_tur_url',
        'latitude',
        'longitude',
        'baslik',
        'aciklama',
        'emlak_tipi',
        'koordinat',
        'metrekare',
        'oda_sayisi',
        'banyo_sayisi',
        'bina_yasi',
        'bulundugu_kat',
        'isitma_tipi',
        'toplam_kat',
        'esya_statusu',
        'user_id',
        'tapu_statusu',
        'aidat',
        'krediye_uygun',
        'takasa_uygun',
        'portfoy_no',
        // Yeni Global Adres Sistemi Alanları
        'ulke_id',
        // Context7: // Context7: // Context7: // Context7: // Context7: // Context7: // Context7: // Context7: // Context7: // Context7: // Context7: // Context7: // Context7: // Context7: // Context7: // Context7: // Context7: region_id kaldırıldı kaldırıldı kaldırıldı kaldırıldı kaldırıldı kaldırıldı kaldırıldı kaldırıldı kaldırıldı kaldırıldı kaldırıldı kaldırıldı kaldırıldı kaldırıldı kaldırıldı kaldırıldı kaldırıldı, il_id kullanılmalı
        'il_id',
        'ilce_id',
        'mahalle_id',
        'sokak',
        'bina_no',
        // Yeni Kategori Sistemi Alanları
        'ana_kategori_id',
        'alt_kategori_id',
        // İlan sahipliği ve ilişkileri
        'ilan_sahibi_id',
        'ilgili_kisi_id',
        'proje_id',
        'site_id',
        // Diğer yeni alanlar
        'ada_no',
        'parsel_no',
        'is_published',
        'ilan_basligi',
        'ilan_aciklamasi',
        'arsa_detaylari',

        // Arsa alanları
        'imar_statusu',
        'ada_parsel',
        'yola_cephesi',
        'elektrik_altyapisi',
        'su_altyapisi',
        'dogalgaz_altyapisi',
        'taks',
        'kaks',

        // Yazlık kiralama alanları
        'min_konaklama',
        'temizlik_ucreti',
        'havuz_var',            // Form'da havuz_var
        'max_misafir',          // Form'da max_misafir
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
        'giris_saati',
        'cikis_saati',

        // Eski alan adları - geriye uyumluluk için
        'baslik',               // ilan_basligi ile mapping
        'aciklama',             // ilan_aciklamasi ile mapping
        'description',
        'alan_m2',

        // İlan Ekleme 2.0 - Stage Management
        'stage',
        'is_draft',
        'owner_id',
        'ai_suggestions',
        'last_saved_at',
        'completion_percentage',
        'net_alan',
        'brut_alan',
        'one_cikan',
        'title',
    ];

    /**
     * Cast edilecek özellikler
     *
     * @var array
     */
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'fiyat' => 'float',
        'fiyat_orijinal' => 'float',
        'fiyat_try_cached' => 'float',
        'kur_orani' => 'float',
        'kur_tarihi' => 'datetime',
        'prices' => 'array',
        'is_per_sqm' => 'boolean',
        'arsa_detaylari' => 'array',
        // 'stage' => IlanStage::class, // Temporarily disabled
        // İlan Ekleme 2.0 - Stage Management
        'is_draft' => 'boolean',
        'ai_suggestions' => 'array',
        'last_saved_at' => 'datetime',
        'completion_percentage' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    /**
     * Fiyat mutator - format normalizasyonu
     */
    public function setFiyatAttribute($value)
    {
        if ($value !== null && $value !== '') {
            // String formatları temizle
            if (is_string($value)) {
                // "10.500.000" -> "10500000"
                $value = str_replace(['.', ' '], '', $value);

                // "10,5 M" -> "10500000"
                if (preg_match('/(\d+(?:,\d+)?)\s*([KM])/i', $value, $matches)) {
                    $number = str_replace(',', '.', $matches[1]);
                    $multiplier = strtoupper($matches[2]);

                    if ($multiplier === 'K') {
                        $value = (float) $number * 1000;
                    } elseif ($multiplier === 'M') {
                        $value = (float) $number * 1000000;
                    }
                }
            }

            // Sayısal değere çevir ve 0'dan büyükse kaydet
            $value = (float) $value;
            $this->attributes['fiyat'] = $value > 0 ? $value : null;
        } else {
            $this->attributes['fiyat'] = null;
        }
    }

    /**
     * Tarih alanları
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * İlanın kategorisi
     */
    public function ilanKategori()
    {
        return $this->belongsTo(IlanKategori::class, 'category_id');
    }

    /**
     * İlanın kategorisi (alias)
     */
    public function kategori()
    {
        return $this->belongsTo(IlanKategori::class, 'category_id');
    }

    /**
     * İlanın ana kategorisi
     */
    public function anaKategori()
    {
        return $this->belongsTo(IlanKategori::class, 'ana_kategori_id');
    }

    /**
     * İlanın alt kategorisi
     */
    public function altKategori()
    {
        return $this->belongsTo(IlanKategori::class, 'alt_kategori_id');
    }

    /**
     * İlanın ülkesi
     */
    public function ulke()
    {
        return $this->belongsTo(\App\Models\Ulke::class, 'ulke_id');
    }

    /**
     * İlanın bölgesi
     */
    // Context7: bolge() relationship kaldırıldı, il() kullanılmalı
    public function il()
    {
        return $this->belongsTo(\App\Models\Il::class, 'il_id');
    }


    /**
     * İlanın ilçesi
     */
    public function ilce()
    {
        return $this->belongsTo(\App\Models\Ilce::class, 'ilce_id');
    }

    /**
     * İlanın mahallesi
     */
    public function mahalle()
    {
        return $this->belongsTo(\App\Models\Mahalle::class, 'mahalle_id');
    }

    /**
     * İlanın kişisi
     */
    public function kisi()
    {
        return $this->belongsTo(\App\Models\Kisi::class, 'kisi_id');
    }

    /**
     * İlanın kapak fotoğrafı ilişkisi
     */
    public function kapakFotografi()
    {
        return $this->hasOne(IlanFotografi::class, 'ilan_id')->where('kapak_fotografi', true);
    }

    /**
     * İlanın danışmanı (User modeli için eager loading)
     */
    public function danisman()
    {
        return $this->belongsTo(User::class, 'danisman_id');
    }

    /**
     * İlanın danışmanını döndürür (Model instance)
     */
    public function getDanisman()
    {
        return User::find($this->danisman_id);
    }

    /**
     * İlanın kullanıcısı (danışman ile aynı)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'danisman_id');
    }

    /**
     * İlanın danışmanı (User modeli için eager loading)
     */
    public function userDanisman()
    {
        return $this->belongsTo(User::class, 'danisman_id');
    }

    /**
     * İlanın sahibi (CRM)
     */
    public function ilanSahibi()
    {
        return $this->belongsTo(\App\Models\Kisi::class, 'ilan_sahibi_id');
    }

    /**
     * İlanla ilgili kişi (CRM)
     */
    public function ilgiliKisi()
    {
        return $this->belongsTo(\App\Models\Kisi::class, 'ilgili_kisi_id');
    }

    /**
     * İlan sahibi (İlan Ekleme 2.0 - CRM kişi)
     */
    public function owner()
    {
        return $this->belongsTo(\App\Modules\Crm\Models\Kisi::class, 'owner_id');
    }

    /**
     * İlanın özellikleri
     */
    public function ozellikler()
    {
        return $this->hasMany(IlanOzellik::class, 'ilan_id');
    }

    /**
     * Yayın tipi ilişkisi
     */
    public function yayinTipi()
    {
        return $this->belongsTo(\App\Models\IlanKategoriYayinTipi::class, 'yayin_tipi_id');
    }

    /**
     * Dinamik özellikler (yeni sistem)
     */
    public function dinamikOzellikler()
    {
        return $this->hasMany(\App\Models\IlanDinamikOzellik::class, 'ilan_id');
    }

    /**
     * Kategori özelliklerini getir
     */
    public function getKategoriOzellikleri()
    {
        if (! $this->altKategori) {
            return collect();
        }

        return $this->altKategori->ozellikBaglantilari()
            ->with('ozellikKategori')
            ->get()
            ->map(function ($baglanti) {
                return [
                    'id' => $baglanti->ozellikKategori->id,
                    'ad' => $baglanti->ozellikKategori->ad,
                    'slug' => $baglanti->ozellikKategori->slug,
                    'veri_tipi' => $baglanti->ozellikKategori->veri_tipi,
                    'deger' => $this->getDinamikOzellikDegeri($baglanti->ozellikKategori->id),
                    'zorunlu' => $baglanti->zorunlu,
                    'icon' => $baglanti->ozellikKategori->icon,
                ];
            });
    }

    /**
     * Dinamik özellik değerini getir
     */
    public function getDinamikOzellikDegeri($ozellikId)
    {
        $ozellik = $this->dinamikOzellikler()
            ->where('ozellik_kategori_id', $ozellikId)
            ->first();

        return $ozellik ? $ozellik->deger : null;
    }

    /**
     * Dinamik özellik değerini set et
     */
    public function setDinamikOzellikDegeri($ozellikId, $deger)
    {
        return $this->dinamikOzellikler()->updateOrCreate(
            ['ozellik_kategori_id' => $ozellikId],
            ['deger' => $deger]
        );
    }

    /**
     * İlanın fotoğrafları
     */
    public function fotograflar()
    {
        return $this->hasMany(IlanFotografi::class, 'ilan_id');
    }

    /**
     * İlanın fotoğrafları (fotograflar ile aynı, geriye dönük uyumluluk için)
     */
    public function photos()
    {
        return $this->hasMany(IlanFotografi::class, 'ilan_id');
    }

    /**
     * İlanın çevirileri
     */
    public function translations()
    {
        return $this->hasMany(IlanTranslation::class, 'ilan_id');
    }

    /**
     * İlanın kapak fotoğrafını getir
     */
    public function getKapakFotografiAttribute()
    {
        return $this->fotograflar()->where('kapak_fotografi', true)->first();
    }

    /**
     * İlanın statusuna göre filtreleme
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Yayında olan ilanlar (frontend görünür)
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)->where(function ($q) {
            $q->whereNull('deleted_at');
        });
    }

    /**
     * İlanın tipine göre filtreleme (Satılık, Kiralık)
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('yayinlama_tipi', $type);
    }

    /**
     * İlanı status olarak işaretler
     */
    public function aktivet()
    {
        $this->status = 'active';

        return $this->save();
    }

    /**
     * İlanı pasif olarak işaretler
     */
    public function pasifet()
    {
        $this->status = 'pasif';

        return $this->save();
    }

    // İl scope'u kaldırıldı - il() relationship'i kullanılıyor

    /**
     * Fiyatı formatlanmış şekilde getir
     */
    public function getFiyatFormatliAttribute()
    {
        if (! $this->fiyat) {
            return 'Belirtilmemiş';
        }

        $formattedPrice = number_format($this->fiyat, 0, ',', '.');

        return $formattedPrice . ' ' . ($this->para_birimi ?? 'TL');
    }

    /**
     * Tam adres getir
     */
    public function getTamAdresAttribute()
    {
        $parts = [];

        if ($this->mahalle) {
            $parts[] = $this->mahalle->mahalle_adi ?? $this->adres_mahalle;
        }
        if ($this->ilce) {
            $parts[] = $this->ilce->ilce_adi ?? $this->adres_ilce;
        }
        if ($this->il) {
            $parts[] = $this->il->il_adi ?? $this->adres_il;
        }

        return implode(', ', array_filter($parts)) ?: 'Adres bilgisi yok';
    }

    /**
     * Kapak fotoğrafı URL'si getir
     */
    public function getKapakFotografiUrlAttribute()
    {
        $kapakFotografi = $this->kapakFotografi;

        if ($kapakFotografi && $kapakFotografi->dosya_yolu) {
            return asset('storage/' . $kapakFotografi->dosya_yolu);
        }

        // Varsayılan resim
        return asset('images/default-property.jpg');
    }

    /**
     * İlan türü accessor (eski alan adı ile uyumluluk)
     */
    public function getIlanTuruAttribute()
    {
        return $this->yayinlama_tipi ?? 'Belirtilmemiş';
    }

    /**
     * Başlık accessor (eski alan adı ile uyumluluk)
     */
    public function getBaslikAttribute()
    {
        return $this->ilan_basligi ?? 'Başlık Yok';
    }

    /**
     * Danışman verisi accessor (hem User hem Danisman modelinden)
     */
    public function getDanismanVerisiAttribute()
    {
        // Önce User modelinden kontrol et
        $userDanisman = User::find($this->danisman_id);
        if ($userDanisman) {
            return (object) [
                'id' => $userDanisman->id,
                'name' => $userDanisman->name,
                'email' => $userDanisman->email,
                'phone_number' => $userDanisman->phone_number,
                'source' => 'user_model',
            ];
        }

        try {
            // Danışman modelinden kontrol et
            $danismanModel = \App\Modules\Danisman\Models\Danisman::find($this->danisman_id);
            if ($danismanModel) {
                return (object) [
                    'id' => $danismanModel->id,
                    'name' => $danismanModel->ad . ' ' . $danismanModel->soyad,
                    'email' => $danismanModel->email,
                    'phone_number' => $danismanModel->telefon,
                    'source' => 'danisman_model',
                ];
            }
        } catch (\Exception $e) {
            // Danışman modeli bulunamazsa null döndür
        }

        return null;
    }

    /**
     * Proje ilişkisi
     */
    public function proje()
    {
        return $this->belongsTo(\App\Modules\Emlak\Models\Proje::class, 'proje_id');
    }

    /**
     * Site ilişkisi
     */
    public function site()
    {
        return $this->belongsTo(\App\Models\Site::class, 'site_id');
    }
}
