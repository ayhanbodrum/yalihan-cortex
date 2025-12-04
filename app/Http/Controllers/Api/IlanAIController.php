<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CortexKnowledgeService;
use App\Services\Response\ResponseService;
use App\Services\Integrations\TKGMService;
use App\Traits\ValidatesApiRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * İlan AI Controller
 *
 * Context7 Standard: C7-ILAN-AI-API-2025-11-30
 *
 * AI-powered endpoints for listing management
 */
class IlanAIController extends Controller
{
    use ValidatesApiRequests;

    protected TKGMService $tkgmService;

    protected CortexKnowledgeService $cortexKnowledgeService;

    public function __construct(
        TKGMService $tkgmService,
        CortexKnowledgeService $cortexKnowledgeService
    ) {
        $this->tkgmService = $tkgmService;
        $this->cortexKnowledgeService = $cortexKnowledgeService;
    }

    /**
     * TKGM'den parsel bilgilerini çek
     *
     * POST /api/ai/fetch-tkgm
     *
     * Input: il_id, ilce_id, mahalle_id, ada_no, parsel_no
     * Response: { "alan_m2": 1500.50, "lat": 38.4, "lng": 27.1, ... }
     */
    public function fetchTkgm(Request $request): JsonResponse
    {
        $validated = $this->validateRequestWithResponse($request, [
            'il_id' => 'required|exists:iller,id',
            'ilce_id' => 'required|exists:ilceler,id',
            'mahalle_id' => 'nullable|exists:mahalleler,id',
            'ada_no' => 'required|string|max:20',
            'parsel_no' => 'required|string|max:20',
        ]);

        if ($validated instanceof JsonResponse) {
            return $validated;
        }

        try {
            // İl, ilçe, mahalle isimlerini çek
            $il = \App\Models\Il::findOrFail($validated['il_id']);
            $ilce = \App\Models\Ilce::findOrFail($validated['ilce_id']);
            $mahalle = $validated['mahalle_id']
                ? \App\Models\Mahalle::findOrFail($validated['mahalle_id'])
                : null;

            // TKGM servisinden parsel bilgilerini çek
            $result = $this->tkgmService->parselSorgula(
                $validated['ada_no'],
                $validated['parsel_no'],
                $il->il_adi,
                $ilce->ilce_adi,
                $mahalle?->mahalle_adi
            );

            if (!isset($result['success']) || !$result['success']) {
                return ResponseService::error(
                    $result['message'] ?? 'TKGM sorgulama başarısız',
                    400
                );
            }

            // Response formatını standardize et
            $parselBilgileri = $result['parsel_bilgileri'] ?? $result;

            return ResponseService::success([
                'alan_m2' => $parselBilgileri['alan_m2'] ?? $parselBilgileri['alan'] ?? null,
                'lat' => $parselBilgileri['lat'] ?? $parselBilgileri['latitude'] ?? null,
                'lng' => $parselBilgileri['lng'] ?? $parselBilgileri['longitude'] ?? null,
                'imar_statusu' => $parselBilgileri['imar_statusu'] ?? $parselBilgileri['zoning_status'] ?? null,
                'kaks' => $parselBilgileri['kaks'] ?? null,
                'taks' => $parselBilgileri['taks'] ?? null,
                'gabari' => $parselBilgileri['gabari'] ?? null,
                'from_cache' => $result['from_cache'] ?? false,
                'raw_data' => $parselBilgileri, // Tam veri (debugging için)
            ], 'TKGM sorgulama başarılı');
        } catch (\Exception $e) {
            return ResponseService::serverError(
                'TKGM sorgulama sırasında bir hata oluştu: ' . $e->getMessage(),
                $e
            );
        }
    }

    /**
     * m² Fiyatı hesapla
     *
     * POST /api/ai/calculate-m2-price
     *
     * Input: satis_fiyati, alan_m2
     * Logic: satis_fiyati / alan_m2
     * Response: { "m2_fiyati": 3500 }
     */
    public function calculateM2Price(Request $request): JsonResponse
    {
        $validated = $this->validateRequestWithResponse($request, [
            'satis_fiyati' => 'required|numeric|min:0',
            'alan_m2' => 'required|numeric|min:0.01', // En az 0.01 m² olmalı
        ]);

        if ($validated instanceof JsonResponse) {
            return $validated;
        }

        try {
            $satisFiyati = (float) $validated['satis_fiyati'];
            $alanM2 = (float) $validated['alan_m2'];

            if ($alanM2 <= 0) {
                return ResponseService::error(
                    'Alan metrekare değeri 0\'dan büyük olmalıdır',
                    400
                );
            }

            // m² fiyatı hesapla: satis_fiyati / alan_m2
            $m2Fiyati = round($satisFiyati / $alanM2, 2);

            return ResponseService::success([
                'm2_fiyati' => $m2Fiyati,
                'satis_fiyati' => $satisFiyati,
                'alan_m2' => $alanM2,
                'formula' => "{$satisFiyati} / {$alanM2} = {$m2Fiyati}",
            ], 'm² fiyatı başarıyla hesaplandı');
        } catch (\Exception $e) {
            return ResponseService::serverError(
                'm² fiyatı hesaplanırken bir hata oluştu: ' . $e->getMessage(),
                $e
            );
        }
    }

