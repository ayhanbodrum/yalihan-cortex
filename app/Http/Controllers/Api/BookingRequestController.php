<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Booking Request API Controller
 * Public booking request form handler
 * Context7 compliant!
 */
class BookingRequestController extends Controller
{
    /**
     * Submit booking request
     * POST /api/booking-request
     */
    public function store(Request $request)
    {
        $request->validate([
            'villa_id' => 'required|exists:ilanlar,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1|max:50',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'message' => 'nullable|string|max:1000',
            'nights' => 'nullable|integer',
            'total_price' => 'nullable|numeric',
        ]);

        try {
            // Villa bilgilerini al
            $villa = Ilan::with(['il', 'ilce', 'ilanSahibi', 'danisman'])
                ->findOrFail($request->villa_id);

            // Rezervasyon bilgileri
            $bookingData = [
                'villa_id' => $villa->id,
                'villa_title' => $villa->baslik,
                'villa_location' => ($villa->il->il_adi ?? '') . ', ' . ($villa->ilce->ilce_adi ?? ''),
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'guests' => $request->guests,
                'nights' => $request->nights ?? 1,
                'total_price' => $request->total_price ?? 0,
                'guest_name' => $request->name,
                'guest_phone' => $request->phone,
                'guest_email' => $request->email,
                'guest_message' => $request->message,
                'created_at' => now(),
            ];

            // Log the request
            Log::info('Booking Request Received', $bookingData);

            // Email gönder (owner ve admin'e)
            $this->sendBookingNotification($villa, $bookingData);

            // TODO: Database'e kaydet (booking_requests table)
            // BookingRequest::create($bookingData);

            return response()->json([
                'success' => true,
                'message' => 'Rezervasyon talebiniz başarıyla alındı. En kısa sürede sizinle iletişime geçeceğiz.',
                'booking_reference' => 'BK-' . now()->format('Ymd') . '-' . strtoupper(substr(md5($request->email), 0, 6)),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Booking Request Error', [
                'error' => $e->getMessage(),
                'villa_id' => $request->villa_id,
                'email' => $request->email,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Rezervasyon talebi gönderilirken bir hata oluştu. Lütfen telefon ile iletişime geçin.',
            ], 500);
        }
    }

    /**
     * Send booking notification email
     */
    private function sendBookingNotification($villa, $bookingData)
    {
        // TODO: Email template ile gönder
        // Mail::to(config('app.booking_email'))->send(new BookingRequestMail($bookingData));
        
        // Şimdilik sadece log
        Log::info('Booking Notification', [
            'to' => config('app.booking_email', 'info@yalihanemlak.com'),
            'villa' => $villa->baslik,
            'guest' => $bookingData['guest_name'],
            'dates' => $bookingData['check_in'] . ' - ' . $bookingData['check_out'],
        ]);
    }

    /**
     * Check availability
     * POST /api/check-availability
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'villa_id' => 'required|exists:ilanlar,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $villa = Ilan::findOrFail($request->villa_id);
        
        // Check for conflicts in events table
        $hasConflict = $villa->events()
            ->where(function($q) use ($request) {
                $q->whereBetween('check_in', [$request->check_in, $request->check_out])
                  ->orWhereBetween('check_out', [$request->check_in, $request->check_out])
                  ->orWhere(function($q) use ($request) {
                      $q->where('check_in', '<=', $request->check_in)
                        ->where('check_out', '>=', $request->check_out);
                  });
            })
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($hasConflict) {
            return response()->json([
                'available' => false,
                'message' => 'Seçtiğiniz tarihler müsait değil. Lütfen başka tarih seçin.',
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Tarihler müsait! Rezervasyon talebini gönderebilirsiniz.',
        ]);
    }

    /**
     * Get booking price
     * POST /api/get-booking-price
     */
    public function getPrice(Request $request)
    {
        $request->validate([
            'villa_id' => 'required|exists:ilanlar,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
        ]);

        $villa = Ilan::findOrFail($request->villa_id);
        
        $checkIn = \Carbon\Carbon::parse($request->check_in);
        $checkOut = \Carbon\Carbon::parse($request->check_out);
        $nights = $checkIn->diffInDays($checkOut);

        // Try to get seasonal price
        $season = $villa->seasons()
            ->where('is_active', true)
            ->where('start_date', '<=', $request->check_in)
            ->where('end_date', '>=', $request->check_out)
            ->first();

        $dailyPrice = $season ? $season->daily_price : ($villa->gunluk_fiyat ?? 0);
        $subtotal = $dailyPrice * $nights;
        
        // Calculate fees
        $cleaningFee = 500; // Fixed
        $serviceFee = round($subtotal * 0.05); // 5%
        $totalPrice = $subtotal + $cleaningFee + $serviceFee;

        return response()->json([
            'success' => true,
            'nights' => $nights,
            'daily_price' => $dailyPrice,
            'subtotal' => $subtotal,
            'cleaning_fee' => $cleaningFee,
            'service_fee' => $serviceFee,
            'total_price' => $totalPrice,
            'currency' => 'TRY',
            'season_name' => $season->name ?? null,
        ]);
    }
}

