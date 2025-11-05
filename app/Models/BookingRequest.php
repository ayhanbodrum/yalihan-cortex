<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Booking Request Model
 * 
 * Context7 Standardı: C7-BOOKING-REQUEST-2025-11-05
 * 
 * Public API'den gelen rezervasyon taleplerini yönetir
 */
class BookingRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'booking_requests';

    protected $fillable = [
        'ilan_id',
        'booking_reference',
        'guest_name',
        'guest_phone',
        'guest_email',
        'guest_message',
        'check_in',
        'check_out',
        'guests',
        'nights',
        'total_price',
        'villa_title',
        'villa_location',
        'status',
        'admin_notes',
        'confirmed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'guests' => 'integer',
        'nights' => 'integer',
        'total_price' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (!$booking->booking_reference) {
                $booking->booking_reference = 'BK-' . now()->format('Ymd') . '-' . strtoupper(substr(md5($booking->guest_email . time()), 0, 6));
            }
            if (!$booking->status) {
                $booking->status = 'pending';
            }
        });
    }

    /**
     * İlan ilişkisi
     */
    public function ilan(): BelongsTo
    {
        return $this->belongsTo(Ilan::class);
    }

    /**
     * Scope: Beklemede olanlar
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Onaylanmış olanlar
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope: Belirli tarih aralığında
     */
    public function scopeBetweenDates($query, $checkIn, $checkOut)
    {
        return $query->where(function($q) use ($checkIn, $checkOut) {
            $q->whereBetween('check_in', [$checkIn, $checkOut])
              ->orWhereBetween('check_out', [$checkIn, $checkOut])
              ->orWhere(function($q) use ($checkIn, $checkOut) {
                  $q->where('check_in', '<=', $checkIn)
                    ->where('check_out', '>=', $checkOut);
              });
        });
    }

    /**
     * Onayla
     */
    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * İptal et
     */
    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'admin_notes' => $reason ? ($this->admin_notes . "\nİptal nedeni: " . $reason) : $this->admin_notes,
        ]);
    }
}
