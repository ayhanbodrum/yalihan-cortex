<?php

namespace App\Http\Controllers\Admin;

use App\Services\WikimapiaService;
use App\Services\NominatimService;
use App\Services\TurkiyeAPIService;
use App\Models\Ilce;
use App\Models\Il;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class WikimapiaSearchController extends AdminController
{
    protected WikimapiaService $wikimapiaService;
    protected NominatimService $nominatimService;
    protected TurkiyeAPIService $turkiyeAPI;

    public function __construct(
        WikimapiaService $wikimapiaService,
        NominatimService $nominatimService,
        TurkiyeAPIService $turkiyeAPI
    ) {
        $this->wikimapiaService = $wikimapiaService;
        $this->nominatimService = $nominatimService;
        $this->turkiyeAPI = $turkiyeAPI;
    }

    /**
     * Site/Apartman sorgulama paneli ana sayfa
     * Context7: TurkiyeAPI lokasyon verileri ile zenginleştirilmiş
     */
    public function index()
    {
        // TurkiyeAPI'den illeri getir (harita için)
        $iller = $this->turkiyeAPI->getProvinces();

        return view('admin.wikimapia-search.index', compact('iller'));
    }

    /**
     * Wikimapia'dan site/apartman araması yap
     */
    public function search(Request $request)
    {
        try {
            $request->validate([
                'query' => 'required|string|min:2',
                'lat' => 'required|numeric',
                'lon' => 'required|numeric',
                'radius' => 'sometimes|numeric|min:0.01|max:1'
            ]);

            $query = $request->input('query');
            $lat = $request->input('lat');
            $lon = $request->input('lon');
            $radius = $request->input('radius', 0.05);

            $results = $this->wikimapiaService->searchResidentialComplexes($query, $lat, $lon, $radius);

            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Arama tamamlandı'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $errorMessages = [];
            foreach ($errors as $field => $messages) {
                $errorMessages[] = implode(', ', $messages);
            }
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $errorMessages)
            ], 422);
        } catch (\Exception $e) {
            Log::error('Wikimapia search error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Arama sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Genel mekan araması
     */
    public function searchPlaces(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric'
        ]);

        try {
            $query = $request->input('query');
            $lat = $request->input('lat');
            $lon = $request->input('lon');

            $results = $this->wikimapiaService->searchPlaces($query, $lat, $lon);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Yakındaki mekanları listele
     *
     * SMART Multi-provider: WikiMapia → OpenStreetMap → Test Data
     * Automatic fallback with quality detection
     */
    public function nearby(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'radius' => 'sometimes|numeric|min:0.01|max:0.5'
        ]);

        try {
            $lat = $request->input('lat');
            $lon = $request->input('lon');
            $radius = $request->input('radius', 0.05);

            // Try WikiMapia first
            $lonMin = $lon - $radius;
            $latMin = $lat - $radius;
            $lonMax = $lon + $radius;
            $latMax = $lat + $radius;

            Log::info('🔍 Nearby search started', ['lat' => $lat, 'lon' => $lon, 'radius' => $radius]);

            $wikimapiaResults = $this->wikimapiaService->getPlacesByArea($lonMin, $latMin, $lonMax, $latMax);

            // Check: WikiMapia returned data?
            $wikimapiaPlaces = $wikimapiaResults['places'] ?? [];
            $wikimapiaFound = $wikimapiaResults['found'] ?? count($wikimapiaPlaces);

            // If WikiMapia returned real data, use it
            if (!empty($wikimapiaPlaces) && $wikimapiaFound > 0) {
                // Quality check: Is it test data?
                $isReal = $this->isRealData($wikimapiaResults);

                if ($isReal) {
                    Log::info('✅ WikiMapia returned REAL data', ['count' => count($wikimapiaPlaces)]);
                    return response()->json([
                        'success' => true,
                        'data' => $wikimapiaResults,
                        'source' => 'wikimapia',
                        'quality' => 'verified'
                    ]);
                }
            }

            // WikiMapia failed (empty, test data, or no results), try OpenStreetMap
            Log::info('⚠️ WikiMapia returned no/empty/test data, trying OpenStreetMap Nominatim');

            $nominatimResults = $this->nominatimService->searchNearby($lat, $lon, $radius);

            if (!empty($nominatimResults['places'])) {
                Log::info('✅ OpenStreetMap Nominatim returned data', ['count' => count($nominatimResults['places'])]);
                return response()->json([
                    'success' => true,
                    'data' => $nominatimResults,
                    'source' => 'openstreetmap',
                    'quality' => 'free_alternative'
                ]);
            }

            // Both failed, return empty results
            Log::warning('⚠️ Both WikiMapia and OpenStreetMap failed, returning empty results');
            return response()->json([
                'success' => true,
                'data' => ['places' => [], 'found' => 0],
                'source' => 'none',
                'quality' => 'no_data',
                'message' => 'Yakınlarda yer bulunamadı'
            ]);
        } catch (\Exception $e) {
            Log::error('Nearby search error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if WikiMapia data is real or test data
     */
    protected function isRealData(array $results): bool
    {
        if (empty($results['places'])) {
            return false;
        }

        $firstPlace = $results['places'][0];
        $description = $firstPlace['description'] ?? '';
        $title = $firstPlace['title'] ?? '';

        // Test data indicators
        $testIndicators = [
            'deneme verisi',
            'deneme site',
            'deneme apartman',
            'test data',
            'Wikimapia API\'den veri gelmediği'
        ];

        foreach ($testIndicators as $indicator) {
            if (
                str_contains(strtolower($description), strtolower($indicator)) ||
                str_contains(strtolower($title), strtolower($indicator))
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Place detaylarını getir
     */
    public function getPlaceDetails($id)
    {
        try {
            $place = $this->wikimapiaService->getPlaceById($id, ['main', 'location', 'photos', 'comments']);

            return response()->json([
                'success' => true,
                'data' => $place
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Site/Apartman'ı veritabanına kaydet
     */
    public function saveSite(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'description' => 'nullable|string',
                'address' => 'nullable|string',
                'tip' => 'nullable|string|in:site,apartman',
                'wikimapia_id' => 'nullable|string',
                'source' => 'nullable|string',
            ], [
                'name.required' => 'Site adı gereklidir',
                'latitude.required' => 'Enlem (latitude) gereklidir',
                'latitude.numeric' => 'Enlem sayısal olmalıdır',
                'latitude.between' => 'Enlem -90 ile 90 arasında olmalıdır',
                'longitude.required' => 'Boylam (longitude) gereklidir',
                'longitude.numeric' => 'Boylam sayısal olmalıdır',
                'longitude.between' => 'Boylam -180 ile 180 arasında olmalıdır',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Site kaydetme validation hatası', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation hatası: ' . implode(', ', array_map(function ($errors) {
                    return implode(', ', $errors);
                }, $e->errors())),
                'errors' => $e->errors()
            ], 422);
        }

        try {
            // Duplicate kontrolü: Aynı koordinatta veya isimde site var mı?
            // Context7: latitude ve longitude kolonları migration ile eklendi
            $existing = \App\Models\SiteApartman::where(function ($q) use ($request) {
                $q->where('name', $request->input('name'));

                // Latitude ve longitude kolonları varsa koordinat kontrolü yap
                if (
                    Schema::hasColumn('site_apartmanlar', 'latitude') &&
                    Schema::hasColumn('site_apartmanlar', 'longitude')
                ) {
                    $q->orWhere(function ($q2) use ($request) {
                        $q2->where('latitude', round($request->input('latitude'), 6))
                            ->where('longitude', round($request->input('longitude'), 6));
                    });
                }
            })->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu site/apartman zaten kayıtlı!',
                    'data' => $existing
                ], 409);
            }

            // Yeni site/apartman oluştur
            $siteData = [
                'name' => $request->input('name'),
                'tip' => $request->input('tip', 'site'),
                'adres' => $request->input('address') ?? $request->input('description'),
                'notlar' => $request->input('description') . "\n\n[WikiMapia ID: " . ($request->input('wikimapia_id') ?? 'N/A') . " | Source: " . ($request->input('source', 'unknown')) . "]",
            ];

            // Context7: Latitude ve longitude kolonları varsa ekle
            if (
                Schema::hasColumn('site_apartmanlar', 'latitude') &&
                Schema::hasColumn('site_apartmanlar', 'longitude')
            ) {
                $siteData['latitude'] = $request->input('latitude');
                $siteData['longitude'] = $request->input('longitude');
            }

            // Sadece varsa ekle (kolonlar migration'da yoksa)
            if (Schema::hasColumn('site_apartmanlar', 'status')) {
                $siteData['status'] = 'Aktif'; // Context7: Database değeri
            }
            if (Schema::hasColumn('site_apartmanlar', 'created_by') && auth()->check()) {
                $siteData['created_by'] = auth()->id();
            }
            if (Schema::hasColumn('site_apartmanlar', 'site_ozellikleri')) {
                $siteData['site_ozellikleri'] = [
                    'wikimapia_id' => $request->input('wikimapia_id'),
                    'source' => $request->input('source', 'unknown'),
                    'imported_from' => 'wikimapia_search'
                ];
            }

            $site = \App\Models\SiteApartman::create($siteData);

            Log::info('Site/Apartman kaydedildi', [
                'site_id' => $site->id,
                'name' => $site->name,
                'source' => $request->input('source')
            ]);

            // Response data hazırla
            $responseData = [
                'id' => $site->id,
                'name' => $site->name,
                'tip' => $site->tip ?? null,
                'adres' => $site->adres ?? null,
            ];

            // Context7: Latitude ve longitude varsa ekle
            if (
                Schema::hasColumn('site_apartmanlar', 'latitude') &&
                Schema::hasColumn('site_apartmanlar', 'longitude')
            ) {
                $responseData['latitude'] = $site->latitude ? (float) $site->latitude : null;
                $responseData['longitude'] = $site->longitude ? (float) $site->longitude : null;
            }

            return response()->json([
                'success' => true,
                'message' => 'Site/Apartman başarıyla kaydedildi!',
                'data' => $responseData
            ]);
        } catch (\Exception $e) {
            Log::error('Site kaydetme hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Kaydetme sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kaydedilen site/apartmanları getir (harita için)
     */
    public function getSavedSites(Request $request)
    {
        try {
            // Tablo yoksa boş liste döndür (ilk kurulumda 500 engelle)
            if (!Schema::hasTable('site_apartmanlar')) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // Gerekli kolonlar mevcut mu?
            $hasLat = Schema::hasColumn('site_apartmanlar', 'latitude');
            $hasLng = Schema::hasColumn('site_apartmanlar', 'longitude');

            if (!($hasLat && $hasLng)) {
                // Latitude/Longitude kolonları yoksa boş döndür (migration bekleniyor)
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // Tüm kayıtları getir (soft delete kontrolü ile)
            $query = \App\Models\SiteApartman::whereNotNull('latitude')
                ->whereNotNull('longitude');

            // Status kolonu varsa aktif olanları getir
            if (Schema::hasColumn('site_apartmanlar', 'status')) {
                $query->where('status', 'Aktif'); // Context7: Database değeri
            }

            // Seçilecek kolonlar listesi (mevcut olanlarla sınırlı)
            $select = ['id', 'name', 'latitude', 'longitude'];
            if (Schema::hasColumn('site_apartmanlar', 'tip')) {
                $select[] = 'tip';
            }
            if (Schema::hasColumn('site_apartmanlar', 'adres')) {
                $select[] = 'adres';
            }

            $sites = $query->get($select);

            return response()->json([
                'success' => true,
                'data' => $sites->map(function ($site) {
                    return [
                        'id' => $site->id,
                        'name' => $site->name,
                        'tip' => $site->tip ?? null,
                        'latitude' => (float) $site->latitude,
                        'longitude' => (float) $site->longitude,
                        'address' => $site->adres ?? null,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * TurkiyeAPI'den lokasyon verilerini getir (harita için)
     * Context7: Harita sistemine TurkiyeAPI entegrasyonu
     */
    public function getLocationData(Request $request)
    {
        try {
            $type = $request->input('type', 'provinces'); // provinces, districts, neighborhoods, all-types
            $provinceId = $request->input('province_id');
            $districtId = $request->input('district_id');

            $data = [];

            switch ($type) {
                case 'provinces':
                    $data = $this->turkiyeAPI->getProvinces();
                    break;

                case 'districts':
                    if (!$provinceId) {
                        return response()->json([
                            'success' => false,
                            'message' => 'province_id gereklidir'
                        ], 422);
                    }
                    $data = $this->turkiyeAPI->getDistricts($provinceId);
                    break;

                case 'neighborhoods':
                    if (!$districtId) {
                        return response()->json([
                            'success' => false,
                            'message' => 'district_id gereklidir'
                        ], 422);
                    }
                    $data = $this->turkiyeAPI->getNeighborhoods($districtId);
                    break;

                case 'all-types':
                    if (!$districtId) {
                        return response()->json([
                            'success' => false,
                            'message' => 'district_id gereklidir'
                        ], 422);
                    }
                    $data = $this->turkiyeAPI->getAllLocations($districtId);
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Geçersiz tip'
                    ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'source' => 'turkiyeapi',
                'type' => $type
            ]);
        } catch (\Exception $e) {
            Log::error('TurkiyeAPI lokasyon verisi getirme hatası', [
                'error' => $e->getMessage(),
                'type' => $request->input('type')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lokasyon verileri alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Koordinatlardan TurkiyeAPI ile lokasyon bilgisi getir
     * Context7: Reverse geocoding için TurkiyeAPI entegrasyonu
     */
    public function getLocationFromCoordinates(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lon' => 'required|numeric|between:-180,180'
        ]);

        try {
            $lat = $request->input('lat');
            $lon = $request->input('lon');

            // TurkiyeAPI'den yakındaki lokasyonları bul
            // Not: TurkiyeAPI direkt koordinat sorgusu yapmıyor, bu yüzden yakındaki mahalleleri bulmak için
            // Haversine formula kullanarak local DB'de arama yapabiliriz

            // Önce yakındaki mahalleleri bul (5km yarıçap)
            $nearbyMahalleler = DB::select("
                SELECT id, mahalle_adi, ilce_id, enlem, boylam,
                (6371 * acos(cos(radians(?)) * cos(radians(enlem)) * cos(radians(boylam) - radians(?)) + sin(radians(?)) * sin(radians(enlem)))) AS distance
                FROM mahalleler
                WHERE enlem IS NOT NULL AND boylam IS NOT NULL
                HAVING distance <= 5
                ORDER BY distance ASC
                LIMIT 5
            ", [$lat, $lon, $lat]);

            if (!empty($nearbyMahalleler)) {
                $mahalle = $nearbyMahalleler[0];
                $ilce = Ilce::with('il')->find($mahalle->ilce_id);

                // TurkiyeAPI'den detaylı bilgi al
                $allLocations = $this->turkiyeAPI->getAllLocations($mahalle->ilce_id);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'mahalle' => [
                            'id' => $mahalle->id,
                            'name' => $mahalle->mahalle_adi,
                            'distance' => round($mahalle->distance * 1000), // metre
                        ],
                        'ilce' => $ilce ? [
                            'id' => $ilce->id,
                            'name' => $ilce->ilce_adi,
                        ] : null,
                        'il' => $ilce && $ilce->il ? [
                            'id' => $ilce->il->id,
                            'name' => $ilce->il->il_adi,
                        ] : null,
                        'all_locations' => $allLocations,
                    ],
                    'source' => 'turkiyeapi+local_db'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Yakında lokasyon bulunamadı'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Koordinat lokasyon getirme hatası', [
                'error' => $e->getMessage(),
                'lat' => $request->input('lat'),
                'lon' => $request->input('lon')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lokasyon bilgisi alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }
}
