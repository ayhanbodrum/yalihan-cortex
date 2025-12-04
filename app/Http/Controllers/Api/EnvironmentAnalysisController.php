<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Services\Response\ResponseService;
use App\Traits\ValidatesApiRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * 🎯 Akıllı Yakın Çevre Analizi Controller
 * AI-powered nearby places analysis for real estate
 */
class EnvironmentAnalysisController extends Controller
{
    use ValidatesApiRequests;

    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * 🤖 AI Destekli Yakın Çevre Analizi
     * GET /api/environment/analyze?lat={lat}&lng={lng}
     */
    public function analyze(Request $request): JsonResponse
    {
        $validated = $this->validateRequestWithResponse($request, [
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:500|max:5000',
        ]);

        if ($validated instanceof JsonResponse) {
            return $validated;
        }

        $lat = $request->get('lat');
        $lng = $request->get('lng');
        $radius = $request->get('radius', 2000); // Default 2km

        try {
            // AI-powered environment analysis
            $analysis = $this->performEnvironmentAnalysis($lat, $lng, $radius);

            // Generate AI insights
            $insights = $this->generateAIInsights($analysis, $lat, $lng);

            return ResponseService::success([
                'location' => [
                    'lat' => $lat,
                    'lng' => $lng,
                    'radius' => $radius,
                ],
                'categories' => $analysis,
                'insights' => $insights,
                'scores' => $this->calculateLocationScores($analysis),
                'recommendations' => $this->generateRecommendations($analysis),
            ], 'Çevre analizi başarıyla tamamlandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('Çevre analizi yapılırken hata oluştu.', $e);
        }
    }

    /**
     * 📍 Points of Interest (POI) API
     * GET /api/environment/pois?lat={lat}&lng={lng}&radius={radius}&types={types}
     *
     * Context7 Standard: Tüm ilan tipleri için ortak POI servisi
     * Returns nearby points of interest (okul, market, hastane, otel, sahil, vb.)
     */
    public function getPOIs(Request $request): JsonResponse
    {
        $validated = $this->validateRequestWithResponse($request, [
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:100|max:5000',
            'types' => 'nullable|string', // Comma-separated: okul,market,hastane,otel,sahil
        ]);

        if ($validated instanceof JsonResponse) {
            return $validated;
        }

        $lat = (float) $request->get('lat');
        $lng = (float) $request->get('lng');
        $radius = (int) ($request->get('radius', 2000)); // Default 2km
        $requestedTypes = $request->get('types') ? explode(',', $request->get('types')) : null;

        try {
            // ✅ Gerçek OSM verilerini Overpass API ile çek
            $pois = $this->getRealPOIs($lat, $lng, $radius, $requestedTypes);

            return ResponseService::success([
                'location' => [
                    'lat' => $lat,
                    'lng' => $lng,
                    'radius' => $radius,
                ],
                'pois' => $pois,
                'total' => count($pois),
            ], 'POI verileri başarıyla alındı');
        } catch (\Exception $e) {
            Log::error('POI API Error', [
                'message' => $e->getMessage(),
                'lat' => $lat,
                'lng' => $lng,
                'trace' => $e->getTraceAsString(),
            ]);

            return ResponseService::serverError('POI verileri alınırken hata oluştu.', $e);
        }
    }

    /**
     * ✅ Gerçek OSM POI verilerini Overpass API ile çek
     * Context7: Deniz içinde POI göstermemek için gerçek veri kullanıyoruz
     */
    private function getRealPOIs(float $lat, float $lng, int $radius, ?array $requestedTypes): array
    {
        // OSM amenity mapping (Türkçe kategori → OSM tag)
        $amenityMapping = [
            'okul' => ['school', 'university', 'college', 'kindergarten'],
            'market' => ['supermarket', 'marketplace', 'convenience', 'mall'],
            'hastane' => ['hospital', 'clinic', 'pharmacy', 'doctors'],
            'otel' => ['hotel', 'hostel', 'guesthouse'],
            'sahil' => ['beach_resort', 'marina'],
            'park' => ['park', 'playground', 'sports_centre', 'fitness_centre'],
            'ulasim' => ['bus_station', 'taxi', 'ferry_terminal'],
        ];

        $allPOIs = [];
        $poiId = 1;

        // Eğer types filtresi yoksa, tüm kategorileri çek
        $typesToQuery = $requestedTypes ?: array_keys($amenityMapping);

        foreach ($typesToQuery as $type) {
            if (!isset($amenityMapping[$type])) {
                continue;
            }

            $amenities = $amenityMapping[$type];
            foreach ($amenities as $amenity) {
                $osmResults = $this->queryOverpassAPI($lat, $lng, $amenity, $radius);

                foreach ($osmResults as $osmItem) {
                    if (!isset($osmItem['lat']) || !isset($osmItem['lng'])) {
                        continue;
                    }

                    // Mesafe hesapla
                    $distance = $this->calculateDistance($lat, $lng, $osmItem['lat'], $osmItem['lng']) * 1000; // km → m

                    // Radius içinde mi kontrol et
                    if ($distance > $radius) {
                        continue;
                    }

                    $name = $osmItem['name'] ?? $this->getDefaultNameForAmenity($amenity);
                    $walkingMinutes = round($distance / 80); // Ortalama yürüme hızı: 80m/dk

                    $allPOIs[] = [
                        'id' => $poiId++,
                        'name' => $name,
                        'type' => $type,
                        'category' => $this->getPOICategoryLabel($type),
                        'lat' => round($osmItem['lat'], 6),
                        'lng' => round($osmItem['lng'], 6),
                        'distance_m' => round($distance),
                        'distance_km' => round($distance / 1000, 2),
                        'walking_minutes' => $walkingMinutes,
                        'icon' => $this->getPOIIcon($type),
                        'osm_id' => $osmItem['id'] ?? null,
                        'tags' => $osmItem['tags'] ?? [],
                    ];
                }
            }
        }

        // Mesafeye göre sırala
        usort($allPOIs, fn($a, $b) => $a['distance_m'] <=> $b['distance_m']);

        // Maksimum 50 POI döndür (performans için)
        return array_slice($allPOIs, 0, 50);
    }

    /**
     * Amenity için varsayılan isim
     */
    private function getDefaultNameForAmenity(string $amenity): string
    {
        $names = [
            'school' => 'Okul',
            'university' => 'Üniversite',
            'supermarket' => 'Süpermarket',
            'hospital' => 'Hastane',
            'pharmacy' => 'Eczane',
            'hotel' => 'Otel',
            'park' => 'Park',
            'bus_station' => 'Otobüs Durağı',
        ];

        return $names[$amenity] ?? 'POI';
    }

    /**
     * POI tipi için Türkçe kategori etiketi
     */
    private function getPOICategoryLabel(string $type): string
    {
        return match ($type) {
            'okul' => 'Eğitim',
            'market' => 'Alışveriş',
            'hastane' => 'Sağlık',
            'otel' => 'Konaklama',
            'sahil' => 'Sahil & Deniz',
            'park' => 'Park & Yeşil Alan',
            'ulasim' => 'Ulaşım',
            default => 'Diğer',
        };
    }

    /**
     * POI tipi için icon adı (frontend'de kullanılacak)
     */
    private function getPOIIcon(string $type): string
    {
        return match ($type) {
            'okul' => 'school',
            'market' => 'shopping-cart',
            'hastane' => 'hospital',
            'otel' => 'hotel',
            'sahil' => 'beach',
            'park' => 'tree',
            'ulasim' => 'bus',
            default => 'map-pin',
        };
    }

    /**
     * Specific category analysis
     * GET /api/environment/category/{category}?lat={lat}&lng={lng}
     */
    public function analyzeCategory(Request $request, string $category): JsonResponse
    {
        $validated = $this->validateRequestWithResponse($request, [
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:500|max:5000',
        ]);

        if ($validated instanceof JsonResponse) {
            return $validated;
        }

        $validCategories = [
            'transportation',
            'healthcare',
            'education',
            'shopping',
            'recreation',
            'coastal',
            'dining',
        ];

        if (! in_array($category, $validCategories)) {
            return ResponseService::error('Geçersiz kategori', 400);
        }

        $lat = $request->get('lat');
        $lng = $request->get('lng');
        $radius = $request->get('radius', 2000);

        try {
            $places = $this->searchPlacesByCategory($lat, $lng, $category, $radius);
            $analysis = $this->analyzeCategoryImpact($places, $category);

            return ResponseService::success([
                'category' => $category,
                'places' => $places,
                'analysis' => $analysis,
                'count' => count($places),
            ], "{$category} analizi başarıyla tamamlandı");
        } catch (\Exception $e) {
            return ResponseService::serverError("'{$category}' analizi yapılırken hata oluştu.", $e);
        }
    }

    /**
     * AI-powered location value prediction
     * POST /api/environment/value-prediction
     */
    public function predictLocationValue(Request $request): JsonResponse
    {
        $validated = $this->validateRequestWithResponse($request, [
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'property_type' => 'required|string|in:apartment,villa,office,land,commercial',
            'size' => 'nullable|numeric|min:1',
            'features' => 'nullable|array',
        ]);

        if ($validated instanceof JsonResponse) {
            return $validated;
        }

        try {
            // Environment analysis for value prediction
            $environmental = $this->performEnvironmentAnalysis(
                $request->get('lat'),
                $request->get('lng'),
                3000 // 3km for value analysis
            );

            // AI prediction using environmental factors
            $prediction = $this->aiService->generate([
                'action' => 'predict_property_value',
                'data' => [
                    'location' => [
                        'lat' => $request->get('lat'),
                        'lng' => $request->get('lng'),
                    ],
                    'property_type' => $request->get('property_type'),
                    'size' => $request->get('size'),
                    'features' => $request->get('features', []),
                    'environment' => $environmental,
                ],
            ]);

            return ResponseService::success([
                'environmental_analysis' => $environmental,
                'value_prediction' => $prediction,
                'factors' => $this->getValueFactors($environmental),
                'investment_score' => $this->calculateInvestmentScore($environmental),
            ], 'Değer tahmini başarıyla tamamlandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('Değer tahmini yapılırken hata oluştu.', $e);
        }
    }

    /**
     * Environment analysis implementation
     */
    private function performEnvironmentAnalysis(float $lat, float $lng, int $radius): array
    {
        $categories = [
            'transportation' => [
                'subway_station',
                'bus_station',
                'public_transport',
                'taxi_stand',
                'ferry_terminal',
            ],
            'healthcare' => [
                'hospital',
                'clinic',
                'pharmacy',
                'dentist',
                'veterinary',
            ],
            'education' => [
                'school',
                'university',
                'kindergarten',
                'college',
                'library',
            ],
            'shopping' => [
                'supermarket',
                'shopping_centre',
                'marketplace',
                'convenience',
                'department_store',
            ],
            'recreation' => [
                'park',
                'playground',
                'sports_centre',
                'swimming_pool',
                'fitness_centre',
                'cinema',
                'theatre',
            ],
            'coastal' => [
                'beach',
                'marina',
                'pier',
                'harbour',
                'waterfront',
            ],
            'dining' => [
                'restaurant',
                'cafe',
                'fast_food',
                'bar',
                'pub',
            ],
        ];

        $results = [];

        foreach ($categories as $category => $amenities) {
            $places = [];
            foreach ($amenities as $amenity) {
                $categoryPlaces = $this->queryOverpassAPI($lat, $lng, $amenity, $radius);
                $places = array_merge($places, $categoryPlaces);
            }

            $results[$category] = [
                'places' => $places,
                'count' => count($places),
                'closest_distance' => $this->findClosestDistance($lat, $lng, $places),
                'density_score' => $this->calculateDensityScore($places, $radius),
            ];
        }

        return $results;
    }

    /**
     * Query Overpass API for specific amenity
     */
    private function queryOverpassAPI(float $lat, float $lng, string $amenity, int $radius): array
    {
        $query = "[out:json][timeout:25];
        (
            node[\"amenity\"=\"{$amenity}\"](around:{$radius},{$lat},{$lng});
            way[\"amenity\"=\"{$amenity}\"](around:{$radius},{$lat},{$lng});
            relation[\"amenity\"=\"{$amenity}\"](around:{$radius},{$lat},{$lng});
        );
        out center;";

        try {
            $response = file_get_contents(
                'https://overpass-api.de/api/interpreter?data=' . urlencode($query)
            );

            if ($response === false) {
                return [];
            }

            $data = json_decode($response, true);

            if (! isset($data['elements'])) {
                return [];
            }

            return array_map(function ($element) use ($amenity) {
                return [
                    'id' => $element['id'],
                    'lat' => $element['lat'] ?? $element['center']['lat'] ?? null,
                    'lng' => $element['lon'] ?? $element['center']['lon'] ?? null,
                    'name' => $element['tags']['name'] ?? "Unnamed {$amenity}",
                    'amenity' => $amenity,
                    'tags' => $element['tags'] ?? [],
                ];
            }, array_filter($data['elements'], function ($element) {
                return isset($element['lat']) || isset($element['center']['lat']);
            }));
        } catch (\Exception $e) {
            Log::warning("Overpass API query failed for {$amenity}: " . $e->getMessage());

            return [];
        }
    }

    /**
     * Find closest distance in a category
     */
    private function findClosestDistance(float $lat, float $lng, array $places): ?float
    {
        if (empty($places)) {
            return null;
        }

        $distances = array_map(function ($place) use ($lat, $lng) {
            return $this->calculateDistance(
                $lat,
                $lng,
                $place['lat'],
                $place['lng']
            );
        }, $places);

        return min($distances);
    }

    /**
     * Calculate distance between two points (Haversine formula)
     */
    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earth_radius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earth_radius * $c;
    }

    /**
     * Calculate density score for a category
     */
    private function calculateDensityScore(array $places, int $radius): float
    {
        $area = pi() * pow($radius / 1000, 2); // km²
        $density = count($places) / $area;

        // Normalize to 0-10 scale
        return min(10, $density * 2);
    }

    /**
     * Generate AI insights
     */
    private function generateAIInsights(array $analysis, float $lat, float $lng): array
    {
        try {
            $prompt = "Bu konum için çevre analizi sonuçlarını değerlendir ve Türkçe öneriler sun:

Konum: {$lat}, {$lng}
Analiz Sonuçları: " . json_encode($analysis, JSON_UNESCAPED_UNICODE);

            $insights = $this->aiService->generate([
                'action' => 'analyze_environment',
                'prompt' => $prompt,
            ]);

            return [
                'summary' => $insights['summary'] ?? 'Analiz tamamlandı',
                'strengths' => $insights['strengths'] ?? [],
                'weaknesses' => $insights['weaknesses'] ?? [],
                'recommendations' => $insights['recommendations'] ?? [],
            ];
        } catch (\Exception $e) {
            return [
                'summary' => 'AI analizi yapılamadı',
                'strengths' => [],
                'weaknesses' => [],
                'recommendations' => [],
            ];
        }
    }

    /**
     * Calculate location scores
     */
    private function calculateLocationScores(array $analysis): array
    {
        $scores = [];

        foreach ($analysis as $category => $data) {
            $score = 0;

            // Distance score (closer is better)
            if ($data['closest_distance'] !== null) {
                if ($data['closest_distance'] <= 0.5) {
                    $score += 40;
                } elseif ($data['closest_distance'] <= 1.0) {
                    $score += 30;
                } elseif ($data['closest_distance'] <= 2.0) {
                    $score += 20;
                } else {
                    $score += 10;
                }
            }

            // Count score
            if ($data['count'] >= 5) {
                $score += 30;
            } elseif ($data['count'] >= 3) {
                $score += 20;
            } elseif ($data['count'] >= 1) {
                $score += 10;
            }

            // Density score
            $score += min(30, $data['density_score'] * 3);

            $scores[$category] = min(100, $score);
        }

        $scores['overall'] = array_sum($scores) / count($scores);

        return $scores;
    }

    /**
     * Generate recommendations
     */
    private function generateRecommendations(array $analysis): array
    {
        $recommendations = [];

        foreach ($analysis as $category => $data) {
            if ($data['count'] === 0) {
                $recommendations[] = "❌ {$category} kategorisinde yakın tesis bulunamadı";
            } elseif ($data['closest_distance'] > 2.0) {
                $recommendations[] = "⚠️ En yakın {$category} tesisi {$data['closest_distance']}km uzakta";
            } else {
                $recommendations[] = "✅ {$category} kategorisinde {$data['count']} tesis mevcut";
            }
        }

        return $recommendations;
    }

    /**
     * Search places by specific category
     * ✅ Context7: performEnvironmentAnalysis kullanılıyor, bu fonksiyon placeholder
     */
    private function searchPlacesByCategory(float $lat, float $lng, string $category, int $radius): array
    {
        // performEnvironmentAnalysis zaten kategori bazlı analiz yapıyor
        $analysis = $this->performEnvironmentAnalysis($lat, $lng, $radius);
        return $analysis[$category]['places'] ?? [];
    }

    /**
     * Analyze category impact on property value
     * ✅ Context7: Gerçek analiz için AI servisi kullanılmalı, placeholder kaldırıldı
     */
    private function analyzeCategoryImpact(array $places, string $category): array
    {
        if (empty($places)) {
            return [
                'impact_score' => 0,
                'value_increase' => '0%',
                'market_demand' => 'Düşük',
            ];
        }

        // Gerçek analiz için AI servisi kullanılabilir
        $count = count($places);
        $avgDistance = array_sum(array_column($places, 'distance')) / $count;

        return [
            'impact_score' => min(100, $count * 10 + (1000 - $avgDistance) / 10),
            'value_increase' => min(25, $count * 2) . '%',
            'market_demand' => $count >= 5 ? 'Yüksek' : ($count >= 2 ? 'Orta' : 'Düşük'),
        ];
    }

    /**
     * Get value affecting factors
     */
    private function getValueFactors(array $environmental): array
    {
        return [
            'positive' => [
                'Metro/toplu taşıma yakınlığı',
                'Sağlık tesisleri erişimi',
                'Eğitim kurumları',
                'Rekreasyon alanları',
            ],
            'negative' => [
                'Ulaşım eksikliği',
                'Alışveriş merkezi uzaklığı',
            ],
        ];
    }

    /**
     * Calculate investment score
     */
    private function calculateInvestmentScore(array $environmental): int
    {
        $score = 0;

        foreach ($environmental as $category => $data) {
            if ($data['closest_distance'] !== null && $data['closest_distance'] <= 1.0) {
                $score += 15;
            }
            $score += min(10, $data['count']);
        }

        return min(100, $score);
    }
}
