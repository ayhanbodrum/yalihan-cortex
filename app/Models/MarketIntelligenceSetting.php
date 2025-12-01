<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Market Intelligence Setting Model
 *
 * Context7: Market Intelligence - Pazar İstihbaratı Bölge Ayarları
 * Kullanıcıların hangi bölgelerden veri çekileceğini belirler
 */
class MarketIntelligenceSetting extends Model
{
    use HasFactory;

    protected $table = 'market_intelligence_settings';

    protected $fillable = [
        'user_id',
        'il_id',
        'ilce_id',
        'mahalle_id',
        'status',
        'priority',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'il_id' => 'integer',
        'ilce_id' => 'integer',
        'mahalle_id' => 'integer',
        'status' => 'boolean', // Context7: tinyInteger boolean olarak cast edilir
        'priority' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * İlişkiler
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function il()
    {
        return $this->belongsTo(Il::class, 'il_id');
    }

    public function ilce()
    {
        return $this->belongsTo(Ilce::class, 'ilce_id');
    }

    public function mahalle()
    {
        return $this->belongsTo(Mahalle::class, 'mahalle_id');
    }

    /**
     * Scope: Aktif ayarlar (status = 1)
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope: Pasif ayarlar (status = 0)
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Scope: Global ayarlar (user_id = NULL)
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Scope: Kullanıcı bazlı ayarlar
     */
    public function scopeForUser($query, ?int $userId = null)
    {
        if ($userId === null) {
            return $query->whereNull('user_id');
        }

        return $query->where(function ($q) use ($userId) {
            $q->whereNull('user_id') // Global ayarlar
                ->orWhere('user_id', $userId); // Kullanıcı bazlı ayarlar
        });
    }

    /**
     * Scope: Yüksek öncelikli (1-10)
     */
    public function scopeHighPriority($query)
    {
        return $query->whereBetween('priority', [1, 10]);
    }

    /**
     * Scope: Orta öncelikli (11-50)
     */
    public function scopeMediumPriority($query)
    {
        return $query->whereBetween('priority', [11, 50]);
    }

    /**
     * Scope: Düşük öncelikli (51-100)
     */
    public function scopeLowPriority($query)
    {
        return $query->whereBetween('priority', [51, 100]);
    }

    /**
     * Ayarın aktif olup olmadığını kontrol et
     */
    public function isActive(): bool
    {
        return (bool) $this->status;
    }

    /**
     * Ayarın global olup olmadığını kontrol et
     */
    public function isGlobal(): bool
    {
        return $this->user_id === null;
    }

    /**
     * Lokasyon metnini döndür
     */
    public function getLocationTextAttribute(): string
    {
        $parts = [];

        if ($this->il) {
            $parts[] = $this->il->il_adi;
        }

        if ($this->ilce) {
            $parts[] = $this->ilce->ilce_adi;
        } else {
            $parts[] = 'Tüm İlçeler';
        }

        if ($this->mahalle) {
            $parts[] = $this->mahalle->mahalle_adi;
        } elseif ($this->ilce) {
            $parts[] = 'Tüm Mahalleler';
        }

        return implode(' > ', $parts);
    }
}
