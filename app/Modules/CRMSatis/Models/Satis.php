<?php

namespace App\Modules\CRMSatis\Models;

use App\Modules\BaseModule\Models\BaseModel;
use App\Modules\Crm\Models\Musteri;
use App\Modules\Emlak\Models\Ilan;
use App\Modules\TakimYonetimi\Models\Gorev;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Satis extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * İlişkilendirilmiş tablo adı
     *
     * @var string
     */
    protected $table = 'satislar';

    /**
     * Toplu atanabilir alanlar
     *
     * @var array
     */
    protected $fillable = [
        'ilan_id',
        'musteri_id',
        'danisman_id',
        // Split Commission Fields (Context7: C7-SPLIT-COMMISSION-2025-11-25)
        'satici_danisman_id',
        'alici_danisman_id',
        'satis_tipi', // satis, kiralama, danismanlik
        'satis_fiyati',
        'para_birimi',
        'komisyon_orani',
        'komisyon_tutari',
        // Split Commission Fields
        'satici_komisyon_orani',
        'alici_komisyon_orani',
        'satici_komisyon_tutari',
        'alici_komisyon_tutari',
        'satis_tarihi',
        'sozlesme_tarihi',
        'teslim_tarihi',
        'status', // baslangic, sozlesme, odeme, teslim, tamamlandi, iptal
        'odeme_durumu', // bekliyor, kismi, tamamlandi
        'odenen_tutar',
        'kalan_tutar',
        'notlar',
        'referans_no',
        'fatura_no',
        'sozlesme_no',
    ];

    /**
     * Cast edilecek özellikler
     *
     * @var array
     */
    protected $casts = [
        'satis_fiyati' => 'decimal:2',
        'komisyon_orani' => 'decimal:2',
        'komisyon_tutari' => 'decimal:2',
        'satici_komisyon_orani' => 'decimal:2',
        'alici_komisyon_orani' => 'decimal:2',
        'satici_komisyon_tutari' => 'decimal:2',
        'alici_komisyon_tutari' => 'decimal:2',
        'odenen_tutar' => 'decimal:2',
        'kalan_tutar' => 'decimal:2',
        'satis_tarihi' => 'date',
        'sozlesme_tarihi' => 'date',
        'teslim_tarihi' => 'date',
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
     * Danışman ile ilişki (Deprecated - Backward compatibility)
     *
     * @deprecated Use saticiDanisman() or aliciDanisman() instead
     */
    public function danisman()
    {
        return $this->belongsTo(\App\Modules\Auth\Models\User::class, 'danisman_id');
    }

    /**
     * Satıcı danışman ile ilişki (Context7: C7-SPLIT-COMMISSION-2025-11-25)
     */
    public function saticiDanisman()
    {
        return $this->belongsTo(\App\Modules\Auth\Models\User::class, 'satici_danisman_id');
    }

    /**
     * Alıcı danışman ile ilişki (Context7: C7-SPLIT-COMMISSION-2025-11-25)
     */
    public function aliciDanisman()
    {
        return $this->belongsTo(\App\Modules\Auth\Models\User::class, 'alici_danisman_id');
    }

    /**
     * Görev ile ilişki
     */
    public function gorev()
    {
        return $this->belongsTo(Gorev::class);
    }

    /**
     * Sözleşme ile ilişki
     */
    public function sozlesme()
    {
        return $this->hasOne(Sozlesme::class);
    }

    /**
     * Satış raporları ile ilişki
     */
    public function raporlar()
    {
        return $this->hasMany(SatisRaporu::class);
    }

    /**
     * Scope: Başlangıç durumundaki satışlar
     */
    public function scopeBaslangic($query)
    {
        return $query->where('status', 'baslangic');
    }

    /**
     * Scope: Sözleşme durumundaki satışlar
     */
    public function scopeSozlesme($query)
    {
        return $query->where('status', 'sozlesme');
    }

    /**
     * Scope: Ödeme durumundaki satışlar
     */
    public function scopeOdeme($query)
    {
        return $query->where('status', 'odeme');
    }

    /**
     * Scope: Teslim durumundaki satışlar
     */
    public function scopeTeslim($query)
    {
        return $query->where('status', 'teslim');
    }

    /**
     * Scope: Tamamlanan satışlar
     */
    public function scopeTamamlanan($query)
    {
        return $query->where('status', 'tamamlandi');
    }

    /**
     * Scope: İptal edilen satışlar
     */
    public function scopeIptal($query)
    {
        return $query->where('status', 'iptal');
    }

    /**
     * Satış durumunu güncelle
     */
    public function updateDurum(string $status): bool
    {
        return $this->update(['status' => $status]);
    }

    /**
     * Ödeme durumunu güncelle
     */
    public function updateOdemeDurumu(string $odemeDurumu, ?float $odenenTutar = null): bool
    {
        $data = ['odeme_durumu' => $odemeDurumu];

        if ($odenenTutar !== null) {
            $data['odenen_tutar'] = $odenenTutar;
            $data['kalan_tutar'] = $this->satis_fiyati - $odenenTutar;
        }

        return $this->update($data);
    }

    /**
     * Satış durumu rengi
     */
    public function getDurumRengiAttribute(): string
    {
        return match ($this->status) {
            'baslangic' => 'blue',
            'sozlesme' => 'yellow',
            'odeme' => 'orange',
            'teslim' => 'green',
            'tamamlandi' => 'green',
            'iptal' => 'red',
            default => 'gray',
        };
    }

    /**
     * Satış tipi etiketi
     */
    public function getSatisTipiEtiketiAttribute(): string
    {
        return match ($this->satis_tipi) {
            'satis' => 'Satış',
            'kiralama' => 'Kiralama',
            'danismanlik' => 'Danışmanlık',
            default => 'Bilinmiyor',
        };
    }

    /**
     * Ödeme durumu etiketi
     */
    public function getOdemeDurumuEtiketiAttribute(): string
    {
        return match ($this->odeme_durumu) {
            'bekliyor' => 'Bekliyor',
            'kismi' => 'Kısmi',
            'tamamlandi' => 'Tamamlandı',
            default => 'Bilinmiyor',
        };
    }

    /**
     * Satış tamamlanabilir mi?
     */
    public function tamamlanabilirMi(): bool
    {
        return in_array($this->status, ['teslim']) && $this->odeme_durumu === 'tamamlandi';
    }

    /**
     * Satış iptal edilebilir mi?
     */
    public function iptalEdilebilirMi(): bool
    {
        return in_array($this->status, ['baslangic', 'sozlesme', 'odeme']);
    }
}