    /**
     * İmar Plan ve İnşaat Hakları Analizi
     *
     * POST /api/ai/analyze-construction
     *
     * Input: ada_no, parsel_no, alan_m2, ilce, mahalle (opsiyonel)
     * Response: { "kaks": 2.0, "taks": 0.6, "gabari": 12.5, ... }
     *
     * Context7: CortexKnowledgeService ile AnythingLLM RAG entegrasyonu
     */
    public function analyzeConstruction(Request $request): JsonResponse
    {
        $validated = $this->validateRequestWithResponse($request, [
            'ada_no' => 'required|string|max:20',
            'parsel_no' => 'required|string|max:20',
            'alan_m2' => 'required|numeric|min:0.01',
            'ilce' => 'required|string|max:100',
            'mahalle' => 'nullable|string|max:100',
        ]);

        if ($validated instanceof JsonResponse) {
            return $validated;
        }

        try {
            // CortexKnowledgeService'e gönderilecek veri formatı
            $data = [
                'ilce' => $validated['ilce'],
                'mahalle' => $validated['mahalle'] ?? null,
                'ada' => $validated['ada_no'],
                'parsel' => $validated['parsel_no'],
                'm2' => (float) $validated['alan_m2'],
            ];

            // CortexKnowledgeService ile AnythingLLM'e sorgu gönder
            $result = $this->cortexKnowledgeService->queryConstructionRights($data);

            if (! $result['success']) {
                return ResponseService::error(
                    $result['message'] ?? 'İmar plan analizi başarısız',
                    400
                );
            }

            // Başarılı response
            return ResponseService::success(
                $result['data'],
                'İmar plan analizi tamamlandı',
                200,
                [
                    'source' => $result['source'] ?? 'AnythingLLM - İmar Plan Notları',
                ]
            );
        } catch (\Exception $e) {
            return ResponseService::serverError(
                'İmar plan analizi sırasında bir hata oluştu: ' . $e->getMessage(),
                $e
            );
        }
    }

    /**
     * Yazlık Sezonluk Fiyatlandırma Hesaplama
     *
     * POST /api/ai/calculate-seasonal-price
     *
     * Input: gunluk_fiyat (required|numeric)
     * Response: { "haftalik": 66500, "aylik": 255000, "kis_sezonu_gunluk": 5000, ... }
     *
     * Context7: C7-YAZLIK-PRICING-AUTOMATION-2025-11-30
     */
    public function calculateSeasonalPrice(Request $request): JsonResponse
    {
        $validated = $this->validateRequestWithResponse($request, [
            'gunluk_fiyat' => 'required|numeric|min:0.01',
        ]);

        if ($validated instanceof JsonResponse) {
            return $validated;
        }

        try {
            $gunlukFiyat = (float) $validated['gunluk_fiyat'];

            // Config'den fiyatlandırma kurallarını al
            $pricingRules = config('yali_options.pricing_rules', []);
            $discounts = $pricingRules['discounts'] ?? [];
            $seasonalMultipliers = $pricingRules['seasonal_multipliers'] ?? [];

            // Varsayılan değerler (config yoksa)
            $weeklyDiscount = $discounts['weekly'] ?? 0.05;      // %5
            $monthlyDiscount = $discounts['monthly'] ?? 0.15;    // %15
            $yazMultiplier = $seasonalMultipliers['yaz'] ?? 1.00;      // %100
            $araSezonMultiplier = $seasonalMultipliers['ara_sezon'] ?? 0.70; // %70
            $kisMultiplier = $seasonalMultipliers['kis'] ?? 0.50;      // %50

            // Haftalık fiyat: günlük * 7 * (1 - weekly_discount)
            $haftalikFiyat = $gunlukFiyat * 7 * (1 - $weeklyDiscount);

            // Aylık fiyat: günlük * 30 * (1 - monthly_discount)
            $aylikFiyat = $gunlukFiyat * 30 * (1 - $monthlyDiscount);

            // Sezonluk günlük fiyatlar
            $yazSezonuGunluk = $gunlukFiyat * $yazMultiplier;
            $araSezonGunluk = $gunlukFiyat * $araSezonMultiplier;
            $kisSezonuGunluk = $gunlukFiyat * $kisMultiplier;

            // Sezonluk haftalık fiyatlar
            $yazSezonuHaftalik = $yazSezonuGunluk * 7 * (1 - $weeklyDiscount);
            $araSezonHaftalik = $araSezonGunluk * 7 * (1 - $weeklyDiscount);
            $kisSezonuHaftalik = $kisSezonuGunluk * 7 * (1 - $weeklyDiscount);

            // Sezonluk aylık fiyatlar
            $yazSezonuAylik = $yazSezonuGunluk * 30 * (1 - $monthlyDiscount);
            $araSezonAylik = $araSezonGunluk * 30 * (1 - $monthlyDiscount);
            $kisSezonuAylik = $kisSezonuGunluk * 30 * (1 - $monthlyDiscount);

            return ResponseService::success([
                'gunluk_fiyat' => round($gunlukFiyat, 2),
                'haftalik_fiyat' => round($haftalikFiyat, 2),
                'aylik_fiyat' => round($aylikFiyat, 2),
                'sezonluk_fiyatlar' => [
                    'yaz' => [
                        'gunluk' => round($yazSezonuGunluk, 2),
                        'haftalik' => round($yazSezonuHaftalik, 2),
                        'aylik' => round($yazSezonuAylik, 2),
                    ],
                    'ara_sezon' => [
                        'gunluk' => round($araSezonGunluk, 2),
                        'haftalik' => round($araSezonHaftalik, 2),
                        'aylik' => round($araSezonAylik, 2),
                    ],
                    'kis' => [
                        'gunluk' => round($kisSezonuGunluk, 2),
                        'haftalik' => round($kisSezonuHaftalik, 2),
                        'aylik' => round($kisSezonuAylik, 2),
                    ],
                ],
                'formulas' => [
                    'haftalik' => "{$gunlukFiyat} × 7 × (1 - {$weeklyDiscount}) = {$haftalikFiyat}",
                    'aylik' => "{$gunlukFiyat} × 30 × (1 - {$monthlyDiscount}) = {$aylikFiyat}",
                    'kis_gunluk' => "{$gunlukFiyat} × {$kisMultiplier} = {$kisSezonuGunluk}",
                ],
            ], 'Sezonluk fiyatlandırma hesaplaması tamamlandı');
        } catch (\Exception $e) {
            return ResponseService::serverError(
                'Sezonluk fiyatlandırma hesaplanırken bir hata oluştu: ' . $e->getMessage(),
                $e
            );
        }
    }

