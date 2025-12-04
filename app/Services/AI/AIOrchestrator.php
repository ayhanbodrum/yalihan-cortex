<?php

namespace App\Services\AI;

use App\Models\Ilan;
use App\Models\Talep;
use App\Modules\Finans\Services\FinansService;
use App\Services\AIService;
use App\Services\Logging\LogService;
use App\Services\Integrations\TKGMService;

/**
 * Yalihan Cortex - Merkezi Zeka Sistemi
 *
 * Context7 Standardı: C7-YALIHAN-CORTEX-2025-11-26
 *
 * Projenin tüm AI zekasını yöneten merkezi "Beyin" sistemi.
 * Tüm AI servislerini koordine eder, akıllı kararlar verir ve
 * sürekli öğrenir.
 *
 * @package App\Services\AI
 */
class YalihanCortex
{
    /**
     * Property Matcher AI Service
     */
    protected SmartPropertyMatcherAI $propertyMatcher;

    /**
     * Churn Risk Service
     */
    protected KisiChurnService $churnService;

    /**
     * Financial Analysis Service
     */
    protected FinansService $finansService;

    /**
     * TKGM (Tapu Kadastro) Service
     */
    protected TKGMService $tkgmService;

    /**
     * AI Content Generation Service
     */
    protected AIService $aiService;

    /**
     * Fallback providers (yedek sistemler)
     */
    protected array $fallbackProviders = [
        'ollama' => ['deepseek', 'openai', 'gemini'],
        'openai' => ['deepseek', 'ollama', 'gemini'],
        'gemini' => ['openai', 'deepseek', 'ollama'],
    ];

    /**
     * Constructor - Dependency Injection
     */
    public function __construct(
        SmartPropertyMatcherAI $propertyMatcher,
        KisiChurnService $churnService,
        FinansService $finansService,
        TKGMService $tkgmService,
        AIService $aiService
    ) {
        $this->propertyMatcher = $propertyMatcher;
        $this->churnService = $churnService;
        $this->finansService = $finansService;
        $this->tkgmService = $tkgmService;
        $this->aiService = $aiService;
    }

