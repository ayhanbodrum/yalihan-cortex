<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ilan;
use App\Models\BookingRequest;
use App\Mail\BookingRequestMail;
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

            // ✅ Database'e kaydet
            $bookingRequest = BookingRequest::create([
                'ilan_id' => $villa->id,
                'guest_name' => $request->name,
                'guest_phone' => $request->phone,
                'guest_email' => $request->email,
                'guest_message' => $request->message,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'guests' => $request->guests,
                'nights' => $request->nights ?? 1,
                'total_price' => $request->total_price ?? 0,
                'villa_title' => $villa->baslik,
                'villa_location' => ($villa->il->il_adi ?? '') . ', ' . ($villa->ilce->ilce_adi ?? ''),
                'status' => 'pending',
            ]);

            // Rezervasyon bilgileri (email için)
            $bookingData = [
                'booking_reference' => $bookingRequest->booking_reference,
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
            ];

            // Log the request
            Log::info('Booking Request Received', $bookingData);

            // ✅ Email gönder (admin ve villa sahibine)
            $this->sendBookingNotification($villa, $bookingData);

            return response()->json([
                'success' => true,
                'message' => 'Rezervasyon talebiniz başarıyla alındı. En kısa sürede sizinle iletişime geçeceğiz.',
                'booking_reference' => $bookingRequest->booking_reference,
                'booking_id' => $bookingRequest->id,
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
     * 
     * Context7 Standardı: C7-BOOKING-MAIL-2025-11-05
     */
    private function sendBookingNotification($villa, $bookingData)
    {
        try {
            // Admin'e email gönder
            $adminEmail = config('app.booking_email', config('mail.from.address'));
            Mail::to($adminEmail)->send(new BookingRequestMail($villa, $bookingData));

            // Villa sahibine email gönder (eğer email varsa)
            if ($villa->ilanSahibi && $villa->ilanSahibi->email) {
                Mail::to($villa->ilanSahibi->email)->send(new BookingRequestMail($villa, $bookingData));
            }

            // Danışmana email gönder (eğer varsa)
            if ($villa->danisman && $villa->danisman->email) {
                Mail::to($villa->danisman->email)->send(new BookingRequestMail($villa, $bookingData));
            }

            Log::info('Booking Notification Sent', [
                'admin_email' => $adminEmail,
                'villa' => $villa->baslik,
                'booking_reference' => $bookingData['booking_reference'] ?? 'N/A',
            ]);
        } catch (\Exception $e) {
            Log::error('Booking Notification Error', [
                'error' => $e->getMessage(),
                'villa_id' => $villa->id,
                'guest_email' => $bookingData['guest_email'] ?? 'N/A',
            ]);
        }
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

