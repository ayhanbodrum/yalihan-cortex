<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;

class LocationController extends Controller
{
    /**
     * İlleri getir (Context7 uyumlu)
     */
    public function getProvinces()
    {
        try {
            $iller = Il::orderBy('il_adi')->get(['id', 'il_adi as name']);

            return response()->json([
                'success' => true,
                'data' => $iller
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'İller yüklenirken hata oluştu',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * İlçeleri getir (il ID'ye göre) - Context7 uyumlu
     */
    public function getDistrictsByProvince($ilId)
    {
        try {
            $ilceler = Ilce::where('il_id', $ilId)
                ->orderBy('ilce_adi')
                ->get(['id', 'ilce_adi as name']);

            return response()->json([
                'success' => true,
                'data' => $ilceler
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'İlçeler yüklenirken hata oluştu',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mahalleleri getir (ilçe ID'ye göre) - Context7 uyumlu
     */
    public function getNeighborhoodsByDistrict($ilceId)
    {
        try {
            $mahalleler = Mahalle::where('ilce_id', $ilceId)
                ->orderBy('mahalle_adi')
                ->get(['id', 'mahalle_adi as name']);

            return response()->json([
                'success' => true,
                'data' => $mahalleler
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Mahalleler yüklenirken hata oluştu',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tüm lokasyon verilerini getir (Context7 uyumlu)
     */
    public function getAllLocations()
    {
        try {
            $iller = Il::with(['ilceler.mahalleler'])
                ->orderBy('il_adi')
                ->get(['id', 'il_adi as name']);

            return response()->json([
                'success' => true,
                'data' => $iller
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Lokasyon verileri yüklenirken hata oluştu',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lokasyon arama (Context7 uyumlu)
     */
    public function searchLocations(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $type = $request->get('type', 'all'); // all, province, district, neighborhood

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $results = [];

            if ($type === 'all' || $type === 'province') {
                $iller = Il::where('il_adi', 'like', "%{$query}%")
                    ->limit(10)
                    ->get(['id', 'il_adi'])
                    ->map(function ($il) {
                        return [
                            'id' => $il->id,
                            'name' => $il->il_adi,
                            'type' => 'province',
                            'display' => $il->il_adi . ' (İl)'
                        ];
                    });
                $results = array_merge($results, $iller->toArray());
            }

            if ($type === 'all' || $type === 'district') {
                $ilceler = Ilce::where('ilce_adi', 'like', "%{$query}%")
                    ->with('il:id,il_adi')
                    ->limit(10)
                    ->get(['id', 'ilce_adi', 'il_id'])
                    ->map(function ($ilce) {
                        return [
                            'id' => $ilce->id,
                            'name' => $ilce->ilce_adi,
                            'type' => 'district',
                            'display' => $ilce->ilce_adi . ' (İlçe) - ' . $ilce->il->il_adi
                        ];
                    });
                $results = array_merge($results, $ilceler->toArray());
            }

            if ($type === 'all' || $type === 'neighborhood') {
                $mahalleler = Mahalle::where('mahalle_adi', 'like', "%{$query}%")
                    ->with(['ilce:id,ilce_adi,il_id', 'ilce.il:id,il_adi'])
                    ->limit(10)
                    ->get(['id', 'mahalle_adi', 'ilce_id'])
                    ->map(function ($mahalle) {
                        return [
                            'id' => $mahalle->id,
                            'name' => $mahalle->mahalle_adi,
                            'type' => 'neighborhood',
                            'display' => $mahalle->mahalle_adi . ' (Mahalle) - ' . $mahalle->ilce->ilce_adi . ', ' . $mahalle->ilce->il->il_adi
                        ];
                    });
                $results = array_merge($results, $mahalleler->toArray());
            }

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Lokasyon arama sırasında hata oluştu',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📍 Gelişmiş Geocoding - Adres → Koordinat
     * Context7 Kural #75: Google Maps entegrasyonu
     */
    public function geocode(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:500',
            'il_id' => 'nullable|integer|exists:iller,id',
            'ilce_id' => 'nullable|integer|exists:ilceler,id'
        ]);

        try {
            $address = $request->input('address');
            $cacheKey = 'geocode_' . md5($address);

            $result = Cache::remember($cacheKey, 86400, function () use ($address) {
                $googleMapsKey = config('services.google_maps.api_key');

                if (!$googleMapsKey) {
                    throw new \Exception('Google Maps API key not configured');
                }

                $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'address' => $address . ', Turkey',
                    'key' => $googleMapsKey,
                    'language' => 'tr',
                    'region' => 'tr'
                ]);

                if ($response->failed()) {
                    throw new \Exception('Google Maps API request failed');
                }

                $data = $response->json();

                if ($data['status'] !== 'OK' || empty($data['results'])) {
                    throw new \Exception('Address not found');
                }

                $result = $data['results'][0];

                return [
                    'latitude' => $result['geometry']['location']['lat'],
                    'longitude' => $result['geometry']['location']['lng'],
                    'formatted_address' => $result['formatted_address'],
                    'place_id' => $result['place_id'],
                    'address_components' => $result['address_components']
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Adres koordinatlara dönüştürüldü (Context7 uyumlu)',
                'context7_status' => 'geocoding_successful'
            ]);

        } catch (\Exception $e) {
            Log::error('LocationController@geocode error: ' . $e->getMessage(), [
                'address' => $request->input('address'),
                'context7_rule' => '#75_geocoding'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Adres koordinatlara dönüştürülemedi: ' . $e->getMessage(),
                'context7_status' => 'geocoding_failed'
            ], 422);
        }
    }

    /**
     * 📍 Reverse Geocoding - Koordinat → Adres
     * Context7 Kural #75: Koordinat bazlı adres bulma
     */
    public function reverseGeocode(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        try {
            $lat = $request->input('latitude');
            $lng = $request->input('longitude');
            $cacheKey = 'reverse_geocode_' . md5("{$lat},{$lng}");

            $result = Cache::remember($cacheKey, 86400, function () use ($lat, $lng) {
                $googleMapsKey = config('services.google_maps.api_key');

                if (!$googleMapsKey) {
                    throw new \Exception('Google Maps API key not configured');
                }

                $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'latlng' => "{$lat},{$lng}",
                    'key' => $googleMapsKey,
                    'language' => 'tr',
                    'region' => 'tr'
                ]);

                if ($response->failed()) {
                    throw new \Exception('Google Maps API request failed');
                }

                $data = $response->json();

                if ($data['status'] !== 'OK' || empty($data['results'])) {
                    throw new \Exception('Location not found');
                }

                $result = $data['results'][0];

                return [
                    'formatted_address' => $result['formatted_address'],
                    'place_id' => $result['place_id'],
                    'address_components' => $result['address_components'],
                    'location_type' => $result['geometry']['location_type'],
                    'latitude' => $lat,
                    'longitude' => $lng
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Koordinatlar adrese dönüştürüldü (Context7 uyumlu)',
                'context7_status' => 'reverse_geocoding_successful'
            ]);

        } catch (\Exception $e) {
            Log::error('LocationController@reverseGeocode error: ' . $e->getMessage(), [
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'context7_rule' => '#75_reverse_geocoding'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Koordinatlar adrese dönüştürülemedi: ' . $e->getMessage(),
                'context7_status' => 'reverse_geocoding_failed'
            ], 422);
        }
    }

    /**
     * 📍 Yakındaki konumları bul - Haversine Formula
     * Context7 Kural #75: Spatial query optimization
     */
    public function findNearby($latitude, $longitude, $radius = 5)
    {
        try {
            // Context7: Haversine formula ile yakındaki mahalleler
            $query = "
                SELECT id, mahalle_adi as name, lat, lng,
                (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance
                FROM mahalleler
                WHERE lat IS NOT NULL AND lng IS NOT NULL
                HAVING distance <= ?
                ORDER BY distance ASC
                LIMIT 20
            ";

            $nearbyPlaces = \DB::select($query, [$latitude, $longitude, $latitude, $radius]);

            return response()->json([
                'success' => true,
                'data' => $nearbyPlaces,
                'count' => count($nearbyPlaces),
                'search_params' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'radius_km' => $radius
                ],
                'message' => "Yakındaki konumlar bulundu (Context7 spatial query)",
                'context7_status' => 'nearby_search_successful'
            ]);

        } catch (\Exception $e) {
            Log::error('LocationController@findNearby error: ' . $e->getMessage(), [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'radius' => $radius,
                'context7_rule' => '#75_spatial_query'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Yakındaki konumlar bulunamadı',
                'context7_status' => 'nearby_search_failed'
            ], 500);
        }
    }

    /**
     * 📍 Adres doğrulama - Hiyerarşik kontrol
     * Context7 Kural #75: Address validation
     */
    public function validateAddress(Request $request)
    {
        $request->validate([
            'il_id' => 'required|integer|exists:iller,id',
            'ilce_id' => 'required|integer|exists:ilceler,id',
            'mahalle_id' => 'nullable|integer|exists:mahalleler,id',
            'full_address' => 'required|string|max:500'
        ]);

        try {
            $validationResults = [];

            // Context7: İl-İlçe ilişkisi doğrulama
            $district = \DB::table('ilceler')
                ->where('id', $request->ilce_id)
                ->where('il_id', $request->il_id)
                ->first();

            if (!$district) {
                return response()->json([
                    'success' => false,
                    'message' => 'İl ve ilçe uyumsuz (Context7 hiyerarşi hatası)',
                    'validation_errors' => ['ilce_id' => ['Seçilen ilçe, seçilen ile ait değil']],
                    'context7_status' => 'hierarchy_validation_failed'
                ], 422);
            }

            $validationResults['il_ilce_relation'] = 'valid';

            // Context7: Mahalle-İlçe ilişkisi doğrulama
            if ($request->mahalle_id) {
                $neighborhood = \DB::table('mahalleler')
                    ->where('id', $request->mahalle_id)
                    ->where('ilce_id', $request->ilce_id)
                    ->first();

                if (!$neighborhood) {
                    return response()->json([
                        'success' => false,
                        'message' => 'İlçe ve mahalle uyumsuz (Context7 hiyerarşi hatası)',
                        'validation_errors' => ['mahalle_id' => ['Seçilen mahalle, seçilen ilçeye ait değil']],
                        'context7_status' => 'hierarchy_validation_failed'
                    ], 422);
                }

                $validationResults['ilce_mahalle_relation'] = 'valid';
            }

            // Context7: Address scoring
            $addressScore = 100;
            if (strlen($request->full_address) < 10) $addressScore -= 20;
            if (!preg_match('/\d+/', $request->full_address)) $addressScore -= 15; // No numbers

            return response()->json([
                'success' => true,
                'data' => [
                    'is_valid' => true,
                    'hierarchy_check' => 'passed',
                    'address_score' => $addressScore,
                    'validation_details' => $validationResults
                ],
                'message' => 'Adres doğrulandı (Context7 uyumlu)',
                'context7_status' => 'address_validation_successful'
            ]);

        } catch (\Exception $e) {
            Log::error('LocationController@validateAddress error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'context7_rule' => '#75_address_validation'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Adres doğrulanamadı (Context7 error)',
                'context7_status' => 'address_validation_error'
            ], 500);
        }
    }
}
