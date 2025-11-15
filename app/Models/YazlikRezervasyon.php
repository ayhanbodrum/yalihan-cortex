<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Yazlık Rezervasyon Modeli
 *
 * Yazlık emlaklar için rezervasyon sistemini yönetir
 */
class YazlikRezervasyon extends Model
{
    use HasFactory;

    protected $table = 'yazlik_rezervasyonlar';

    protected $fillable = [
        'ilan_id',
        'kisi_adi', // Context7: musteri_adi → kisi_adi (migration: 2025_11_11_103355)
        'kisi_telefon', // Context7: musteri_telefon → kisi_telefon (migration: 2025_11_11_103355)
        'kisi_email', // Context7: musteri_email → kisi_email (migration: 2025_11_11_103355)
        'check_in',
        'check_out',
        'misafir_sayisi',
        'cocuk_sayisi',
        'pet_sayisi',
        'ozel_istekler',
        'toplam_fiyat',
        'kapora_tutari',
        'status',
        'iptal_nedeni',
        'onay_tarihi',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'misafir_sayisi' => 'integer',
        'cocuk_sayisi' => 'integer',
        'pet_sayisi' => 'integer',
        'toplam_fiyat' => 'decimal:2',
        'kapora_tutari' => 'decimal:2',
        'onay_tarihi' => 'datetime',
    ];

    /**
     * İlan ilişkisi
     */
    public function ilan(): BelongsTo
    {
        return $this->belongsTo(Ilan::class);
    }

    /**
     * Rezervasyon status seçenekleri
     */
    public static function getDurumlar(): array
    {
        return [
            'beklemede' => 'Beklemede',
            'onaylandi' => 'Onaylandı',
            'iptal' => 'İptal Edildi',
            'tamamlandi' => 'Tamamlandı',
        ];
    }

    /**
     * Aktif rezervasyonlar scope'u
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['beklemede', 'onaylandi']);
    }

    /**
     * Gelecek rezervasyonlar scope'u
     */
    public function scopeGelecek($query)
    {
        return $query->where('check_in', '>', now());
    }

    /**
     * Mevcut rezervasyonlar scope'u
     */
    public function scopeMevcut($query)
    {
        return $query->where('check_in', '<=', now())
            ->where('check_out', '>', now());
    }

    /**
     * Geçmiş rezervasyonlar scope'u
     */
    public function scopeGecmis($query)
    {
        return $query->where('check_out', '<', now());
    }

    /**
     * Belirli tarih aralığında çakışan rezervasyonlar
     */
    public function scopeCakisan($query, $checkIn, $checkOut, $excludeId = null)
    {
        $query = $query->where(function ($q) use ($checkIn, $checkOut) {
            $q->whereBetween('check_in', [$checkIn, $checkOut])
                ->orWhereBetween('check_out', [$checkIn, $checkOut])
                ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                    $q2->where('check_in', '<=', $checkIn)
                        ->where('check_out', '>=', $checkOut);
                });
        })->whereIn('status', ['beklemede', 'onaylandi']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query;
    }

    /**
     * Rezervasyon süresini gün olarak hesapla
     */
    public function getKonaklumaSuresiAttribute(): int
    {
        return $this->check_in->diffInDays($this->check_out);
    }

    /**
     * Rezervasyon statusunu güncelle
     */
    public function updateDurum(string $status, ?string $not = null): bool
    {
        $this->status = $status;

        if ($status === 'onaylandi') {
            $this->onay_tarihi = now();
        }

        if ($status === 'iptal' && $not) {
            $this->iptal_nedeni = $not;
        }

        return $this->save();
    }

    /**
     * Rezervasyon iptal edilebilir mi kontrol et
     */
    public function iptalEdilebilinirMi(): bool
    {
        return in_array($this->status, ['beklemede', 'onaylandi']) &&
               $this->check_in->isFuture();
    }

    /**
     * Rezervasyon formatlanmış tarih aralığı
     */
    public function getTarihAralığıAttribute(): string
    {
        return $this->check_in->format('d.m.Y').' - '.$this->check_out->format('d.m.Y');
    }
}
