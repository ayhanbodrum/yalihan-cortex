<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * TKGM (Tapu ve Kadastro Genel Müdürlüğü) Entegrasyon Servisi
 *
 * Context7 Standardı: C7-TKGM-INTEGRATION-2025-12-02
 * Yalıhan Bekçi: TKGM Auto-Fill System
 *
 * Amaç: Ada/Parsel girildiğinde arsa bilgilerini otomatik doldurmak
 * Gerçek TKGM MEGSIS API entegrasyonu (TKGMAgent kullanarak)
 *
 * Kullanım:
 * $service = app(TKGMService::class);
 * $result = $service->queryParcel('Muğla', 'Bodrum', '1234', '5');
 * $result = $service->getParcelByCoordinates(37.0361, 27.4305);
 */
class TKGMService
{
    /**
     * Cache TTL (seconds)
     * ✅ 7 days cache
     */
    protected const CACHE_TTL = 7 * 24 * 60 * 60; // 7 days

    /**
     * TKGMAgent instance
     */
    protected TKGMAgent $agent;

    /**
     * Constructor - Dependency Injection
     */
    public function __construct(TKGMAgent $agent)
    {
        $this->agent = $agent;
    }

    /**
     * Koordinat bazlı parsel sorgulama
     *
     * @param float $lat Enlem
     * @param float $lon Boylam
     * @return array|null Parsel bilgileri veya null (hata durumunda)
     */
    public function getParcelByCoordinates(float $lat, float $lon): ?array
    {
        $cacheKey = $this->buildCoordinateCacheKey($lat, $lon);
        $staleCacheKey = $cacheKey . ':stale';

        // Cache check - Fresh data
        if ($cached = Cache::get($cacheKey)) {
            return array_merge($cached, [
                'cache_status' => 'hit',
                'cached_at' => $cached['cached_at'] ?? now()->toIso8601String(),
            ]);
        }

        try {
            // ✅ GERÇEK AJANI ÇAĞIR
            $data = $this->agent->getParcelData($lat, $lon);

            if (!$data) {
                // Stale cache kontrolü
                if ($staleData = Cache::get($staleCacheKey)) {
                    Log::warning('TKGM API failed, using stale cache', [
                        'lat' => $lat,
                        'lon' => $lon,
                        'fallback' => 'stale_cache',
                    ]);

                    return array_merge($staleData, [
                        'cache_status' => 'stale',
                        'stale_reason' => 'api_failed',
                        'warning' => 'API hatası nedeniyle eski veri kullanıldı',
                    ]);
                }

                Log::error('TKGM API: Veri alınamadı', [
                    'lat' => $lat,
                    'lon' => $lon,
                ]);

                return null;
            }

            // TKGMAgent'ten gelen veriyi Context7 formatına çevir
            $parsedData = $this->normalizeAgentData($data, $lat, $lon);

            // Add cache metadata
            $parsedData['cache_status'] = 'miss';
            $parsedData['cached_at'] = now()->toIso8601String();

            // Cache SUCCESS response (7 days)
            Cache::put($cacheKey, $parsedData, self::CACHE_TTL);

            // Store as stale backup (30 days)
            Cache::put($staleCacheKey, $parsedData, 30 * 24 * 60 * 60);

            return $parsedData;
        } catch (\Exception $e) {
            Log::error('TKGM Service Error', [
                'lat' => $lat,
                'lon' => $lon,
                'error' => $e->getMessage(),
            ]);

            // Stale cache fallback
            if ($staleData = Cache::get($staleCacheKey)) {
                return array_merge($staleData, [
                    'cache_status' => 'stale',
                    'stale_reason' => 'exception',
                    'warning' => 'API hatası nedeniyle eski veri kullanıldı',
                ]);
            }

            return null;
        }
    }

