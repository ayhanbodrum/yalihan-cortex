<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * Event Model - Rezervasyon/Etkinlik sistemi
 * Airbnb/TatildeKirala tarzı booking system
 */
class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ilan_id',
        'check_in',
        'check_out',
        'check_in_time',
        'check_out_time',
        'night_count',
        'guest_name',
        'guest_email',
        'guest_phone',
        'guest_count',
        'child_count',
        'infant_count',
        'pet_count',
        'daily_price',
        'total_price',
        'cleaning_fee',
        'service_fee',
        'deposit_amount',
        'paid_amount',
        'currency',
        'status',
        'payment_status',
        'special_requests',
        'notes',
        'cancellation_reason',
        'confirmed_at',
        'cancelled_at',
        'source',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'night_count' => 'integer',
        'guest_count' => 'integer',
        'child_count' => 'integer',
        'infant_count' => 'integer',
        'pet_count' => 'integer',
        'daily_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'cleaning_fee' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'weekend_pricing_enabled' => 'boolean',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Otomatik gece sayısı hesaplama
        static::creating(function ($event) {
            if ($event->check_in && $event->check_out) {
                $checkIn = Carbon::parse($event->check_in);
                $checkOut = Carbon::parse($event->check_out);
                $event->night_count = $checkOut->diffInDays($checkIn);
            }
        });
    }

    /**
     * Relationship: İlan
     */
    public function ilan()
    {
        return $this->belongsTo(Ilan::class);
    }

    /**
     * Scope: Onaylı rezervasyonlar
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope: Bekleyen rezervasyonlar
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Aktif rezervasyonlar (confirmed + pending)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed']);
    }

    /**
     * Scope: Tarih aralığında rezervasyonlar
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('check_in', [$startDate, $endDate])
              ->orWhereBetween('check_out', [$startDate, $endDate])
              ->orWhere(function ($q) use ($startDate, $endDate) {
                  $q->where('check_in', '<=', $startDate)
                    ->where('check_out', '>=', $endDate);
              });
        });
    }

    /**
     * Accessor: Toplam misafir sayısı
     */
    public function getTotalGuestsAttribute()
    {
        return $this->guest_count + $this->child_count + $this->infant_count;
    }

    /**
     * Accessor: Kalan ödeme
     */
    public function getRemainingPaymentAttribute()
    {
        return $this->total_price - $this->paid_amount;
    }

    /**
     * Accessor: Ödeme tamamlandı mı?
     */
    public function getIsFullyPaidAttribute()
    {
        return $this->paid_amount >= $this->total_price;
    }

    /**
     * Helper: Rezervasyonu onayla
     */
    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
        
        return $this;
    }

    /**
     * Helper: Rezervasyonu iptal et
     */
    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
        
        return $this;
    }

    /**
     * Helper: Çakışma kontrolü
     * @param int $ilanId
     * @param string $checkIn
     * @param string $checkOut
     * @param int|null $excludeEventId
     * @return bool
     */
    public static function hasConflict($ilanId, $checkIn, $checkOut, $excludeEventId = null)
    {
        $query = static::where('ilan_id', $ilanId)
            ->active()
            ->betweenDates($checkIn, $checkOut);

        if ($excludeEventId) {
            $query->where('id', '!=', $excludeEventId);
        }

        return $query->exists();
    }

    /**
     * Helper: Müsait mi?
     */
    public static function isAvailable($ilanId, $checkIn, $checkOut)
    {
        return !static::hasConflict($ilanId, $checkIn, $checkOut);
    }
}
