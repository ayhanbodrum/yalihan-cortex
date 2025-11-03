<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class KisiAdres extends Pivot
{
    protected $table = 'kisi_adresler';

    protected $fillable = [
        'kisi_id',
        'adres_id',
        'adres_tipi',
        'is_primary',
        'status',
        'notlar',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'status' => 'boolean',
    ];

    public $incrementing = true;

    /**
     * Kişi ilişkisi
     */
    public function kisi(): BelongsTo
    {
        return $this->belongsTo(Kisi::class);
    }

    /**
     * Adres ilişkisi
     */
    public function adres(): BelongsTo
    {
        return $this->belongsTo(Adres::class);
    }

    /**
     * Ana adres mi kontrol et
     */
    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    /**
     * Aktif mi kontrol et
     */
    public function isActive(): bool
    {
        return $this->status;
    }

    /**
     * Scope: Sadece ana adresler
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope: Sadece status adresler
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope: Belirli adres tipindeki kayıtlar
     */
    public function scopeOfType($query, $adres_tipi)
    {
        return $query->where('adres_tipi', $adres_tipi);
    }
}
