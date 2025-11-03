<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Anahtar Yönetimi Model
 *
 * Context7: Anahtar teslim sistemi için
 * - Anahtar durumu, teslim tarihi
 * - Anahtar takibi, notlar
 * - İlan ilişkisi
 */
class AnahtarYonetimi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'anahtar_yonetimi';

    protected $fillable = [
        'ilan_id',
        'anahtar_durumu',
        'teslim_tarihi',
        'teslim_eden_kisi_id',
        'teslim_alan_kisi_id',
        'anahtar_konumu',
        'anahtar_notlari',
        'anahtar_tipi',
        'anahtar_sayisi',
        'anahtar_ozellikleri',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'teslim_tarihi' => 'datetime',
        'anahtar_ozellikleri' => 'array',
        'anahtar_sayisi' => 'integer'
    ];

    /**
     * İlan ilişkisi
     */
    public function ilan()
    {
        return $this->belongsTo(Ilan::class, 'ilan_id');
    }

    /**
     * Teslim eden kişi
     */
    public function teslimEden()
    {
        return $this->belongsTo(User::class, 'teslim_eden_kisi_id');
    }

    /**
     * Teslim alan kişi
     */
    public function teslimAlan()
    {
        return $this->belongsTo(User::class, 'teslim_alan_kisi_id');
    }

    /**
     * Oluşturan kullanıcı
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Güncelleyen kullanıcı
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope: Aktif anahtarlar
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    /**
     * Scope: Teslim edilmiş anahtarlar
     */
    public function scopeTeslimEdilmis($query)
    {
        return $query->where('anahtar_durumu', 'Teslim Edildi');
    }

    /**
     * Scope: Bekleyen anahtarlar
     */
    public function scopeBekleyen($query)
    {
        return $query->where('anahtar_durumu', 'Beklemede');
    }

    /**
     * Anahtar durumu kontrolü
     */
    public function isTeslimEdildi(): bool
    {
        return $this->anahtar_durumu === 'Teslim Edildi';
    }

    /**
     * Anahtar bekliyor mu?
     */
    public function isBekliyor(): bool
    {
        return $this->anahtar_durumu === 'Beklemede';
    }

    /**
     * Anahtar teslim edilebilir mi?
     */
    public function canBeDelivered(): bool
    {
        return in_array($this->anahtar_durumu, ['Beklemede', 'Hazır']);
    }
}
