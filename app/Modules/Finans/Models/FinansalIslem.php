<?php

namespace App\Modules\Finans\Models;

use App\Modules\BaseModule\Models\BaseModel;
use App\Modules\Emlak\Models\Ilan;
use App\Modules\Crm\Models\Musteri;
use App\Modules\TakimYonetimi\Models\Gorev;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinansalIslem extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * İlişkilendirilmiş tablo adı
     *
     * @var string
     */
    protected $table = 'finansal_islemler';

    /**
     * Toplu atanabilir alanlar
     *
     * @var array
     */
    protected $fillable = [
        'ilan_id',
        'musteri_id',
        'gorev_id',
        'islem_tipi', // komisyon, odeme, masraf, gelir, gider
        'miktar',
        'para_birimi',
        'aciklama',
        'tarih',
        'status', // bekliyor, onaylandi, reddedildi, tamamlandi
        'onaylayan_id',
        'onay_tarihi',
        'referans_no',
        'fatura_no',
        'notlar',
    ];

    /**
     * Cast edilecek özellikler
     *
     * @var array
     */
    protected $casts = [
        'miktar' => 'decimal:2',
        'tarih' => 'date',
        'onay_tarihi' => 'datetime',
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
     * Görev ile ilişki
     */
    public function gorev()
    {
        return $this->belongsTo(Gorev::class);
    }

    /**
     * Onaylayan kullanıcı ile ilişki
     */
    public function onaylayan()
    {
        return $this->belongsTo(\App\Modules\Auth\Models\User::class, 'onaylayan_id');
    }

    /**
     * Scope: Bekleyen işlemler
     */
    public function scopeBekleyen($query)
    {
        return $query->where('status', 'bekliyor');
    }

    /**
     * Scope: Onaylanan işlemler
     */
    public function scopeOnaylanan($query)
    {
        return $query->where('status', 'onaylandi');
    }

    /**
     * Scope: Tamamlanan işlemler
     */
    public function scopeTamamlanan($query)
    {
        return $query->where('status', 'tamamlandi');
    }

    /**
     * Scope: İşlem tipine göre filtrele
     */
    public function scopeIslemTipi($query, $tip)
    {
        return $query->where('islem_tipi', $tip);
    }

    /**
     * İşlemi onayla
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
     * İşlemi reddet
     */
    public function reddet($onaylayanId, $not = null): bool
    {
        return $this->update([
            'status' => 'reddedildi',
            'onaylayan_id' => $onaylayanId,
            'onay_tarihi' => now(),
            'notlar' => $not,
        ]);
    }

    /**
     * İşlemi tamamla
     */
    public function tamamla(): bool
    {
        return $this->update([
            'status' => 'tamamlandi',
        ]);
    }

    /**
     * İşlem durumu rengi
     */
    public function getDurumRengiAttribute(): string
    {
        return match($this->status) {
            'bekliyor' => 'yellow',
            'onaylandi' => 'green',
            'reddedildi' => 'red',
            'tamamlandi' => 'blue',
            default => 'gray',
        };
    }

    /**
     * İşlem tipi etiketi
     */
    public function getIslemTipiEtiketiAttribute(): string
    {
        return match($this->islem_tipi) {
            'komisyon' => 'Komisyon',
            'odeme' => 'Ödeme',
            'masraf' => 'Masraf',
            'gelir' => 'Gelir',
            'gider' => 'Gider',
            default => 'Bilinmiyor',
        };
    }
}