    /**
     * Konut Metrikleri Hesaplama
     *
     * POST /api/ai/calculate-konut-metrics
     *
     * Input: satis_fiyati, brut_m2
     * Response: { "m2_birim_fiyat": 25000, "formatted": "25.000 TL/m²" }
     *
     * Context7: C7-KONUT-METRICS-2025-11-30
     */
    public function calculateKonutMetrics(Request $request): JsonResponse
    {
        $validated = $this->validateRequestWithResponse($request, [
            'satis_fiyati' => 'required|numeric|min:0.01',
            'brut_m2' => 'required|numeric|min:10',
        ]);

        if ($validated instanceof JsonResponse) {
            return $validated;
        }

        try {
            $satisFiyati = (float) $validated['satis_fiyati'];
            $brutM2 = (float) $validated['brut_m2'];

            if ($brutM2 <= 0) {
                return ResponseService::error(
                    'Brüt metrekare değeri 0\'dan büyük olmalıdır',
                    400
                );
            }

            // m² birim fiyatı hesapla: satis_fiyati / brut_m2
            $m2BirimFiyat = round($satisFiyati / $brutM2, 2);

            // Formatlanmış değer (Türkçe format)
            $formatted = number_format($m2BirimFiyat, 0, ',', '.') . ' TL/m²';

            // Piyasa analizi (basit karşılaştırma)
            $piyasaOrtalamasi = 35000; // TL/m² (örnek değer, gerçekte veritabanından çekilebilir)
            $durum = $m2BirimFiyat > $piyasaOrtalamasi ? 'üstünde' : ($m2BirimFiyat < $piyasaOrtalamasi * 0.8 ? 'altında' : 'ortalamada');

            return ResponseService::success([
                'm2_birim_fiyat' => $m2BirimFiyat,
                'formatted' => $formatted,
                'satis_fiyati' => $satisFiyati,
                'brut_m2' => $brutM2,
                'formula' => "{$satisFiyati} / {$brutM2} = {$m2BirimFiyat}",
                'piyasa_analizi' => [
                    'durum' => $durum,
                    'piyasa_ortalamasi' => $piyasaOrtalamasi,
                    'fark_yuzdesi' => round((($m2BirimFiyat - $piyasaOrtalamasi) / $piyasaOrtalamasi) * 100, 2),
                ],
            ], 'm² birim fiyatı başarıyla hesaplandı');
        } catch (\Exception $e) {
            return ResponseService::serverError(
                'Konut metrikleri hesaplanırken bir hata oluştu: ' . $e->getMessage(),
                $e
            );
        }
    }
}
