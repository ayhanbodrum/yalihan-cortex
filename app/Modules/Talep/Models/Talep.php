<?php

namespace App\Modules\Talep\Models;

use App\Models\Il;
use App\Models\Ilan;
use App\Models\IlanTalepEslesme;
use App\Models\Ilce;
use App\Models\Kisi;
use App\Models\Mahalle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Talep extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'talepler';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kisi_id',
        'kategori',
        'tip',
        'baslik',
        'aciklama',
        'min_fiyat',
        'max_fiyat',
        'min_metrekare',
        'max_metrekare',
        'oda_sayisi',
        'salon_sayisi',
        'banyo_sayisi',
        'balkon_var',
        'asansor_var',
        'otopark_var',
        'esyali',
        'il_id',
        'ilce_id',
        'mahalle_id',
        'adres_detay',
        'koordinat_lat',
        'koordinat_lng',
        'yakinlik_kriterleri',
        'ozel_istekler',
        'oncelik_seviyesi',
        'status',
        'status_notu',
        'status_degisim_tarihi',
        'tamamlanma_tarihi',
        'son_aktivite_tarihi',
        'takip_eden_id',
        'olusturan_id',
        'guncelleyen_id',
        'notlar',
        'etiketler',
        'kaynak',
        'referans_no',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'min_fiyat' => 'decimal:2',
        'max_fiyat' => 'decimal:2',
        'min_metrekare' => 'integer',
        'max_metrekare' => 'integer',
        'oda_sayisi' => 'integer',
        'salon_sayisi' => 'integer',
        'banyo_sayisi' => 'integer',
        'balkon_var' => 'boolean',
        'asansor_var' => 'boolean',
        'otopark_var' => 'boolean',
        'esyali' => 'boolean',
        'koordinat_lat' => 'decimal:8',
        'koordinat_lng' => 'decimal:8',
        'yakinlik_kriterleri' => 'array',
        'ozel_istekler' => 'array',
        'status_degisim_tarihi' => 'datetime',
        'tamamlanma_tarihi' => 'datetime',
        'son_aktivite_tarihi' => 'datetime',
        'etiketler' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($talep) {
            if (! $talep->referans_no) {
                $talep->referans_no = 'TLP-'.date('Y').'-'.str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
            }
            if (! $talep->status) {
                $talep->status = 'active';
            }
            $talep->son_aktivite_tarihi = now();
        });

        static::updating(function ($talep) {
            $talep->son_aktivite_tarihi = now();
        });
    }

    /**
     * Kişi ile ilişki
     */
    public function kisi()
    {
        return $this->belongsTo(Kisi::class);
    }

    /**
     * İl ile ilişki
     */
    public function il()
    {
        return $this->belongsTo(Il::class);
    }

    /**
     * İlçe ile ilişki
     */
    public function ilce()
    {
        return $this->belongsTo(Ilce::class);
    }

    /**
     * Mahalle ile ilişki
     */
    public function mahalle()
    {
        return $this->belongsTo(Mahalle::class);
    }

    /**
     * Takip eden kullanıcı ile ilişki
     */
    public function takipEden()
    {
        return $this->belongsTo(User::class, 'takip_eden_id');
    }

    /**
     * Oluşturan kullanıcı ile ilişki
     */
    public function olusturan()
    {
        return $this->belongsTo(User::class, 'olusturan_id');
    }

    /**
     * Güncelleyen kullanıcı ile ilişki
     */
    public function guncelleyen()
    {
        return $this->belongsTo(User::class, 'guncelleyen_id');
    }

    /**
     * İlan eşleştirmeleri ile ilişki
     */
    public function eslesmeler()
    {
        return $this->hasMany(IlanTalepEslesme::class);
    }

    /**
     * Eşleşen ilanlar ile ilişki
     */
    public function eslesenIlanlar()
    {
        return $this->belongsToMany(Ilan::class, 'ilan_talep_eslesmeler')
            ->withPivot(['skor', 'notlar', 'status', 'created_at'])
            ->withTimestamps();
    }

    /**
     * Talep analizleri ile ilişki
     */
    public function analizler()
    {
        return $this->hasMany(TalepAnaliz::class);
    }

    /**
     * Aktiviteler ile ilişki (CRM modülü ile entegre edilecek)
     */
    public function aktiviteler()
    {
        // Bu ilişki CRM modülü tamamlandığında status edilecek
        // return $this->morphMany(Aktivite::class, 'aktivite_sahibi');
        return collect([]);
    }

    /**
     * Scope: Aktif talepler
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Beklemedeki talepler
     */
    public function scopeBeklemede($query)
    {
        return $query->where('status', 'beklemede');
    }

    /**
     * Scope: Tamamlanan talepler
     */
    public function scopeTamamlanan($query)
    {
        return $query->where('status', 'tamamlandi');
    }

    /**
     * Scope: Kategoriye göre filtrele
     */
    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope: Fiyat aralığına göre filtrele
     */
    public function scopeFiyatAralik($query, $minFiyat = null, $maxFiyat = null)
    {
        if ($minFiyat) {
            $query->where('min_fiyat', '>=', $minFiyat);
        }
        if ($maxFiyat) {
            $query->where('max_fiyat', '<=', $maxFiyat);
        }

        return $query;
    }

    /**
     * Scope: Lokasyona göre filtrele
     */
    public function scopeLokasyon($query, $ilId = null, $ilceId = null, $mahalleId = null)
    {
        if ($ilId) {
            $query->where('il_id', $ilId);
        }
        if ($ilceId) {
            $query->where('ilce_id', $ilceId);
        }
        if ($mahalleId) {
            $query->where('mahalle_id', $mahalleId);
        }

        return $query;
    }

    /**
     * Scope: Son aktivite tarihine göre sırala
     */
    public function scopeSonAktivite($query, $direction = 'desc')
    {
        return $query->orderBy('son_aktivite_tarihi', $direction);
    }

    /**
     * Scope: Öncelik seviyesine göre sırala
     */
    public function scopeOncelik($query, $direction = 'desc')
    {
        return $query->orderBy('oncelik_seviyesi', $direction);
    }

    /**
     * Talep yaşını hesapla (gün olarak)
     */
    public function getYasAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Son aktiviteden bu yana geçen süreyi hesapla
     */
    public function getSonAktiviteSuresiAttribute()
    {
        if (! $this->son_aktivite_tarihi) {
            return null;
        }

        return $this->son_aktivite_tarihi->diffForHumans();
    }

    /**
     * Durum rengini getir
     */
    public function getDurumRengiAttribute()
    {
        $renkler = [
            'active' => 'success',
            'beklemede' => 'warning',
            'tamamlandi' => 'info',
            'iptal' => 'danger',
        ];

        return $renkler[$this->status] ?? 'secondary';
    }

    /**
     * Öncelik seviyesi rengini getir
     */
    public function getOncelikRengiAttribute()
    {
        $renkler = [
            'dusuk' => 'success',
            'normal' => 'info',
            'yuksek' => 'warning',
            'acil' => 'danger',
        ];

        return $renkler[$this->oncelik_seviyesi] ?? 'secondary';
    }

    /**
     * Fiyat aralığını formatla
     */
    public function getFiyatAralikAttribute()
    {
        if ($this->min_fiyat && $this->max_fiyat) {
            return number_format($this->min_fiyat, 0, ',', '.').' - '.number_format($this->max_fiyat, 0, ',', '.').' ₺';
        } elseif ($this->min_fiyat) {
            return number_format($this->min_fiyat, 0, ',', '.').' ₺ ve üzeri';
        } elseif ($this->max_fiyat) {
            return number_format($this->max_fiyat, 0, ',', '.').' ₺ ve altı';
        }

        return 'Belirtilmemiş';
    }

    /**
     * Metrekare aralığını formatla
     */
    public function getMetrekareAralikAttribute()
    {
        if ($this->min_metrekare && $this->max_metrekare) {
            return $this->min_metrekare.' - '.$this->max_metrekare.' m²';
        } elseif ($this->min_metrekare) {
            return $this->min_metrekare.' m² ve üzeri';
        } elseif ($this->max_metrekare) {
            return $this->max_metrekare.' m² ve altı';
        }

        return 'Belirtilmemiş';
    }

    /**
     * Tam lokasyon adresini getir
     */
    public function getTamLokasyonAttribute()
    {
        $lokasyon = [];

        if ($this->mahalle) {
            $lokasyon[] = $this->mahalle->ad;
        }
        if ($this->ilce) {
            $lokasyon[] = $this->ilce->ad;
        }
        if ($this->il) {
            $lokasyon[] = $this->il->ad;
        }

        return implode(', ', $lokasyon);
    }

    /**
     * Eşleştirme sayısını getir
     */
    public function getEslesmeSayisiAttribute()
    {
        return $this->eslesmeler()->count();
    }

    /**
     * En yüksek eşleştirme skorunu getir
     */
    public function getEnYuksekSkorAttribute()
    {
        return $this->eslesmeler()->max('skor') ?? 0;
    }

    /**
     * Talep tamamlanabilir mi?
     */
    public function tamamlanabilirMi()
    {
        return in_array($this->status, ['active', 'beklemede']);
    }

    /**
     * Talep düzenlenebilir mi?
     */
    public function duzenlenebilirMi()
    {
        return in_array($this->status, ['active', 'beklemede']);
    }

    /**
     * Talep silinebilir mi?
     */
    public function silinebilirMi()
    {
        return true; // Soft delete kullanıldığı için her zaman silinebilir
    }

    /**
     * Kategori listesi
     */
    public static function getKategoriler()
    {
        return [
            'satilik' => 'Satılık',
            'kiralik' => 'Kiralık',
            'devren_satilik' => 'Devren Satılık',
            'devren_kiralik' => 'Devren Kiralık',
        ];
    }

    /**
     * Tip listesi
     */
    public static function getTipler()
    {
        return [
            'konut' => 'Konut',
            'isyeri' => 'İşyeri',
            'arsa' => 'Arsa',
            'bina' => 'Bina',
        ];
    }

    /**
     * Durum listesi
     */
    public static function getDurumlar()
    {
        return [
            'active' => 'Aktif',
            'beklemede' => 'Beklemede',
            'tamamlandi' => 'Tamamlandı',
            'iptal' => 'İptal',
        ];
    }

    /**
     * Öncelik seviyeleri
     */
    public static function getOncelikSeviyeleri()
    {
        return [
            'dusuk' => 'Düşük',
            'normal' => 'Normal',
            'yuksek' => 'Yüksek',
            'acil' => 'Acil',
        ];
    }

    /**
     * Kaynak listesi
     */
    public static function getKaynaklar()
    {
        return [
            'web' => 'Web Sitesi',
            'telefon' => 'Telefon',
            'email' => 'E-posta',
            'sosyal_medya' => 'Sosyal Medya',
            'referans' => 'Referans',
            'diger' => 'Diğer',
        ];
    }
}
