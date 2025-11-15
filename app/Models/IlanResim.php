<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * İlan Resim Model
 *
 * Context7 standartlarına uygun ilan resim yönetimi
 */
class IlanResim extends Model
{
    use HasFactory;

    protected $table = 'ilan_resimleri';

    protected $fillable = [
        'ilan_id',
        'dosya_adi',
        'dosya_yolu',
        'dosya_boyutu',
        'mime_type',
        'sira_no',
        'ana_resim',
        'alt_text',
        'aciklama',
        'status'
    ];

    protected $casts = [
        'ana_resim' => 'boolean',
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
     * Scope: Aktif resimler
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
     * Scope: Ana resimler
     */
    public function scopeAnaResim($query)
    {
        return $query->where('ana_resim', true);
    }

    /**
     * Scope: Sıraya göre sırala
     */
    public function scopeSirali($query)
    {
        return $query->orderBy('sira_no', 'asc');
    }

    /**
     * Resim URL'sini al
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->dosya_yolu);
    }

    /**
     * Resim boyutunu formatla
     */
    public function getFormattedSizeAttribute(): string
    {
        if (!$this->dosya_boyutu) {
            return 'Bilinmiyor';
        }

        $bytes = (int) $this->dosya_boyutu;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
