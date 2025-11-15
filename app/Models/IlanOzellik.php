<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * İlan Özellik Model
 *
 * Context7 standartlarına uygun ilan özellik yönetimi
 */
class IlanOzellik extends Model
{
    use HasFactory;

    protected $table = 'ilan_ozellikleri';

    protected $fillable = [
        'ilan_id',
        'ozellik_id',
        'deger',
        'aciklama',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * İlan ilişkisi
     */
    public function ilan(): BelongsTo
    {
        return $this->belongsTo(Ilan::class, 'ilan_id');
    }

    /**
     * Özellik ilişkisi
     */
    public function ozellik(): BelongsTo
    {
        return $this->belongsTo(Ozellik::class, 'ozellik_id');
    }

    /**
     * Scope: Aktif özellikler
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    /**
     * Scope: İlan ID'ye göre filtrele
     */
    public function scopeIlanId($query, $ilanId)
    {
        return $query->where('ilan_id', $ilanId);
    }

    /**
     * Scope: Özellik ID'ye göre filtrele
     */
    public function scopeOzellikId($query, $ozellikId)
    {
        return $query->where('ozellik_id', $ozellikId);
    }
}
