<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * Season Model - Sezonluk fiyatlandırma sistemi
 * TatildeKirala/Airbnb tarzı dynamic pricing
 */
class Season extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ilan_id',
        'name',
        'type',
        'start_date',
        'end_date',
        'daily_price',
        'weekly_price',
        'monthly_price',
        'currency',
        'minimum_stay',
        'maximum_stay',
        'weekend_price',
        'weekend_pricing_enabled',
        'cleaning_fee',
        'service_fee_percent',
        'deposit_percent',
        'is_active',
        'priority',
        'description',
        'special_conditions',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'daily_price' => 'decimal:2',
        'weekly_price' => 'decimal:2',
        'monthly_price' => 'decimal:2',
        'weekend_price' => 'decimal:2',
        'cleaning_fee' => 'decimal:2',
        'service_fee_percent' => 'decimal:2',
        'minimum_stay' => 'integer',
        'maximum_stay' => 'integer',
        'deposit_percent' => 'integer',
        'priority' => 'integer',
        'is_active' => 'boolean',
        'weekend_pricing_enabled' => 'boolean',
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
        return $query->where('is_active', true);
    }

    /**
     * Scope: Sezon tipine göre
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Belirli bir tarihi kapsayan sezonlar
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->active();
    }

    /**
     * Scope: Tarih aralığını kapsayan sezonlar
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($q) use ($startDate, $endDate) {
                  $q->where('start_date', '<=', $startDate)
                    ->where('end_date', '>=', $endDate);
              });
        })->active();
    }

    /**
     * Helper: Belirli bir tarih için fiyat getir
     * @param string $date
     * @param bool $isWeekend
     * @return float|null
     */
    public function getPriceForDate($date, $isWeekend = false)
    {
        // Hafta sonu fiyatı aktif ve hafta sonuysa
        if ($this->weekend_pricing_enabled && $isWeekend && $this->weekend_price) {
            return (float) $this->weekend_price;
        }

        return (float) $this->daily_price;
    }

    /**
     * Helper: Tarih aralığı için toplam fiyat hesapla
     * @param string $checkIn
     * @param string $checkOut
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

        if ($nightCount >= 30 && $this->monthly_price) {
            // Aylık indirim
            $monthCount = floor($nightCount / 30);
            $remainingDays = $nightCount % 30;
            $finalPrice = ($this->monthly_price * $monthCount) + ($this->daily_price * $remainingDays);
        } elseif ($nightCount >= 7 && $this->weekly_price) {
            // Haftalık indirim
            $weekCount = floor($nightCount / 7);
            $remainingDays = $nightCount % 7;
            $finalPrice = ($this->weekly_price * $weekCount) + ($this->daily_price * $remainingDays);
        }

        // Ek ücretler
        $cleaningFee = (float) $this->cleaning_fee;
        $serviceFee = $finalPrice * ((float) $this->service_fee_percent / 100);
        $depositAmount = $finalPrice * ($this->deposit_percent / 100);

        return [
            'night_count' => $nightCount,
            'base_price' => $dailyTotal,
            'final_price' => $finalPrice,
            'cleaning_fee' => $cleaningFee,
            'service_fee' => $serviceFee,
            'total_price' => $finalPrice + $cleaningFee + $serviceFee,
            'deposit_amount' => $depositAmount,
            'currency' => $this->currency,
        ];
    }

    /**
     * Helper: Minimum konaklama kontrolü
     */
    public function meetsMinimumStay($nightCount)
    {
        return $nightCount >= $this->minimum_stay;
    }

    /**
     * Helper: Maksimum konaklama kontrolü
     */
    public function meetsMaximumStay($nightCount)
    {
        if (!$this->maximum_stay) {
            return true;
        }

        return $nightCount <= $this->maximum_stay;
    }

    /**
     * Static: Belirli tarih için en uygun sezonu bul
     */
    public static function findBestForDate($ilanId, $date)
    {
        return static::where('ilan_id', $ilanId)
            ->forDate($date)
            ->orderBy('priority', 'desc')
            ->first();
    }

    /**
     * Static: Tarih aralığı için fiyat hesapla
     */
    public static function calculatePriceForDateRange($ilanId, $checkIn, $checkOut)
    {
        $season = static::where('ilan_id', $ilanId)
            ->forDateRange($checkIn, $checkOut)
            ->orderBy('priority', 'desc')
            ->first();

        if (!$season) {
            return null;
        }

        return $season->calculatePrice($checkIn, $checkOut);
    }
}
