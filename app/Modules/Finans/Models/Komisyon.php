<?php

namespace App\Modules\Finans\Models;

use App\Modules\BaseModule\Models\BaseModel;
use App\Modules\Emlak\Models\Ilan;
use App\Modules\Crm\Models\Musteri;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Komisyon extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * İlişkilendirilmiş tablo adı
     *
     * @var string
     */
    protected $table = 'komisyonlar';

    /**
     * Toplu atanabilir alanlar
     *
     * @var array
     */
    protected $fillable = [
        'ilan_id',
        'musteri_id',
        'danisman_id',
        'komisyon_tipi', // satis, kiralama, danismanlik
        'komisyon_orani',
        'komisyon_tutari',
        'para_birimi',
        'ilan_fiyati',
        'hesaplama_tarihi',
        'odeme_tarihi',
        'status', // hesaplandı, onaylandı, ödendi
        'notlar',
    ];

    /**
     * Cast edilecek özellikler
     *
     * @var array
     */
    protected $casts = [
        'komisyon_orani' => 'decimal:2',
        'komisyon_tutari' => 'decimal:2',
        'ilan_fiyati' => 'decimal:2',
        'hesaplama_tarihi' => 'date',
        'odeme_tarihi' => 'date',
    ];

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
     * Scope: Hesaplanan komisyonlar
     */
    public function scopeHesaplanan($query)
    {
        return $query->where('status', 'hesaplandi');
    }

    /**
     * Scope: Onaylanan komisyonlar
     */
    public function scopeOnaylanan($query)
    {
        return $query->where('status', 'onaylandi');
    }

    /**
     * Scope: Ödenen komisyonlar
     */
    public function scopeOdenen($query)
    {
        return $query->where('status', 'odendi');
    }

    /**
     * Scope: Komisyon tipine göre filtrele
     */
    public function scopeKomisyonTipi($query, $tip)
    {
        return $query->where('komisyon_tipi', $tip);
    }

    /**
     * Komisyon hesapla
     */
    public function hesaplaKomisyon(): void
    {
        $oran = $this->getKomisyonOrani();
        $this->komisyon_tutari = $this->ilan_fiyati * ($oran / 100);
        $this->hesaplama_tarihi = now();
        $this->status = 'hesaplandi';
        $this->save();
    }

    /**
     * Komisyon oranını al
     */
    private function getKomisyonOrani(): float
    {
        return match($this->komisyon_tipi) {
            'satis' => 3.0, // %3
            'kiralama' => 1.0, // %1
            'danismanlik' => 2.0, // %2
            default => 0.0,
        };
    }

    /**
     * Komisyonu onayla
     */
    public function onayla(): bool
    {
        return $this->update([
            'status' => 'onaylandi',
        ]);
    }

    /**
     * Komisyonu öde
     */
    public function ode(): bool
    {
        return $this->update([
            'status' => 'odendi',
            'odeme_tarihi' => now(),
        ]);
    }

    /**
     * Komisyon durumu rengi
     */
    public function getDurumRengiAttribute(): string
    {
        return match($this->status) {
            'hesaplandi' => 'yellow',
            'onaylandi' => 'green',
            'odendi' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Komisyon tipi etiketi
     */
    public function getKomisyonTipiEtiketiAttribute(): string
    {
        return match($this->komisyon_tipi) {
            'satis' => 'Satış Komisyonu',
            'kiralama' => 'Kiralama Komisyonu',
            'danismanlik' => 'Danışmanlık Komisyonu',
            default => 'Bilinmiyor',
        };
    }
}
