<?php

namespace App\Services\AI;

use App\Models\AiLog;
use App\Models\Ilan;
use App\Models\Talep;
use App\Modules\Finans\Services\FinansService;
use App\Services\AIService;
use App\Services\Logging\LogService;
use App\Services\TKGMService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
     * @CortexDecision
     * Churn skoru + Match skoru ile kapsamlı analiz yapar
     *
     * Context7: MCP uyumluluğu için timer ve AiLog kayıtları
     *
     * @param Talep $talep
     * @param array $options
     * @return array
     */
    public function matchForSale(Talep $talep, array $options = []): array
    {
        // MCP uyumluluğu: Timer başlat
        $startTime = LogService::startTimer('yalihan_cortex_match_for_sale');

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
            $churnScore = 0;
            if ($talep->kisi_id && $talep->kisi) {
                $churnRisk = $this->churnService->calculateChurnRisk($talep->kisi);
                $churnScore = $churnRisk['score']; // 0-100 arası churn skoru
                $result['churn_analysis'] = [
                    'risk_score' => $churnRisk['score'],
                    'risk_level' => $this->getRiskLevel($churnRisk['score']),
                    'breakdown' => $churnRisk['breakdown'],
                    'recommendation' => $this->getChurnRecommendation($churnRisk['score']),
                ];
            }

            // 2. Property Matching
            $matches = $this->propertyMatcher->match($talep);
            $result['matches'] = $this->enrichMatches($matches, $talep, $churnScore);

            // 3. Akıllı Öneriler
            $result['recommendations'] = $this->generateRecommendations($talep, $result);

            // 4. Performans metrikleri
            $durationMs = LogService::stopTimer($startTime);
            $result['metadata']['duration_ms'] = $durationMs;
            $result['metadata']['matches_count'] = count($result['matches']);
            $result['metadata']['success'] = true;

            // MCP uyumluluğu: AiLog'a kayıt ekle
            $this->logCortexDecision('match_for_sale', [
                'talep_id' => $talep->id,
                'kisi_id' => $talep->kisi_id,
                'matches_count' => count($result['matches']),
                'churn_score' => $churnScore,
            ], $durationMs, true);

            LogService::ai(
                'yalihan_cortex_match_completed',
                'YalihanCortex',
                [
                    'talep_id' => $talep->id,
                    'matches_count' => count($result['matches']),
                    'duration_ms' => $durationMs,
                ]
            );

            return $result;
        } catch (\Exception $e) {
            $durationMs = LogService::stopTimer($startTime);

            // MCP uyumluluğu: Hata durumunda da AiLog'a kayıt ekle
            $this->logCortexDecision('match_for_sale', [
                'talep_id' => $talep->id,
                'kisi_id' => $talep->kisi_id,
                'error' => $e->getMessage(),
            ], $durationMs, false);

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
                    'duration_ms' => $durationMs,
                ],
            ];
        }
    }

    /**
     * İlan için kapsamlı değerleme
     *
     * @CortexDecision
     * TKGM (Tapu) + Finansal analiz ile tam değerleme
     *
     * Context7: MCP uyumluluğu için timer ve AiLog kayıtları
     *
     * @param Ilan $ilan
     * @param array $options
     * @return array
     */
    public function priceValuation(Ilan $ilan, array $options = []): array
    {
        // MCP uyumluluğu: Timer başlat
        $startTime = LogService::startTimer('yalihan_cortex_price_valuation');

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
            $durationMs = LogService::stopTimer($startTime);
            $result['metadata']['duration_ms'] = $durationMs;
            $result['metadata']['success'] = true;

            // MCP uyumluluğu: AiLog'a kayıt ekle
            $this->logCortexDecision('price_valuation', [
                'ilan_id' => $ilan->id,
                'confidence_score' => $result['valuation']['confidence_score'],
                'market_value' => $result['valuation']['market_value'],
            ], $durationMs, true);

            LogService::ai(
                'yalihan_cortex_valuation_completed',
                'YalihanCortex',
                [
                    'ilan_id' => $ilan->id,
                    'confidence_score' => $result['valuation']['confidence_score'],
                    'duration_ms' => $durationMs,
                ]
            );

            return $result;
        } catch (\Exception $e) {
            $durationMs = LogService::stopTimer($startTime);

            // MCP uyumluluğu: Hata durumunda da AiLog'a kayıt ekle
            $this->logCortexDecision('price_valuation', [
                'ilan_id' => $ilan->id,
                'error' => $e->getMessage(),
            ], $durationMs, false);

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
                    'duration_ms' => $durationMs,
                ],
            ];
        }
    }

    /**
     * Churn Risk Analizi
     *
     * @CortexDecision
     * Müşteri kaybı riskini hesaplar ve analiz eder
     *
     * Context7: MCP uyumluluğu için timer ve AiLog kayıtları
     *
     * @param \App\Models\Kisi $kisi
     * @param array $options
     * @return array
     */
    public function calculateChurnRisk(\App\Models\Kisi $kisi, array $options = []): array
    {
        // MCP uyumluluğu: Timer başlat
        $startTime = LogService::startTimer('yalihan_cortex_churn_risk');

        try {
            LogService::ai(
                'yalihan_cortex_churn_risk_started',
                'YalihanCortex',
                [
                    'kisi_id' => $kisi->id,
                    'kisi_adi' => $kisi->tam_ad ?? 'Bilinmiyor',
                ]
            );

            // Churn risk hesaplama
            $churnRisk = $this->churnService->calculateChurnRisk($kisi);

            $result = [
                'kisi_id' => $kisi->id,
                'risk_score' => $churnRisk['score'],
                'risk_level' => $this->getRiskLevel($churnRisk['score']),
                'breakdown' => $churnRisk['breakdown'],
                'recommendation' => $this->getChurnRecommendation($churnRisk['score']),
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'algorithm' => 'YalihanCortex v1.0',
                ],
            ];

            // Performans metrikleri
            $durationMs = LogService::stopTimer($startTime);
            $result['metadata']['duration_ms'] = $durationMs;
            $result['metadata']['success'] = true;

            // MCP uyumluluğu: AiLog'a kayıt ekle
            $this->logCortexDecision('churn_risk', [
                'kisi_id' => $kisi->id,
                'risk_score' => $churnRisk['score'],
                'risk_level' => $result['risk_level'],
            ], $durationMs, true);

            LogService::ai(
                'yalihan_cortex_churn_risk_completed',
                'YalihanCortex',
                [
                    'kisi_id' => $kisi->id,
                    'risk_score' => $churnRisk['score'],
                    'duration_ms' => $durationMs,
                ]
            );

            return $result;
        } catch (\Exception $e) {
            $durationMs = LogService::stopTimer($startTime);

            // MCP uyumluluğu: Hata durumunda da AiLog'a kayıt ekle
            $this->logCortexDecision('churn_risk', [
                'kisi_id' => $kisi->id,
                'error' => $e->getMessage(),
            ], $durationMs, false);

            LogService::error(
                'YalihanCortex churn risk calculation failed',
                [
                    'kisi_id' => $kisi->id,
                    'error' => $e->getMessage(),
                ],
                $e,
                LogService::CHANNEL_AI
            );

            return [
                'kisi_id' => $kisi->id,
                'success' => false,
                'error' => $e->getMessage(),
                'risk_score' => 0,
                'risk_level' => 'unknown',
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'algorithm' => 'YalihanCortex v1.0',
                    'duration_ms' => $durationMs,
                ],
            ];
        }
    }

    /**
     * Top Churn Risks Analizi
     *
     * @CortexDecision
     * En yüksek churn riskine sahip müşterileri listeler
     *
     * Context7: MCP uyumluluğu için timer ve AiLog kayıtları
     *
     * @param int $limit
     * @param int|null $userId
     * @return array
     */
    public function getTopChurnRisks(int $limit = 10, ?int $userId = null): array
    {
        // MCP uyumluluğu: Timer başlat
        $startTime = LogService::startTimer('yalihan_cortex_top_churn_risks');

        try {
            LogService::ai(
                'yalihan_cortex_top_churn_risks_started',
                'YalihanCortex',
                [
                    'limit' => $limit,
                    'user_id' => $userId,
                ]
            );

            // Limit validasyonu
            $limit = max(1, min(50, $limit));

            // Aktif müşterileri çek
            $candidates = \App\Models\Kisi::query()
                ->where(function ($q) {
                    $q->whereRaw('LOWER(COALESCE(segment, "")) = ?', ['aktif'])
                        ->orWhereIn('status', ['Aktif', 1, true]);
                })
                ->orderByDesc('updated_at')
                ->limit(500)
                ->get(['id', 'ad', 'soyad', 'segment', 'pipeline_stage', 'danisman_id']);

            // Her müşteri için churn risk hesapla (Cortex üzerinden)
            $scored = $candidates->map(function (\App\Models\Kisi $kisi) {
                $cortexResult = $this->calculateChurnRisk($kisi);

                return [
                    'id' => $kisi->id,
                    'ad' => $kisi->ad,
                    'soyad' => $kisi->soyad,
                    'score' => $cortexResult['risk_score'] ?? 0,
                    'risk_level' => $cortexResult['risk_level'] ?? 'unknown',
                    'segment' => $kisi->segment,
                    'pipeline_stage' => $kisi->pipeline_stage,
                ];
            })
                ->filter(function ($item) {
                    // Başarısız hesaplamaları filtrele
                    return isset($item['score']) && $item['score'] >= 0;
                })
                ->sortByDesc('score')
                ->values()
                ->take($limit);

            $result = [
                'customers' => $scored->toArray(),
                'count' => $scored->count(),
                'metadata' => [
                    'provider' => 'YalihanCortex',
                    'normalized' => true,
                    'sample_size' => $candidates->count(),
                    'limit' => $limit,
                    'processed_at' => now()->toISOString(),
                    'algorithm' => 'YalihanCortex v1.0',
                ],
            ];

            // Performans metrikleri
            $durationMs = LogService::stopTimer($startTime);
            $result['metadata']['duration_ms'] = $durationMs;
            $result['metadata']['success'] = true;

            // MCP uyumluluğu: AiLog'a kayıt ekle
            $this->logCortexDecision('top_churn_risks', [
                'limit' => $limit,
                'user_id' => $userId,
                'customers_count' => $scored->count(),
                'sample_size' => $candidates->count(),
            ], $durationMs, true);

            LogService::ai(
                'yalihan_cortex_top_churn_risks_completed',
                'YalihanCortex',
                [
                    'limit' => $limit,
                    'customers_count' => $scored->count(),
                    'duration_ms' => $durationMs,
                ]
            );

            return $result;
        } catch (\Exception $e) {
            $durationMs = LogService::stopTimer($startTime);

            // MCP uyumluluğu: Hata durumunda da AiLog'a kayıt ekle
            $this->logCortexDecision('top_churn_risks', [
                'limit' => $limit,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ], $durationMs, false);

            LogService::error(
                'YalihanCortex top churn risks calculation failed',
                [
                    'limit' => $limit,
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ],
                $e,
                LogService::CHANNEL_AI
            );

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'customers' => [],
                'count' => 0,
                'metadata' => [
                    'provider' => 'YalihanCortex',
                    'processed_at' => now()->toISOString(),
                    'algorithm' => 'YalihanCortex v1.0',
                    'duration_ms' => $durationMs,
                ],
            ];
        }
    }

    /**
     * AI Feedback Submission
     *
     * Kullanıcı geri bildirimlerini kaydeder ve AI öğrenme döngüsüne katkı sağlar
     * Context7: C7-AI-FEEDBACK-2025-11-25
     *
     * @param int $aiLogId
     * @param array $feedbackData
     * @param int|null $userId
     * @return array
     */
    public function submitFeedback(int $aiLogId, array $feedbackData, ?int $userId = null): array
    {
        try {
            // AiLog kaydını bul
            $aiLog = AiLog::find($aiLogId);

            if (! $aiLog) {
                return [
                    'success' => false,
                    'error' => 'AI log kaydı bulunamadı',
                    'log_id' => $aiLogId,
                ];
            }

            // Kullanıcı kontrolü (sadece ilgili danışman feedback verebilir)
            if ($userId && $aiLog->user_id && $aiLog->user_id !== $userId) {
                return [
                    'success' => false,
                    'error' => 'Bu AI log kaydı için geri bildirim verme yetkiniz yok',
                    'log_id' => $aiLogId,
                    'code' => 403,
                ];
            }

            // Feedback verilerini al
            $rating = $feedbackData['rating'] ?? null;
            $feedbackType = $feedbackData['feedback_type'] ?? null;
            $reason = $feedbackData['reason'] ?? null;

            // Validation
            if (! $rating || ! $feedbackType) {
                return [
                    'success' => false,
                    'error' => 'Rating ve feedback_type zorunludur',
                    'log_id' => $aiLogId,
                ];
            }

            // Geri bildirimi kaydet
            $aiLog->update([
                'user_rating' => $rating,
                'feedback_type' => $feedbackType,
                'feedback_reason' => $reason,
                'feedback_at' => now(),
            ]);

            // LogService ile geri bildirimi logla
            LogService::action(
                'ai_feedback_submitted',
                'ai_log',
                $aiLog->id,
                [
                    'user_id' => $userId ?? $aiLog->user_id,
                    'rating' => $rating,
                    'feedback_type' => $feedbackType,
                    'reason_length' => $reason ? strlen($reason) : 0,
                    'provider' => $aiLog->provider,
                    'request_type' => $aiLog->request_type,
                    'content_type' => $aiLog->content_type,
                    'content_id' => $aiLog->content_id,
                ],
                LogService::LEVEL_INFO
            );

            return [
                'success' => true,
                'log_id' => $aiLog->id,
                'rating' => $rating,
                'feedback_type' => $feedbackType,
                'message' => 'Geri bildirim başarıyla kaydedildi',
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'provider' => $aiLog->provider,
                    'request_type' => $aiLog->request_type,
                ],
            ];
        } catch (\Exception $e) {
            LogService::error(
                'YalihanCortex feedback submission failed',
                [
                    'log_id' => $aiLogId,
                    'error' => $e->getMessage(),
                ],
                $e,
                LogService::CHANNEL_AI
            );

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'log_id' => $aiLogId,
            ];
        }
    }

    /**
     * Fallback yönetimi
     *
     * Bir provider çökerse otomatik yedek sisteme geçer
     * Context7: ProviderException yakalama ve kural tabanlı çözüm
     *
     * @param string $provider
     * @param array $data
     * @return array
     */
    public function handleFallback(string $provider, array $data): array
    {
        $fallbackProviders = $this->fallbackProviders[$provider] ?? ['deepseek', 'openai', 'ollama'];

        // ProviderException kontrolü için özel exception sınıfı
        $providerExceptionClass = "App\\Exceptions\\ProviderException";
        $hasProviderException = class_exists($providerExceptionClass);

        foreach ($fallbackProviders as $fallbackProvider) {
            try {
                LogService::info("AI Provider fallback attempt", [
                    'original' => $provider,
                    'fallback' => $fallbackProvider,
                ]);

                // Kural tabanlı çözüm: Fallback provider ile işlemi dene
                // AIService üzerinden provider değiştir ve tekrar dene
                // Not: AIService provider'ı property olarak tutuyor, switchProvider kullan
                $this->aiService->switchProvider($fallbackProvider);

                try {
                    // Provider'a göre işlem yap (örnek: generate, analyze, suggest)
                    $action = $data['action'] ?? 'generate';
                    $prompt = $data['prompt'] ?? '';
                    $options = $data['options'] ?? [];

                    $result = match ($action) {
                        'generate' => $this->aiService->generate($prompt, $options),
                        'analyze' => $this->aiService->analyze($data, $options),
                        'suggest' => $this->aiService->suggest($data, $options['type'] ?? 'general'),
                        default => ['success' => false, 'error' => 'Unknown action'],
                    };

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
                } catch (\Exception $callbackException) {
                    throw $callbackException;
                }
            } catch (\Exception $e) {
                // ProviderException kontrolü
                if ($hasProviderException && $e instanceof $providerExceptionClass) {
                    LogService::warning("AI ProviderException caught", [
                        'original' => $provider,
                        'fallback' => $fallbackProvider,
                        'error' => $e->getMessage(),
                        'exception_type' => 'ProviderException',
                    ]);
                } else {
                LogService::warning("AI Provider fallback failed", [
                    'original' => $provider,
                    'fallback' => $fallbackProvider,
                    'error' => $e->getMessage(),
                ]);
                }

                continue; // Sonraki fallback'i dene
            }
        }

        // Tüm fallback'ler başarısız - Kural tabanlı çözüm
        return $this->applyRuleBasedFallback($provider, $data);
    }

    /**
     * Kural tabanlı fallback çözümü
     */
    private function applyRuleBasedFallback(string $provider, array $data): array
    {
        LogService::warning("All AI providers failed, applying rule-based fallback", [
            'original_provider' => $provider,
            'data_keys' => array_keys($data),
        ]);

        // Kural tabanlı çözüm: Basit bir response döndür
        return [
            'success' => false,
            'error' => 'All AI providers failed. Rule-based fallback applied.',
            'metadata' => [
                'original_provider' => $provider,
                'fallback_type' => 'rule_based',
                'applied_at' => now()->toISOString(),
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
            $recentMatches = DB::table('ai_logs')
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
            $logs = DB::table('ai_logs')
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
     *
     * KÂR ODAKLI ZEKÂ: Match ve Churn skorlarını birleştirerek Action Score hesaplar
     * Action Score = Match Score + (Churn Score * 0.5)
     * Sadece action_score > 85 olan ilk 5 ilanı döndürür
     */
    private function enrichMatches(array $matches, Talep $talep, float $churnScore = 0): array
    {
        return collect($matches)
            ->map(function ($match) use ($talep, $churnScore) {
            $ilan = $match['ilan'];
                $matchScore = (float) $match['score']; // 0-100 arası match skoru

                // Action Score hesaplama: match_score + (churn_score * 0.5)
                $actionScore = $matchScore + ($churnScore * 0.5);

            return [
                'ilan_id' => $ilan->id,
                'baslik' => $ilan->baslik,
                'fiyat' => $ilan->fiyat,
                'para_birimi' => $ilan->para_birimi ?? 'TRY',
                    'match_score' => round($matchScore, 2), // 0-100 arası
                    'churn_score' => round($churnScore, 2), // 0-100 arası
                    'action_score' => round($actionScore, 2), // Birleşik skor
                    'match_level' => $this->getMatchLevel($matchScore),
                'reasons' => $match['reasons'] ?? [],
                'breakdown' => $match['breakdown'] ?? [],
                    'priority' => $this->calculatePriority($match, $talep, $churnScore),
            ];
            })
            ->filter(function ($match) {
                // Sadece action_score > 85 olanları filtrele
                return ($match['action_score'] ?? 0) > 85;
            })
            ->sortByDesc('action_score') // Action score'a göre sırala
            ->take(5) // İlk 5 ilanı al
            ->values()
            ->toArray();
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
     *
     * Action score'a göre öncelik belirler
     */
    private function calculatePriority(array $match, Talep $talep, float $churnScore = 0): int
    {
        $matchScore = (float) ($match['score'] ?? 0);
        $actionScore = $matchScore + ($churnScore * 0.5);

        // Action score'a göre öncelik (0-10 arası)
        $priority = (int) ($actionScore / 10);

        return min(10, max(0, $priority));
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

    /**
     * Pazarlık Stratejisi Analizi
     *
     * @CortexDecision
     * Müşterinin finansal profili ve davranış kalıplarını analiz ederek
     * pazarlık stratejisi önerisi üretir
     *
     * Context7: MCP uyumluluğu için timer ve AiLog kayıtları
     *
     * @param \App\Models\Kisi $kisi
     * @param array $options
     * @return array
     */
    public function getNegotiationStrategy(\App\Models\Kisi $kisi, array $options = []): array
    {
        // MCP uyumluluğu: Timer başlat
        $startTime = LogService::startTimer('yalihan_cortex_negotiation_strategy');

        try {
            LogService::ai(
                'negotiation_strategy_started',
                'YalihanCortex',
                [
                    'kisi_id' => $kisi->id,
                    'kisi_ad' => $kisi->tam_ad,
                ]
            );

            // 1. Müşteri verilerini topla
            $customerData = [
                'yatirimci_profili' => $kisi->yatirimci_profili?->value ?? 'bilinmiyor',
                'satis_potansiyeli' => $kisi->satis_potansiyeli ?? 0,
                'gelir_duzeyi' => $kisi->gelir_duzeyi ?? 'bilinmiyor',
                'toplam_islem_tutari' => $kisi->toplam_islem_tutari ?? 0,
                'toplam_islem' => $kisi->toplam_islem ?? 0,
                'memnuniyet_skoru' => $kisi->memnuniyet_skoru ?? null,
                'karar_verici_mi' => $kisi->karar_verici_mi ?? true,
                'crm_status' => $kisi->crm_status?->value ?? 'bilinmiyor',
            ];

            // 2. AI'dan pazarlık stratejisi üret
            $prompt = $this->buildNegotiationPrompt($customerData);
            $aiResponse = $this->aiService->generate($prompt, [
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            // 3. AI yanıtını parse et
            $strategy = $this->parseNegotiationResponse($aiResponse, $customerData);

            // 4. Performans metrikleri
            $durationMs = LogService::stopTimer($startTime);

            $result = [
                'kisi_id' => $kisi->id,
                'strategy' => $strategy,
                'customer_profile' => $customerData,
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'algorithm' => 'YalihanCortex v1.0',
                    'duration_ms' => $durationMs,
                    'success' => true,
                ],
            ];

            // MCP uyumluluğu: AiLog'a kayıt ekle
            $this->logCortexDecision('negotiation_strategy', [
                'kisi_id' => $kisi->id,
                'yatirimci_profili' => $customerData['yatirimci_profili'],
                'satis_potansiyeli' => $customerData['satis_potansiyeli'],
            ], $durationMs, true);

            LogService::ai(
                'negotiation_strategy_completed',
                'YalihanCortex',
                [
                    'kisi_id' => $kisi->id,
                    'duration_ms' => $durationMs,
                ]
            );

            return $result;
        } catch (\Exception $e) {
            $durationMs = LogService::stopTimer($startTime);

            // MCP uyumluluğu: Hata durumunda da AiLog'a kayıt ekle
            $this->logCortexDecision('negotiation_strategy', [
                'kisi_id' => $kisi->id,
                'error' => $e->getMessage(),
            ], $durationMs, false);

            LogService::error(
                'YalihanCortex negotiation strategy failed',
                [
                    'kisi_id' => $kisi->id,
                    'error' => $e->getMessage(),
                ],
                $e,
                LogService::CHANNEL_AI
            );

            return [
                'kisi_id' => $kisi->id,
                'success' => false,
                'error' => $e->getMessage(),
                'strategy' => [
                    'summary' => 'Analiz sırasında bir hata oluştu. Lütfen daha sonra tekrar deneyin.',
                    'recommendation' => 'Standart pazarlık stratejisi uygulayın.',
                ],
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'algorithm' => 'YalihanCortex v1.0',
                    'duration_ms' => $durationMs,
                ],
            ];
        }
    }

    /**
     * Pazarlık stratejisi için prompt oluştur
     */
    private function buildNegotiationPrompt(array $customerData): string
    {
        $yatirimciProfili = $customerData['yatirimci_profili'];
        $satisPotansiyeli = $customerData['satis_potansiyeli'];
        $gelirDuzeyi = $customerData['gelir_duzeyi'];
        $toplamIslemTutari = number_format($customerData['toplam_islem_tutari'] ?? 0, 0, ',', '.');
        $kararVericiMi = $customerData['karar_verici_mi'] ? 'Evet' : 'Hayır';

        return <<<PROMPT
Bir emlak danışmanısın. Aşağıdaki müşteri profili için pazarlık stratejisi öner:

**Müşteri Profili:**
- Yatırımcı Profili: {$yatirimciProfili}
- Satış Potansiyeli: {$satisPotansiyeli}/100
- Gelir Düzeyi: {$gelirDuzeyi}
- Toplam İşlem Tutarı: {$toplamIslemTutari} ₺
- Karar Verici: {$kararVericiMi}

**Görev:**
Bu müşteriyle pazarlık yaparken nasıl bir strateji izlemeliyim? Şu konularda öneri ver:
1. İndirim yaklaşımı (agresif mi, yumuşak mı?)
2. Fiyat vurgusu mu, kalite vurgusu mu?
3. İlk teklif nasıl olmalı?
4. Pazarlık sırasında dikkat edilmesi gerekenler

**Format:**
Kısa, net ve uygulanabilir öneriler ver. Maksimum 200 kelime.

**Örnek Çıktı:**
"Bu müşteri, agresif bir indirim bekler. %10 indirimle başlayın ve müşterinin tepkisine göre %15'e kadar çıkabilirsiniz. Fiyat yerine kalite ve konum avantajlarını vurgulayın."
PROMPT;
    }

    /**
     * AI yanıtını parse et ve yapılandırılmış strateji oluştur
     */
    private function parseNegotiationResponse(mixed $aiResponse, array $customerData): array
    {
        // AI yanıtı string ise direkt kullan
        if (is_string($aiResponse)) {
            return [
                'summary' => $aiResponse,
                'recommendation' => $this->extractRecommendation($aiResponse, $customerData),
                'discount_approach' => $this->extractDiscountApproach($aiResponse, $customerData),
                'focus' => $this->extractFocus($aiResponse, $customerData),
            ];
        }

        // Array ise direkt döndür
        if (is_array($aiResponse)) {
            return array_merge([
                'summary' => $aiResponse['summary'] ?? $aiResponse['strategy'] ?? 'Standart pazarlık stratejisi uygulayın.',
                'recommendation' => $aiResponse['recommendation'] ?? 'Müşteri ile görüşme sırasında esnek olun.',
                'discount_approach' => $aiResponse['discount_approach'] ?? 'moderate',
                'focus' => $aiResponse['focus'] ?? 'balanced',
            ], $aiResponse);
        }

        // Fallback
        return [
            'summary' => 'Standart pazarlık stratejisi uygulayın.',
            'recommendation' => 'Müşteri ile görüşme sırasında esnek olun.',
            'discount_approach' => 'moderate',
            'focus' => 'balanced',
        ];
    }

    /**
     * Öneriyi çıkar
     */
    private function extractRecommendation(string $text, array $customerData): string
    {
        // Basit keyword matching ile öneri çıkar
        if (stripos($text, 'agresif') !== false || stripos($text, '%10') !== false || stripos($text, '%15') !== false) {
            return 'Agresif indirim yaklaşımı önerilir. Müşteri fiyata duyarlı görünüyor.';
        }

        if (stripos($text, 'kalite') !== false || stripos($text, 'konum') !== false) {
            return 'Kalite ve konum avantajlarını vurgulayın. Fiyat yerine değer odaklı yaklaşım.';
        }

        return 'Esnek bir pazarlık stratejisi uygulayın. Müşterinin tepkisine göre yaklaşımınızı ayarlayın.';
    }

    /**
     * İndirim yaklaşımını çıkar
     */
    private function extractDiscountApproach(string $text, array $customerData): string
    {
        if (stripos($text, 'agresif') !== false || stripos($text, '%10') !== false) {
            return 'aggressive';
        }

        if (stripos($text, 'yumuşak') !== false || stripos($text, 'dikkatli') !== false) {
            return 'conservative';
        }

        return 'moderate';
    }

    /**
     * Odak noktasını çıkar
     */
    private function extractFocus(string $text, array $customerData): string
    {
        if (stripos($text, 'fiyat') !== false && stripos($text, 'kalite') === false) {
            return 'price';
        }

        if (stripos($text, 'kalite') !== false || stripos($text, 'konum') !== false) {
            return 'quality';
        }

        return 'balanced';
    }

    /**
     * Cortex kararını AiLog'a kaydet
     * Context7: MCP uyumluluğu için milisaniye bazında kayıt
     *
     * @param string $decisionType
     * @param array $context
     * @param float $durationMs
     * @param bool $success
     * @return void
     */
    private function logCortexDecision(string $decisionType, array $context, float $durationMs, bool $success): void
    {
        try {
            AiLog::create([
                'provider' => 'YalihanCortex',
                'request_type' => 'cortex_decision',
                'content_type' => $decisionType,
                'content_id' => $context['talep_id'] ?? $context['ilan_id'] ?? $context['kisi_id'] ?? null,
                'status' => $success ? 'success' : 'failed',
                'response_time' => (int) $durationMs, // Milisaniye olarak kaydet
                'request_data' => $context,
                'response_data' => [
                    'decision_type' => $decisionType,
                    'duration_ms' => $durationMs,
                    'success' => $success,
                ],
                'user_id' => Auth::id(),
                'model' => 'YalihanCortex v1.0',
                'version' => '1.0',
                'ip_address' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            // AiLog kaydı başarısız olsa bile ana işlem devam etmeli
            LogService::warning('Failed to log Cortex decision to AiLog', [
                'decision_type' => $decisionType,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Sesli komut ile hızlı kayıt oluşturma
     *
     * @CortexDecision
     * Doğal dilden gelen metni JSON'a çevirip Kisi ve Talep draft kayıtları oluşturur
     *
     * Context7: C7-VOICE-TO-CRM-2025-11-27
     * Kullanım: Telegram/WhatsApp sesli mesajdan gelen metin → Kisi + Talep draft
     *
     * @param string $rawText Doğal dil metni (örn: "Yeni talep, Ahmet Yılmaz, 10 milyon TL, Bodrum Yalıkavak'ta villa arıyor.")
     * @param int $danismanId Danışman ID
     * @param array $options Ek seçenekler
     * @return array
     */
    public function createDraftFromText(string $rawText, int $danismanId, array $options = []): array
    {
        // MCP uyumluluğu: Timer başlat
        $startTime = LogService::startTimer('yalihan_cortex_voice_to_crm');

        try {
            LogService::ai(
                'yalihan_cortex_voice_to_crm_started',
                'YalihanCortex',
                [
                    'danisman_id' => $danismanId,
                    'text_length' => strlen($rawText),
                    'text_preview' => substr($rawText, 0, 100),
                ]
            );

            // 1. NLP ile metni JSON'a çevir
            $structuredData = $this->extractStructuredDataFromText($rawText);

            // 2. JSON'u validate et
            $validationResult = $this->validateStructuredData($structuredData);
            if (!$validationResult['valid']) {
                throw new \InvalidArgumentException('Structured data validation failed: ' . $validationResult['error']);
            }

            // 3. Kisi oluştur veya bul
            $kisi = $this->createOrFindKisi($structuredData['kisi'], $danismanId);

            // 4. Talep draft oluştur
            $talep = $this->createDraftTalep($structuredData['talep'], $kisi->id, $danismanId);

            // 5. Performans metrikleri
            $durationMs = LogService::stopTimer($startTime);

            $result = [
                'success' => true,
                'kisi_id' => $kisi->id,
                'talep_id' => $talep->id,
                'kisi' => [
                    'id' => $kisi->id,
                    'ad' => $kisi->ad,
                    'soyad' => $kisi->soyad,
                    'telefon' => $kisi->telefon,
                    'email' => $kisi->email,
                ],
                'talep' => [
                    'id' => $talep->id,
                    'baslik' => $talep->baslik ?? null,
                    'status' => $talep->status,
                    'tip' => $talep->tip ?? null,
                ],
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'algorithm' => 'YalihanCortex v1.0',
                    'duration_ms' => $durationMs,
                    'confidence_score' => $structuredData['confidence_score'] ?? 0,
                ],
            ];

            // MCP uyumluluğu: AiLog'a kayıt ekle
            $this->logCortexDecision('voice_to_crm', [
                'danisman_id' => $danismanId,
                'kisi_id' => $kisi->id,
                'talep_id' => $talep->id,
                'text_length' => strlen($rawText),
            ], $durationMs, true);

            LogService::ai(
                'yalihan_cortex_voice_to_crm_completed',
                'YalihanCortex',
                [
                    'danisman_id' => $danismanId,
                    'kisi_id' => $kisi->id,
                    'talep_id' => $talep->id,
                    'duration_ms' => $durationMs,
                ]
            );

            return $result;
        } catch (\Exception $e) {
            $durationMs = LogService::stopTimer($startTime);

            // MCP uyumluluğu: Hata durumunda da AiLog'a kayıt ekle
            $this->logCortexDecision('voice_to_crm', [
                'danisman_id' => $danismanId,
                'error' => $e->getMessage(),
                'text_length' => strlen($rawText),
            ], $durationMs, false);

            LogService::error(
                'YalihanCortex voice to CRM failed',
                [
                    'danisman_id' => $danismanId,
                    'text_length' => strlen($rawText),
                    'error' => $e->getMessage(),
                ],
                $e,
                LogService::CHANNEL_AI
            );

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'algorithm' => 'YalihanCortex v1.0',
                    'duration_ms' => $durationMs,
                ],
            ];
        }
    }

    /**
     * NLP ile metni yapılandırılmış JSON'a çevir
     *
     * @param string $rawText
     * @return array
     */
    private function extractStructuredDataFromText(string $rawText): array
    {
        // AI'dan JSON çıktısı almak için prompt oluştur
        $prompt = $this->buildNLPParsePrompt($rawText);

        // AIService ile Ollama'dan NLP yap
        try {
            $aiResponse = $this->aiService->generate($prompt, [
                'temperature' => 0.3, // Düşük temperature = daha tutarlı çıktı
                'max_tokens' => 1000,
            ]);

            // AI yanıtını parse et
            $structuredData = $this->parseAIResponseToJSON($aiResponse);

            return $structuredData;
        } catch (\Exception $e) {
            // Fallback: Basit regex-based parsing
            LogService::warning('AI NLP parsing failed, using fallback', [
                'error' => $e->getMessage(),
            ]);

            return $this->fallbackTextParsing($rawText);
        }
    }

    /**
     * NLP prompt oluştur
     *
     * @param string $rawText
     * @return string
     */
    private function buildNLPParsePrompt(string $rawText): string
    {
        return <<<PROMPT
Sen bir emlak CRM sistemi için doğal dil işleme (NLP) uzmanısın. Aşağıdaki Türkçe metni analiz edip JSON formatına çevir.

**Giriş Metni:**
{$rawText}

**Görev:**
Bu metni analiz ederek şu bilgileri çıkar:

1. **Kişi Bilgileri:**
   - ad (isim)
   - soyad (soyisim)
   - telefon (varsa)
   - email (varsa)

2. **Talep Bilgileri:**
   - tip: "Satılık" veya "Kiralık" veya "Günlük Kiralık"
   - baslik (kısa başlık, 50-100 karakter)
   - min_fiyat (sayısal değer)
   - max_fiyat (sayısal değer, opsiyonel)
   - il_adi (il adı, örn: "Muğla", "İstanbul")
   - ilce_adi (ilçe adı, örn: "Bodrum", "Kadıköy")
   - mahalle_adi (mahalle adı, opsiyonel)
   - kategori (örn: "Villa", "Daire", "Arsa")
   - aciklama (kısa açıklama, 200 karakter max)

**Çıktı Formatı:**
SADECE JSON döndür, başka hiçbir açıklama ekleme. JSON formatı şu şekilde olmalı:

{
  "kisi": {
    "ad": "Ahmet",
    "soyad": "Yılmaz",
    "telefon": null,
    "email": null
  },
  "talep": {
    "tip": "Satılık",
    "baslik": "Bodrum Yalıkavak'ta Villa Arayışı",
    "min_fiyat": 10000000,
    "max_fiyat": null,
    "il_adi": "Muğla",
    "ilce_adi": "Bodrum",
    "mahalle_adi": "Yalıkavak",
    "kategori": "Villa",
    "aciklama": "Denize sıfır villa arıyor"
  },
  "confidence_score": 85
}

**Önemli Kurallar:**
- Eksik bilgiler için null kullan
- Fiyatları sayısal değer olarak gönder (TL işaretini kaldır)
- İl/İlçe adlarını standart Türkçe formatında yaz (büyük harf ile başla)
- confidence_score: 0-100 arası bir değer (ne kadar emin olduğunu göster)

**Hemen JSON döndür:**
PROMPT;
    }

    /**
     * AI yanıtını JSON'a parse et
     *
     * @param mixed $aiResponse
     * @return array
     */
    private function parseAIResponseToJSON(mixed $aiResponse): array
    {
        // String ise JSON extract et
        if (is_string($aiResponse)) {
            // JSON bloğunu bul (```json ... ``` veya { ... })
            if (preg_match('/```json\s*(\{.*?\})\s*```/s', $aiResponse, $matches)) {
                $jsonString = $matches[1];
            } elseif (preg_match('/(\{.*\})/s', $aiResponse, $matches)) {
                $jsonString = $matches[1];
            } else {
                $jsonString = $aiResponse;
            }

            $decoded = json_decode($jsonString, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        // Array ise direkt döndür
        if (is_array($aiResponse)) {
            return $aiResponse;
        }

        // Fallback
        throw new \RuntimeException('AI response could not be parsed to JSON');
    }

    /**
     * Fallback: Basit regex-based parsing
     *
     * @param string $rawText
     * @return array
     */
    private function fallbackTextParsing(string $rawText): array
    {
        $result = [
            'kisi' => [
                'ad' => null,
                'soyad' => null,
                'telefon' => null,
                'email' => null,
            ],
            'talep' => [
                'tip' => 'Satılık',
                'baslik' => 'Yeni Talep',
                'min_fiyat' => null,
                'max_fiyat' => null,
                'il_adi' => null,
                'ilce_adi' => null,
                'mahalle_adi' => null,
                'kategori' => null,
                'aciklama' => $rawText,
            ],
            'confidence_score' => 30, // Düşük confidence (fallback)
        ];

        // Basit regex patterns
        // İsim soyisim (büyük harfle başlayan kelimeler)
        if (preg_match('/([A-ZÇĞİÖŞÜ][a-zçğıöşü]+)\s+([A-ZÇĞİÖŞÜ][a-zçğıöşü]+)/', $rawText, $matches)) {
            $result['kisi']['ad'] = $matches[1];
            $result['kisi']['soyad'] = $matches[2];
        }

        // Telefon (0 ile başlayan 11 haneli)
        if (preg_match('/(0[0-9]{10})/', $rawText, $matches)) {
            $result['kisi']['telefon'] = $matches[1];
        }

        // Fiyat (milyon, bin gibi kelimelerle)
        if (preg_match('/(\d+)\s*(?:milyon|m)/i', $rawText, $matches)) {
            $result['talep']['min_fiyat'] = (int)$matches[1] * 1000000;
        } elseif (preg_match('/(\d+)\s*(?:bin|b)/i', $rawText, $matches)) {
            $result['talep']['min_fiyat'] = (int)$matches[1] * 1000;
        } elseif (preg_match('/(\d{1,3}(?:[.,]\d{3})*(?:[.,]\d{2})?)/', $rawText, $matches)) {
            $result['talep']['min_fiyat'] = (int)str_replace(['.', ','], '', $matches[1]);
        }

        // Lokasyon (Muğla, Bodrum, Yalıkavak gibi)
        $iller = ['Muğla', 'İstanbul', 'Ankara', 'İzmir', 'Antalya'];
        $ilceler = ['Bodrum', 'Marmaris', 'Fethiye', 'Kaş'];
        foreach ($iller as $il) {
            if (stripos($rawText, $il) !== false) {
                $result['talep']['il_adi'] = $il;
                break;
            }
        }
        foreach ($ilceler as $ilce) {
            if (stripos($rawText, $ilce) !== false) {
                $result['talep']['ilce_adi'] = $ilce;
                break;
            }
        }

        // Kategori
        $kategoriler = ['Villa', 'Daire', 'Arsa', 'İşyeri', 'Ofis'];
        foreach ($kategoriler as $kategori) {
            if (stripos($rawText, strtolower($kategori)) !== false) {
                $result['talep']['kategori'] = $kategori;
                break;
            }
        }

        return $result;
    }

    /**
     * Structured data'yı validate et
     *
     * @param array $data
     * @return array
     */
    private function validateStructuredData(array $data): array
    {
        // Minimum gereksinimler
        if (!isset($data['kisi']) || !isset($data['talep'])) {
            return [
                'valid' => false,
                'error' => 'Missing required keys: kisi or talep',
            ];
        }

        // Kişi validasyonu
        if (empty($data['kisi']['ad'])) {
            return [
                'valid' => false,
                'error' => 'Kişi adı zorunludur',
            ];
        }

        // Talep validasyonu
        if (empty($data['talep']['baslik'])) {
            // Basit bir başlık oluştur
            $data['talep']['baslik'] = 'Yeni Talep - ' . ($data['kisi']['ad'] ?? 'Müşteri');
        }

        return [
            'valid' => true,
            'data' => $data,
        ];
    }

    /**
     * Kisi oluştur veya bul
     *
     * @param array $kisiData
     * @param int $danismanId
     * @return \App\Models\Kisi
     */
    private function createOrFindKisi(array $kisiData, int $danismanId): \App\Models\Kisi
    {
        // Telefon varsa önce ara
        if (!empty($kisiData['telefon'])) {
            $existing = \App\Models\Kisi::where('telefon', $kisiData['telefon'])->first();
            if ($existing) {
                return $existing;
            }
        }

        // Email varsa ara
        if (!empty($kisiData['email'])) {
            $existing = \App\Models\Kisi::where('email', $kisiData['email'])->first();
            if ($existing) {
                return $existing;
            }
        }

        // Yeni kişi oluştur
        return \App\Models\Kisi::create([
            'ad' => $kisiData['ad'] ?? 'Bilinmeyen',
            'soyad' => $kisiData['soyad'] ?? '',
            'telefon' => $kisiData['telefon'] ?? null,
            'email' => $kisiData['email'] ?? null,
            'kisi_tipi' => 'Potansiyel',
            'status' => 'Aktif',
            'danisman_id' => $danismanId,
            'kaynak' => 'Sesli Komut',
        ]);
    }

    /**
     * Talep draft oluştur
     *
     * @param array $talepData
     * @param int $kisiId
     * @param int $danismanId
     * @return \App\Models\Talep
     */
    private function createDraftTalep(array $talepData, int $kisiId, int $danismanId): \App\Models\Talep
    {
        // İl/İlçe ID'lerini bul
        $ilId = null;
        $ilceId = null;
        $mahalleId = null;

        if (!empty($talepData['il_adi'])) {
            $il = \App\Models\Il::where('il_adi', $talepData['il_adi'])->first();
            if ($il) {
                $ilId = $il->id;

                if (!empty($talepData['ilce_adi'])) {
                    $ilce = \App\Models\Ilce::where('il_id', $ilId)
                        ->where('ilce_adi', $talepData['ilce_adi'])
                        ->first();
                    if ($ilce) {
                        $ilceId = $ilce->id;

                        if (!empty($talepData['mahalle_adi'])) {
                            $mahalle = \App\Models\Mahalle::where('ilce_id', $ilceId)
                                ->where('mahalle_adi', $talepData['mahalle_adi'])
                                ->first();
                            if ($mahalle) {
                                $mahalleId = $mahalle->id;
                            }
                        }
                    }
                }
            }
        }

        // Talep oluştur
        return \App\Models\Talep::create([
            'baslik' => $talepData['baslik'] ?? 'Yeni Talep',
            'aciklama' => $talepData['aciklama'] ?? null,
            'tip' => $talepData['tip'] ?? 'Satılık',
            'status' => 'Taslak', // Draft status
            'kisi_id' => $kisiId,
            'danisman_id' => $danismanId,
            'il_id' => $ilId,
            'ilce_id' => $ilceId,
            'mahalle_id' => $mahalleId,
            'min_fiyat' => $talepData['min_fiyat'] ?? null,
            'max_fiyat' => $talepData['max_fiyat'] ?? null,
            'metadata' => [
                'source' => 'voice_command',
                'created_at' => now()->toISOString(),
                'confidence_score' => $talepData['confidence_score'] ?? 0,
            ],
        ]);
    }
}
