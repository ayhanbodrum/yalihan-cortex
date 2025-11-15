<?php

namespace App\Modules\CRMSatis\Models;

use App\Modules\BaseModule\Models\BaseModel;
use App\Modules\Emlak\Models\Ilan;
use App\Modules\Crm\Models\Musteri;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sozlesme extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * İlişkilendirilmiş tablo adı
     *
     * @var string
     */
    protected $table = 'sozlesmeler';

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
        'sozlesme_tipi', // satis, kiralama, danismanlik
        'sozlesme_no',
        'sozlesme_tarihi',
        'gecerlilik_tarihi',
        'status', // taslak, onaylandi, imzalandi, iptal
        'sozlesme_metni',
        'ozel_kosullar',
        'imza_tarihi',
        'imza_yeri',
        'notlar',
        'ek_dosyalar',
        'onaylayan_id',
        'onay_tarihi',
    ];

    /**
     * Cast edilecek özellikler
     *
     * @var array
     */
    protected $casts = [
        'sozlesme_tarihi' => 'date',
        'gecerlilik_tarihi' => 'date',
        'imza_tarihi' => 'date',
        'onay_tarihi' => 'datetime',
        'ek_dosyalar' => 'array',
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
     * Onaylayan kullanıcı ile ilişki
     */
    public function onaylayan()
    {
        return $this->belongsTo(\App\Modules\Auth\Models\User::class, 'onaylayan_id');
    }

    /**
     * Scope: Taslak sözleşmeler
     */
    public function scopeTaslak($query)
    {
        return $query->where('status', 'taslak');
    }

    /**
     * Scope: Onaylanan sözleşmeler
     */
    public function scopeOnaylanan($query)
    {
        return $query->where('status', 'onaylandi');
    }

    /**
     * Scope: İmzalanan sözleşmeler
     */
    public function scopeImzalanan($query)
    {
        return $query->where('status', 'imzalandi');
    }

    /**
     * Scope: İptal edilen sözleşmeler
     */
    public function scopeIptal($query)
    {
        return $query->where('status', 'iptal');
    }

    /**
     * Sözleşme durumunu güncelle
     */
    public function updateDurum(string $status): bool
    {
        return $this->update(['status' => $status]);
    }

    /**
     * Sözleşmeyi onayla
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
     * Sözleşmeyi imzala
     */
    public function imzala($imzaTarihi = null, $imzaYeri = null): bool
    {
        return $this->update([
            'status' => 'imzalandi',
            'imza_tarihi' => $imzaTarihi ?? now(),
            'imza_yeri' => $imzaYeri,
        ]);
    }

    /**
     * Sözleşmeyi iptal et
     */
    public function iptalEt($not = null): bool
    {
        return $this->update([
            'status' => 'iptal',
            'notlar' => $not,
        ]);
    }

    /**
     * Sözleşme durumu rengi
     */
    public function getDurumRengiAttribute(): string
    {
        return match($this->status) {
            'taslak' => 'gray',
            'onaylandi' => 'blue',
            'imzalandi' => 'green',
            'iptal' => 'red',
            default => 'gray',
        };
    }

    /**
     * Sözleşme tipi etiketi
     */
    public function getSozlesmeTipiEtiketiAttribute(): string
    {
        return match($this->sozlesme_tipi) {
            'satis' => 'Satış Sözleşmesi',
            'kiralama' => 'Kiralama Sözleşmesi',
            'danismanlik' => 'Danışmanlık Sözleşmesi',
            default => 'Bilinmiyor',
        };
    }

    /**
     * Sözleşme geçerli mi?
     */
    public function gecerliMi(): bool
    {
        return $this->status === 'imzalandi' &&
               $this->gecerlilik_tarihi &&
               $this->gecerlilik_tarihi->isFuture();
    }

    /**
     * Sözleşme süresi dolmuş mu?
     */
    public function suresiDolmusMu(): bool
    {
        return $this->gecerlilik_tarihi &&
               $this->gecerlilik_tarihi->isPast();
    }

    /**
     * Sözleşme onaylanabilir mi?
     */
    public function onaylanabilirMi(): bool
    {
        return $this->status === 'taslak' &&
               !empty($this->sozlesme_metni);
    }

    /**
     * Sözleşme imzalanabilir mi?
     */
    public function imzalanabilirMi(): bool
    {
        return $this->status === 'onaylandi';
    }
}
