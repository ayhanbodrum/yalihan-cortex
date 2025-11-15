<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Unified Location Service
 *
 * TurkiyeAPI + WikiMapia entegrasyonu
 * Resmi lokasyon + Çevresel özellikler
 *
 * Context7: Smart location profiling with environmental analysis
 */
class UnifiedLocationService
{
    protected TurkiyeAPIService $turkiyeAPI;
    protected WikimapiaService $wikiMapia;

    public function __construct(
        TurkiyeAPIService $turkiyeAPI,
        WikimapiaService $wikiMapia
    ) {
        $this->turkiyeAPI = $turkiyeAPI;
        $this->wikiMapia = $wikiMapia;
    }

    /**
     * Get comprehensive location profile
     * TurkiyeAPI (official) + WikiMapia (environmental)
     *
     * @param float $lat Latitude
     * @param float $lon Longitude
     * @param int|null $districtId District ID for location suggestions
     * @return array
     */
    public function getLocationProfile(float $lat, float $lon, ?int $districtId = null): array
    {
        $cacheKey = "unified.location.{$lat}.{$lon}." . ($districtId ?? 'none');

        return Cache::remember($cacheKey, 3600, function () use ($lat, $lon, $districtId) {
            $profile = [
                'coordinates' => compact('lat', 'lon'),
                'official' => null,
                'environment' => [],
                'scores' => [],
                'suggestions' => []
            ];

            try {
                // 1. TurkiyeAPI → Resmi lokasyon bilgisi
                if ($districtId) {
                    $allLocations = $this->turkiyeAPI->getAllLocations($districtId);
                    $profile['official'] = [
                        'district_id' => $districtId,
                        'locations' => $allLocations
                    ];
                }

                // 2. WikiMapia → Çevresel özellikler (2km çevresinde)
                $nearbyPlaces = $this->wikiMapia->getNearestPlaces($lat, $lon, [
                    'count' => 100,
                    'data_blocks' => ['main', 'location']
                ]);

                if ($nearbyPlaces && isset($nearbyPlaces['places'])) {
                    $profile['environment'] = $this->categorizeNearbyPlaces(
                        $nearbyPlaces['places'],
                        $lat,
                        $lon
                    );
                }

                // 3. Skorlama
                $profile['scores'] = $this->calculateScores($profile['environment']);

                // 4. Akıllı öneriler
                $profile['suggestions'] = $this->generateSuggestions($profile);

                return $profile;
            } catch (\Exception $e) {
                Log::error('UnifiedLocationService profile exception', ['error' => $e->getMessage()]);
                return $profile;
            }
        });
    }

    /**
     * Categorize nearby places from WikiMapia
     *
     * @param array $places WikiMapia places
     * @param float $baseLat Base latitude
     * @param float $baseLon Base longitude
     * @return array
     */
    private function categorizeNearbyPlaces($places, $baseLat, $baseLon)
    {
        $categorized = [
            'residential' => [],  // Siteler, apartmanlar
            'education' => [],    // Okullar
            'health' => [],       // Sağlık
            'shopping' => [],     // Market, AVM
            'transport' => [],    // Ulaşım
            'social' => [],       // Park, plaj, spor
            'food' => [],         // Restoran, kafe
            'other' => []
        ];

        foreach ($places as $place) {
            $title = strtolower($place['title'] ?? '');
            $category = $this->detectCategory($title);
            $distance = $this->calculateDistance(
                $baseLat,
                $baseLon,
                $place['location']['lat'] ?? $baseLat,
                $place['location']['lon'] ?? $baseLon
            );

            $placeData = [
                'id' => $place['id'],
                'name' => $place['title'] ?? 'Unknown',
                'distance' => round($distance * 1000), // metre
                'url' => $place['url'] ?? null,
                'description' => $place['description'] ?? null
            ];

            $categorized[$category][] = $placeData;
        }

        // Her kategoriyi mesafeye göre sırala
        foreach ($categorized as $key => $items) {
            usort($categorized[$key], fn($a, $b) => $a['distance'] <=> $b['distance']);
        }

        return $categorized;
    }

