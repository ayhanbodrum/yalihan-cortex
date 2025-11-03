<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TuristikTesisRezervasyon extends Model
{
    use HasFactory;

    protected $table = 'turistik_tesis_rezervasyon';

    protected $fillable = [
        'turistik_tesis_detay_id',
        'tarih',
        'status',
        'oda_tipi',
        'fiyat',
        'musait_oda_sayisi',
        'notlar',
        'ozel_kosullar',
    ];

    protected $casts = [
        'tarih' => 'date',
        'fiyat' => 'decimal:2',
        'ozel_kosullar' => 'array',
    ];

    /**
     * Turistik tesis detay ilişkisi
     */
    public function turistikTesisDetay(): BelongsTo
    {
        return $this->belongsTo(TuristikTesisDetay::class, 'turistik_tesis_detay_id');
    }

    /**
     * Rezervasyon statusları enum değerleri
     */
    public static function getDurumlar(): array
    {
        return [
            'available' => 'Müsait',
            'option' => 'Opsiyon',
            'booked' => 'Rezervasyon Yapıldı',
            'block' => 'Bloke',
        ];
    }

    /**
     * Oda tipleri enum değerleri
     */
    public static function getOdaTipleri(): array
    {
        return [
            'standart' => 'Standart',
            'deluxe' => 'Deluxe',
            'suite' => 'Suite',
            'baskanlik' => 'Başkanlık',
        ];
    }

    /**
     * Durum etiketi
     */
    public function getDurumEtiketiAttribute(): string
    {
        return self::getDurumlar()[$this->status] ?? $this->status;
    }

    /**
     * Oda tipi etiketi
     */
    public function getOdaTipiEtiketiAttribute(): string
    {
        return self::getOdaTipleri()[$this->oda_tipi] ?? $this->oda_tipi;
    }

    /**
     * Müsait mi?
     */
    public function isMusait(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Opsiyon mu?
     */
    public function isOpsiyon(): bool
    {
        return $this->status === 'option';
    }

    /**
     * Rezervasyon yapıldı mı?
     */
    public function isRezervasyonYapildi(): bool
    {
        return $this->status === 'booked';
    }

    /**
     * Bloke mi?
     */
    public function isBloke(): bool
    {
        return $this->status === 'block';
    }

    /**
     * Fiyat formatlanmış
     */
    public function getFiyatFormatliAttribute(): string
    {
        if (! $this->fiyat) {
            return 'Fiyat belirtilmemiş';
        }

        return number_format($this->fiyat, 2, ',', '.').' TL';
    }

    /**
     * Tarih formatlanmış
     */
    public function getTarihFormatliAttribute(): string
    {
        if (! $this->tarih) {
            return 'Tarih belirtilmemiş';
        }

        return $this->tarih->format('d.m.Y');
    }

    /**
     * Scope: Belirli statusa göre filtrele
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Müsait olanları filtrele
     */
    public function scopeMusait($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope: Belirli oda tipine göre filtrele
     */
    public function scopeOdaTipi($query, $tip)
    {
        return $query->where('oda_tipi', $tip);
    }

    /**
     * Scope: Belirli tarih aralığına göre filtrele
     */
    public function scopeTarihAraligi($query, $baslangic, $bitis)
    {
        return $query->whereBetween('tarih', [$baslangic, $bitis]);
    }

    /**
     * Scope: Belirli fiyat aralığına göre filtrele
     */
    public function scopeFiyatAraligi($query, $min, $max)
    {
        return $query->whereBetween('fiyat', [$min, $max]);
    }

    /**
     * Scope: Müsait oda sayısı olanları filtrele
     */
    public function scopeMusaitOdaSayisi($query, $min = 1)
    {
        return $query->where('musait_oda_sayisi', '>=', $min);
    }

    /**
     * Scope: Gelecek tarihleri filtrele
     */
    public function scopeGecmisTarihler($query)
    {
        return $query->where('tarih', '<', now());
    }

    /**
     * Scope: Bugün ve gelecek tarihleri filtrele
     */
    public function scopeBugunVeGelecek($query)
    {
        return $query->where('tarih', '>=', now()->startOfDay());
    }

    /**
     * Scope: Bu hafta içindeki tarihleri filtrele
     */
    public function scopeBuHafta($query)
    {
        return $query->whereBetween('tarih', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    /**
     * Scope: Bu ay içindeki tarihleri filtrele
     */
    public function scopeBuAy($query)
    {
        return $query->whereBetween('tarih', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ]);
    }
}
