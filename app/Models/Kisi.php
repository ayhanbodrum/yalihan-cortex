<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\KisiTipi;
use Illuminate\Support\Facades\Crypt;

/**
 * App\Models\Kisi
 *
 * @property int $id
 * @property string $ad
 * @property string $soyad
 * @property string|null $telefon
 * @property string|null $email
 * @property string|null $notlar
 * @property \App\Enums\KisiTipi|null $kisi_tipi Context7: Primary field (Enum)
 * @property string|null $musteri_tipi Deprecated: Use kisi_tipi instead
 * @property string $status
 * @property string|null $kaynak
 * @property int|null $danisman_id
 *
 * // Global Adres İlişkisel Alanları
 * @property int|null $ulke_id
 * @property int|null $il_id
 * @property int|null $ilce_id
 * @property int|null $mahalle_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * // Accessors
 * @property-read string $tam_ad
 * @property-read string $tam_adres
 *
 * // İlişkiler (Relationships)
 * @property-read User|null $danisman
 * @property-read Ulke|null $ulke
 * @property-read Il|null $il
 * @property-read Ilce|null $ilce
 * @property-read Mahalle|null $mahalle
 * @property-read \Illuminate\Database\Eloquent\Collection|Talep[] $talepler
 * @property-read int|null $talepler_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Ilan[] $ilanlarAsSahibi
 * @property-read int|null $ilanlar_as_sahibi_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Ilan[] $ilanlarAsIlgili
 * @property-read int|null $ilanlar_as_ilgili_count
 */
