<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TuristikTesisDetay extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'turistik_tesis_detaylari';

    protected $fillable = [
        'ilan_id',
        'tesis_tipi',
        'tesis_alt_tipi',
        'tesis_kategorisi',
        'toplam_oda_sayisi',
        'toplam_yatak_sayisi',
        'maksimum_kapasite',
        'minimum_kapasite',
        'yildiz_sayisi',
        'kalite_sinifi',
        'tesis_standarti',
        'pansiyon_turu',
        'pansiyon_detaylari',
        'yemek_hizmeti',
        'yemek_turleri',
        'oda_tipleri',
        'oda_ozellikleri',
        'oda_metrekare',
        'oda_kapasitesi',
        'havuz_mevcut',
        'havuz_tipi',
        'spa_mevcut',
        'spa_hizmetleri',
        'spor_imkanlari',
        'cocuk_kulubu',
        'cocuk_aktivite',
        'denize_uzaklik',
        'merkeze_uzaklik',
        'havaalani_uzaklik',
        'transfer_hizmeti',
        'otopark_mevcut',
        'otopark_tipi',
        'yuksek_sezon_baslangic',
        'yuksek_sezon_bitis',
        'dusuk_sezon_baslangic',
        'dusuk_sezon_bitis',
        'sezonluk_fiyatlandirma',
        'rezervasyon_sistemi',
        'on_odeme_gerekli',
        'on_odeme_orani',
        'iptal_politikasi',
        'ai_kalite_puani',
        'ai_onerilen_fiyat',
        'ai_hedef_musteri',
        'ai_pazarlama_stratejisi',
        'ai_analiz_tarihi',
        'ozel_ozellikler',
        'tesis_notlari',
    ];

    protected $casts = [
        'yemek_hizmeti' => 'boolean',
        'yemek_turleri' => 'array',
        'oda_tipleri' => 'array',
        'oda_ozellikleri' => 'array',
        'havuz_mevcut' => 'boolean',
        'spa_mevcut' => 'boolean',
        'spa_hizmetleri' => 'array',
        'spor_imkanlari' => 'array',
        'cocuk_kulubu' => 'boolean',
        'cocuk_aktivite' => 'array',
        'transfer_hizmeti' => 'boolean',
        'otopark_mevcut' => 'boolean',
        'sezonluk_fiyatlandirma' => 'boolean',
        'on_odeme_gerekli' => 'boolean',
        'yuksek_sezon_baslangic' => 'date',
        'yuksek_sezon_bitis' => 'date',
        'dusuk_sezon_baslangic' => 'date',
        'dusuk_sezon_bitis' => 'date',
        'ai_analiz_tarihi' => 'datetime',
        'ozel_ozellikler' => 'array',
        'ai_onerilen_fiyat' => 'decimal:2',
        'on_odeme_orani' => 'decimal:2',
    ];

    /**
     * İlan ilişkisi
     */
    public function ilan(): BelongsTo
    {
        return $this->belongsTo(Ilan::class, 'ilan_id');
    }

    /**
     * Turistik tesis özellikleri ilişkisi
     */
    public function ozellikler(): HasMany
    {
        return $this->hasMany(TuristikTesisOzellik::class, 'turistik_tesis_detay_id');
    }

    /**
     * Rezervasyon takvimi ilişkisi
     */
    public function rezervasyonlar(): HasMany
    {
        return $this->hasMany(TuristikTesisRezervasyon::class, 'turistik_tesis_detay_id');
    }

    /**
     * Tesis tipi enum değerleri
     */
    public static function getTesisTipleri(): array
    {
        return [
            'otel' => 'Otel',
            'pansiyon' => 'Pansiyon',
            'tatil_koyu' => 'Tatil Köyü',
            'villa_kiralama' => 'Villa Kiralama',
            'apart_otel' => 'Apart Otel',
        ];
    }

    /**
     * Tesis kategorileri enum değerleri
     */
    public static function getTesisKategorileri(): array
    {
        return [
            'butik' => 'Butik',
            'resort' => 'Resort',
            'business' => 'Business',
            'aile' => 'Aile',
            'luks' => 'Lüks',
            'ekonomik' => 'Ekonomik',
        ];
    }

    /**
     * Pansiyon türleri enum değerleri
     */
    public static function getPansiyonTurleri(): array
    {
        return [
            'bb' => 'BB (Bed & Breakfast)',
            'hb' => 'HB (Half Board)',
            'fb' => 'FB (Full Board)',
            'ai' => 'AI (All Inclusive)',
            'yp' => 'YP (Yarım Pansiyon)',
        ];
    }

    /**
     * Kalite sınıfları enum değerleri
     */
    public static function getKaliteSiniflari(): array
    {
        return [
            'ekonomik' => 'Ekonomik',
            'orta' => 'Orta',
            'yuksek' => 'Yüksek',
            'luks' => 'Lüks',
        ];
    }

    /**
     * Tesis standartları enum değerleri
     */
    public static function getTesisStandartlari(): array
    {
        return [
            'uluslararasi' => 'Uluslararası',
            'yerel' => 'Yerel',
            'ozel' => 'Özel',
        ];
    }

    /**
     * Rezervasyon sistemleri enum değerleri
     */
    public static function getRezervasyonSistemleri(): array
    {
        return [
            'manuel' => 'Manuel',
            'online' => 'Online',
            'hibrit' => 'Hibrit',
        ];
    }

    /**
     * Otopark tipleri enum değerleri
     */
    public static function getOtoparkTipleri(): array
    {
        return [
            'ucretsiz' => 'Ücretsiz',
            'ucretli' => 'Ücretli',
            'kapali' => 'Kapalı',
        ];
    }

    /**
     * Havuz tipleri enum değerleri
     */
    public static function getHavuzTipleri(): array
    {
        return [
            'acik' => 'Açık',
            'kapali' => 'Kapalı',
            'cocuk' => 'Çocuk',
            'yetişkin' => 'Yetişkin',
        ];
    }

    /**
     * Oda tipleri enum değerleri
     */
    public static function getOdaTipleri(): array
    {
        return [
            'standart' => 'Standart',
            'deluxe' => 'Deluxe',
            'suite' => 'Suite',
            'baskanlik' => 'Başkanlık',
        ];
    }

    /**
     * Sezonluk fiyatlandırma status mi?
     */
    public function isSezonlukFiyatlandirma(): bool
    {
        return $this->sezonluk_fiyatlandirma &&
               $this->yuksek_sezon_baslangic &&
               $this->yuksek_sezon_bitis;
    }

    /**
     * Şu an yüksek sezon mu?
     */
    public function isYuksekSezon(): bool
    {
        if (! $this->isSezonlukFiyatlandirma()) {
            return false;
        }

        $now = now();

        return $now->between(
            $this->yuksek_sezon_baslangic,
            $this->yuksek_sezon_bitis
        );
    }

    /**
     * Şu an düşük sezon mu?
     */
    public function isDusukSezon(): bool
    {
        if (! $this->isSezonlukFiyatlandirma()) {
            return false;
        }

        $now = now();

        return $now->between(
            $this->dusuk_sezon_baslangic,
            $this->dusuk_sezon_bitis
        );
    }

    /**
     * Denize yakın mı? (500m içinde)
     */
    public function isDenizeYakin(): bool
    {
        return $this->denize_uzaklik && $this->denize_uzaklik <= 500;
    }

    /**
     * Merkeze yakın mı? (2km içinde)
     */
    public function isMerkezeYakin(): bool
    {
        return $this->merkeze_uzaklik && $this->merkeze_uzaklik <= 2000;
    }

    /**
     * Havaalanına yakın mı? (30km içinde)
     */
    public function isHavaalaninaYakin(): bool
    {
        return $this->havaalani_uzaklik && $this->havaalani_uzaklik <= 30000;
    }

    /**
     * AI kalite puanı yüksek mi? (80+)
     */
    public function isYuksekKalite(): bool
    {
        return $this->ai_kalite_puani && $this->ai_kalite_puani >= 80;
    }

    /**
     * AI kalite puanı orta mı? (60-79)
     */
    public function isOrtaKalite(): bool
    {
        return $this->ai_kalite_puani && $this->ai_kalite_puani >= 60 && $this->ai_kalite_puani < 80;
    }

    /**
     * AI kalite puanı düşük mü? (0-59)
     */
    public function isDusukKalite(): bool
    {
        return $this->ai_kalite_puani && $this->ai_kalite_puani < 60;
    }

    /**
     * Tesis tipi etiketi
     */
    public function getTesisTipiEtiketiAttribute(): string
    {
        return self::getTesisTipleri()[$this->tesis_tipi] ?? $this->tesis_tipi;
    }

    /**
     * Tesis kategori etiketi
     */
    public function getTesisKategoriEtiketiAttribute(): string
    {
        return self::getTesisKategorileri()[$this->tesis_kategorisi] ?? $this->tesis_kategorisi;
    }

    /**
     * Pansiyon türü etiketi
     */
    public function getPansiyonTuruEtiketiAttribute(): string
    {
        return self::getPansiyonTurleri()[$this->pansiyon_turu] ?? $this->pansiyon_turu;
    }

    /**
     * Kalite sınıfı etiketi
     */
    public function getKaliteSinifiEtiketiAttribute(): string
    {
        return self::getKaliteSiniflari()[$this->kalite_sinifi] ?? $this->kalite_sinifi;
    }

    /**
     * Tesis standart etiketi
     */
    public function getTesisStandartEtiketiAttribute(): string
    {
        return self::getTesisStandartlari()[$this->tesis_standarti] ?? $this->tesis_standarti;
    }

    /**
     * Rezervasyon sistemi etiketi
     */
    public function getRezervasyonSistemiEtiketiAttribute(): string
    {
        return self::getRezervasyonSistemleri()[$this->rezervasyon_sistemi] ?? $this->rezervasyon_sistemi;
    }

    /**
     * Otopark tipi etiketi
     */
    public function getOtoparkTipiEtiketiAttribute(): string
    {
        return self::getOtoparkTipleri()[$this->otopark_tipi] ?? $this->otopark_tipi;
    }

    /**
     * Havuz tipi etiketi
     */
    public function getHavuzTipiEtiketiAttribute(): string
    {
        return self::getHavuzTipleri()[$this->havuz_tipi] ?? $this->havuz_tipi;
    }

    /**
     * Sezon bilgisi
     */
    public function getSezonBilgisiAttribute(): string
    {
        if ($this->isYuksekSezon()) {
            return 'Yüksek Sezon';
        } elseif ($this->isDusukSezon()) {
            return 'Düşük Sezon';
        }

        return 'Normal Sezon';
    }

    /**
     * Konum özeti
     */
    public function getKonumOzetiAttribute(): string
    {
        $ozet = [];

        if ($this->isDenizeYakin()) {
            $ozet[] = 'Denize yakın';
        }

        if ($this->isMerkezeYakin()) {
            $ozet[] = 'Merkeze yakın';
        }

        if ($this->isHavaalaninaYakin()) {
            $ozet[] = 'Havaalanına yakın';
        }

        return implode(', ', $ozet) ?: 'Konum bilgisi yok';
    }

    /**
     * Kalite özeti
     */
    public function getKaliteOzetiAttribute(): string
    {
        if ($this->isYuksekKalite()) {
            return 'Yüksek Kalite';
        } elseif ($this->isOrtaKalite()) {
            return 'Orta Kalite';
        } elseif ($this->isDusukKalite()) {
            return 'Düşük Kalite';
        }

        return 'Kalite değerlendirilmemiş';
    }

    /**
     * Scope: Belirli tesis tipine göre filtrele
     */
    public function scopeTesisTipi($query, $tip)
    {
        return $query->where('tesis_tipi', $tip);
    }

    /**
     * Scope: Belirli yıldız sayısına göre filtrele
     */
    public function scopeYildizSayisi($query, $yildiz)
    {
        return $query->where('yildiz_sayisi', $yildiz);
    }

    /**
     * Scope: Yıldız aralığına göre filtrele
     */
    public function scopeYildizAraligi($query, $min, $max)
    {
        return $query->whereBetween('yildiz_sayisi', [$min, $max]);
    }

    /**
     * Scope: Belirli pansiyon türüne göre filtrele
     */
    public function scopePansiyonTuru($query, $pansiyon)
    {
        return $query->where('pansiyon_turu', $pansiyon);
    }

    /**
     * Scope: Havuz mevcut olanları filtrele
     */
    public function scopeHavuzMevcut($query)
    {
        return $query->where('havuz_mevcut', true);
    }

    /**
     * Scope: Spa mevcut olanları filtrele
     */
    public function scopeSpaMevcut($query)
    {
        return $query->where('spa_mevcut', true);
    }

    /**
     * Scope: Çocuk kulübü mevcut olanları filtrele
     */
    public function scopeCocukKulubuMevcut($query)
    {
        return $query->where('cocuk_kulubu', true);
    }

    /**
     * Scope: AI kalite puanına göre filtrele
     */
    public function scopeAiKalitePuani($query, $min, $max = null)
    {
        if ($max) {
            return $query->whereBetween('ai_kalite_puani', [$min, $max]);
        }

        return $query->where('ai_kalite_puani', '>=', $min);
    }

    /**
     * Scope: Sezonluk fiyatlandırması olanları filtrele
     */
    public function scopeSezonlukFiyatlandirma($query)
    {
        return $query->where('sezonluk_fiyatlandirma', true);
    }

    /**
     * Scope: Transfer hizmeti olanları filtrele
     */
    public function scopeTransferHizmeti($query)
    {
        return $query->where('transfer_hizmeti', true);
    }

    /**
     * Scope: Otopark mevcut olanları filtrele
     */
    public function scopeOtoparkMevcut($query)
    {
        return $query->where('otopark_mevcut', true);
    }
}
