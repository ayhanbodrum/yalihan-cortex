<?php

namespace App\Services\AI;

use App\Models\AiLog;
use App\Models\Ilan;
use App\Models\Talep;
use App\Modules\Finans\Services\FinansService;
use App\Services\AIService;
use App\Services\AI\OllamaService;
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
     * Arsa Proje Analiz Servisi
     */
    protected ArsaProjectService $arsaProjectService;

    /**
     * AI Content Generation Service
     */
    protected AIService $aiService;

    /**
     * Ollama Service (Local AI)
     */
    protected OllamaService $ollamaService;

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
        ArsaProjectService $arsaProjectService,
        AIService $aiService,
        OllamaService $ollamaService
    ) {
        $this->propertyMatcher = $propertyMatcher;
        $this->churnService = $churnService;
        $this->finansService = $finansService;
        $this->tkgmService = $tkgmService;
        $this->arsaProjectService = $arsaProjectService;
        $this->aiService = $aiService;
        $this->ollamaService = $ollamaService;
    }

    /**
     * Arsa için Proje Potansiyeli Analizi
     *
     * @CortexDecision
     * TKGM (KAKS/TAKS) + basit fiyat varsayımı ile
     * maksimum inşaat alanı, konut sayısı ve tahmini
     * satış gelirini hesaplar.
     *
     * Örnek Senaryo:
     *  Alan: 2.845 m², KAKS: 0.60 → 1.707 m² inşaat alanı
     *  Konut: 1.707 / 200 ≈ 8 daire
     *  Bölge ortalaması: 1.500.000 ₺/daire → ~12M ₺ proje potansiyeli
     *
     * @param Ilan  $ilan
     * @param array $options ['proje_tipi' => 'villa|daire|otomatik']
     * @return array
     */
    public function analyzeProjectPotential(Ilan $ilan, array $options = []): array
    {
        $startTime = LogService::startTimer('yalihan_cortex_arsa_project_potential');

        try {
            LogService::ai(
                'arsa_project_potential_started',
                'YalihanCortex',
                [
                    'ilan_id' => $ilan->id,
                    'baslik' => $ilan->baslik,
                    'kategori' => $ilan->anaKategori->slug ?? null,
                ]
            );

            // Sadece arsa kategorisi için çalıştır
            $kategoriSlug = strtolower($ilan->anaKategori->slug ?? '');
            if ($kategoriSlug !== 'arsa') {
                $durationMs = LogService::stopTimer($startTime);

                return [
                    'success' => false,
                    'error' => 'Bu analiz sadece arsa ilanları için geçerlidir.',
                    'ilan_id' => $ilan->id,
                    'category' => $kategoriSlug,
                    'metadata' => [
                        'processed_at' => now()->toISOString(),
                        'duration_ms' => $durationMs,
                        'algorithm' => 'YalihanCortex v1.0',
                    ],
                ];
            }

            // TKGM parsel bilgilerini al (mevcut TKGMService kullanımı ile uyumlu)
            $tkgmResult = $this->tkgmService->parselSorgula(
                $ilan->ada_no,
                $ilan->parsel_no,
                $ilan->il_id,
                $ilan->ilce_id,
                $ilan->mahalle_id
            );

            if (! ($tkgmResult['success'] ?? false) || ! isset($tkgmResult['parsel_bilgileri'])) {
                $durationMs = LogService::stopTimer($startTime);

                return [
                    'success' => false,
                    'error' => $tkgmResult['message'] ?? 'TKGM parsel bilgileri alınamadı.',
                    'ilan_id' => $ilan->id,
                    'metadata' => [
                        'processed_at' => now()->toISOString(),
                        'duration_ms' => $durationMs,
                        'algorithm' => 'YalihanCortex v1.0',
                    ],
                ];
            }

            $parsel = $tkgmResult['parsel_bilgileri'];

            // TKGM verisini ArsaProjectService için normalize et
            $tkgmNormalized = [
                'alan_m2' => $parsel['yuzolcumu'] ?? null,
                'kaks' => $parsel['kaks'] ?? null,
            ];

            $projeTipiInput = $options['proje_tipi'] ?? 'otomatik';

            $projectAnalysis = $this->arsaProjectService->calculateProfitPotential(
                $tkgmNormalized,
                $projeTipiInput
            );

            $durationMs = LogService::stopTimer($startTime);

            $result = [
                'success' => $projectAnalysis['success'],
                'ilan_id' => $ilan->id,
                'project' => $projectAnalysis,
                'tkgm' => [
                    'parsel' => $parsel,
                    'hesaplamalar' => $tkgmResult['hesaplamalar'] ?? null,
                    'source' => $tkgmResult['metadata']['source'] ?? 'TKGM',
                ],
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'duration_ms' => $durationMs,
                    'algorithm' => 'YalihanCortex v1.0',
                    'category' => $kategoriSlug,
                ],
            ];

            // AiLog kaydı
            $this->logCortexDecision('arsa_project_potential', [
                'ilan_id' => $ilan->id,
                'ada_no' => $ilan->ada_no,
                'parsel_no' => $ilan->parsel_no,
                'max_insaat_alani' => $projectAnalysis['max_insaat_alani'] ?? null,
                'max_konut_sayisi' => $projectAnalysis['max_konut_sayisi'] ?? null,
                'tahmini_satis_fiyati' => $projectAnalysis['tahmini_satis_fiyati'] ?? null,
                'onerilen_proje_tipi' => $projectAnalysis['onerilen_proje_tipi'] ?? null,
            ], $durationMs, $projectAnalysis['success'] ?? false);

            LogService::ai(
                'arsa_project_potential_completed',
                'YalihanCortex',
                [
                    'ilan_id' => $ilan->id,
                    'max_insaat_alani' => $projectAnalysis['max_insaat_alani'] ?? null,
                    'max_konut_sayisi' => $projectAnalysis['max_konut_sayisi'] ?? null,
                    'tahmini_satis_fiyati' => $projectAnalysis['tahmini_satis_fiyati'] ?? null,
                    'duration_ms' => $durationMs,
                ]
            );

            return $result;
        } catch (\Exception $e) {
            $durationMs = LogService::stopTimer($startTime);

            $this->logCortexDecision('arsa_project_potential', [
                'ilan_id' => $ilan->id,
                'error' => $e->getMessage(),
            ], $durationMs, false);

            LogService::error(
                'YalihanCortex arsa project potential failed',
                [
                    'ilan_id' => $ilan->id,
                    'error' => $e->getMessage(),
                ],
                $e,
                LogService::CHANNEL_AI
            );

            return [
                'success' => false,
                'ilan_id' => $ilan->id,
                'error' => $e->getMessage(),
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'duration_ms' => $durationMs,
                    'algorithm' => 'YalihanCortex v1.0',
                ],
            ];
        }
    }

    /**
     * Pazarlama Videosu için Metin Scripti Üretimi
     *
     * Ton: Sakin, güven veren ve lüks.
     * İçerik: TKGM verileri (alan, imar) + nearby_places (POI listesi)
     * 3 bölüm: Giriş, Çevre, Özellikler.
     */
    public function generateVideoScript(Ilan $ilan): array
    {
        try {
            $tkgmSummary = [
                'ada_no' => $ilan->ada_no,
                'parsel_no' => $ilan->parsel_no,
                'alan_m2' => $ilan->alan_m2,
                'imar_statusu' => $ilan->imar_statusu,
                'kaks' => $ilan->kaks,
                'taks' => $ilan->taks,
            ];

            $nearby = $ilan->nearby_places ?? [];

            $prompt = [
                'instruction' => 'Sakin, güven veren ve lüks bir tonda Türkçe pazarlama videosu scripti üret.',
                'structure' => [
                    'bolumler' => ['Giriş', 'Çevre', 'Özellikler'],
                    'language' => 'tr-TR',
                ],
                'ilan' => [
                    'id' => $ilan->id,
                    'baslik' => $ilan->baslik,
                    'aciklama' => $ilan->aciklama,
                    'fiyat' => $ilan->fiyat,
                    'para_birimi' => $ilan->para_birimi,
                    'adres' => $ilan->adres,
                ],
                'tkgm' => $tkgmSummary,
                'nearby_places' => $nearby,
            ];

            $result = $this->aiService->generate(json_encode($prompt, JSON_UNESCAPED_UNICODE), [
                'tone' => 'calm_luxury',
                'max_tokens' => 800,
            ]);

            $script = $result['content'] ?? ($result['text'] ?? null);

            return [
                'success' => $script !== null,
                'ilan_id' => $ilan->id,
                'script' => $script,
                'sections' => [
                    'intro' => null,
                    'environment' => null,
                    'features' => null,
                ],
                'preview_url' => null,
            ];
        } catch (\Throwable $e) {
            LogService::error(
                'YalihanCortex video script generation failed',
                [
                    'ilan_id' => $ilan->id,
                    'error' => $e->getMessage(),
                ],
                $e,
                LogService::CHANNEL_AI
            );

            return [
                'success' => false,
                'ilan_id' => $ilan->id,
                'error' => $e->getMessage(),
            ];
        }
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

    /**
     * Check İlan Quality - Pre-Publishing Validation
     *
     * ✅ Context7: İlan yayınlanmadan önce kalite kontrolü yapı
     * Zorunlu alanların %80'inin doldurulduğundan emin olur.
     *
     * Arsa: 16 zorunlu alan
     * Yazlık: 14 zorunlu alan
     *
     * @param \App\Models\Ilan $ilan
     * @return array ['passed' => bool, 'risk_level' => 'low|medium|high', 'missing_fields' => [], 'completion_percentage' => int, 'message' => string]
     */
    public function checkIlanQuality(Ilan $ilan): array
    {
        $kategoriSlug = null;
        $missingFields = [];
        $requiredFields = [];
        $filledCount = 0;

        // Kategoriye göre zorunlu alanları belirle
        if ($ilan->anaKategori) {
            $kategoriSlug = strtolower($ilan->anaKategori->slug ?? '');
        }

        // ✅ ARSA KATEGORISI: 16 zorunlu alan
        if ($kategoriSlug === 'arsa') {
            $requiredFields = [
                'baslik' => 'İlan Başlığı',
                'aciklama' => 'İlan Açıklaması',
                'fiyat' => 'Fiyat',
                'il_id' => 'İl',
                'ilce_id' => 'İlçe',
                'alan_m2' => 'Alan (m²)',
                'ada_no' => 'Ada Numarası',
                'parsel_no' => 'Parsel Numarası',
                'imar_statusu' => 'İmar Durumu',
                'yola_cephe' => 'Yola Cephe',
                'altyapi_elektrik' => 'Elektrik Altyapısı',
                'altyapi_su' => 'Su Altyapısı',
                'kaks' => 'KAKS',
                'taks' => 'TAKS',
                'latitude' => 'Enlem',
                'longitude' => 'Boylam',
            ];
        }
        // ✅ YAZLIK KATEGORISI: 14 zorunlu alan
        elseif ($kategoriSlug === 'yazlık' || $kategoriSlug === 'yazlik') {
            $requiredFields = [
                'baslik' => 'İlan Başlığı',
                'aciklama' => 'İlan Açıklaması',
                'gunluk_fiyat' => 'Günlük Fiyat',
                'il_id' => 'İl',
                'ilce_id' => 'İlçe',
                'oda_sayisi' => 'Oda Sayısı',
                'banyo_sayisi' => 'Banyo Sayısı',
                'net_m2' => 'Net Alan (m²)',
                'max_misafir' => 'Maksimum Misafir',
                'havuz' => 'Havuz',
                'sezon_baslangic' => 'Sezon Başlangıcı',
                'sezon_bitis' => 'Sezon Bitişi',
                'latitude' => 'Enlem',
                'longitude' => 'Boylam',
            ];
        }
        // Diğer kategoriler için temel kontrol (10 alan)
        else {
            $requiredFields = [
                'baslik' => 'İlan Başlığı',
                'aciklama' => 'İlan Açıklaması',
                'fiyat' => 'Fiyat',
                'il_id' => 'İl',
                'ilce_id' => 'İlçe',
                'ilan_sahibi_id' => 'İlan Sahibi',
                'status' => 'Durum',
                'latitude' => 'Enlem',
                'longitude' => 'Boylam',
                'ana_kategori_id' => 'Ana Kategori',
            ];
        }

        // Zorunlu alanları kontrol et
        foreach ($requiredFields as $field => $label) {
            $value = $ilan->{$field} ?? null;

            // Değer kontrolü (boş, null, 0, '0', false değerlerini kontrol et)
            $isFilled = !empty($value) && $value !== '0' && $value !== 0 && $value !== false;

            if ($isFilled) {
                $filledCount++;
            } else {
                $missingFields[] = [
                    'field' => $field,
                    'label' => $label,
                ];
            }
        }

        // Tamamlanma yüzdesini hesapla
        $totalFields = count($requiredFields);
        $completionPercentage = $totalFields > 0 ? round(($filledCount / $totalFields) * 100) : 0;

        // %80 ve üzerinde başarılı
        $passed = $completionPercentage >= 80;

        // Risk seviyesini belirle
        $riskLevel = 'low'; // Default
        if ($completionPercentage < 50) {
            $riskLevel = 'high';
        } elseif ($completionPercentage < 80) {
            $riskLevel = 'medium';
        }

        // ✅ Detaylı mesaj oluştur
        if ($passed) {
            $message = "✅ İlan başarılı bir şekilde yayınlanmaya hazır ({$completionPercentage}% tamamlanmış).";
        } else {
            $categoryName = match ($kategoriSlug) {
                'arsa' => 'Arsa',
                'yazlık', 'yazlik' => 'Yazlık',
                default => 'İlan'
            };

            $missingCount = count($missingFields);

            if ($riskLevel === 'high') {
                $criticalFields = array_slice($missingFields, 0, 3);
                $criticalLabels = implode(', ', array_map(fn($f) => $f['label'], $criticalFields));
                $message = "🚨 UYARI: {$categoryName} kategorisinde kritik zorunlu alanlar ({$criticalLabels}) eksik! Lütfen hemen düzenleyiniz. ({$completionPercentage}% tamamlanmış)";
            } else {
                $message = "⚠️ ÖNEMLİ: İlanın {$categoryName} Modülünde {$missingCount} adet eksik alan tespit edildi. Yayın kalitenizi artırmak için doldurun. ({$completionPercentage}% tamamlanmış)";
            }
        }

        return [
            'passed' => $passed,
            'risk_level' => $riskLevel,
            'completion_percentage' => $completionPercentage,
            'missing_fields' => $missingFields,
            'total_required_fields' => $totalFields,
            'filled_fields' => $filledCount,
            'category' => $kategoriSlug ?? 'unknown',
            'message' => $message,
        ];
    }

    /**
     * İlan Başlığı Üretimi
     *
     * Context7: YalihanCortex üzerinden merkezi başlık üretimi
     * Yalıhan Bekçi: Tüm AI başlık üretimi bu metod üzerinden yönetilir
     *
     * @param Ilan|array $ilan İlan modeli veya ilan verisi
     * @param array $options ['tone' => 'seo|kurumsal|hizli_satis|luks', 'provider' => 'ollama|openai|gemini']
     * @return array
     */
    public function generateIlanTitle($ilan, array $options = []): array
    {
        $startTime = LogService::startTimer('yalihan_cortex_generate_title');

        try {
            // İlan verisini normalize et
            $ilanData = $this->normalizeIlanData($ilan);

            // Provider seçimi (akıllı seçim)
            $provider = $options['provider'] ?? $this->selectBestProvider('title', $ilanData);

            LogService::ai(
                'yalihan_cortex_title_generation_started',
                'YalihanCortex',
                [
                    'ilan_id' => $ilanData['id'] ?? null,
                    'provider' => $provider,
                    'tone' => $options['tone'] ?? 'seo',
                ]
            );

            // OllamaService kullan (şimdilik sadece Ollama)
            $ollamaData = [
                'kategori' => $ilanData['kategori'] ?? 'Gayrimenkul',
                'lokasyon' => $this->buildLocationString($ilanData),
                'yayin_tipi' => $ilanData['yayin_tipi'] ?? 'Satılık',
                'fiyat' => $this->formatPriceForAI($ilanData['fiyat'] ?? null, $ilanData['para_birimi'] ?? 'TRY'),
                'tone' => $options['tone'] ?? 'seo',
            ];

            $titles = $this->ollamaService->generateTitle($ollamaData);

            $durationMs = LogService::stopTimer($startTime);

            $result = [
                'success' => !empty($titles),
                'titles' => $titles,
                'count' => count($titles),
                'provider' => $provider,
                'model' => config('ai.ollama_model', 'ollama'),
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'duration_ms' => $durationMs,
                    'algorithm' => 'YalihanCortex v2.0',
                    'tone' => $options['tone'] ?? 'seo',
                ],
            ];

            // AiLog kaydı
            $this->logCortexDecision('generate_ilan_title', [
                'ilan_id' => $ilanData['id'] ?? null,
                'provider' => $provider,
                'titles_count' => count($titles),
                'tone' => $options['tone'] ?? 'seo',
            ], $durationMs, $result['success']);

            LogService::ai(
                'yalihan_cortex_title_generation_completed',
                'YalihanCortex',
                [
                    'ilan_id' => $ilanData['id'] ?? null,
                    'titles_count' => count($titles),
                    'duration_ms' => $durationMs,
                ]
            );

            return $result;
        } catch (\Exception $e) {
            $durationMs = LogService::stopTimer($startTime);

            $this->logCortexDecision('generate_ilan_title', [
                'ilan_id' => is_array($ilan) ? ($ilan['id'] ?? null) : ($ilan->id ?? null),
                'error' => $e->getMessage(),
            ], $durationMs, false);

            LogService::error(
                'YalihanCortex title generation failed',
                [
                    'error' => $e->getMessage(),
                    'ilan_id' => is_array($ilan) ? ($ilan['id'] ?? null) : ($ilan->id ?? null),
                ],
                $e,
                LogService::CHANNEL_AI
            );

            return [
                'success' => false,
                'titles' => [],
                'error' => $e->getMessage(),
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'duration_ms' => $durationMs,
                    'algorithm' => 'YalihanCortex v2.0',
                ],
            ];
        }
    }

    /**
     * İlan Açıklaması Üretimi
     *
     * Context7: YalihanCortex üzerinden merkezi açıklama üretimi
     * Yalıhan Bekçi: Tüm AI açıklama üretimi bu metod üzerinden yönetilir
     *
     * @param Ilan|array $ilan İlan modeli veya ilan verisi
     * @param array $options ['tone' => 'seo|kurumsal|hizli_satis|luks', 'length' => 'short|medium|long', 'provider' => 'ollama|openai|gemini']
     * @return array
     */
    public function generateIlanDescription($ilan, array $options = []): array
    {
        $startTime = LogService::startTimer('yalihan_cortex_generate_description');

        try {
            // İlan verisini normalize et
            $ilanData = $this->normalizeIlanData($ilan);

            // Provider seçimi
            $provider = $options['provider'] ?? $this->selectBestProvider('description', $ilanData);

            LogService::ai(
                'yalihan_cortex_description_generation_started',
                'YalihanCortex',
                [
                    'ilan_id' => $ilanData['id'] ?? null,
                    'provider' => $provider,
                    'tone' => $options['tone'] ?? 'seo',
                ]
            );

            // OllamaService kullan
            $ollamaData = [
                'kategori' => $ilanData['kategori'] ?? 'Gayrimenkul',
                'lokasyon' => $this->buildLocationString($ilanData),
                'fiyat' => $this->formatPriceForAI($ilanData['fiyat'] ?? null, $ilanData['para_birimi'] ?? 'TRY'),
                'metrekare' => $ilanData['metrekare'] ?? '',
                'oda_sayisi' => $ilanData['oda_sayisi'] ?? '',
                'tone' => $options['tone'] ?? 'seo',
            ];

            $description = $this->ollamaService->generateDescription($ollamaData);

            $durationMs = LogService::stopTimer($startTime);

            $result = [
                'success' => !empty($description),
                'description' => $description,
                'length' => strlen($description),
                'provider' => $provider,
                'model' => config('ai.ollama_model', 'ollama'),
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'duration_ms' => $durationMs,
                    'algorithm' => 'YalihanCortex v2.0',
                    'tone' => $options['tone'] ?? 'seo',
                ],
            ];

            // AiLog kaydı
            $this->logCortexDecision('generate_ilan_description', [
                'ilan_id' => $ilanData['id'] ?? null,
                'provider' => $provider,
                'description_length' => strlen($description),
                'tone' => $options['tone'] ?? 'seo',
            ], $durationMs, $result['success']);

            LogService::ai(
                'yalihan_cortex_description_generation_completed',
                'YalihanCortex',
                [
                    'ilan_id' => $ilanData['id'] ?? null,
                    'description_length' => strlen($description),
                    'duration_ms' => $durationMs,
                ]
            );

            return $result;
        } catch (\Exception $e) {
            $durationMs = LogService::stopTimer($startTime);

            $this->logCortexDecision('generate_ilan_description', [
                'ilan_id' => is_array($ilan) ? ($ilan['id'] ?? null) : ($ilan->id ?? null),
                'error' => $e->getMessage(),
            ], $durationMs, false);

            LogService::error(
                'YalihanCortex description generation failed',
                [
                    'error' => $e->getMessage(),
                    'ilan_id' => is_array($ilan) ? ($ilan['id'] ?? null) : ($ilan->id ?? null),
                ],
                $e,
                LogService::CHANNEL_AI
            );

            return [
                'success' => false,
                'description' => '',
                'error' => $e->getMessage(),
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'duration_ms' => $durationMs,
                    'algorithm' => 'YalihanCortex v2.0',
                ],
            ];
        }
    }

    /**
     * Lokasyon Analizi
     *
     * Context7: YalihanCortex üzerinden merkezi lokasyon analizi
     *
     * @param array $locationData ['il', 'ilce', 'mahalle', 'latitude', 'longitude']
     * @return array
     */
    public function analyzeLocation(array $locationData): array
    {
        $startTime = LogService::startTimer('yalihan_cortex_analyze_location');

        try {
            LogService::ai(
                'yalihan_cortex_location_analysis_started',
                'YalihanCortex',
                [
                    'il' => $locationData['il'] ?? null,
                    'ilce' => $locationData['ilce'] ?? null,
                    'mahalle' => $locationData['mahalle'] ?? null,
                ]
            );

            $analysis = $this->ollamaService->analyzeLocation($locationData);

            $durationMs = LogService::stopTimer($startTime);

            $result = [
                'success' => true,
                'analysis' => $analysis,
                'provider' => 'ollama',
                'model' => config('ai.ollama_model', 'ollama'),
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'duration_ms' => $durationMs,
                    'algorithm' => 'YalihanCortex v2.0',
                ],
            ];

            // AiLog kaydı
            $this->logCortexDecision('analyze_location', [
                'il' => $locationData['il'] ?? null,
                'ilce' => $locationData['ilce'] ?? null,
                'mahalle' => $locationData['mahalle'] ?? null,
            ], $durationMs, true);

            return $result;
        } catch (\Exception $e) {
            $durationMs = LogService::stopTimer($startTime);

            $this->logCortexDecision('analyze_location', [
                'error' => $e->getMessage(),
            ], $durationMs, false);

            LogService::error(
                'YalihanCortex location analysis failed',
                [
                    'error' => $e->getMessage(),
                ],
                $e,
                LogService::CHANNEL_AI
            );

            return [
                'success' => false,
                'analysis' => [],
                'error' => $e->getMessage(),
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'duration_ms' => $durationMs,
                    'algorithm' => 'YalihanCortex v2.0',
                ],
            ];
        }
    }

    /**
     * Fiyat Önerisi
     *
     * Context7: YalihanCortex üzerinden merkezi fiyat önerisi
     *
     * @param Ilan|array $ilan İlan modeli veya ilan verisi
     * @param array $options ['strategy' => 'aggressive|moderate|premium']
     * @return array
     */
    public function suggestPrice($ilan, array $options = []): array
    {
        $startTime = LogService::startTimer('yalihan_cortex_suggest_price');

        try {
            // İlan verisini normalize et
            $ilanData = $this->normalizeIlanData($ilan);

            LogService::ai(
                'yalihan_cortex_price_suggestion_started',
                'YalihanCortex',
                [
                    'ilan_id' => $ilanData['id'] ?? null,
                    'base_price' => $ilanData['fiyat'] ?? null,
                ]
            );

            $propertyData = [
                'base_price' => (float) ($ilanData['fiyat'] ?? 0),
                'kategori' => $ilanData['kategori'] ?? 'Gayrimenkul',
                'metrekare' => (float) ($ilanData['metrekare'] ?? 0),
                'lokasyon' => $this->buildLocationString($ilanData),
            ];

            $suggestions = $this->ollamaService->suggestPrice($propertyData);

            $durationMs = LogService::stopTimer($startTime);

            $result = [
                'success' => !empty($suggestions),
                'suggestions' => $suggestions,
                'provider' => 'ollama',
                'model' => config('ai.ollama_model', 'ollama'),
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'duration_ms' => $durationMs,
                    'algorithm' => 'YalihanCortex v2.0',
                ],
            ];

            // AiLog kaydı
            $this->logCortexDecision('suggest_price', [
                'ilan_id' => $ilanData['id'] ?? null,
                'base_price' => $ilanData['fiyat'] ?? null,
                'suggestions_count' => count($suggestions),
            ], $durationMs, $result['success']);

            return $result;
        } catch (\Exception $e) {
            $durationMs = LogService::stopTimer($startTime);

            $this->logCortexDecision('suggest_price', [
                'ilan_id' => is_array($ilan) ? ($ilan['id'] ?? null) : ($ilan->id ?? null),
                'error' => $e->getMessage(),
            ], $durationMs, false);

            LogService::error(
                'YalihanCortex price suggestion failed',
                [
                    'error' => $e->getMessage(),
                    'ilan_id' => is_array($ilan) ? ($ilan['id'] ?? null) : ($ilan->id ?? null),
                ],
                $e,
                LogService::CHANNEL_AI
            );

            return [
                'success' => false,
                'suggestions' => [],
                'error' => $e->getMessage(),
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'duration_ms' => $durationMs,
                    'algorithm' => 'YalihanCortex v2.0',
                ],
            ];
        }
    }

    /**
     * AI Provider Seçimi (Akıllı Fallback)
     *
     * Context7: Task tipine göre en uygun provider seçilir
     *
     * @param string $taskType 'title|description|analysis|generation'
     * @param array $context
     * @return string Provider name ('ollama', 'openai', 'gemini', 'deepseek')
     */
    protected function selectBestProvider(string $taskType, array $context = []): string
    {
        // Şimdilik sadece Ollama kullan (local, hızlı, ücretsiz)
        // Gelecekte: Task tipine, context'e ve provider health durumuna göre seçim yapılabilir
        return 'ollama';
    }

    /**
     * İlan verisini normalize et
     *
     * @param Ilan|array $ilan
     * @return array
     */
    protected function normalizeIlanData($ilan): array
    {
        if (is_array($ilan)) {
            return $ilan;
        }

        // Ilan modelinden array'e çevir
        return [
            'id' => $ilan->id ?? null,
            'kategori' => $ilan->altKategori->name ?? $ilan->anaKategori->name ?? 'Gayrimenkul',
            'il' => $ilan->il->il_adi ?? null,
            'ilce' => $ilan->ilce->ilce_adi ?? null,
            'mahalle' => $ilan->mahalle->mahalle_adi ?? null,
            'yayin_tipi' => $ilan->yayinTipi->name ?? 'Satılık',
            'fiyat' => $ilan->fiyat ?? null,
            'para_birimi' => $ilan->para_birimi ?? 'TRY',
            'metrekare' => $ilan->metrekare ?? null,
            'oda_sayisi' => $ilan->oda_sayisi ?? null,
            'baslik' => $ilan->baslik ?? null,
            'aciklama' => $ilan->aciklama ?? null,
        ];
    }

    /**
     * Lokasyon string'i oluştur
     *
     * @param array $ilanData
     * @return string
     */
    protected function buildLocationString(array $ilanData): string
    {
        $parts = array_filter([
            $ilanData['il'] ?? null,
            $ilanData['ilce'] ?? null,
            $ilanData['mahalle'] ?? null,
        ]);

        return implode(', ', $parts) ?: 'Bodrum';
    }

    /**
     * Fiyatı AI için formatla
     *
     * @param float|null $amount
     * @param string $currency
     * @return string
     */
    protected function formatPriceForAI(?float $amount, string $currency = 'TRY'): string
    {
        if (!$amount) {
            return '';
        }

        $symbols = [
            'TRY' => '₺',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
        ];

        $formatted = number_format($amount, 0, ',', '.');
        $symbol = $symbols[$currency] ?? '₺';

        return "{$formatted} {$symbol}";
    }

    /**
     * Pazar İstihbaratı: Piyasa Trend Analizi
     *
     * Context7: Market Intelligence - AI destekli piyasa analizi
     *
     * @param array $filters Bölge, kategori, tarih aralığı filtreleri
     * @param array $options Analiz seçenekleri
     * @return array Trend analizi sonuçları
     */
    public function analyzeMarketTrends(array $filters = [], array $options = []): array
    {
        $startTime = LogService::startTimer('yalihan_cortex_market_trends');

        try {
            LogService::ai(
                'yalihan_cortex_market_trends_started',
                'YalihanCortex',
                ['filters' => $filters, 'options' => $options]
            );

            // MarketListing verilerini çek
            $query = \App\Models\MarketListing::query();

            if (isset($filters['il_id'])) {
                $query->where('location_il', $filters['il_id']);
            }
            if (isset($filters['ilce_id'])) {
                $query->where('location_ilce', $filters['ilce_id']);
            }
            if (isset($filters['date_from'])) {
                $query->where('listing_date', '>=', $filters['date_from']);
            }
            if (isset($filters['date_to'])) {
                $query->where('listing_date', '<=', $filters['date_to']);
            }

            $listings = $query->where('status', 1)
                ->orderBy('listing_date', 'desc')
                ->limit(1000)
                ->get();

            // AI ile trend analizi
            $prompt = $this->buildMarketTrendPrompt($listings, $filters);
            $aiResult = $this->aiService->generate($prompt, [
                'type' => 'market_trends',
                'max_tokens' => 1000,
            ]);

            $parsedResult = $this->parseAIResponse($aiResult);

            $result = [
                'success' => true,
                'trends' => $this->extractTrends($parsedResult, $listings),
                'statistics' => $this->calculateMarketStatistics($listings),
                'insights' => $parsedResult['insights'] ?? [],
                'recommendations' => $parsedResult['recommendations'] ?? [],
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'listings_analyzed' => $listings->count(),
                    'algorithm' => 'YalihanCortex Market Intelligence v1.0',
                ],
            ];

            $durationMs = LogService::stopTimer($startTime);
            $result['metadata']['duration_ms'] = $durationMs;

            $this->logCortexDecision('market_trends', [
                'listings_count' => $listings->count(),
                'filters' => $filters,
            ], $durationMs, true);

            return $result;
        } catch (\Exception $e) {
            LogService::error('Market trend analysis failed', [
                'error' => $e->getMessage(),
                'filters' => $filters,
            ], $e, LogService::CHANNEL_AI);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                ],
            ];
        }
    }

    /**
     * Pazar İstihbaratı: Fiyat Karşılaştırması
     *
     * @param Ilan $ilan Karşılaştırılacak ilan
     * @param array $options Karşılaştırma seçenekleri
     * @return array Fiyat karşılaştırma sonuçları
     */
    public function compareMarketPrices(Ilan $ilan, array $options = []): array
    {
        $startTime = LogService::startTimer('yalihan_cortex_price_compare');

        try {
            // Benzer ilanları bul (MarketListing'lerden)
            $similarListings = \App\Models\MarketListing::query()
                ->where('location_il', $ilan->il->il_adi ?? null)
                ->where('location_ilce', $ilan->ilce->ilce_adi ?? null)
                ->where('m2_brut', '>=', ($ilan->metrekare ?? 0) * 0.8)
                ->where('m2_brut', '<=', ($ilan->metrekare ?? 0) * 1.2)
                ->where('status', 1)
                ->orderBy('listing_date', 'desc')
                ->limit(50)
                ->get();

            $currentPrice = $ilan->fiyat ?? 0;
            $avgMarketPrice = $similarListings->avg('price') ?? $currentPrice;
            $minMarketPrice = $similarListings->min('price') ?? $currentPrice;
            $maxMarketPrice = $similarListings->max('price') ?? $currentPrice;

            $priceDifference = $currentPrice - $avgMarketPrice;
            $priceDifferencePercent = $avgMarketPrice > 0 ? ($priceDifference / $avgMarketPrice) * 100 : 0;

            // AI ile fiyat önerisi
            $prompt = $this->buildPriceComparisonPrompt($ilan, $similarListings);
            $aiResult = $this->aiService->generate($prompt, [
                'type' => 'price_comparison',
                'max_tokens' => 500,
            ]);

            $result = [
                'success' => true,
                'current_price' => $currentPrice,
                'market_average' => round($avgMarketPrice, 2),
                'market_min' => round($minMarketPrice, 2),
                'market_max' => round($maxMarketPrice, 2),
                'price_difference' => round($priceDifference, 2),
                'price_difference_percent' => round($priceDifferencePercent, 2),
                'competitiveness' => $this->calculateCompetitiveness($priceDifferencePercent),
                'ai_recommendation' => $this->parseAIResponse($aiResult)['recommendation'] ?? null,
                'similar_listings_count' => $similarListings->count(),
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'algorithm' => 'YalihanCortex Price Comparison v1.0',
                ],
            ];

            $durationMs = LogService::stopTimer($startTime);
            $result['metadata']['duration_ms'] = $durationMs;

            return $result;
        } catch (\Exception $e) {
            LogService::error('Price comparison failed', [
                'ilan_id' => $ilan->id,
                'error' => $e->getMessage(),
            ], $e, LogService::CHANNEL_AI);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * İlanlarım: Kullanıcının İlanlarını Analiz Et
     *
     * @param int $userId Kullanıcı ID
     * @param array $options Analiz seçenekleri
     * @return array İlanlar analizi
     */
    public function analyzeMyListings(int $userId, array $options = []): array
    {
        $startTime = LogService::startTimer('yalihan_cortex_my_listings');

        try {
            $listings = Ilan::where('danisman_id', $userId)
                ->with(['altKategori', 'il', 'ilce', 'fotograflar'])
                ->get();

            $stats = [
                'total' => $listings->count(),
                'active' => $listings->where('status', 'Aktif')->count(),
                'pending' => $listings->where('status', 'Beklemede')->count(),
                'draft' => $listings->where('status', 'Taslak')->count(),
                'total_views' => $listings->sum('goruntulenme') ?? 0,
                'avg_price' => $listings->avg('fiyat') ?? 0,
            ];

            // AI ile öneriler
            $prompt = $this->buildMyListingsAnalysisPrompt($listings, $stats);
            $aiResult = $this->aiService->generate($prompt, [
                'type' => 'my_listings_analysis',
                'max_tokens' => 800,
            ]);

            $result = [
                'success' => true,
                'statistics' => $stats,
                'insights' => $this->parseAIResponse($aiResult)['insights'] ?? [],
                'recommendations' => $this->parseAIResponse($aiResult)['recommendations'] ?? [],
                'top_performers' => $this->getTopPerformers($listings),
                'needs_attention' => $this->getNeedsAttention($listings),
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'user_id' => $userId,
                ],
            ];

            $durationMs = LogService::stopTimer($startTime);
            $result['metadata']['duration_ms'] = $durationMs;

            return $result;
        } catch (\Exception $e) {
            LogService::error('My listings analysis failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ], $e, LogService::CHANNEL_AI);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Rapor Üretimi: AI Destekli Rapor Oluştur
     *
     * @param string $reportType Rapor tipi (ilan, satis, finans, musteri, performans)
     * @param array $filters Rapor filtreleri
     * @param array $options Rapor seçenekleri
     * @return array Rapor verileri
     */
    public function generateReport(string $reportType, array $filters = [], array $options = []): array
    {
        $startTime = LogService::startTimer('yalihan_cortex_generate_report');

        try {
            $data = $this->collectReportData($reportType, $filters);
            $prompt = $this->buildReportPrompt($reportType, $data, $filters);
            $aiResult = $this->aiService->generate($prompt, [
                'type' => 'report_generation',
                'max_tokens' => 1500,
            ]);

            $result = [
                'success' => true,
                'report_type' => $reportType,
                'data' => $data,
                'summary' => $aiResult['summary'] ?? null,
                'insights' => $this->parseAIResponse($aiResult)['insights'] ?? [],
                'recommendations' => $this->parseAIResponse($aiResult)['recommendations'] ?? [],
                'charts' => $this->generateChartData($data, $reportType),
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'filters' => $filters,
                ],
            ];

            $durationMs = LogService::stopTimer($startTime);
            $result['metadata']['duration_ms'] = $durationMs;

            return $result;
        } catch (\Exception $e) {
            LogService::error('Report generation failed', [
                'report_type' => $reportType,
                'error' => $e->getMessage(),
            ], $e, LogService::CHANNEL_AI);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Bildirimler: Akıllı Bildirim Önceliklendirme
     *
     * @param array $notifications Bildirimler
     * @param array $options Seçenekler
     * @return array Önceliklendirilmiş bildirimler
     */
    public function prioritizeNotifications(array $notifications, array $options = []): array
    {
        $startTime = LogService::startTimer('yalihan_cortex_notifications');

        try {
            // AI ile öncelik skoru hesapla
            $prompt = $this->buildNotificationPriorityPrompt($notifications);
            $aiResult = $this->aiService->generate($prompt, [
                'type' => 'notification_priority',
                'max_tokens' => 600,
            ]);

            $prioritized = [];
            foreach ($notifications as $notification) {
                $priority = $this->calculateNotificationPriority($notification, $aiResult);
                $prioritized[] = [
                    'notification' => $notification,
                    'priority_score' => $priority,
                    'priority_level' => $this->getPriorityLevel($priority),
                ];
            }

            // Önceliğe göre sırala
            usort($prioritized, fn($a, $b) => $b['priority_score'] <=> $a['priority_score']);

            $result = [
                'success' => true,
                'notifications' => $prioritized,
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'total_count' => count($notifications),
                ],
            ];

            $durationMs = LogService::stopTimer($startTime);
            $result['metadata']['duration_ms'] = $durationMs;

            return $result;
        } catch (\Exception $e) {
            LogService::error('Notification prioritization failed', [
                'error' => $e->getMessage(),
            ], $e, LogService::CHANNEL_AI);

            return [
                'success' => false,
                'notifications' => $notifications,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Takım Yönetimi: Takım Performans Analizi
     *
     * @param int $teamId Takım ID (opsiyonel)
     * @param array $options Analiz seçenekleri
     * @return array Performans analizi
     */
    public function analyzeTeamPerformance(?int $teamId = null, array $options = []): array
    {
        $startTime = LogService::startTimer('yalihan_cortex_team_performance');

        try {
            $query = \App\Modules\TakimYonetimi\Models\Gorev::query();

            if ($teamId) {
                $query->whereHas('takimUyesi', fn($q) => $q->where('takim_id', $teamId));
            }

            $gorevler = $query->with(['takimUyesi', 'proje'])
                ->where('created_at', '>=', now()->subDays($options['days'] ?? 30))
                ->get();

            $stats = [
                'total' => $gorevler->count(),
                'completed' => $gorevler->where('durum', 'tamamlandi')->count(),
                'in_progress' => $gorevler->where('durum', 'devam_ediyor')->count(),
                'overdue' => $gorevler->where('bitis_tarihi', '<', now())
                    ->where('durum', '!=', 'tamamlandi')
                    ->count(),
            ];

            // AI ile performans analizi
            $prompt = $this->buildTeamPerformancePrompt($gorevler, $stats);
            $aiResult = $this->aiService->generate($prompt, [
                'type' => 'team_performance',
                'max_tokens' => 800,
            ]);

            $result = [
                'success' => true,
                'statistics' => $stats,
                'insights' => $this->parseAIResponse($aiResult)['insights'] ?? [],
                'recommendations' => $this->parseAIResponse($aiResult)['recommendations'] ?? [],
                'top_performers' => $this->getTopTeamPerformers($gorevler),
                'needs_attention' => $this->getTeamNeedsAttention($gorevler),
                'metadata' => [
                    'processed_at' => now()->toISOString(),
                    'team_id' => $teamId,
                ],
            ];

            $durationMs = LogService::stopTimer($startTime);
            $result['metadata']['duration_ms'] = $durationMs;

            return $result;
        } catch (\Exception $e) {
            LogService::error('Team performance analysis failed', [
                'team_id' => $teamId,
                'error' => $e->getMessage(),
            ], $e, LogService::CHANNEL_AI);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    // ========== HELPER METHODS ==========

    protected function buildMarketTrendPrompt($listings, array $filters): string
    {
        $avgPrice = $listings->avg('price') ?? 0;
        $priceTrend = $this->calculatePriceTrend($listings);

        return "Piyasa trend analizi yap:\n\n" .
            "Toplam ilan sayısı: {$listings->count()}\n" .
            "Ortalama fiyat: " . number_format($avgPrice, 2) . " TRY\n" .
            "Fiyat trendi: {$priceTrend}\n\n" .
            "Bu verilere göre piyasa trendlerini, fırsatları ve riskleri analiz et.";
    }

    protected function buildPriceComparisonPrompt(Ilan $ilan, $similarListings): string
    {
        $currentPrice = $ilan->fiyat ?? 0;
        $avgPrice = $similarListings->avg('price') ?? $currentPrice;

        return "Fiyat karşılaştırması yap:\n\n" .
            "İlan fiyatı: " . number_format($currentPrice, 2) . " TRY\n" .
            "Piyasa ortalaması: " . number_format($avgPrice, 2) . " TRY\n" .
            "Benzer ilan sayısı: {$similarListings->count()}\n\n" .
            "Bu ilanın fiyatının rekabetçi olup olmadığını değerlendir ve öneriler sun.";
    }

    protected function buildMyListingsAnalysisPrompt($listings, array $stats): string
    {
        return "Kullanıcının ilanlarını analiz et:\n\n" .
            "Toplam ilan: {$stats['total']}\n" .
            "Aktif ilan: {$stats['active']}\n" .
            "Toplam görüntülenme: {$stats['total_views']}\n" .
            "Ortalama fiyat: " . number_format($stats['avg_price'], 2) . " TRY\n\n" .
            "Performans analizi yap ve iyileştirme önerileri sun.";
    }

    protected function buildReportPrompt(string $reportType, array $data, array $filters): string
    {
        return "{$reportType} raporu oluştur:\n\n" .
            "Veri: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n\n" .
            "Bu verilere göre detaylı bir rapor hazırla, önemli bulguları ve önerileri belirt.";
    }

    protected function buildNotificationPriorityPrompt(array $notifications): string
    {
        return "Bildirimleri önceliklendir:\n\n" .
            "Toplam bildirim: " . count($notifications) . "\n" .
            "Bildirimler: " . json_encode($notifications, JSON_UNESCAPED_UNICODE) . "\n\n" .
            "Her bildirimin önemini ve aciliyetini değerlendir.";
    }

    protected function buildTeamPerformancePrompt($gorevler, array $stats): string
    {
        return "Takım performans analizi yap:\n\n" .
            "Toplam görev: {$stats['total']}\n" .
            "Tamamlanan: {$stats['completed']}\n" .
            "Devam eden: {$stats['in_progress']}\n" .
            "Geciken: {$stats['overdue']}\n\n" .
            "Performans analizi yap ve iyileştirme önerileri sun.";
    }

    protected function extractTrends($aiResult, $listings): array
    {
        return [
            'price_trend' => $this->calculatePriceTrend($listings),
            'volume_trend' => $this->calculateVolumeTrend($listings),
            'ai_insights' => $aiResult['insights'] ?? [],
        ];
    }

    protected function calculateMarketStatistics($listings): array
    {
        return [
            'total_listings' => $listings->count(),
            'avg_price' => round($listings->avg('price') ?? 0, 2),
            'min_price' => round($listings->min('price') ?? 0, 2),
            'max_price' => round($listings->max('price') ?? 0, 2),
            'median_price' => round($listings->median('price') ?? 0, 2),
        ];
    }

    protected function calculatePriceTrend($listings): string
    {
        if ($listings->count() < 2) {
            return 'yetersiz_veri';
        }

        $recent = $listings->take(10)->avg('price') ?? 0;
        $older = $listings->skip(10)->take(10)->avg('price') ?? 0;

        if ($older == 0) {
            return 'yetersiz_veri';
        }

        $change = (($recent - $older) / $older) * 100;

        if ($change > 5) {
            return 'artis';
        } elseif ($change < -5) {
            return 'azalis';
        }

        return 'stabil';
    }

    protected function calculateVolumeTrend($listings): string
    {
        $recent = $listings->where('listing_date', '>=', now()->subDays(7))->count();
        $older = $listings->where('listing_date', '>=', now()->subDays(14))
            ->where('listing_date', '<', now()->subDays(7))
            ->count();

        if ($older == 0) {
            return 'yetersiz_veri';
        }

        $change = (($recent - $older) / $older) * 100;

        if ($change > 10) {
            return 'artis';
        } elseif ($change < -10) {
            return 'azalis';
        }

        return 'stabil';
    }

    protected function calculateCompetitiveness(float $priceDifferencePercent): string
    {
        if ($priceDifferencePercent < -10) {
            return 'cok_uygun';
        } elseif ($priceDifferencePercent < 0) {
            return 'uygun';
        } elseif ($priceDifferencePercent < 10) {
            return 'normal';
        } else {
            return 'pahali';
        }
    }

    protected function getTopPerformers($listings)
    {
        return $listings->sortByDesc('goruntulenme')
            ->take(5)
            ->map(fn($l) => [
                'id' => $l->id,
                'baslik' => $l->baslik,
                'views' => $l->goruntulenme ?? 0,
            ])
            ->values();
    }

    protected function getNeedsAttention($listings)
    {
        return $listings->where('status', 'Aktif')
            ->where('goruntulenme', '<', 10)
            ->take(5)
            ->map(fn($l) => [
                'id' => $l->id,
                'baslik' => $l->baslik,
                'views' => $l->goruntulenme ?? 0,
            ])
            ->values();
    }

    protected function collectReportData(string $reportType, array $filters): array
    {
        switch ($reportType) {
            case 'ilan':
                return $this->collectIlanReportData($filters);
            case 'satis':
                return $this->collectSatisReportData($filters);
            case 'finans':
                return $this->collectFinansReportData($filters);
            case 'musteri':
                return $this->collectMusteriReportData($filters);
            case 'performans':
                return $this->collectPerformansReportData($filters);
            default:
                return [];
        }
    }

    protected function collectIlanReportData(array $filters): array
    {
        $query = Ilan::query();
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        return [
            'total' => $query->count(),
            'active' => $query->where('status', 'Aktif')->count(),
            'by_category' => $query->groupBy('alt_kategori_id')->count(),
        ];
    }

    protected function collectSatisReportData(array $filters): array
    {
        // Satış raporu verileri
        return [];
    }

    protected function collectFinansReportData(array $filters): array
    {
        // Finans raporu verileri
        return [];
    }

    protected function collectMusteriReportData(array $filters): array
    {
        // Müşteri raporu verileri
        return [];
    }

    protected function collectPerformansReportData(array $filters): array
    {
        // Performans raporu verileri
        return [];
    }

    protected function generateChartData(array $data, string $reportType): array
    {
        // Grafik verileri oluştur
        return [];
    }

    protected function calculateNotificationPriority($notification, $aiResult): float
    {
        // Bildirim öncelik skoru hesapla
        $baseScore = 50;
        if (isset($notification['type'])) {
            $typeScores = [
                'urgent' => 90,
                'important' => 70,
                'normal' => 50,
                'low' => 30,
            ];
            $baseScore = $typeScores[$notification['type']] ?? 50;
        }
        return $baseScore;
    }

    protected function getPriorityLevel(float $score): string
    {
        if ($score >= 80) {
            return 'yuksek';
        } elseif ($score >= 50) {
            return 'orta';
        }
        return 'dusuk';
    }

    protected function getTopTeamPerformers($gorevler)
    {
        return $gorevler->groupBy('atanan_user_id')
            ->map(fn($g) => [
                'user_id' => $g->first()->atanan_user_id,
                'completed' => $g->where('durum', 'tamamlandi')->count(),
                'total' => $g->count(),
            ])
            ->sortByDesc('completed')
            ->take(5)
            ->values();
    }

    protected function getTeamNeedsAttention($gorevler)
    {
        return $gorevler->where('bitis_tarihi', '<', now())
            ->where('durum', '!=', 'tamamlandi')
            ->take(5)
            ->map(fn($g) => [
                'id' => $g->id,
                'baslik' => $g->baslik,
                'overdue_days' => now()->diffInDays($g->bitis_tarihi),
            ])
            ->values();
    }

    /**
     * AI yanıtını parse et
     *
     * @param mixed $aiResult AI servisinden gelen ham yanıt
     * @return array Parse edilmiş yanıt
     */
    protected function parseAIResponse($aiResult): array
    {
        if (is_string($aiResult)) {
            // String ise JSON parse etmeyi dene
            $decoded = json_decode($aiResult, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
            // JSON değilse, basit bir yapı oluştur
            return [
                'summary' => $aiResult,
                'insights' => [],
                'recommendations' => [],
            ];
        }

        if (is_array($aiResult)) {
            return $aiResult;
        }

        return [
            'summary' => 'AI analizi tamamlandı',
            'insights' => [],
            'recommendations' => [],
        ];
    }
}
