<?php

namespace App\Modules\Talep\Models;

use App\Models\Ilan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IlanTalepEslesme extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ilan_talep_eslesmeler';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'talep_id',
        'ilan_id',
        'skor',
        'eslesme_detaylari',
        'status',
        'notlar',
        'musteri_gorusu',
        'danisman_notu',
        'goruntulenme_tarihi',
        'iletisim_tarihi',
        'randevu_tarihi',
        'sonuc',
        'sonuc_notu',
        'olusturan_id',
        'guncelleyen_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'skor' => 'decimal:2',
        'eslesme_detaylari' => 'array',
        'goruntulenme_tarihi' => 'datetime',
        'iletisim_tarihi' => 'datetime',
        'randevu_tarihi' => 'datetime',
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

        static::creating(function ($eslesme) {
            if (! $eslesme->status) {
                $eslesme->status = 'yeni';
            }
        });
    }

    /**
     * Talep ile ilişki
     */
    public function talep()
    {
        return $this->belongsTo(Talep::class);
    }

    /**
     * İlan ile ilişki
     */
    public function ilan()
    {
        return $this->belongsTo(Ilan::class);
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
     * Scope: Yeni eşleştirmeler
     */
    public function scopeYeni($query)
    {
        return $query->where('status', 'yeni');
    }

    /**
     * Scope: Görüntülenen eşleştirmeler
     */
    public function scopeGoruntulenen($query)
    {
        return $query->where('status', 'goruntulendi');
    }

    /**
     * Scope: İletişim kurulan eşleştirmeler
     */
    public function scopeIletisimKurulan($query)
    {
        return $query->where('status', 'iletisim_kuruldu');
    }

    /**
     * Scope: Randevu alınan eşleştirmeler
     */
    public function scopeRandevuAlinan($query)
    {
        return $query->where('status', 'randevu_alindi');
    }

    /**
     * Scope: Tamamlanan eşleştirmeler
     */
    public function scopeTamamlanan($query)
    {
        return $query->where('status', 'tamamlandi');
    }

    /**
     * Scope: İptal edilen eşleştirmeler
     */
    public function scopeIptalEdilen($query)
    {
        return $query->where('status', 'iptal');
    }

    /**
     * Scope: Skor aralığına göre filtrele
     */
    public function scopeSkorAralik($query, $minSkor = null, $maxSkor = null)
    {
        if ($minSkor !== null) {
            $query->where('skor', '>=', $minSkor);
        }
        if ($maxSkor !== null) {
            $query->where('skor', '<=', $maxSkor);
        }

        return $query;
    }

    /**
     * Scope: Yüksek skorlu eşleştirmeler (80 ve üzeri)
     */
    public function scopeYuksekSkor($query)
    {
        return $query->where('skor', '>=', 80);
    }

    /**
     * Scope: Orta skorlu eşleştirmeler (60-79 arası)
     */
    public function scopeOrtaSkor($query)
    {
        return $query->whereBetween('skor', [60, 79]);
    }

    /**
     * Scope: Düşük skorlu eşleştirmeler (60 altı)
     */
    public function scopeDusukSkor($query)
    {
        return $query->where('skor', '<', 60);
    }

    /**
     * Scope: Skor sıralaması
     */
    public function scopeSkorSirala($query, $direction = 'desc')
    {
        return $query->orderBy('skor', $direction);
    }

    /**
     * Scope: Son eşleştirmeler
     */
    public function scopeSonEslesmeler($query, $direction = 'desc')
    {
        return $query->orderBy('created_at', $direction);
    }

    /**
     * Eşleştirme yaşını hesapla (gün olarak)
     */
    public function getYasAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Skor rengini getir
     */
    public function getSkorRengiAttribute()
    {
        if ($this->skor >= 80) {
            return 'success';
        } elseif ($this->skor >= 60) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    /**
     * Skor seviyesini getir
     */
    public function getSkorSeviyesiAttribute()
    {
        if ($this->skor >= 80) {
            return 'Yüksek';
        } elseif ($this->skor >= 60) {
            return 'Orta';
        } else {
            return 'Düşük';
        }
    }

    /**
     * Durum rengini getir
     */
    public function getDurumRengiAttribute()
    {
        $renkler = [
            'yeni' => 'primary',
            'goruntulendi' => 'info',
            'iletisim_kuruldu' => 'warning',
            'randevu_alindi' => 'success',
            'tamamlandi' => 'success',
            'iptal' => 'danger',
        ];

        return $renkler[$this->status] ?? 'secondary';
    }

    /**
     * Durum açıklamasını getir
     */
    public function getDurumAciklamasiAttribute()
    {
        $aciklamalar = [
            'yeni' => 'Yeni eşleştirme, henüz görüntülenmedi',
            'goruntulendi' => 'Müşteri tarafından görüntülendi',
            'iletisim_kuruldu' => 'Müşteri ile iletişim kuruldu',
            'randevu_alindi' => 'Görüşme randevusu alındı',
            'tamamlandi' => 'Eşleştirme başarıyla tamamlandı',
            'iptal' => 'Eşleştirme iptal edildi',
        ];

        return $aciklamalar[$this->status] ?? 'Bilinmeyen status';
    }

    /**
     * Sonuç rengini getir
     */
    public function getSonucRengiAttribute()
    {
        $renkler = [
            'basarili' => 'success',
            'basarisiz' => 'danger',
            'beklemede' => 'warning',
            'iptal' => 'secondary',
        ];

        return $renkler[$this->sonuc] ?? 'info';
    }

    /**
     * Eşleştirme detaylarını formatla
     */
    public function getFormatlananDetaylarAttribute()
    {
        if (! $this->eslesme_detaylari) {
            return [];
        }

        $detaylar = [];
        foreach ($this->eslesme_detaylari as $kriter => $deger) {
            switch ($kriter) {
                case 'fiyat_uyumu':
                    $detaylar['Fiyat Uyumu'] = $deger.'%';
                    break;
                case 'lokasyon_uyumu':
                    $detaylar['Lokasyon Uyumu'] = $deger.'%';
                    break;
                case 'metrekare_uyumu':
                    $detaylar['Metrekare Uyumu'] = $deger.'%';
                    break;
                case 'ozellik_uyumu':
                    $detaylar['Özellik Uyumu'] = $deger.'%';
                    break;
                default:
                    $detaylar[ucfirst(str_replace('_', ' ', $kriter))] = $deger;
            }
        }

        return $detaylar;
    }

    /**
     * Eşleştirme görüntülendi mi?
     */
    public function goruntulendMi()
    {
        return ! is_null($this->goruntulenme_tarihi);
    }

    /**
     * İletişim kuruldu mu?
     */
    public function iletisimKurulduMu()
    {
        return ! is_null($this->iletisim_tarihi);
    }

    /**
     * Randevu alındı mı?
     */
    public function randevuAlindiMi()
    {
        return ! is_null($this->randevu_tarihi);
    }

    /**
     * Eşleştirme status mi?
     */
    public function statusMi()
    {
        return ! in_array($this->status, ['tamamlandi', 'iptal']);
    }

    /**
     * Eşleştirme düzenlenebilir mi?
     */
    public function duzenlenebilirMi()
    {
        return in_array($this->status, ['yeni', 'goruntulendi', 'iletisim_kuruldu', 'randevu_alindi']);
    }

    /**
     * Eşleştirme silinebilir mi?
     */
    public function silinebilirMi()
    {
        return true; // Soft delete kullanıldığı için her zaman silinebilir
    }

    /**
     * Durum listesi
     */
    public static function getDurumlar()
    {
        return [
            'yeni' => 'Yeni',
            'goruntulendi' => 'Görüntülendi',
            'iletisim_kuruldu' => 'İletişim Kuruldu',
            'randevu_alindi' => 'Randevu Alındı',
            'tamamlandi' => 'Tamamlandı',
            'iptal' => 'İptal',
        ];
    }

    /**
     * Sonuç listesi
     */
    public static function getSonuclar()
    {
        return [
            'basarili' => 'Başarılı',
            'basarisiz' => 'Başarısız',
            'beklemede' => 'Beklemede',
            'iptal' => 'İptal',
        ];
    }

    /**
     * Skor aralıkları
     */
    public static function getSkorAraliklari()
    {
        return [
            'yuksek' => ['min' => 80, 'max' => 100, 'label' => 'Yüksek (80-100)'],
            'orta' => ['min' => 60, 'max' => 79, 'label' => 'Orta (60-79)'],
            'dusuk' => ['min' => 0, 'max' => 59, 'label' => 'Düşük (0-59)'],
        ];
    }

    /**
     * Eşleştirme istatistikleri
     */
    public static function getIstatistikler()
    {
        return [
            'toplam' => static::count(),
            'yeni' => static::yeni()->count(),
            'goruntulenen' => static::goruntulenen()->count(),
            'iletisim_kurulan' => static::iletisimKurulan()->count(),
            'randevu_alinan' => static::randevuAlinan()->count(),
            'tamamlanan' => static::tamamlanan()->count(),
            'iptal_edilen' => static::iptalEdilen()->count(),
            'yuksek_skor' => static::yuksekSkor()->count(),
            'orta_skor' => static::ortaSkor()->count(),
            'dusuk_skor' => static::dusukSkor()->count(),
        ];
    }
}