    /**
     * Arsa/Parsel sorgulama (İl/İlçe/Ada/Parsel ile)
     *
     * @param string $il İl adı (örn: Muğla)
     * @param string $ilce İlçe adı (örn: Bodrum)
     * @param string $ada Ada numarası
     * @param string $parsel Parsel numarası
     * @return array|null Parsel bilgileri veya null (hata durumunda)
     *
     * Not: Koordinat bulunamazsa hata döndürür
     */
    public function queryParcel(string $il, string $ilce, string $ada, string $parsel): ?array
    {
        $cacheKey = $this->buildCacheKey($il, $ilce, $ada, $parsel);
        $staleCacheKey = $cacheKey . ':stale';

        // Cache check - Fresh data
        if ($cached = Cache::get($cacheKey)) {
            return array_merge($cached, [
                'cache_status' => 'hit',
                'cached_at' => $cached['cached_at'] ?? now()->toIso8601String(),
            ]);
        }

        try {
            // Önce koordinat bul (geocoding ile)
            $coordinates = $this->findCoordinatesByAddress($il, $ilce, $ada, $parsel);

            if (!$coordinates) {
                Log::warning('TKGM: Koordinat bulunamadı', [
                    'il' => $il,
                    'ilce' => $ilce,
                    'ada' => $ada,
                    'parsel' => $parsel,
                ]);

                return [
                    'success' => false,
                    'message' => 'Koordinat bulunamadı. Lütfen haritadan konum seçin.',
                    'data' => null,
                ];
            }

            // Koordinat ile parsel sorgula (GERÇEK VERİ)
            $result = $this->getParcelByCoordinates($coordinates['lat'], $coordinates['lon']);

            if (!$result || !$result['success']) {
                return [
                    'success' => false,
                    'message' => 'Parsel bilgileri bulunamadı. Lütfen haritadan konum seçin.',
                    'data' => null,
                ];
            }

            // Ada ve Parsel bilgilerini ekle (API'den gelmeyebilir)
            if (!isset($result['data']['ada_no'])) {
                $result['data']['ada_no'] = $ada;
            }
            if (!isset($result['data']['parsel_no'])) {
                $result['data']['parsel_no'] = $parsel;
            }

            // Cache SUCCESS response (7 days)
            Cache::put($cacheKey, $result, self::CACHE_TTL);
            Cache::put($staleCacheKey, $result, 30 * 24 * 60 * 60);

            return $result;
        } catch (\Exception $e) {
            Log::error('TKGM queryParcel Error', [
                'il' => $il,
                'ilce' => $ilce,
                'ada' => $ada,
                'parsel' => $parsel,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Parsel sorgulama hatası: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * TKGMAgent'ten gelen veriyi Context7 formatına çevir
     *
     * @param array $agentData TKGMAgent'ten gelen normalize edilmiş veri
     * @param float $lat Enlem
     * @param float $lon Boylam
     * @return array Context7 formatında veri
     */
    protected function normalizeAgentData(array $agentData, float $lat, float $lon): array
    {
        return [
            'success' => true,
            'data' => [
                'ada_no' => $agentData['ada'] ?? null,
                'parsel_no' => $agentData['parsel'] ?? null,
                'alan_m2' => isset($agentData['alan_m2']) ? (float) $agentData['alan_m2'] : null,
                'nitelik' => $agentData['nitelik'] ?? 'Arsa',
                'mevkii' => $agentData['mevkii'] ?? null,
                'pafta' => $agentData['pafta'] ?? null,
                'il' => $agentData['il'] ?? null,
                'ilce' => $agentData['ilce'] ?? null,
                'mahalle' => $agentData['mahalle'] ?? null,
                'imar_statusu' => null,
                'kaks' => null,
                'taks' => null,
                'gabari' => null,
                'center_lat' => $lat,
                'center_lng' => $lon,
                'enlem' => $lat,
                'boylam' => $lon,
                'yola_cephe' => false,
                'altyapi_elektrik' => false,
                'altyapi_su' => false,
                'altyapi_dogalgaz' => false,
                'tapu_durumu' => null,
                'sehir_plan_bilgisi' => null,
                'yol_durumu' => null,
                'source' => 'TKGM_LIVE',
                'query_date' => now()->toIso8601String(),
            ],
        ];
    }

    /**
     * Ada/Parsel için koordinat bul (Geocoding)
     *
     * @param string $il
     * @param string $ilce
     * @param string $ada
     * @param string $parsel
     * @return array|null ['lat' => float, 'lon' => float] veya null
     */
    protected function findCoordinatesByAddress(string $il, string $ilce, string $ada, string $parsel): ?array
    {
        $geocodeCacheKey = sprintf(
            'tkgm:geocode:%s:%s:%s:%s',
            $this->slugify($il),
            $this->slugify($ilce),
            $this->slugify($ada),
            $this->slugify($parsel)
        );

        return Cache::remember($geocodeCacheKey, 86400, function () use ($il, $ilce, $ada, $parsel) {
            try {
                $query = sprintf('%s, %s, Ada %s Parsel %s, Türkiye', $il, $ilce, $ada, $parsel);
                $geocodeUrl = config('app.url') . '/api/geo/geocode';

                $response = \Illuminate\Support\Facades\Http::timeout(5)
                    ->withOptions(['verify' => false])
                    ->post($geocodeUrl, [
                        'query' => $query,
                        'limit' => 1,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['success']) && $data['success'] && !empty($data['data'])) {
                        $firstResult = $data['data'][0];
                        return [
                            'lat' => (float) $firstResult['lat'],
                            'lon' => (float) $firstResult['lon'],
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('TKGM Geocoding failed', [
                    'il' => $il,
                    'ilce' => $ilce,
                    'ada' => $ada,
                    'parsel' => $parsel,
                    'error' => $e->getMessage(),
                ]);
            }

            return null;
        });
    }

    /**
     * Build cache key (Ada/Parsel bazlı)
     */
    protected function buildCacheKey(string $il, string $ilce, string $ada, string $parsel): string
    {
        return sprintf(
            'tkgm:parcel:%s:%s:%s:%s',
            $this->slugify($il),
            $this->slugify($ilce),
            $this->slugify($ada),
            $this->slugify($parsel)
        );
    }

    /**
     * Build cache key (Koordinat bazlı)
     */
    protected function buildCoordinateCacheKey(float $lat, float $lon): string
    {
        return sprintf('tkgm:parcel:coord:%s:%s', round($lat, 6), round($lon, 6));
    }

    /**
     * Slugify helper for cache keys
     */
    protected function slugify(string $text): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
    }

    /**
     * Health check: TKGM API'ye erişilebilir mi?
     *
     * @return array Status bilgisi
     */
    public function healthCheck(): array
    {
        try {
            // Test koordinatı ile API'yi test et (Bodrum merkez)
            $testLat = 37.0361;
            $testLon = 27.4305;

            $data = $this->agent->getParcelData($testLat, $testLon);

            return [
                'success' => $data !== null,
                'status' => $data !== null ? 'ok' : 'error',
                'message' => $data !== null ? 'TKGM API erişilebilir' : 'TKGM API erişilemiyor',
                'source' => 'TKGMAgent',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'TKGM API erişilemiyor: ' . $e->getMessage(),
                'source' => 'TKGMAgent',
            ];
        }
    }
}
