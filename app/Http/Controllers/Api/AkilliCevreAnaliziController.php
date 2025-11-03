<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AkilliCevreAnaliziService;
use App\Services\AIAkilliOnerilerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AkilliCevreAnaliziController extends Controller
{
    protected $cevreAnaliziService;
    protected $aiOnerilerService;

    public function __construct(
        AkilliCevreAnaliziService $cevreAnaliziService,
        AIAkilliOnerilerService $aiOnerilerService
    ) {
        $this->cevreAnaliziService = $cevreAnaliziService;
        $this->aiOnerilerService = $aiOnerilerService;
    }

    /**
     * Çevre analizi yap
     */
    public function analyzeEnvironment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'property_type' => 'required|string|in:arsa,yazlik,villa_daire,isyeri'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $propertyType = $request->input('property_type');

            $analysis = $this->cevreAnaliziService->analyzeNearbyEnvironment(
                $latitude,
                $longitude,
                $propertyType
            );

            return response()->json([
                'success' => true,
                'data' => $analysis
            ]);
        } catch (\Exception $e) {
            \Log::error('Çevre analizi hatası: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Çevre analizi sırasında hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI akıllı öneriler al
     */
    public function getSmartRecommendations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ilan_data' => 'required|array',
            'context' => 'sometimes|string|in:create,edit,view'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $ilanData = $request->input('ilan_data');
            $context = $request->input('context', 'create');

            $recommendations = $this->aiOnerilerService->getSmartRecommendations($ilanData, $context);

            return response()->json([
                'success' => true,
                'data' => $recommendations
            ]);
        } catch (\Exception $e) {
            \Log::error('AI öneriler hatası: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'AI öneriler alınırken hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mesafe hesaplama
     */
    public function calculateDistance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_lat' => 'required|numeric|between:-90,90',
            'from_lon' => 'required|numeric|between:-180,180',
            'to_lat' => 'required|numeric|between:-90,90',
            'to_lon' => 'required|numeric|between:-180,180',
            'mode' => 'sometimes|string|in:walking,driving,public_transport'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $fromLat = $request->input('from_lat');
            $fromLon = $request->input('from_lon');
            $toLat = $request->input('to_lat');
            $toLon = $request->input('to_lon');
            $mode = $request->input('mode', 'walking');

            $distance = $this->calculateDistanceBetweenPoints(
                $fromLat, $fromLon, $toLat, $toLon, $mode
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'distance' => $distance,
                    'mode' => $mode,
                    'from' => ['lat' => $fromLat, 'lon' => $fromLon],
                    'to' => ['lat' => $toLat, 'lon' => $toLon]
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Mesafe hesaplama hatası: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Mesafe hesaplama sırasında hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POI arama
     */
    public function searchPOI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'query' => 'required|string|max:255',
            'radius' => 'sometimes|integer|min:100|max:5000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $query = $request->input('query');
            $radius = $request->input('radius', 1000);

            $pois = $this->searchNearbyPOI($latitude, $longitude, $query, $radius);

            return response()->json([
                'success' => true,
                'data' => $pois
            ]);
        } catch (\Exception $e) {
            \Log::error('POI arama hatası: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'POI arama sırasında hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * İki nokta arası mesafe hesapla
     */
    private function calculateDistanceBetweenPoints(
        float $fromLat,
        float $fromLon,
        float $toLat,
        float $toLon,
        string $mode
    ): array {
        // Haversine formülü ile mesafe hesaplama
        $earthRadius = 6371000; // Dünya yarıçapı (metre)

        $lat1 = deg2rad($fromLat);
        $lat2 = deg2rad($toLat);
        $deltaLat = deg2rad($toLat - $fromLat);
        $deltaLon = deg2rad($toLon - $fromLon);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1) * cos($lat2) *
             sin($deltaLon / 2) * sin($deltaLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c; // Metre cinsinden

        // Mod'a göre süre tahmini
        $duration = $this->estimateDuration($distance, $mode);

        return [
            'distance' => round($distance, 2),
            'duration' => $duration,
            'mode' => $mode
        ];
    }

    /**
     * Süre tahmini
     */
    private function estimateDuration(float $distance, string $mode): int
    {
        $speeds = [
            'walking' => 1.4, // m/s
            'driving' => 13.9, // m/s (50 km/h)
            'public_transport' => 8.3 // m/s (30 km/h)
        ];

        $speed = $speeds[$mode] ?? 1.4;
        return round($distance / $speed); // Saniye cinsinden
    }

    /**
     * Yakın POI arama
     */
    private function searchNearbyPOI(float $latitude, float $longitude, string $query, int $radius): array
    {
        // Nominatim API ile POI arama
        try {
            $response = \Http::timeout(30)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'json',
                'limit' => 10,
                'viewbox' => $this->createBoundingBox($latitude, $longitude, $radius),
                'bounded' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->formatPOIResults($data, $latitude, $longitude);
            }
        } catch (\Exception $e) {
            \Log::error('POI arama API hatası: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Bounding box oluştur
     */
    private function createBoundingBox(float $latitude, float $longitude, int $radius): string
    {
        $latOffset = $radius / 111000; // 1 derece ≈ 111km
        $lonOffset = $radius / (111000 * cos(deg2rad($latitude)));

        $minLon = $longitude - $lonOffset;
        $minLat = $latitude - $latOffset;
        $maxLon = $longitude + $lonOffset;
        $maxLat = $latitude + $latOffset;

        return "$minLon,$minLat,$maxLon,$maxLat";
    }

    /**
     * POI sonuçlarını formatla
     */
    private function formatPOIResults(array $data, float $latitude, float $longitude): array
    {
        $results = [];

        foreach ($data as $item) {
            $results[] = [
                'name' => $item['display_name'] ?? 'İsimsiz',
                'latitude' => floatval($item['lat']),
                'longitude' => floatval($item['lon']),
                'type' => $item['type'] ?? 'unknown',
                'distance' => $this->calculateDistanceBetweenPoints(
                    $latitude, $longitude,
                    floatval($item['lat']), floatval($item['lon']),
                    'walking'
                )['distance']
            ];
        }

        // Mesafeye göre sırala
        usort($results, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return $results;
    }
}
