<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ilan;
use App\Models\IlanFotografi;
use App\Models\IlanKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class YazlikKiralamaController extends AdminController
{
    /**
     * Display summer rental listings dashboard
     */
    public function index(Request $request)
    {
        // Yazlık Kiralama kategorisini bul
        $yazlikKategori = IlanKategori::where('slug', 'yazlik-kiralama')->first();

        if (!$yazlikKategori) {
            return redirect()->back()->with('error', 'Yazlık Kiralama kategorisi bulunamadı.');
        }

        $query = Ilan::with(['kategori', 'fotograflar'])
            ->where('kategori_id', $yazlikKategori->id);

        // Search filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('baslik', 'LIKE', "%{$search}%")
                  ->orWhere('aciklama', 'LIKE', "%{$search}%")
                  ->orWhere('adres', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('il_id')) {
            $query->where('il_id', $request->get('il_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('price_range')) {
            [$min, $max] = explode('-', $request->get('price_range'));
            $query->whereBetween('fiyat', [(int)$min, (int)$max]);
        }

        // Date range for seasonal rentals
        if ($request->filled('season')) {
            $season = $request->get('season');
            $currentYear = date('Y');

            switch ($season) {
                case 'summer':
                    $query->whereJsonContains('seasonal_availability', ['summer' => true]);
                    break;
                case 'winter':
                    $query->whereJsonContains('seasonal_availability', ['winter' => true]);
                    break;
                case 'year_round':
                    $query->whereJsonContains('seasonal_availability', ['year_round' => true]);
                    break;
            }
        }

        $ilanlar = $query->orderBy('created_at', 'desc')
                        ->paginate(20);

        // Statistics
        try {
            $activeReservations = DB::table('yazlik_rezervasyonlar')
                ->join('ilanlar', 'yazlik_rezervasyonlar.ilan_id', '=', 'ilanlar.id')
                ->where('ilanlar.kategori_id', $yazlikKategori->id)
                ->whereIn('yazlik_rezervasyonlar.status', ['beklemede', 'onaylandi'])
                ->count();
        } catch (\Exception $e) {
            $activeReservations = 0;
        }

        $stats = [
            'total_yazlik' => Ilan::where('kategori_id', $yazlikKategori->id)->count(),
            'active_reservations' => $activeReservations,
            'monthly_revenue' => $this->calculateMonthlyRevenue(),
            'occupancy_rate' => 75.5 // Mock data - implement real calculation
        ];

        $yazliklar = $ilanlar;

        return view('admin.yazlik-kiralama.index', compact('yazliklar', 'stats'));
    }

    /**
     * Show the form for creating a new summer rental listing
     */
    public function create()
    {
        $kategoriler = IlanKategori::where('status', 1)
            ->where('slug', 'yazlik-kiralama')
            ->orWhere('parent_id', function($q) {
                $q->select('id')
                  ->from('ilan_kategorileri')
                  ->where('slug', 'yazlik-kiralama')
                  ->limit(1);
            })
            ->orderBy('name')
            ->get();

        $amenities = $this->getAmenityOptions();
        $rentalTypes = $this->getRentalTypeOptions();

        return view('admin.yazlik-kiralama.create', compact('kategoriler', 'amenities', 'rentalTypes'));
    }

    /**
     * Store a newly created summer rental listing
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'baslik' => 'required|string|max:255',
            'aciklama' => 'required|string',
            'kategori_id' => 'required|exists:ilan_kategorileri,id',
            'il_id' => 'required|exists:iller,id',
            'ilce_id' => 'required|exists:ilceler,id',
            'mahalle_id' => 'nullable|exists:mahalleler,id',
            'adres' => 'required|string|max:500',
            'fiyat' => 'required|numeric|min:0',
            'doviz' => 'required|in:TRY,USD,EUR',
            'metrekare' => 'required|numeric|min:1',
            'oda_sayisi' => 'required|integer|min:1',
            'salon_sayisi' => 'required|integer|min:1',
            'banyo_sayisi' => 'required|integer|min:1',
            'balkon_sayisi' => 'nullable|integer|min:0',
            'kat' => 'nullable|integer',
            'bina_kati' => 'nullable|integer',
            'yatak_odasi_sayisi' => 'required|integer|min:1',
            'max_guests' => 'required|integer|min:1|max:20',
            'min_stay_days' => 'required|integer|min:1',
            'max_stay_days' => 'nullable|integer|min:1',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i',
            'seasonal_availability' => 'required|array',
            'amenities' => 'nullable|array',
            'rental_type' => 'required|in:daily,weekly,monthly,seasonal',
            'booking_type' => 'required|in:instant,request',
            'cancellation_policy' => 'required|in:flexible,moderate,strict',
            'security_deposit' => 'nullable|numeric|min:0',
            'cleaning_fee' => 'nullable|numeric|min:0',
            'extra_guest_fee' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,pending',
            'fotograflar.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $ilan = Ilan::create([
                'baslik' => $request->baslik,
                'aciklama' => $request->aciklama,
                'kategori_id' => $request->kategori_id,
                'il_id' => $request->il_id,
                'ilce_id' => $request->ilce_id,
                'mahalle_id' => $request->mahalle_id,
                'adres' => $request->adres,
                'fiyat' => $request->fiyat,
                'doviz' => $request->doviz,
                'metrekare' => $request->metrekare,
                'oda_sayisi' => $request->oda_sayisi,
                'salon_sayisi' => $request->salon_sayisi,
                'banyo_sayisi' => $request->banyo_sayisi,
                'balkon_sayisi' => $request->balkon_sayisi,
                'kat' => $request->kat,
                'bina_kati' => $request->bina_kati,
                'yatak_odasi_sayisi' => $request->yatak_odasi_sayisi,

                // Yazlık-specific fields
                'max_guests' => $request->max_guests,
                'min_stay_days' => $request->min_stay_days,
                'max_stay_days' => $request->max_stay_days,
                'check_in_time' => $request->check_in_time,
                'check_out_time' => $request->check_out_time,
                'seasonal_availability' => json_encode($request->seasonal_availability),
                'amenities' => json_encode($request->amenities ?? []),
                'rental_type' => $request->rental_type,
                'booking_type' => $request->booking_type,
                'cancellation_policy' => $request->cancellation_policy,
                'security_deposit' => $request->security_deposit,
                'cleaning_fee' => $request->cleaning_fee,
                'extra_guest_fee' => $request->extra_guest_fee,

                'status' => $request->status,
                'created_by' => auth()->id(),
                'is_published' => $request->status === 'active'
            ]);

            // Handle photo uploads
            if ($request->hasFile('fotograflar')) {
                foreach ($request->file('fotograflar') as $index => $file) {
                    $filename = time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('ilanlar/' . $ilan->id, $filename, 'public');

                    IlanFotografi::create([
                        'ilan_id' => $ilan->id,
                        'dosya_adi' => $filename,
                        'dosya_yolu' => $path,
                        'sira' => $index + 1,
                        'is_main' => $index === 0
                    ]);
                }
            }

            DB::commit();

            Log::info('Summer rental listing created', [
                'ilan_id' => $ilan->id,
                'title' => $ilan->baslik,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Yazlık kiralama ilanı başarıyla oluşturuldu.',
                'data' => ['ilan_id' => $ilan->id]
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Summer rental listing creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'İlan oluşturma işlemi başarısız: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified summer rental listing
     */
    public function show($id)
    {
        $ilan = Ilan::with(['kategori', 'fotograflar', 'il', 'ilce', 'mahalle'])
            ->findOrFail($id);

        $bookingStats = $this->getBookingStats($id);
        $revenueStats = $this->getRevenueStats($id);
        $availabilityCalendar = $this->getAvailabilityCalendar($id);

        return view('admin.yazlik-kiralama.show', compact(
            'ilan',
            'bookingStats',
            'revenueStats',
            'availabilityCalendar'
        ));
    }

    /**
     * Show the form for editing the specified summer rental listing
     */
    public function edit($id)
    {
        $ilan = Ilan::with(['kategori', 'fotograflar'])->findOrFail($id);

        $kategoriler = IlanKategori::where('status', 1)
            ->where('slug', 'yazlik-kiralama')
            ->orWhere('parent_id', function($q) {
                $q->select('id')
                  ->from('ilan_kategorileri')
                  ->where('slug', 'yazlik-kiralama')
                  ->limit(1);
            })
            ->orderBy('name')
            ->get();

        $amenities = $this->getAmenityOptions();
        $rentalTypes = $this->getRentalTypeOptions();

        return view('admin.yazlik-kiralama.edit', compact(
            'ilan',
            'kategoriler',
            'amenities',
            'rentalTypes'
        ));
    }

    /**
     * Update the specified summer rental listing
     */
    public function update(Request $request, $id)
    {
        $ilan = Ilan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'baslik' => 'required|string|max:255',
            'aciklama' => 'required|string',
            'kategori_id' => 'required|exists:ilan_kategorileri,id',
            'il_id' => 'required|exists:iller,id',
            'ilce_id' => 'required|exists:ilceler,id',
            'mahalle_id' => 'nullable|exists:mahalleler,id',
            'adres' => 'required|string|max:500',
            'fiyat' => 'required|numeric|min:0',
            'doviz' => 'required|in:TRY,USD,EUR',
            'metrekare' => 'required|numeric|min:1',
            'max_guests' => 'required|integer|min:1|max:20',
            'min_stay_days' => 'required|integer|min:1',
            'rental_type' => 'required|in:daily,weekly,monthly,seasonal',
            'status' => 'required|in:active,inactive,pending'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $ilan->update($request->only([
                'baslik', 'aciklama', 'kategori_id', 'il_id', 'ilce_id', 'mahalle_id',
                'adres', 'fiyat', 'doviz', 'metrekare', 'max_guests', 'min_stay_days',
                'rental_type', 'status', 'seasonal_availability', 'amenities',
                'booking_type', 'cancellation_policy', 'security_deposit',
                'cleaning_fee', 'extra_guest_fee'
            ]));

            $ilan->is_published = $request->status === 'active';
            $ilan->updated_by = auth()->id();
            $ilan->save();

            // Handle new photo uploads
            if ($request->hasFile('new_fotograflar')) {
                $maxSira = $ilan->fotograflar()->max('sira') ?? 0;

                foreach ($request->file('new_fotograflar') as $index => $file) {
                    $filename = time() . '_' . ($maxSira + $index + 1) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('ilanlar/' . $ilan->id, $filename, 'public');

                    IlanFotografi::create([
                        'ilan_id' => $ilan->id,
                        'dosya_adi' => $filename,
                        'dosya_yolu' => $path,
                        'sira' => $maxSira + $index + 1
                    ]);
                }
            }

            DB::commit();

            Log::info('Summer rental listing updated', [
                'ilan_id' => $ilan->id,
                'title' => $ilan->baslik,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Yazlık kiralama ilanı başarıyla güncellendi.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Summer rental listing update failed', [
                'ilan_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'İlan güncelleme işlemi başarısız: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified summer rental listing
     */
    public function destroy($id)
    {
        try {
            $ilan = Ilan::findOrFail($id);

            DB::beginTransaction();

            // Delete photos from storage
            foreach ($ilan->fotograflar as $foto) {
                if (Storage::disk('public')->exists($foto->dosya_yolu)) {
                    Storage::disk('public')->delete($foto->dosya_yolu);
                }
            }

            // Delete photos records
            $ilan->fotograflar()->delete();

            // Delete the listing
            $ilan->delete();

            DB::commit();

            Log::info('Summer rental listing deleted', [
                'ilan_id' => $id,
                'title' => $ilan->baslik,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Yazlık kiralama ilanı başarıyla silindi.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Summer rental listing deletion failed', [
                'ilan_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'İlan silme işlemi başarısız: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle booking management
     */
    public function bookings(Request $request, $id = null)
    {
        $query = DB::table('yazlik_rezervasyonlar')
            ->join('ilanlar', 'yazlik_rezervasyonlar.ilan_id', '=', 'ilanlar.id')
            ->select([
                'yazlik_rezervasyonlar.*',
                'ilanlar.baslik as ilan_baslik'
            ]);

        if ($id) {
            $query->where('yazlik_rezervasyonlar.ilan_id', $id);
        }

        if ($request->filled('status')) {
            $query->where('yazlik_rezervasyonlar.status', $request->get('status'));
        }

        if ($request->filled('date_range')) {
            $dateRange = explode(' - ', $request->get('date_range'));
            if (count($dateRange) === 2) {
                $query->whereBetween('yazlik_rezervasyonlar.check_in', [
                    Carbon::parse($dateRange[0])->format('Y-m-d'),
                    Carbon::parse($dateRange[1])->format('Y-m-d')
                ]);
            }
        }

        $bookings = $query->orderBy('yazlik_rezervasyonlar.created_at', 'desc')
                         ->paginate(20);

        return view('admin.yazlik-kiralama.bookings', compact('bookings', 'id'));
    }

    /**
     * Update booking status
     */
    public function updateBookingStatus(Request $request, $bookingId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::table('yazlik_rezervasyonlar')
                ->where('id', $bookingId)
                ->update([
                    'status' => $request->status,
                    'admin_notes' => $request->notes,
                    'updated_at' => now(),
                    'updated_by' => auth()->id()
                ]);

            Log::info('Booking status updated', [
                'booking_id' => $bookingId,
                'status' => $request->status,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rezervasyon durumu güncellendi.'
            ]);

        } catch (\Exception $e) {
            Log::error('Booking status update failed', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Durum güncelleme başarısız: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper methods
     */
    private function getAmenityOptions()
    {
        return [
            'wifi' => 'Wi-Fi',
            'parking' => 'Otopark',
            'pool' => 'Havuz',
            'garden' => 'Bahçe',
            'sea_view' => 'Deniz Manzarası',
            'mountain_view' => 'Dağ Manzarası',
            'air_conditioning' => 'Klima',
            'heating' => 'Isıtma',
            'kitchen' => 'Mutfak',
            'dishwasher' => 'Bulaşık Makinesi',
            'washing_machine' => 'Çamaşır Makinesi',
            'tv' => 'TV',
            'balcony' => 'Balkon',
            'terrace' => 'Teras',
            'bbq' => 'Barbekü',
            'security' => 'Güvenlik'
        ];
    }

    private function getRentalTypeOptions()
    {
        return [
            'daily' => 'Günlük',
            'weekly' => 'Haftalık',
            'monthly' => 'Aylık',
            'seasonal' => 'Sezonluk'
        ];
    }

    private function calculateMonthlyRevenue()
    {
        try {
            // Try yazlik_rezervasyonlar first (new table)
            return DB::table('yazlik_rezervasyonlar')
                ->where('status', 'onaylandi')
                ->whereMonth('check_in', date('m'))
                ->whereYear('check_in', date('Y'))
                ->sum('toplam_fiyat') ?? 0;
        } catch (\Exception $e) {
            // Fallback to mock data if table doesn't exist
            return 0;
        }
    }

    private function getBookingStats($ilanId)
    {
        try {
            return [
                'total_bookings' => DB::table('yazlik_rezervasyonlar')->where('ilan_id', $ilanId)->count(),
                'confirmed_bookings' => DB::table('yazlik_rezervasyonlar')->where('ilan_id', $ilanId)->where('status', 'onaylandi')->count(),
                'pending_bookings' => DB::table('yazlik_rezervasyonlar')->where('ilan_id', $ilanId)->where('status', 'beklemede')->count(),
                'occupancy_rate' => 75.5, // Mock data
                'avg_stay_duration' => 5.2 // Mock data
            ];
        } catch (\Exception $e) {
            return [
                'total_bookings' => 0,
                'confirmed_bookings' => 0,
                'pending_bookings' => 0,
                'occupancy_rate' => 0,
                'avg_stay_duration' => 0
            ];
        }
    }

    private function getRevenueStats($ilanId)
    {
        try {
            return [
                'monthly_revenue' => DB::table('yazlik_rezervasyonlar')->where('ilan_id', $ilanId)->where('status', 'onaylandi')->whereMonth('created_at', date('m'))->sum('toplam_fiyat') ?? 0,
                'total_revenue' => DB::table('yazlik_rezervasyonlar')->where('ilan_id', $ilanId)->where('status', 'onaylandi')->sum('toplam_fiyat') ?? 0,
                'avg_nightly_rate' => 850, // Mock data
                'revenue_growth' => 12.5 // Mock data percentage
            ];
        } catch (\Exception $e) {
            return [
                'monthly_revenue' => 0,
                'total_revenue' => 0,
                'avg_nightly_rate' => 0,
                'revenue_growth' => 0
            ];
        }
    }

    private function getAvailabilityCalendar($ilanId)
    {
        // Mock calendar data - implement based on booking system
        $calendar = [];
        $startDate = Carbon::now()->startOfMonth();

        for ($i = 0; $i < 90; $i++) {
            $date = $startDate->copy()->addDays($i);
            $calendar[] = [
                'date' => $date->format('Y-m-d'),
                'status' => rand(0, 10) > 7 ? 'booked' : 'available',
                'price' => rand(500, 1500)
            ];
        }

        return $calendar;
    }
}
