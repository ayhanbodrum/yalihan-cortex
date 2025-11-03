<?php

namespace App\Http\Controllers;

use App\Models\Ilan;
use App\Models\IlanKategori;
use App\Models\Event;
use App\Models\Season;
use App\Models\Il;
use App\Models\Ilce;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Public Villa Listing Controller
 * TatildeKirala/Airbnb tarzı villa kiralama
 */
class VillaController extends Controller
{
    /**
     * Villa listing page (TatildeKirala tarzı)
     * Route: /yazliklar
     */
    public function index(Request $request)
    {
        // Yazlık kategorisi
        $yazlikKategori = IlanKategori::where('slug', 'yazlik')->first();

        if (!$yazlikKategori) {
            abort(404, 'Yazlık kiralama kategorisi bulunamadı');
        }

        // Base query
        $query = Ilan::with(['photos', 'featuredPhoto', 'il', 'ilce', 'mahalle', 'seasons'])
            ->where('ana_kategori_id', $yazlikKategori->id)
            ->where('status', 'Aktif');

        // Filters
        if ($request->filled('location')) {
            $location = $request->get('location');
            $query->where(function($q) use ($location) {
                $q->whereHas('il', fn($q) => $q->where('name', 'LIKE', "%{$location}%"))
                  ->orWhereHas('ilce', fn($q) => $q->where('name', 'LIKE', "%{$location}%"))
                  ->orWhereHas('mahalle', fn($q) => $q->where('name', 'LIKE', "%{$location}%"));
            });
        }

        // Guest count filter
        if ($request->filled('guests')) {
            $guests = (int) $request->get('guests');
            $query->where('maksimum_misafir', '>=', $guests);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('gunluk_fiyat', '>=', $request->get('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('gunluk_fiyat', '<=', $request->get('max_price'));
        }

        // Date availability filter
        if ($request->filled('check_in') && $request->filled('check_out')) {
            $checkIn = $request->get('check_in');
            $checkOut = $request->get('check_out');
            
            // Müsait villaları filtrele (çakışma olmayanlar)
            $query->whereDoesntHave('events', function($q) use ($checkIn, $checkOut) {
                $q->active()->betweenDates($checkIn, $checkOut);
            });
        }

        // Amenities filter (havuz, deniz manzarası, etc.)
        if ($request->filled('amenities')) {
            $amenities = $request->get('amenities');
            foreach ($amenities as $amenity) {
                $query->whereHas('features', fn($q) => $q->where('features.slug', $amenity));
            }
        }

        // Sorting
        $sortBy = $request->get('sort', 'popular');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('gunluk_fiyat', 'asc');
                break;
            case 'price_high':
                $query->orderBy('gunluk_fiyat', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'popular':
            default:
                $query->orderBy('view_count', 'desc');
                break;
        }

        // Paginate
        $villas = $query->paginate(24);

        // Locations for filter (popular cities with villas)
        $locations = Il::whereIn('id', function($query) use ($yazlikKategori) {
            $query->select('il_id')
                  ->from('ilanlar')
                  ->where('ana_kategori_id', $yazlikKategori->id)
                  ->where('status', 'Aktif')
                  ->distinct();
        })->orderBy('il_adi')->get();

        // Popular amenities for filter
        $popularAmenities = $this->getPopularAmenities();

        // Stats
        $stats = [
            'total' => $villas->total(),
            'available_today' => $this->getAvailableToday($yazlikKategori->id),
        ];

        return view('villas.index', compact('villas', 'locations', 'popularAmenities', 'stats'));
    }

    /**
     * Villa detail page (Airbnb tarzı)
     * Route: /yazliklar/{id}
     */
    public function show($id)
    {
        $villa = Ilan::with([
            'photos', 
            'featuredPhoto', 
            'il', 
            'ilce', 
            'mahalle',
            'features',
            'seasons' => fn($q) => $q->active(),
            'events' => fn($q) => $q->active()
        ])
        ->where('status', 'Aktif') // Context7 compliant!
        ->findOrFail($id);

        // View count artır
        $villa->increment('view_count');

        // Müsaitlik takvimi (next 3 months)
        $availabilityCalendar = $this->getAvailabilityCalendar($villa, 3);

        // Fiyat bilgisi
        $pricing = $this->getPricingInfo($villa);

        // Benzer villalar
        $similarVillas = $this->getSimilarVillas($villa, 4);

        return view('villas.show', compact(
            'villa',
            'availabilityCalendar',
            'pricing',
            'similarVillas'
        ));
    }

    /**
     * Rezervasyon formu (AJAX)
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'ilan_id' => 'required|exists:ilanlar,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
        ]);

        $villa = Ilan::findOrFail($request->ilan_id);
        $checkIn = $request->check_in;
        $checkOut = $request->check_out;

        // Çakışma kontrolü
        $hasConflict = Event::hasConflict($villa->id, $checkIn, $checkOut);

        if ($hasConflict) {
            return response()->json([
                'available' => false,
                'message' => 'Seçtiğiniz tarihler dolu. Lütfen başka tarih seçin.',
            ]);
        }

        // Fiyat hesapla
        $pricing = Season::calculatePriceForDateRange($villa->id, $checkIn, $checkOut);

        if (!$pricing) {
            // Fallback: varsayılan fiyat
            $nightCount = Carbon::parse($checkOut)->diffInDays(Carbon::parse($checkIn));
            $pricing = [
                'night_count' => $nightCount,
                'daily_price' => $villa->gunluk_fiyat ?? 0,
                'total_price' => ($villa->gunluk_fiyat ?? 0) * $nightCount,
                'currency' => 'TRY',
            ];
        }

        return response()->json([
            'available' => true,
            'message' => 'Tarihler müsait!',
            'pricing' => $pricing,
        ]);
    }

    /**
     * Helper: Müsaitlik takvimi
     */
    private function getAvailabilityCalendar($villa, $months = 3)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->addMonths($months)->endOfMonth();

        // Rezervasyonları al
        $events = Event::where('ilan_id', $villa->id)
            ->active()
            ->betweenDates($startDate, $endDate)
            ->get(['check_in', 'check_out', 'status']);

        // Takvim formatına çevir
        $calendar = [];
        foreach ($events as $event) {
            $current = Carbon::parse($event->check_in);
            $end = Carbon::parse($event->check_out);

            while ($current->lte($end)) {
                $calendar[$current->format('Y-m-d')] = [
                    'available' => false,
                    'status' => $event->status,
                ];
                $current->addDay();
            }
        }

        return $calendar;
    }

    /**
     * Helper: Fiyat bilgisi
     */
    private function getPricingInfo($villa)
    {
        $seasons = $villa->seasons()->active()->get();

        return [
            'daily_min' => $seasons->min('daily_price') ?? $villa->gunluk_fiyat,
            'daily_max' => $seasons->max('daily_price') ?? $villa->gunluk_fiyat,
            'weekly' => $seasons->first()->weekly_price ?? null,
            'monthly' => $seasons->first()->monthly_price ?? null,
            'currency' => $villa->para_birimi ?? 'TRY',
            'seasons' => $seasons,
        ];
    }

    /**
     * Helper: Benzer villalar
     */
    private function getSimilarVillas($villa, $limit = 4)
    {
        return Ilan::with(['featuredPhoto', 'il', 'ilce'])
            ->where('id', '!=', $villa->id)
            ->where('ana_kategori_id', $villa->ana_kategori_id)
            ->where('il_id', $villa->il_id)
            ->where('status', 'Aktif') // Context7 compliant!
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Helper: Popüler amenities
     */
    private function getPopularAmenities()
    {
        return [
            ['slug' => 'havuz', 'name' => 'Özel Havuz', 'icon' => '🏊'],
            ['slug' => 'deniz-manzarasi', 'name' => 'Deniz Manzarası', 'icon' => '🌅'],
            ['slug' => 'jakuzi', 'name' => 'Jakuzi', 'icon' => '🛁'],
            ['slug' => 'wifi', 'name' => 'WiFi', 'icon' => '📶'],
            ['slug' => 'klima', 'name' => 'Klima', 'icon' => '❄️'],
            ['slug' => 'otopark', 'name' => 'Otopark', 'icon' => '🚗'],
            ['slug' => 'sauna', 'name' => 'Sauna', 'icon' => '🧖'],
            ['slug' => 'cocuk-oyun-alani', 'name' => 'Çocuk Dostu', 'icon' => '👶'],
        ];
    }

    /**
     * Helper: Bugün müsait villa sayısı
     */
    private function getAvailableToday($kategoriId)
    {
        $today = Carbon::today();

        return Ilan::where('ana_kategori_id', $kategoriId)
            ->where('status', 'Aktif') // Context7 compliant!
            ->whereDoesntHave('events', function($q) use ($today) {
                $q->active()
                  ->where('check_in', '<=', $today)
                  ->where('check_out', '>', $today);
            })
            ->count();
    }
}