    /**
     * Talep için zenginleştirilmiş eşleştirme
     *
     * Churn skoru + Match skoru ile kapsamlı analiz yapar
     *
     * @param Talep $talep
     * @param array $options
     * @return array
     */
    public function matchForSale(Talep $talep, array $options = []): array
    {
        $startTime = microtime(true);

        try {
            LogService::ai(
                'yalihan_cortex_match_started',
                'YalihanCortex',
                [
                    'talep_id' => $talep->id,
                    'talep_baslik' => $talep->baslik,
                    'kisi_id' => $talep->kisi_id,
                ]
            );

            $result = [
                'talep_id' => $talep->id,
                'kisi_id' => $talep->kisi_id,
                'churn_analysis' => null,
                'matches' => [],
                'recommendations' => [],
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'algorithm' => 'YalihanCortex v1.0',
                ],
            ];

            // 1. Churn Risk Analizi (Eğer kişi varsa)
            if ($talep->kisi_id && $talep->kisi) {
                $churnRisk = $this->churnService->calculateChurnRisk($talep->kisi);
                $result['churn_analysis'] = [
                    'risk_score' => $churnRisk['score'],
                    'risk_level' => $this->getRiskLevel($churnRisk['score']),
                    'breakdown' => $churnRisk['breakdown'],
                    'recommendation' => $this->getChurnRecommendation($churnRisk['score']),
                ];
            }

            // 2. Property Matching
            $matches = $this->propertyMatcher->match($talep);
            $result['matches'] = $this->enrichMatches($matches, $talep);

            // 3. Akıllı Öneriler
            $result['recommendations'] = $this->generateRecommendations($talep, $result);

            // 4. Performans metrikleri
            $duration = microtime(true) - $startTime;
            $result['metadata']['duration_ms'] = round($duration * 1000, 2);
            $result['metadata']['matches_count'] = count($result['matches']);
            $result['metadata']['success'] = true;

            LogService::ai(
                'yalihan_cortex_match_completed',
                'YalihanCortex',
                [
                    'talep_id' => $talep->id,
                    'matches_count' => count($result['matches']),
                    'duration_ms' => $result['metadata']['duration_ms'],
                ]
            );

            return $result;
        } catch (\Exception $e) {
            LogService::error(
                'YalihanCortex match failed',
                [
                    'talep_id' => $talep->id,
                    'error' => $e->getMessage(),
                ],
                $e,
                LogService::CHANNEL_AI
            );

            return [
                'talep_id' => $talep->id,
                'success' => false,
                'error' => $e->getMessage(),
                'matches' => [],
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'algorithm' => 'YalihanCortex v1.0',
                ],
            ];
        }
    }

    /**
     * İlan için kapsamlı değerleme
     *
     * TKGM (Tapu) + Finansal analiz ile tam değerleme
     *
     * @param Ilan $ilan
     * @param array $options
     * @return array
     */
    public function priceValuation(Ilan $ilan, array $options = []): array
    {
        $startTime = microtime(true);

        try {
            LogService::ai(
                'yalihan_cortex_valuation_started',
                'YalihanCortex',
                [
                    'ilan_id' => $ilan->id,
                    'ilan_baslik' => $ilan->baslik,
                ]
            );

            $result = [
                'ilan_id' => $ilan->id,
                'valuation' => [
                    'market_value' => null,
                    'tkgm_data' => null,
                    'financial_analysis' => null,
                    'confidence_score' => 0,
                ],
                'recommendations' => [],
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'algorithm' => 'YalihanCortex v1.0',
                ],
            ];

            // 1. TKGM Parsel Bilgileri (Eğer arsa ise)
            if ($ilan->ada_no && $ilan->parsel_no && $ilan->il_id && $ilan->ilce_id) {
                try {
                    $tkgmData = $this->tkgmService->parselSorgula(
                        $ilan->ada_no,
                        $ilan->parsel_no,
                        $ilan->il_id,
                        $ilan->ilce_id,
                        $ilan->mahalle_id
                    );

                    if ($tkgmData['success'] ?? false) {
                        $result['valuation']['tkgm_data'] = $tkgmData;
                        $result['valuation']['confidence_score'] += 30;
                    }
                } catch (\Exception $e) {
                    LogService::warning('TKGM sorgulama hatası', [
                        'ilan_id' => $ilan->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // 2. Finansal Analiz
            try {
                $financialData = [
                    'ilan_fiyati' => $ilan->fiyat,
                    'para_birimi' => $ilan->para_birimi ?? 'TRY',
                    'metrekare' => $ilan->metrekare,
                    'oda_sayisi' => $ilan->oda_sayisi,
                    'yas' => $ilan->yas,
                ];

                $financialAnalysis = $this->finansService->analyzeFinancials($financialData, [
                    'ilan_id' => $ilan->id,
                    'type' => 'property_valuation',
                ]);

                if ($financialAnalysis['success'] ?? false) {
                    $result['valuation']['financial_analysis'] = $financialAnalysis;
                    $result['valuation']['confidence_score'] += 40;
                }
            } catch (\Exception $e) {
                LogService::warning('Finansal analiz hatası', [
                    'ilan_id' => $ilan->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // 3. Piyasa Değeri Hesaplama
            $result['valuation']['market_value'] = $this->calculateMarketValue($ilan, $result);

            // 4. Güven Skoru Normalizasyonu (0-100)
            $result['valuation']['confidence_score'] = min(100, $result['valuation']['confidence_score']);

            // 5. Öneriler
            $result['recommendations'] = $this->generateValuationRecommendations($ilan, $result);

            // 6. Performans metrikleri
            $duration = microtime(true) - $startTime;
            $result['metadata']['duration_ms'] = round($duration * 1000, 2);
            $result['metadata']['success'] = true;

            LogService::ai(
                'yalihan_cortex_valuation_completed',
                'YalihanCortex',
                [
                    'ilan_id' => $ilan->id,
                    'confidence_score' => $result['valuation']['confidence_score'],
                    'duration_ms' => $result['metadata']['duration_ms'],
                ]
            );

            return $result;
        } catch (\Exception $e) {
            LogService::error(
                'YalihanCortex valuation failed',
                [
                    'ilan_id' => $ilan->id,
                    'error' => $e->getMessage(),
                ],
                $e,
                LogService::CHANNEL_AI
            );

            return [
                'ilan_id' => $ilan->id,
                'success' => false,
                'error' => $e->getMessage(),
                'valuation' => [
                    'market_value' => null,
                    'confidence_score' => 0,
                ],
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'algorithm' => 'YalihanCortex v1.0',
                ],
            ];
        }
    }

    /**
     * Fallback yönetimi
     *
     * Bir provider çökerse otomatik yedek sisteme geçer
     *
     * @param string $provider
     * @param array $data
     * @param callable $callback
     * @return array
     */
    public function handleFallback(string $provider, array $data, callable $callback): array
    {
        $fallbackProviders = $this->fallbackProviders[$provider] ?? ['deepseek', 'openai', 'ollama'];

        foreach ($fallbackProviders as $fallbackProvider) {
            try {
                LogService::info("AI Provider fallback attempt", [
                    'original' => $provider,
                    'fallback' => $fallbackProvider,
                ]);

                // Fallback provider ile dene
                $result = $callback($fallbackProvider);

                if ($result['success'] ?? false) {
                    LogService::info("AI Provider fallback success", [
                        'original' => $provider,
                        'fallback' => $fallbackProvider,
                    ]);

                    return array_merge($result, [
                        'metadata' => array_merge($result['metadata'] ?? [], [
                            'original_provider' => $provider,
                            'fallback_provider' => $fallbackProvider,
                            'used_fallback' => true,
                        ]),
                    ]);
                }
            } catch (\Exception $e) {
                LogService::warning("AI Provider fallback failed", [
                    'original' => $provider,
                    'fallback' => $fallbackProvider,
                    'error' => $e->getMessage(),
                ]);

                continue; // Sonraki fallback'i dene
            }
        }

        // Tüm fallback'ler başarısız
        return [
            'success' => false,
            'error' => 'All AI providers failed',
            'metadata' => [
                'original_provider' => $provider,
                'tried_fallbacks' => $fallbackProviders,
            ],
        ];
    }

    /**
     * Context analizi
     *
     * Sistem genel durumunu analiz eder
     *
     * @return array
     */
    public function analyzeContext(): array
    {
        try {
            $activeTalepler = Talep::where('status', 'Aktif')->count();
            $activeIlanlar = Ilan::where('status', 'Aktif')->count();

            // Son 24 saatteki eşleştirmeler
            $recentMatches = \DB::table('ai_logs')
                ->where('action_type', 'property_matching_completed')
                ->where('created_at', '>=', now()->subDay())
                ->count();

            return [
                'active_talepler' => $activeTalepler,
                'active_ilanlar' => $activeIlanlar,
                'recent_matches' => $recentMatches,
                'match_ratio' => $activeIlanlar > 0 ? round(($activeTalepler / $activeIlanlar) * 100, 2) : 0,
                'system_health' => $this->calculateSystemHealth(),
                'analyzed_at' => now()->toISOString(),
            ];
        } catch (\Exception $e) {
            LogService::error('Context analysis failed', ['error' => $e->getMessage()], $e);

            return [
                'error' => $e->getMessage(),
                'analyzed_at' => now()->toISOString(),
            ];
        }
    }

    /**
     * Performans izleme
     *
     * Tüm AI servislerinin performansını izler
     *
     * @return array
     */
    public function getPerformance(): array
    {
        try {
            // Son 24 saatteki AI loglarını analiz et
            $logs = \DB::table('ai_logs')
                ->where('created_at', '>=', now()->subDay())
                ->get();

            $performance = [
                'total_requests' => $logs->count(),
                'success_rate' => 0,
                'avg_duration_ms' => 0,
                'services' => [],
            ];

            if ($logs->count() > 0) {
                $successful = $logs->where('status', 'success')->count();
                $performance['success_rate'] = round(($successful / $logs->count()) * 100, 2);

                $avgDuration = $logs->avg('duration_ms');
                $performance['avg_duration_ms'] = round($avgDuration ?? 0, 2);
            }

            return $performance;
        } catch (\Exception $e) {
            LogService::error('Performance monitoring failed', ['error' => $e->getMessage()], $e);

            return [
                'error' => $e->getMessage(),
                'monitored_at' => now()->toISOString(),
            ];
        }
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Risk seviyesi belirleme
     */
    private function getRiskLevel(int $score): string
    {
        if ($score >= 70) {
            return 'high';
        } elseif ($score >= 40) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Churn risk önerisi
     */
    private function getChurnRecommendation(int $score): string
    {
        if ($score >= 70) {
            return 'Acil müdahale gerekli. Müşteri ile hemen iletişime geçin.';
        } elseif ($score >= 40) {
            return 'Dikkatli takip edilmeli. Proaktif iletişim önerilir.';
        } else {
            return 'Düşük risk. Normal takip yeterli.';
        }
    }

    /**
     * Eşleşmeleri zenginleştir
     */
    private function enrichMatches(array $matches, Talep $talep): array
    {
        return collect($matches)->map(function ($match) use ($talep) {
            $ilan = $match['ilan'];

            return [
                'ilan_id' => $ilan->id,
                'baslik' => $ilan->baslik,
                'fiyat' => $ilan->fiyat,
                'para_birimi' => $ilan->para_birimi ?? 'TRY',
                'match_score' => $match['score'],
                'match_level' => $this->getMatchLevel($match['score']),
                'reasons' => $match['reasons'] ?? [],
                'breakdown' => $match['breakdown'] ?? [],
                'priority' => $this->calculatePriority($match, $talep),
            ];
        })->toArray();
    }

    /**
     * Eşleşme seviyesi
     */
    private function getMatchLevel(float $score): string
    {
        if ($score >= 85) {
            return 'excellent';
        } elseif ($score >= 70) {
            return 'good';
        } elseif ($score >= 50) {
            return 'fair';
        } else {
            return 'poor';
        }
    }

    /**
     * Öncelik hesaplama
     */
    private function calculatePriority(array $match, Talep $talep): int
    {
        $priority = 0;

        // Yüksek skor = yüksek öncelik
        $priority += (int) ($match['score'] / 10);

        // Churn riski varsa öncelik artar
        if ($talep->kisi_id) {
            // Burada churn skorunu kullanabiliriz
        }

        return min(10, $priority);
    }

    /**
     * Öneriler oluştur
     */
    private function generateRecommendations(Talep $talep, array $result): array
    {
        $recommendations = [];

        // Churn riski yüksekse
        if (($result['churn_analysis']['risk_score'] ?? 0) >= 70) {
            $recommendations[] = [
                'type' => 'urgent',
                'title' => 'Yüksek Churn Riski',
                'message' => 'Müşteri ile acil iletişime geçin.',
                'action' => 'contact_customer',
            ];
        }

        // Eşleşme yoksa
        if (empty($result['matches'])) {
            $recommendations[] = [
                'type' => 'info',
                'title' => 'Eşleşme Bulunamadı',
                'message' => 'Kriterleri genişletmeyi düşünün.',
                'action' => 'expand_criteria',
            ];
        }

        // Yüksek skorlu eşleşmeler varsa
        $highScoreMatches = collect($result['matches'])->where('match_score', '>=', 85)->count();
        if ($highScoreMatches > 0) {
            $recommendations[] = [
                'type' => 'success',
                'title' => 'Mükemmel Eşleşmeler Bulundu',
                'message' => "{$highScoreMatches} adet yüksek kaliteli eşleşme mevcut.",
                'action' => 'review_matches',
            ];
        }

        return $recommendations;
    }

    /**
     * Piyasa değeri hesaplama
     */
    private function calculateMarketValue(Ilan $ilan, array $valuationData): ?float
    {
        // Basit hesaplama: İlan fiyatı + finansal analiz + TKGM verileri
        $basePrice = $ilan->fiyat ?? 0;

        // Finansal analiz varsa ayarlama yap
        if (isset($valuationData['valuation']['financial_analysis']['insights'])) {
            // Burada daha kompleks hesaplama yapılabilir
        }

        return $basePrice > 0 ? round($basePrice, 2) : null;
    }

    /**
     * Değerleme önerileri
     */
    private function generateValuationRecommendations(Ilan $ilan, array $result): array
    {
        $recommendations = [];

        $confidence = $result['valuation']['confidence_score'] ?? 0;

        if ($confidence < 50) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'Düşük Güven Skoru',
                'message' => 'Daha fazla veri toplanması önerilir.',
            ];
        }

        if ($result['valuation']['tkgm_data'] === null) {
            $recommendations[] = [
                'type' => 'info',
                'title' => 'TKGM Verisi Eksik',
                'message' => 'Parsel bilgileri eklenirse değerleme daha doğru olur.',
            ];
        }

        return $recommendations;
    }

    /**
     * Sistem sağlığı hesaplama
     */
    private function calculateSystemHealth(): string
    {
        try {
            $performance = $this->getPerformance();
            $successRate = $performance['success_rate'] ?? 0;

            if ($successRate >= 95) {
                return 'excellent';
            } elseif ($successRate >= 85) {
                return 'good';
            } elseif ($successRate >= 70) {
                return 'fair';
            } else {
                return 'poor';
            }
        } catch (\Exception $e) {
            return 'unknown';
        }
    }
}
