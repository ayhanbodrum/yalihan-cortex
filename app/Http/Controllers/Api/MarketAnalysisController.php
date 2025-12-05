<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Intelligence\TKGMLearningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * 📊 MARKET ANALYSIS API CONTROLLER
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 *
 * TKGM Learning Engine ile pazar analizi API endpoint'leri
 *
 * @author Yalihan AI Team
 * @version 1.0.0
 * @date 2025-12-05
 *
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 */
class MarketAnalysisController extends Controller
{
    protected TKGMLearningService $learningEngine;

    public function __construct(TKGMLearningService $learningEngine)
    {
        $this->learningEngine = $learningEngine;
    }

    /**
     * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     * 💰 FİYAT TAHMİNİ
     * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     *
     * POST /api/v1/market-analysis/predict-price
     *
     * Request:
     * {
     *   "il_id": 48,
     *   "ilce_id": 341,
     *   "alan_m2": 1600,
     *   "kaks": 0.50
     * }
     *
     * Response:
     * {
     *   "success": true,
     *   "prediction": {
     *     "min": 10500000,
     *     "max": 12500000,
     *     "recommended": 11500000,
     *     "unit_price": 6850,
     *     "confidence": 75,
     *     "based_on": "12 satış analizi"
     *   }
     * }
     */
    public function predictPrice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'il_id' => 'required|integer|exists:iller,id',
            'ilce_id' => 'nullable|integer|exists:ilceler,id',
            'mahalle_id' => 'nullable|integer|exists:mahalleler,id',
            'alan_m2' => 'required|numeric|min:0',
            'kaks' => 'nullable|numeric|min:0|max:5',
            'imar_statusu' => 'nullable|string',
        ]);

        try {
            $prediction = $this->learningEngine->predictPrice($validated);

            return response()->json([
                'success' => true,
                'prediction' => $prediction,
                'metadata' => [
                    'algorithm' => 'TKGM Learning Engine v1.0',
                    'timestamp' => now()->toIso8601String(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Fiyat tahmini yapılamadı',
                'message' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     * 📊 PAZAR ANALİZİ
     * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     *
     * GET /api/v1/market-analysis/{il_id}/{ilce_id?}
     *
     * Response:
     * {
     *   "success": true,
     *   "analysis": {
     *     "summary": {...},
     *     "kaks_analysis": {...},
     *     "velocity_analysis": {...},
     *     "trend_analysis": {...}
     *   }
     * }
     */
    public function getAnalysis(Request $request, int $ilId, ?int $ilceId = null): JsonResponse
    {
        try {
            // İl kontrolü
            if (!\App\Models\Il::where('id', $ilId)->exists()) {
                return response()->json([
                    'success' => false,
                    'error' => 'İl bulunamadı',
                ], 404);
            }

            // İlçe kontrolü (opsiyonel)
            if ($ilceId && !\App\Models\Ilce::where('id', $ilceId)->exists()) {
                return response()->json([
                    'success' => false,
                    'error' => 'İlçe bulunamadı',
                ], 404);
            }

            $analysis = $this->learningEngine->getMarketAnalysis($ilId, $ilceId);

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'location' => [
                    'il_id' => $ilId,
                    'ilce_id' => $ilceId,
                ],
                'metadata' => [
                    'generated_at' => now()->toIso8601String(),
                    'cache_ttl' => 3600, // 1 saat
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Pazar analizi yapılamadı',
                'message' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     * 🏆 YATIRIM HOTSPOT'LAR
     * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     *
     * GET /api/v1/market-analysis/hotspots/{il_id}
     *
     * Response:
     * {
     *   "success": true,
     *   "hotspots": [
     *     {
     *       "ilce_id": 341,
     *       "ilce_adi": "Bodrum",
     *       "roi_score": 196,
     *       "avg_unit_price": 7450,
     *       "avg_days_to_sell": 38,
     *       "sample_count": 8
     *     }
     *   ]
     * }
     */
    public function getHotspots(int $ilId): JsonResponse
    {
        try {
            // İl kontrolü
            if (!\App\Models\Il::where('id', $ilId)->exists()) {
                return response()->json([
                    'success' => false,
                    'error' => 'İl bulunamadı',
                ], 404);
            }

            // Her ilçe için ROI hesapla
            $hotspots = \App\Models\TkgmQuery::where('il_id', $ilId)
                ->sold()
                ->active()
                ->whereNotNull('satis_fiyati')
                ->whereNotNull('alan_m2')
                ->whereNotNull('satis_suresi_gun')
                ->whereNotNull('ilce_id')
                ->selectRaw('
                    ilce_id,
                    COUNT(*) as sample_count,
                    AVG(satis_fiyati / alan_m2) as avg_unit_price,
                    AVG(satis_suresi_gun) as avg_days_to_sell,
                    (AVG(satis_fiyati / alan_m2) / AVG(satis_suresi_gun)) * 100 as roi_score
                ')
                ->groupBy('ilce_id')
                ->having('sample_count', '>=', 3)
                ->orderByDesc('roi_score')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    $ilce = \App\Models\Ilce::find($item->ilce_id);
                    return [
                        'ilce_id' => $item->ilce_id,
                        'ilce_adi' => $ilce->ilce_adi ?? 'Bilinmeyen',
                        'roi_score' => round($item->roi_score, 2),
                        'avg_unit_price' => round($item->avg_unit_price, 2),
                        'avg_days_to_sell' => round($item->avg_days_to_sell),
                        'sample_count' => $item->sample_count,
                        'rating' => $this->getRatingFromROI($item->roi_score),
                    ];
                });

            return response()->json([
                'success' => true,
                'hotspots' => $hotspots,
                'total_count' => $hotspots->count(),
                'metadata' => [
                    'il_id' => $ilId,
                    'generated_at' => now()->toIso8601String(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Hotspot analizi yapılamadı',
                'message' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     * 📈 İSTATİSTİKLER
     * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     *
     * GET /api/v1/market-analysis/stats
     *
     * Response:
     * {
     *   "success": true,
     *   "stats": {
     *     "total_queries": 1250,
     *     "total_sales": 458,
     *     "active_patterns": 24,
     *     "high_confidence_patterns": 18,
     *     "avg_confidence": 78.5
     *   }
     * }
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'total_queries' => \App\Models\TkgmQuery::active()->count(),
                'total_sales' => \App\Models\TkgmQuery::sold()->active()->count(),
                'active_patterns' => \App\Models\TkgmLearningPattern::active()->count(),
                'high_confidence_patterns' => \App\Models\TkgmLearningPattern::active()
                    ->highConfidence()
                    ->count(),
                'avg_confidence' => \App\Models\TkgmLearningPattern::active()
                    ->avg('confidence_level'),
                'latest_query' => \App\Models\TkgmQuery::active()
                    ->latest('queried_at')
                    ->first()
                    ?->queried_at
                    ?->toIso8601String(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'metadata' => [
                    'generated_at' => now()->toIso8601String(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'İstatistikler alınamadı',
                'message' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * ROI skorundan rating hesapla
     */
    private function getRatingFromROI(float $roiScore): string
    {
        if ($roiScore >= 150) {
            return 'A+'; // Çok sıcak
        } elseif ($roiScore >= 100) {
            return 'A'; // Sıcak
        } elseif ($roiScore >= 80) {
            return 'B'; // İyi
        } elseif ($roiScore >= 50) {
            return 'C'; // Orta
        } else {
            return 'D'; // Soğuk
        }
    }
}