    /**
     * Detect category from place title
     *
     * @param string $title Place title (lowercase)
     * @return string Category key
     */
    private function detectCategory($title)
    {
        $patterns = [
            'residential' => ['site', 'apartman', 'residence', 'konut', 'villa', 'evler'],
            'education' => ['okul', 'üniversite', 'lise', 'ilkokul', 'kreş', 'school'],
            'health' => ['hastane', 'sağlık', 'eczane', 'klinik', 'hospital', 'clinic'],
            'shopping' => ['market', 'migros', 'carrefour', 'a101', 'bim', 'şok', 'avm', 'mall'],
            'transport' => ['durak', 'otogar', 'metro', 'havaalanı', 'iskele', 'terminal'],
            'social' => ['park', 'plaj', 'beach', 'spor', 'gym', 'sahil'],
            'food' => ['restoran', 'kafe', 'restaurant', 'cafe', 'lokanta'],
        ];

        foreach ($patterns as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($title, $keyword) !== false) {
                    return $category;
                }
            }
        }

        return 'other';
    }

    /**
     * Calculate distance between two coordinates (Haversine)
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float Distance in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    /**
     * Calculate location scores
     *
     * @param array $environment Categorized places
     * @return array Scores (0-100)
     */
    private function calculateScores($environment)
    {
        $scores = [
            'walkability' => 0,
            'convenience' => 0,
            'family_friendly' => 0,
            'investment_potential' => 0,
            'beach_proximity' => 0
        ];

        // Yürünebilirlik (Walkability)
        $nearestMarket = $environment['shopping'][0] ?? null;
        if ($nearestMarket) {
            if ($nearestMarket['distance'] < 500) $scores['walkability'] += 40;
            elseif ($nearestMarket['distance'] < 1000) $scores['walkability'] += 25;
            elseif ($nearestMarket['distance'] < 2000) $scores['walkability'] += 10;
        }

        $nearestTransport = $environment['transport'][0] ?? null;
        if ($nearestTransport) {
            if ($nearestTransport['distance'] < 500) $scores['walkability'] += 30;
            elseif ($nearestTransport['distance'] < 1000) $scores['walkability'] += 15;
        }

        if (count($environment['social']) > 0) {
            $scores['walkability'] += 20;
        }

        if (count($environment['food']) > 0) {
            $scores['walkability'] += 10;
        }

        // Kolaylık (Convenience)
        $scores['convenience'] = min(
            count($environment['shopping']) * 15 +
            count($environment['transport']) * 20 +
            count($environment['food']) * 10 +
            count($environment['health']) * 15,
            100
        );

        // Aile Uygunluğu (Family Friendly)
        $nearestSchool = $environment['education'][0] ?? null;
        if ($nearestSchool) {
            if ($nearestSchool['distance'] < 1000) $scores['family_friendly'] += 50;
            elseif ($nearestSchool['distance'] < 2000) $scores['family_friendly'] += 30;
        }

        if (count($environment['social']) > 0) {
            $scores['family_friendly'] += 30;
        }

        if (count($environment['health']) > 0) {
            $scores['family_friendly'] += 20;
        }

        // Plaja Yakınlık (Beach Proximity)
        foreach ($environment['social'] as $place) {
            $title = strtolower($place['name']);
            if (strpos($title, 'plaj') !== false || strpos($title, 'beach') !== false) {
                if ($place['distance'] < 500) $scores['beach_proximity'] = 100;
                elseif ($place['distance'] < 1000) $scores['beach_proximity'] = 80;
                elseif ($place['distance'] < 2000) $scores['beach_proximity'] = 60;
                break;
            }
        }

        // Yatırım Potansiyeli (Investment)
        $scores['investment_potential'] = round(
            ($scores['walkability'] * 0.3) +
            ($scores['convenience'] * 0.3) +
            ($scores['family_friendly'] * 0.2) +
            ($scores['beach_proximity'] * 0.2)
        );

        return $scores;
    }

    /**
     * Generate smart suggestions
     *
     * @param array $profile Location profile
     * @return array Suggestions
     */
    private function generateSuggestions($profile)
    {
        $suggestions = [];

        $env = $profile['environment'];
        $scores = $profile['scores'];

        // Yüksek walkability
        if ($scores['walkability'] >= 80) {
            $suggestions[] = [
                'type' => 'positive',
                'icon' => '✅',
                'text' => 'Yürüme mesafesinde her şey var! Araç gerektirmez.'
            ];
        }

        // Site önerisi
        if (count($env['residential'] ?? []) > 0) {
            $nearest = $env['residential'][0];
            $suggestions[] = [
                'type' => 'site',
                'icon' => '🏘️',
                'text' => "Yakın site: {$nearest['name']} ({$nearest['distance']}m)",
                'action' => 'select_site',
                'site_id' => $nearest['id']
            ];
        }

        // Plaj yakınlığı
        if ($scores['beach_proximity'] >= 80) {
            $suggestions[] = [
                'type' => 'positive',
                'icon' => '🏖️',
                'text' => 'Denize çok yakın! Tatil villaları için ideal.'
            ];
        }

        // Aile uygunluğu
        if ($scores['family_friendly'] >= 70) {
            $suggestions[] = [
                'type' => 'positive',
                'icon' => '👨‍👩‍👧‍👦',
                'text' => 'Aileler için uygun bölge. Okul ve park yakın.'
            ];
        }

        // Market yoksa uyarı
        if (count($env['shopping'] ?? []) === 0) {
            $suggestions[] = [
                'type' => 'warning',
                'icon' => '⚠️',
                'text' => 'Yakında market bulunamadı. Uzak bölge olabilir.'
            ];
        }

        return $suggestions;
    }

    /**
     * Get nearest residential complex (Site/Apartman)
     * WikiMapia'dan en yakın siteyi bul
     *
     * @param float $lat
     * @param float $lon
     * @param int $limit
     * @return array
     */
    public function getNearestResidentialComplex($lat, $lon, $limit = 5)
    {
        try {
            $nearby = $this->wikiMapia->getNearestPlaces($lat, $lon, [
                'count' => 50
            ]);

            $sites = [];

            if ($nearby && isset($nearby['places'])) {
                foreach ($nearby['places'] as $place) {
                    $title = strtolower($place['title'] ?? '');

                    // Site/apartman filtrele
                    if (strpos($title, 'site') !== false ||
                        strpos($title, 'apartman') !== false ||
                        strpos($title, 'residence') !== false ||
                        strpos($title, 'konut') !== false ||
                        strpos($title, 'evler') !== false) {

                        $distance = $this->calculateDistance(
                            $lat,
                            $lon,
                            $place['location']['lat'] ?? $lat,
                            $place['location']['lon'] ?? $lon
                        );

                        $sites[] = [
                            'wikimapia_id' => $place['id'],
                            'name' => $place['title'],
                            'description' => $place['description'] ?? null,
                            'distance' => round($distance * 1000), // metre
                            'url' => $place['url'] ?? null,
                            'coordinates' => [
                                'lat' => $place['location']['lat'] ?? null,
                                'lon' => $place['location']['lon'] ?? null
                            ]
                        ];

                        if (count($sites) >= $limit) break;
                    }
                }
            }

            // Mesafeye göre sırala
            usort($sites, fn($a, $b) => $a['distance'] <=> $b['distance']);

            return $sites;
        } catch (\Exception $e) {
            Log::error('getNearestResidentialComplex exception', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get environmental summary
     * Çevresel özet bilgi
     *
     * @param array $environment Categorized places
     * @return array
     */
    public function getEnvironmentalSummary($environment)
    {
        $summary = [];

        $categories = [
            'shopping' => ['icon' => '🛒', 'label' => 'Alışveriş'],
            'education' => ['icon' => '🏫', 'label' => 'Eğitim'],
            'health' => ['icon' => '🏥', 'label' => 'Sağlık'],
            'transport' => ['icon' => '🚇', 'label' => 'Ulaşım'],
            'social' => ['icon' => '🏊', 'label' => 'Sosyal'],
            'food' => ['icon' => '🍽️', 'label' => 'Yeme-İçme'],
            'residential' => ['icon' => '🏘️', 'label' => 'Siteler'],
        ];

        foreach ($categories as $key => $info) {
            $places = $environment[$key] ?? [];
            $count = count($places);
            $nearest = $places[0] ?? null;

            $summary[$key] = [
                'icon' => $info['icon'],
                'label' => $info['label'],
                'count' => $count,
                'nearest' => $nearest ? [
                    'name' => $nearest['name'],
                    'distance' => $nearest['distance']
                ] : null
            ];
        }

        return $summary;
    }

    /**
     * Export location data for AI description
     * AI için lokasyon verisi hazırla
     *
     * @param array $profile Location profile
     * @return string
     */
    public function exportForAI($profile)
    {
        $text = '';

        // Official location
        if ($profile['official']) {
            $text .= "Resmi Konum: {$profile['official']['location_name']} ";
            $text .= "({$profile['official']['location_type']})";
            if ($profile['official']['population']) {
                $text .= ", Nüfus: {$profile['official']['population']}";
            }
            if ($profile['official']['is_coastal']) {
                $text .= ", Kıyı bölgesi";
            }
            $text .= ". ";
        }

        // Environment
        $env = $profile['environment'];
        $summary = $this->getEnvironmentalSummary($env);

        if ($summary['shopping']['nearest']) {
            $text .= "En yakın market: {$summary['shopping']['nearest']['name']} ({$summary['shopping']['nearest']['distance']}m). ";
        }

        if ($summary['social']['nearest']) {
            $text .= "En yakın sosyal alan: {$summary['social']['nearest']['name']} ({$summary['social']['nearest']['distance']}m). ";
        }

        // Scores
        if ($profile['scores']['walkability'] >= 80) {
            $text .= "Yürünebilirlik skoru yüksek ({$profile['scores']['walkability']}/100). ";
        }

        if ($profile['scores']['beach_proximity'] >= 80) {
            $text .= "Denize çok yakın lokasyon. ";
        }

        return trim($text);
    }
}
