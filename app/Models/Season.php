<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Season Model - Sezonluk fiyatlandırma sistemi
 * TatildeKirala/Airbnb tarzı dynamic pricing
 */
class Season extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'yazlik_fiyatlandirma';

    protected $fillable = [
        'ilan_id',
        'sezon_tipi', // ✅ Context7: Tablodaki gerçek kolon adı
        'baslangic_tarihi', // ✅ Context7: Tablodaki gerçek kolon adı
        'bitis_tarihi', // ✅ Context7: Tablodaki gerçek kolon adı
        'gunluk_fiyat', // ✅ Context7: Tablodaki gerçek kolon adı
        'haftalik_fiyat', // ✅ Context7: Tablodaki gerçek kolon adı
        'aylik_fiyat', // ✅ Context7: Tablodaki gerçek kolon adı
        'minimum_konaklama', // ✅ Context7: Tablodaki gerçek kolon adı
        'maksimum_konaklama', // ✅ Context7: Tablodaki gerçek kolon adı
        'ozel_gunler', // ✅ Context7: Tablodaki gerçek kolon adı
        'status', // ✅ Context7: Tablodaki gerçek kolon adı
    ];

    protected $casts = [
        'baslangic_tarihi' => 'date',
        'bitis_tarihi' => 'date',
        'gunluk_fiyat' => 'decimal:2',
        'haftalik_fiyat' => 'decimal:2',
        'aylik_fiyat' => 'decimal:2',
        'minimum_konaklama' => 'integer',
        'maksimum_konaklama' => 'integer',
        'ozel_gunler' => 'array',
        'status' => 'boolean',
    ];

    /**
     * Relationship: İlan
     */
    public function ilan()
    {
        return $this->belongsTo(Ilan::class);
    }

    /**
     * Scope: Aktif sezonlar
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope: Sezon tipine göre
     */
    public function scopeByType($query, $type)
    {
        return $query->where('sezon_tipi', $type);
    }

    /**
     * Scope: Belirli bir tarihi kapsayan sezonlar
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('baslangic_tarihi', '<=', $date)
            ->where('bitis_tarihi', '>=', $date)
            ->active();
    }

    /**
     * Scope: Tarih aralığını kapsayan sezonlar
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('baslangic_tarihi', [$startDate, $endDate])
                ->orWhereBetween('bitis_tarihi', [$startDate, $endDate])
                ->orWhere(function ($q) use ($startDate, $endDate) {
                    $q->where('baslangic_tarihi', '<=', $startDate)
                        ->where('bitis_tarihi', '>=', $endDate);
                });
        })->active();
    }

    /**
     * Backward compatibility accessors
     */
    public function getStartDateAttribute()
    {
        return $this->baslangic_tarihi;
    }

    public function getEndDateAttribute()
    {
        return $this->bitis_tarihi;
    }

    public function getTypeAttribute()
    {
        return $this->sezon_tipi;
    }

    public function getDailyPriceAttribute()
    {
        return $this->gunluk_fiyat;
    }

    public function getWeeklyPriceAttribute()
    {
        return $this->haftalik_fiyat;
    }

    public function getMonthlyPriceAttribute()
    {
        return $this->aylik_fiyat;
    }

    public function getMinimumStayAttribute()
    {
        return $this->minimum_konaklama;
    }

    public function getMaximumStayAttribute()
    {
        return $this->maksimum_konaklama;
    }

    public function getIsActiveAttribute()
    {
        return $this->status;
    }

    /**
     * Helper: Belirli bir tarih için fiyat getir
     *
     * @param  string  $date
     * @param  bool  $isWeekend
     * @return float|null
     */
    public function getPriceForDate($date, $isWeekend = false)
    {
        // Özel günler kontrolü (ozel_gunler JSON'dan)
        if ($this->ozel_gunler && is_array($this->ozel_gunler)) {
            $dateKey = Carbon::parse($date)->format('Y-m-d');
            if (isset($this->ozel_gunler[$dateKey])) {
                return (float) $this->ozel_gunler[$dateKey];
            }
        }

        return (float) $this->gunluk_fiyat;
    }

    /**
     * Helper: Tarih aralığı için toplam fiyat hesapla
     *
     * @param  string  $checkIn
     * @param  string  $checkOut
     * @return array
     */
    public function calculatePrice($checkIn, $checkOut)
    {
        $checkInDate = Carbon::parse($checkIn);
        $checkOutDate = Carbon::parse($checkOut);
        $nightCount = $checkOutDate->diffInDays($checkInDate);

        // Günlük toplam hesapla
        $dailyTotal = 0;
        $currentDate = $checkInDate->copy();

        for ($i = 0; $i < $nightCount; $i++) {
            $isWeekend = $currentDate->isWeekend();
            $dailyTotal += $this->getPriceForDate($currentDate, $isWeekend);
            $currentDate->addDay();
        }

        // Haftalık/aylık indirim kontrolü
        $finalPrice = $dailyTotal;

        if ($nightCount >= 30 && $this->aylik_fiyat) {
            // Aylık indirim
            $monthCount = floor($nightCount / 30);
            $remainingDays = $nightCount % 30;
            $finalPrice = ($this->aylik_fiyat * $monthCount) + ($this->gunluk_fiyat * $remainingDays);
        } elseif ($nightCount >= 7 && $this->haftalik_fiyat) {
            // Haftalık indirim
            $weekCount = floor($nightCount / 7);
            $remainingDays = $nightCount % 7;
            $finalPrice = ($this->haftalik_fiyat * $weekCount) + ($this->gunluk_fiyat * $remainingDays);
        }

        return [
            'night_count' => $nightCount,
            'base_price' => $dailyTotal,
            'final_price' => $finalPrice,
            'total_price' => $finalPrice,
            'currency' => 'TRY', // para_birimi ilanlar tablosunda
        ];
    }

    /**
     * Helper: Minimum konaklama kontrolü
     */
    public function meetsMinimumStay($nightCount)
    {
        return $nightCount >= $this->minimum_konaklama;
    }

    /**
     * Helper: Maksimum konaklama kontrolü
     */
    public function meetsMaximumStay($nightCount)
    {
        if (! $this->maksimum_konaklama) {
            return true;
        }

        return $nightCount <= $this->maksimum_konaklama;
    }

    /**
     * Static: Belirli tarih için en uygun sezonu bul
     */
    public static function findBestForDate($ilanId, $date)
    {
        return static::where('ilan_id', $ilanId)
            ->forDate($date)
            ->orderBy('baslangic_tarihi', 'desc')
            ->first();
    }

    /**
     * Static: Tarih aralığı için fiyat hesapla
     */
    public static function calculatePriceForDateRange($ilanId, $checkIn, $checkOut)
    {
        $season = static::where('ilan_id', $ilanId)
            ->forDateRange($checkIn, $checkOut)
            ->orderBy('baslangic_tarihi', 'desc') // ✅ Context7: Tablodaki gerçek kolon adı
            ->first();

        if (! $season) {
            return null;
        }

        return $season->calculatePrice($checkIn, $checkOut);
    }
}
