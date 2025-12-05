<?php

namespace App\Services\Integrations;

use App\Services\Intelligence\TKGMLearningService;
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
     * Learning Engine instance
     */
    protected TKGMLearningService $learningEngine;

    /**
     * Constructor - Dependency Injection
     */
    public function __construct(TKGMAgent $agent, TKGMLearningService $learningEngine)
    {
        $this->agent = $agent;
        $this->learningEngine = $learningEngine;
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

            // 🧠 LEARNING ENGINE: Kaydet ve öğren
            $this->learnFromQuery($parsedData, $lat, $lon);

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

    /**
     * Eski sistem uyumluluğu için: parselSorgula metodu
     * Yeni sistem: queryParcel metodunu kullanır
     *
     * @param string $ada Ada numarası
     * @param string $parsel Parsel numarası
     * @param string $il İl adı
     * @param string $ilce İlçe adı
     * @param string|null $mahalle Mahalle adı (opsiyonel, kullanılmıyor)
     * @return array Eski format uyumlu sonuç
     */
    public function parselSorgula(string $ada, string $parsel, string $il, string $ilce, ?string $mahalle = null): array
    {
        $result = $this->queryParcel($il, $ilce, $ada, $parsel);

        if (!$result || !isset($result['success']) || !$result['success']) {
            return [
                'success' => false,
                'message' => $result['message'] ?? 'Parsel sorgulama başarısız',
                'parsel_bilgileri' => null,
            ];
        }

        // Yeni formatı eski formata çevir
        $data = $result['data'] ?? [];

        return [
            'success' => true,
            'message' => 'Parsel sorgulama başarılı',
            'parsel_bilgileri' => [
                'ada' => $data['ada_no'] ?? $ada,
                'parsel' => $data['parsel_no'] ?? $parsel,
                'il' => $data['il'] ?? $il,
                'ilce' => $data['ilce'] ?? $ilce,
                'mahalle' => $data['mahalle'] ?? $mahalle,
                'yuzolcumu' => $data['alan_m2'] ?? null,
                'nitelik' => $data['nitelik'] ?? 'Arsa',
                'imar_durumu' => $data['imar_statusu'] ?? null,
                'taks' => $data['taks'] ?? null,
                'kaks' => $data['kaks'] ?? null,
                'gabari' => $data['gabari'] ?? null,
                'maksimum_kat' => null,
                'malik_adi' => null,
                'pafta_no' => $data['pafta'] ?? null,
                'koordinat_x' => $data['center_lng'] ?? null,
                'koordinat_y' => $data['center_lat'] ?? null,
            ],
            'metadata' => [
                'query_time' => now()->toDateTimeString(),
                'source' => $data['source'] ?? 'TKGM_LIVE',
                'reliability' => 'high',
            ],
        ];
    }

    /**
     * Cache temizle
     *
     * @param string|null $ada
     * @param string|null $parsel
     * @param string|null $il
     * @param string|null $ilce
     * @return bool
     */
    public function clearCache(?string $ada = null, ?string $parsel = null, ?string $il = null, ?string $ilce = null): bool
    {
        if ($ada && $parsel && $il && $ilce) {
            $cacheKey = $this->buildCacheKey($il, $ilce, $ada, $parsel);
            Cache::forget($cacheKey);
            Cache::forget($cacheKey . ':stale');
            return true;
        }

        // Tüm TKGM cache'ini temizle (dikkatli kullan!)
        Cache::flush();
        return true;
    }

    /**
     * Yatırım analizi (Parsel bilgilerine göre)
     *
     * @param array $parselBilgileri Parsel bilgileri array'i
     * @return array Yatırım analizi sonuçları
     */
    public function yatirimAnalizi(array $parselBilgileri): array
    {
        $skor = 0;
        $maxSkor = 100;
        $analizler = [];

        // KAKS skoru (0-30)
        $kaks = $parselBilgileri['kaks'] ?? 0;
        if ($kaks >= 1.5) {
            $kaksSkor = 30;
            $analizler[] = "✅ Yüksek KAKS ({$kaks}) - Mükemmel inşaat potansiyeli";
        } elseif ($kaks >= 1.0) {
            $kaksSkor = 20;
            $analizler[] = "✅ İyi KAKS ({$kaks}) - İyi inşaat potansiyeli";
        } elseif ($kaks >= 0.5) {
            $kaksSkor = 10;
            $analizler[] = "⚠️ Orta KAKS ({$kaks}) - Orta inşaat potansiyeli";
        } else {
            $kaksSkor = 0;
            $analizler[] = "❌ Düşük KAKS ({$kaks}) - Sınırlı inşaat";
        }
        $skor += $kaksSkor;

        // TAKS skoru (0-20)
        $taks = $parselBilgileri['taks'] ?? 0;
        if ($taks >= 30 && $taks <= 40) {
            $taksSkor = 20;
            $analizler[] = "✅ Optimal TAKS ({$taks}%) - İdeal taban alanı";
        } elseif ($taks >= 20) {
            $taksSkor = 15;
            $analizler[] = "✅ İyi TAKS ({$taks}%)";
        } else {
            $taksSkor = 5;
            $analizler[] = "⚠️ Düşük TAKS ({$taks}%)";
        }
        $skor += $taksSkor;

        // İmar durumu skoru (0-30)
        $imarDurumu = $parselBilgileri['imar_durumu'] ?? '';
        if (stripos($imarDurumu, 'İmarlı') !== false || stripos($imarDurumu, 'İmarda') !== false) {
            $imarSkor = 30;
            $analizler[] = '✅ İmarlı arsa - Yapılaşmaya hazır';
        } elseif (stripos($imarDurumu, 'Plan') !== false) {
            $imarSkor = 25;
            $analizler[] = '✅ Plan içinde - İmara açılabilir';
        } else {
            $imarSkor = 5;
            $analizler[] = '⚠️ İmar dışı - Yapılaşma riski';
        }
        $skor += $imarSkor;

        // Alan skoru (0-20)
        $yuzolcumu = $parselBilgileri['yuzolcumu'] ?? 0;
        if ($yuzolcumu >= 1000) {
            $alanSkor = 20;
            $analizler[] = "✅ Büyük parsel ({$yuzolcumu} m²) - Proje imkanı";
        } elseif ($yuzolcumu >= 500) {
            $alanSkor = 15;
            $analizler[] = "✅ Orta büyüklük ({$yuzolcumu} m²)";
        } elseif ($yuzolcumu >= 200) {
            $alanSkor = 10;
            $analizler[] = "⚠️ Küçük parsel ({$yuzolcumu} m²)";
        } else {
            $alanSkor = 5;
            $analizler[] = '⚠️ Çok küçük parsel';
        }
        $skor += $alanSkor;

        // Genel değerlendirme
        $degerlendirme = '';
        $harfNotu = '';
        if ($skor >= 80) {
            $degerlendirme = 'Mükemmel yatırım fırsatı';
            $harfNotu = 'A+';
        } elseif ($skor >= 60) {
            $degerlendirme = 'İyi yatırım potansiyeli';
            $harfNotu = 'A';
        } elseif ($skor >= 40) {
            $degerlendirme = 'Orta seviye yatırım';
            $harfNotu = 'B';
        } else {
            $degerlendirme = 'Düşük yatırım potansiyeli';
            $harfNotu = 'C';
        }

        return [
            'yatirim_skoru' => $skor,
            'max_skor' => $maxSkor,
            'harf_notu' => $harfNotu,
            'degerlendirme' => $degerlendirme,
            'analizler' => $analizler,
            'risk_seviyesi' => $this->calculateRiskLevel($skor),
            'tahmini_getiri' => $this->estimateROI($skor, $parselBilgileri),
        ];
    }

    /**
     * Risk seviyesi hesaplama
     */
    protected function calculateRiskLevel(int $skor): string
    {
        if ($skor >= 70) {
            return 'Düşük';
        } elseif ($skor >= 50) {
            return 'Orta';
        } else {
            return 'Yüksek';
        }
    }

    /**
     * ROI tahmini
     */
    protected function estimateROI(int $skor, array $parselBilgileri): string
    {
        if ($skor >= 80) {
            return 'Yıllık %15-20 değer artışı beklenir';
        } elseif ($skor >= 60) {
            return 'Yıllık %10-15 değer artışı beklenir';
        } elseif ($skor >= 40) {
            return 'Yıllık %5-10 değer artışı beklenir';
        } else {
            return 'Uzun vadeli yatırım (5+ yıl)';
        }
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // 🧠 LEARNING ENGINE ENTEGRASYONU
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    /**
     * TKGM sorgusunu Learning Engine'e kaydet
     *
     * @param array $parsedData TKGM verisi
     * @param float $lat Enlem
     * @param float $lon Boylam
     * @return void
     */
    protected function learnFromQuery(array $parsedData, float $lat, float $lon): void
    {
        try {
            // İl/İlçe bilgisini bul (Nominatim'den gelebilir)
            $locationData = $this->getLocationFromCoordinates($lat, $lon);

            $context = [
                'il_id' => $locationData['il_id'] ?? null,
                'ilce_id' => $locationData['ilce_id'] ?? null,
                'mahalle_id' => $locationData['mahalle_id'] ?? null,
                'source' => 'tkgm_service',
                'user_id' => auth()->id(),
            ];

            // Learning Engine'e kaydet
            $this->learningEngine->learn($parsedData, $context);
        } catch (\Exception $e) {
            // Learning engine hatası ana akışı etkilememeli
            Log::warning('Learning Engine error (non-critical)', [
                'error' => $e->getMessage(),
                'lat' => $lat,
                'lon' => $lon,
            ]);
        }
    }

    /**
     * Koordinatlardan İl/İlçe bilgisi al (Nominatim API)
     *
     * @param float $lat
     * @param float $lon
     * @return array
     */
    protected function getLocationFromCoordinates(float $lat, float $lon): array
    {
        $cacheKey = "nominatim_location_{$lat}_{$lon}";

        return Cache::remember($cacheKey, 86400, function () use ($lat, $lon) {
            // Nominatim API çağrısı (mevcut implementasyon varsa kullan)
            // Yoksa basit DB lookup yap

            // TODO: Nominatim entegrasyonu veya DB'den en yakın il/ilçe bulma
            // Şimdilik null döndür, sonra implement edilecek

            return [
                'il_id' => null,
                'ilce_id' => null,
                'mahalle_id' => null,
            ];
        });
    }
}
