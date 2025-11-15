<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * KisiTakip Model
 * 
 * Context7 Standardı: kisi_takip table
 * Replaces: MusteriTakip (deprecated)
 */
class KisiTakip extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kisi_takip'; // ✅ Context7: kisi_takip

    protected $fillable = [
        'kisi_id',
        'danisman_id',
        'takip_tipi',
        'notlar',
        'son_takip_tarihi',
        'sonraki_takip_tarihi',
        'oncelik',
    ];

    protected $casts = [
        'son_takip_tarihi' => 'datetime',
        'sonraki_takip_tarihi' => 'datetime',
    ];

    /**
     * Takip tipi seçenekleri
     */
    public static function getTakipTipleri(): array
    {
        return [
            'Aktif' => 'Aktif',
            'Pasif' => 'Pasif',
            'Potansiyel' => 'Potansiyel',
            'Kayıp' => 'Kayıp',
        ];
    }

    /**
     * Öncelik seçenekleri
     */
    public static function getOncelikler(): array
    {
        return [
            'Düşük' => 'Düşük',
            'Normal' => 'Normal',
            'Yüksek' => 'Yüksek',
            'Acil' => 'Acil',
        ];
    }

    /**
     * Kisi ile ilişki
     */
    public function kisi(): BelongsTo
    {
        return $this->belongsTo(Kisi::class, 'kisi_id');
    }

    /**
     * Danışman ile ilişki
     */
    public function danisman(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'danisman_id');
    }

    /**
     * Takip tipi etiketi
     */
    public function getTakipTipiEtiketiAttribute(): string
    {
        $renkler = [
            'Aktif' => 'bg-green-100 text-green-800',
            'Pasif' => 'bg-gray-100 text-gray-800',
            'Potansiyel' => 'bg-blue-100 text-blue-800',
            'Kayıp' => 'bg-red-100 text-red-800',
        ];

        return $renkler[$this->takip_tipi] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Öncelik etiketi
     */
    public function getOncelikEtiketiAttribute(): string
    {
        $renkler = [
            'Düşük' => 'bg-gray-100 text-gray-800',
            'Normal' => 'bg-blue-100 text-blue-800',
            'Yüksek' => 'bg-orange-100 text-orange-800',
            'Acil' => 'bg-red-100 text-red-800',
        ];

        return $renkler[$this->oncelik] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Sonraki takip tarihi geçmiş mi?
     */
    public function isTakipGecmis(): bool
    {
        return $this->sonraki_takip_tarihi && $this->sonraki_takip_tarihi->isPast();
    }

    /**
     * Sonraki takip tarihi bugün mü?
     */
    public function isTakipBugun(): bool
    {
        return $this->sonraki_takip_tarihi && $this->sonraki_takip_tarihi->isToday();
    }

    /**
     * Sonraki takip tarihi yarın mı?
     */
    public function isTakipYarin(): bool
    {
        return $this->sonraki_takip_tarihi && $this->sonraki_takip_tarihi->isTomorrow();
    }

    /**
     * Takip gecikmiş mi?
     */
    public function isTakipGecikmis(): bool
    {
        return $this->sonraki_takip_tarihi &&
               $this->sonraki_takip_tarihi->isPast() &&
               $this->takip_tipi === 'Aktif';
    }

    /**
     * Takip acil mi?
     */
    public function isAcil(): bool
    {
        return $this->oncelik === 'Acil';
    }

    /**
     * Takip yüksek öncelikli mi?
     */
    public function isYuksekOncelik(): bool
    {
        return in_array($this->oncelik, ['Yüksek', 'Acil']);
    }

    /**
     * Scope: Belirli takip tipi
     */
    public function scopeTakipTipi($query, $tip)
    {
        return $query->where('takip_tipi', $tip);
    }

    /**
     * Scope: Belirli öncelik
     */
    public function scopeOncelik($query, $oncelik)
    {
        return $query->where('oncelik', $oncelik);
    }

    /**
     * Scope: Gecikmiş takipler
     */
    public function scopeGecikmis($query)
    {
        return $query->where('sonraki_takip_tarihi', '<', now())
            ->where('takip_tipi', 'Aktif');
    }

    /**
     * Scope: Bugünkü takipler
     */
    public function scopeBugun($query)
    {
        return $query->whereDate('sonraki_takip_tarihi', today());
    }

    /**
     * Scope: Yarınki takipler
     */
    public function scopeYarin($query)
    {
        return $query->whereDate('sonraki_takip_tarihi', now()->addDay());
    }

    /**
     * Scope: Bu haftaki takipler
     */
    public function scopeBuHafta($query)
    {
        return $query->whereBetween('sonraki_takip_tarihi', [now(), now()->endOfWeek()]);
    }

    /**
     * Scope: Acil takipler
     */
    public function scopeAcil($query)
    {
        return $query->where('oncelik', 'Acil');
    }

    /**
     * Scope: Yüksek öncelikli takipler
     */
    public function scopeYuksekOncelik($query)
    {
        return $query->whereIn('oncelik', ['Yüksek', 'Acil']);
    }
}

