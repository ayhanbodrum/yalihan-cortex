<?php

namespace App\Modules\CRMSatis\Models;

use App\Modules\BaseModule\Models\BaseModel;
use App\Modules\Emlak\Models\Ilan;
use App\Modules\Crm\Models\Musteri;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SatisRaporu extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * İlişkilendirilmiş tablo adı
     *
     * @var string
     */
    protected $table = 'satis_raporlari';

    /**
     * Toplu atanabilir alanlar
     *
     * @var array
     */
    protected $fillable = [
        'satis_id',
        'ilan_id',
        'musteri_id',
        'danisman_id',
        'rapor_tipi', // aylik, haftalik, gunluk, ozel
        'rapor_tarihi',
        'baslangic_tarihi',
        'bitis_tarihi',
        'satis_sayisi',
        'toplam_ciro',
        'ortalama_satis_fiyati',
        'komisyon_toplami',
        'basari_orani',
        'musteri_memnuniyeti',
        'rapor_metni',
        'analiz_sonuclari',
        'oneri_ve_gorusler',
        'ek_veriler',
        'status', // hazirlaniyor, tamamlandi, onaylandi
        'olusturan_id',
        'onaylayan_id',
        'onay_tarihi',
    ];

    /**
     * Cast edilecek özellikler
     *
     * @var array
     */
    protected $casts = [
        'rapor_tarihi' => 'date',
        'baslangic_tarihi' => 'date',
        'bitis_tarihi' => 'date',
        'toplam_ciro' => 'decimal:2',
        'ortalama_satis_fiyati' => 'decimal:2',
        'komisyon_toplami' => 'decimal:2',
        'basari_orani' => 'decimal:2',
        'musteri_memnuniyeti' => 'decimal:2',
        'ek_veriler' => 'array',
        'onay_tarihi' => 'datetime',
    ];

    /**
     * Satış ile ilişki
     */
    public function satis()
    {
        return $this->belongsTo(Satis::class);
    }

    /**
     * İlan ile ilişki
     */
    public function ilan()
    {
        return $this->belongsTo(Ilan::class);
    }

    /**
     * Müşteri ile ilişki
     */
    public function musteri()
    {
        return $this->belongsTo(Musteri::class);
    }

    /**
     * Danışman ile ilişki
     */
    public function danisman()
    {
        return $this->belongsTo(\App\Modules\Auth\Models\User::class, 'danisman_id');
    }

    /**
     * Oluşturan kullanıcı ile ilişki
     */
    public function olusturan()
    {
        return $this->belongsTo(\App\Modules\Auth\Models\User::class, 'olusturan_id');
    }

    /**
     * Onaylayan kullanıcı ile ilişki
     */
    public function onaylayan()
    {
        return $this->belongsTo(\App\Modules\Auth\Models\User::class, 'onaylayan_id');
    }

    /**
     * Scope: Hazırlanan raporlar
     */
    public function scopeHazirlaniyor($query)
    {
        return $query->where('status', 'hazirlaniyor');
    }

    /**
     * Scope: Tamamlanan raporlar
     */
    public function scopeTamamlanan($query)
    {
        return $query->where('status', 'tamamlandi');
    }

    /**
     * Scope: Onaylanan raporlar
     */
    public function scopeOnaylanan($query)
    {
        return $query->where('status', 'onaylandi');
    }

    /**
     * Scope: Rapor tipine göre filtrele
     */
    public function scopeRaporTipi($query, $tip)
    {
        return $query->where('rapor_tipi', $tip);
    }

    /**
     * Rapor durumunu güncelle
     */
    public function updateDurum(string $status): bool
    {
        return $this->update(['status' => $status]);
    }

    /**
     * Raporu onayla
     */
    public function onayla($onaylayanId): bool
    {
        return $this->update([
            'status' => 'onaylandi',
            'onaylayan_id' => $onaylayanId,
            'onay_tarihi' => now(),
        ]);
    }

    /**
     * Rapor durumu rengi
     */
    public function getDurumRengiAttribute(): string
    {
        return match($this->status) {
            'hazirlaniyor' => 'yellow',
            'tamamlandi' => 'blue',
            'onaylandi' => 'green',
            default => 'gray',
        };
    }

    /**
     * Rapor tipi etiketi
     */
    public function getRaporTipiEtiketiAttribute(): string
    {
        return match($this->rapor_tipi) {
            'aylik' => 'Aylık Rapor',
            'haftalik' => 'Haftalık Rapor',
            'gunluk' => 'Günlük Rapor',
            'ozel' => 'Özel Rapor',
            default => 'Bilinmiyor',
        };
    }

    /**
     * Rapor tamamlanabilir mi?
     */
    public function tamamlanabilirMi(): bool
    {
        return $this->status === 'hazirlaniyor' && 
               !empty($this->rapor_metni) &&
               !empty($this->analiz_sonuclari);
    }

    /**
     * Rapor onaylanabilir mi?
     */
    public function onaylanabilirMi(): bool
    {
        return $this->status === 'tamamlandi';
    }

    /**
     * Rapor performans skoru
     */
    public function getPerformansSkoruAttribute(): float
    {
        $skor = 0;
        
        // Satış sayısı skoru (0-40 puan)
        if ($this->satis_sayisi > 0) {
            $skor += min(40, $this->satis_sayisi * 2);
        }
        
        // Başarı oranı skoru (0-30 puan)
        if ($this->basari_orani > 0) {
            $skor += min(30, $this->basari_orani * 0.3);
        }
        
        // Müşteri memnuniyeti skoru (0-30 puan)
        if ($this->musteri_memnuniyeti > 0) {
            $skor += min(30, $this->musteri_memnuniyeti * 0.3);
        }
        
        return $skor;
    }

    /**
     * Rapor performans seviyesi
     */
    public function getPerformansSeviyesiAttribute(): string
    {
        $skor = $this->performans_skoru;
        
        return match(true) {
            $skor >= 80 => 'Mükemmel',
            $skor >= 60 => 'İyi',
            $skor >= 40 => 'Orta',
            $skor >= 20 => 'Zayıf',
            default => 'Çok Zayıf',
        };
    }
}