class Kisi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kisiler';

    protected $fillable = [
        // Temel Kişi Bilgileri
        'ad',
        'soyad',
        'telefon',
        'email',
        'notlar',

        // Context7 Standart Alanları
        'status', // Context7 kuralı: status kullanımı
        'kisi_tipi', // ✅ Context7: PREFERRED field name
        'musteri_tipi', // ⚠️ DEPRECATED: Backward compatibility only, use kisi_tipi
        'kaynak',
        'danisman_id',

        // Global Adres Sistemi (Context7 uyumlu)
        'ulke_id',
        'il_id', // Context7 kuralı: il_id kullanımı (il_id yerine)
        'ilce_id',
        'mahalle_id',
        'adres', // ✅ Context7: Adres detayı (text kolonu)

        // CRM Genişletilmiş Alanları
        'tc_kimlik',
        'tc_kimlik_encrypted',
        'dogum_tarihi',
        'meslek',
        'gelir_duzeyi',
        'medeni_status',
        'vergi_no',
        'vergi_dairesi',
        'banka_bilgileri',
        'acil_status_iletisim',
        'tercihler',
        'risk_profili',
        'musteri_segmenti',
        'son_aktivite',
        'toplam_islem',
        'toplam_islem_tutari',
        'memnuniyet_skoru',
    ];

    protected $appends = ['tam_ad', 'danisman_verisi'];

    protected $casts = [
        'dogum_tarihi' => 'date',
        'banka_bilgileri' => 'array',
        'acil_status_iletisim' => 'array',
        'tercihler' => 'array',
        'son_aktivite' => 'datetime',
        'toplam_islem_tutari' => 'decimal:2',
        'memnuniyet_skoru' => 'decimal:1',
        // kisi_tipi cast'i kaldırıldı - accessor/mutator kullanılıyor (Türkçe string → Enum dönüşümü için)
    ];

    // ======================================================================
    // ERİŞİMCİLER & DEĞİŞTİRİCİLER (ACCESSORS & MUTATORS)
    // ======================================================================

    /**
     * kisi_tipi accessor: Veritabanındaki Türkçe string'i Enum'a çevirir
     */
    public function getKisiTipiAttribute($value): ?KisiTipi
    {
        if (!$value) {
            return null;
        }

        // Eğer zaten enum ise, direkt döndür
        if ($value instanceof KisiTipi) {
            return $value;
        }

        // Türkçe string değerleri enum değerlerine çevir
        $mapping = [
            'Ev Sahibi' => KisiTipi::EV_SAHIBI,
            'ev_sahibi' => KisiTipi::EV_SAHIBI,
            'Alıcı' => KisiTipi::ALICI,
            'alici' => KisiTipi::ALICI,
            'Kiracı' => KisiTipi::KIRACI,
            'kiraci' => KisiTipi::KIRACI,
            'Satıcı' => KisiTipi::SATICI,
            'satici' => KisiTipi::SATICI,
            'Yatırımcı' => KisiTipi::YATIRIMCI,
            'yatirimci' => KisiTipi::YATIRIMCI,
            'Aracı' => KisiTipi::ARACI,
            'araci' => KisiTipi::ARACI,
            'Danışman' => KisiTipi::DANISMAN,
            'danisman' => KisiTipi::DANISMAN,
            'Müşteri' => KisiTipi::ALICI, // Fallback
        ];

        return $mapping[$value] ?? KisiTipi::tryFrom($value);
    }

    public function setTcKimlikAttribute($value): void
    {
        $this->attributes['tc_kimlik'] = $value;
        try {
            $this->attributes['tc_kimlik_encrypted'] = $value ? Crypt::encryptString($value) : null;
        } catch (\Throwable $e) {
            $this->attributes['tc_kimlik_encrypted'] = null;
        }
    }

    public function getTcKimlikMaskedAttribute(): ?string
    {
        $v = $this->attributes['tc_kimlik'] ?? null;
        if (!$v) return null;
        $len = strlen($v);
        if ($len <= 4) return str_repeat('*', max(0, $len));
        return str_repeat('*', $len - 4) . substr($v, -4);
    }

    /**
     * kisi_tipi mutator: Enum'ı veritabanına kaydedilecek string'e çevirir
     */
    public function setKisiTipiAttribute($value): void
    {
        if ($value instanceof KisiTipi) {
            $this->attributes['kisi_tipi'] = $value->value;
        } elseif (is_string($value)) {
            // Türkçe string ise enum'a çevirip value'sunu al
            $enum = $this->getKisiTipiAttribute($value);
            $this->attributes['kisi_tipi'] = $enum?->value ?? $value;
        } else {
            $this->attributes['kisi_tipi'] = $value;
        }
    }

    public function getTamAdAttribute(): string
    {
        return trim($this->ad . ' ' . $this->soyad);
    }

    public function getTamAdresAttribute(): string
    {
        $adresParcalari = [
            $this->mahalle->name ?? null,
            $this->ilce->name ?? null,
            $this->il->il_adi ?? null,
            $this->ulke->name ?? null,
        ];

        return implode(', ', array_filter($adresParcalari));
    }

    /**
     * Danışman verilerini hem User hem de Danisman modelinden alır
     */
    public function getDanismanVerisiAttribute()
    {
        if (! $this->danisman_id) {
            return null;
        }

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

        // User modelinde bulunamazsa, Danışman modelinden kontrol et
        try {
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

    // ======================================================================
    // İLİŞKİLER (RELATIONSHIPS)
    // ======================================================================

    /**
     * Bu kişinin danışmanını döndürür (User modeli ile)
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

    public function talepler(): HasMany
    {
        return $this->hasMany(Talep::class, 'kisi_id');
    }

    /**
     * Bu kişinin "ilan sahibi" olduğu ilanları döndürür.
     */
    public function ilanlarAsSahibi(): HasMany
    {
        return $this->hasMany(Ilan::class, 'ilan_sahibi_id');
    }

    /**
     * Bu kişinin "ilgili kişi" olduğu ilanları döndürür.
     */
    public function ilanlarAsIlgili(): HasMany
    {
        return $this->hasMany(Ilan::class, 'ilgili_kisi_id');
    }

    // --- Global Adres İlişkileri ---

    public function ulke(): BelongsTo
    {
        return $this->belongsTo(Ulke::class, 'ulke_id');
    }

    // Context7 kuralı: il() relationship kullanımı

    /**
     * İl relationship (External Context standard)
     */
    public function il(): BelongsTo
    {
        return $this->belongsTo(Il::class, 'il_id');
    }

    // Context7 kuralı: il() relationship kullanımı

    public function ilce(): BelongsTo
    {
        return $this->belongsTo(Ilce::class, 'ilce_id');
    }

    public function mahalle(): BelongsTo
    {
        return $this->belongsTo(Mahalle::class, 'mahalle_id');
    }

    /**
     * Bu kişinin etiketlerini döndürür.
     */
    public function etiketler()
    {
        return $this->belongsToMany(Etiket::class, 'etiket_kisi', 'kisi_id', 'etiket_id')
            ->withPivot('user_id')
            ->withTimestamps();
    }

    /**
     * Bu kişinin status etiketlerini döndürür (Context7 uyumlu).
     */
    public function statusEtiketler()
    {
        return $this->belongsToMany(Etiket::class, 'etiket_kisi', 'kisi_id', 'etiket_id')
            ->where('status', 'Aktif') // Context7: status alanı string değer
            ->withPivot('user_id')
            ->withTimestamps();
    }

    /**
     * Bu kişinin notlarını döndürür.
     */
    public function notlar()
    {
        return $this->hasMany(KisiNot::class, 'kisi_id')->with('user')->yeni();
    }

    /**
     * Bu kişinin önemli notlarını döndürür.
     */
    public function onemliNotlar()
    {
        return $this->hasMany(KisiNot::class, 'kisi_id')
            ->where('onemli', true)
            ->with('user')
            ->orderBy('created_at', 'desc');
    }

    // ======================================================================
    // SCOPES (Context7 Uyumlu)
    // ======================================================================

    /**
     * Aktif kişileri getir (Context7 uyumlu).
     */
    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['Aktif', 1, true]);
    }

    public function scopeActive($query)
    {
        return $this->scopeAktif($query);
    }

    /**
     * Pasif kişileri getir (Context7 uyumlu).
     */
    public function scopePasif($query)
    {
        return $query->where('status', 'Pasif');
    }

    /**
     * Kişi arama scope (Context7 uyumlu).
     */
    public function scopeSearch($query, $searchTerm)
    {
        if (empty($searchTerm)) {
            return $query;
        }

        return $query->where(function ($q) use ($searchTerm) {
            $q->whereRaw("CONCAT(ad, ' ', soyad) LIKE ?", ["%{$searchTerm}%"])
                ->orWhere('telefon', 'like', "%{$searchTerm}%")
                ->orWhere('email', 'like', "%{$searchTerm}%")
                ->orWhere('tc_kimlik', 'like', "%{$searchTerm}%");
        });
    }

    /**
     * Danışmana göre filtrele (Context7 uyumlu).
     */
    public function scopeByDanisman(\Illuminate\Database\Eloquent\Builder $query, int $danismanId): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('danisman_id', $danismanId);
    }

    /**
     * Müşteri tipine göre filtrele (Context7 uyumlu).
     * @deprecated Use scopeByKisiTipi instead. This method uses kisi_tipi for backward compatibility.
     */
    public function scopeByMusteriTipi($query, $musteriTipi)
    {
        // Context7: musteri_tipi → kisi_tipi migration
        return $query->where('kisi_tipi', $musteriTipi);
    }

    /**
     * Kişi tipine göre filtrele (Context7 standard).
     */
    public function scopeByKisiTipi($query, $kisiTipi)
    {
        return $query->where('kisi_tipi', $kisiTipi);
    }

    // ======================================================================
    // HELPER METHODS (Context7 Uyumlu)
    // ======================================================================

    /**
     * Kişinin tam adını döndürür (Context7 uyumlu).
     */
    public function getFullNameAttribute(): string
    {
        return $this->tam_ad;
    }

    /**
     * Kişinin iletişim bilgilerini döndürür (Context7 uyumlu).
     */
    public function getIletisimBilgileriAttribute(): array
    {
        return [
            'telefon' => $this->telefon,
            'email' => $this->email,
            'adres' => $this->tam_adres,
        ];
    }

    /**
     * Kişinin CRM skorunu hesaplar (Context7 uyumlu).
     */
    public function getCrmScoreAttribute(): int
    {
        $score = 0;

        // Temel bilgiler (40 puan)
        if ($this->ad && $this->soyad) $score += 10;
        if ($this->telefon) $score += 10;
        if ($this->email) $score += 10;
        if ($this->tc_kimlik) $score += 10;

        // Adres bilgileri (30 puan)
        if ($this->il_id) $score += 10;
        if ($this->ilce_id) $score += 10;
        if ($this->mahalle_id) $score += 10;

        // CRM bilgileri (30 puan)
        // ✅ Context7: kisi_tipi preferred, musteri_tipi backward compat
        if ($this->kisi_tipi ?? $this->musteri_tipi) $score += 10;
        if ($this->meslek) $score += 10;
        if ($this->gelir_duzeyi) $score += 10;

        return min($score, 100); // Maksimum 100 puan
    }

    /**
     * Kişinin ilan sahibi olma uygunluğunu kontrol eder (Context7 uyumlu).
     */
    public function isOwnerEligible(): bool
    {
        return $this->status === 'Aktif' &&
            $this->tc_kimlik &&
            $this->telefon &&
            $this->il_id;
    }

    /**
     * Kişinin potansiyel müşteri olma statusunu kontrol eder (Context7 uyumlu).
     */
    public function isPotentialCustomer(): bool
    {
        // ✅ Context7: kisi_tipi preferred, musteri_tipi backward compat
        $tip = $this->kisi_tipi ?? $this->musteri_tipi;
        return in_array($tip, ['alici', 'kiraci']) &&
            $this->status === 'Aktif';
    }

    /**
     * Kişinin satıcı olma statusunu kontrol eder (Context7 uyumlu).
     */
    public function isSeller(): bool
    {
        // ✅ Context7: kisi_tipi preferred, musteri_tipi backward compat
        $tip = $this->kisi_tipi ?? $this->musteri_tipi;
        return in_array($tip, ['satici', 'ev_sahibi']) &&
            $this->status === 'Aktif';
    }

    // --- Yeni CRM İlişkileri ---

    /**
     * Bu kişinin müşteri notları
     */
    public function musteriNotlar(): HasMany
    {
        return $this->hasMany(MusteriNot::class, 'kisi_id');
    }

    /**
     * Bu kişinin müşteri etiketleri
     */
    public function musteriEtiketler()
    {
        return $this->belongsToMany(MusteriEtiket::class, 'kisi_etiket', 'kisi_id', 'etiket_id')
            ->withTimestamps();
    }

    /**
     * Bu kişinin müşteri aktiviteleri
     */
    public function musteriAktiviteler(): HasMany
    {
        return $this->hasMany(MusteriAktivite::class, 'kisi_id');
    }

    /**
     * Bu kişinin müşteri takibi
     */
    public function musteriTakip(): HasMany
    {
        return $this->hasMany(MusteriTakip::class, 'kisi_id');
    }

    /**
     * Bu kişinin status müşteri takibi
     */
    public function statusMusteriTakip()
    {
        return $this->hasOne(MusteriTakip::class, 'kisi_id')->where('takip_tipi', 'Aktif');
    }

    /**
     * Bu kişinin son aktivitesi
     */
    public function sonAktivite()
    {
        return $this->hasOne(MusteriAktivite::class, 'kisi_id')->latest('aktivite_tarihi');
    }

    /**
     * Bu kişinin bugünkü aktiviteleri
     */
    public function bugunAktiviteler()
    {
        return $this->hasMany(MusteriAktivite::class, 'kisi_id')->bugun();
    }

    /**
     * Bu kişinin bu haftaki aktiviteleri
     */
    public function buHaftaAktiviteler()
    {
        return $this->hasMany(MusteriAktivite::class, 'kisi_id')->buHafta();
    }

    /**
     * Bu kişinin bu ayki aktiviteleri
     */
    public function buAyAktiviteler()
    {
        return $this->hasMany(MusteriAktivite::class, 'kisi_id')->buAy();
    }

    /**
     * Bu kişinin gecikmiş takipleri
     */
    public function gecikmisTakipler()
    {
        return $this->hasMany(MusteriTakip::class, 'kisi_id')->gecikmis();
    }

    /**
     * Bu kişinin acil takipleri
     */
    public function acilTakipler()
    {
        return $this->hasMany(MusteriTakip::class, 'kisi_id')->acil();
    }

    /**
     * Bu kişinin yüksek öncelikli takipleri
     */
    public function yuksekOncelikliTakipler()
    {
        return $this->hasMany(MusteriTakip::class, 'kisi_id')->yuksekOncelik();
    }
}
