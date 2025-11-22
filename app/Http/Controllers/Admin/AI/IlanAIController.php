<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\OllamaService;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * İlan AI Controller
 *
 * Context7 Standardı: C7-ILAN-AI-CONTROLLER-2025-10-11
 *
 * Ollama Gemma2:2b ile AI içerik üretimi
 */
class IlanAIController extends Controller
{
    protected OllamaService $ollamaService;

    public function __construct(OllamaService $ollamaService)
    {
        $this->ollamaService = $ollamaService;
    }

    /**
     * AI Öneri Endpoint (Unified)
     *
     * POST /admin/ilanlar/ai-suggest
     */
    public function suggest(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:title,description,location,price'
        ]);

        try {
            $action = $request->input('action');

            switch ($action) {
                case 'title':
                    return $this->generateTitle($request);

                case 'description':
                    return $this->generateDescription($request);

                case 'location':
                    return $this->analyzeLocation($request);

                case 'price':
                    return $this->suggestPrice($request);

                default:
                    return response()->json([
                        'success' => false,
                        'error' => 'Invalid action'
                    ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'AI işlemi başarısız',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Başlık üret
     */
    protected function generateTitle(Request $request): JsonResponse
    {
        $data = [
            'kategori' => $request->input('kategori', 'Gayrimenkul'),
            'lokasyon' => $this->getLocation($request),
            'yayin_tipi' => $request->input('yayin_tipi', 'Satılık'),
            'fiyat' => $this->formatPrice($request->input('fiyat'), $request->input('para_birimi')),
            'tone' => $request->input('ai_tone', 'seo')
        ];

        $titles = $this->ollamaService->generateTitle($data);

        // ✅ Context7: Settings'ten gerçek model adını al
        $currentModel = $this->getCurrentModel();

        return response()->json([
            'success' => true,
            'variants' => $titles,
            'count' => count($titles),
            'model' => $currentModel
        ]);
    }

    /**
     * Açıklama üret
     */
    protected function generateDescription(Request $request): JsonResponse
    {
        $data = [
            'kategori' => $request->input('kategori', 'Gayrimenkul'),
            'lokasyon' => $this->getLocation($request),
            'fiyat' => $this->formatPrice($request->input('fiyat'), $request->input('para_birimi')),
            'metrekare' => $request->input('metrekare', ''),
            'oda_sayisi' => $request->input('oda_sayisi', ''),
            'tone' => $request->input('ai_tone', 'seo')
        ];

        $description = $this->ollamaService->generateDescription($data);

        // ✅ Context7: Settings'ten gerçek model adını al
        $currentModel = $this->getCurrentModel();

        return response()->json([
            'success' => true,
            'description' => $description,
            'length' => strlen($description),
            'model' => $currentModel
        ]);
    }

    /**
     * Lokasyon analizi
     */
    protected function analyzeLocation(Request $request): JsonResponse
    {
        $locationData = [
            'il' => $request->input('il'),
            'ilce' => $request->input('ilce'),
            'mahalle' => $request->input('mahalle', ''),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude')
        ];

        $analysis = $this->ollamaService->analyzeLocation($locationData);

        // ✅ Context7: Settings'ten gerçek model adını al
        $currentModel = $this->getCurrentModel();

        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'model' => $currentModel
        ]);
    }

    /**
     * Fiyat önerisi
     */
    protected function suggestPrice(Request $request): JsonResponse
    {
        $propertyData = [
            'base_price' => (float) $request->input('fiyat', 0),
            'kategori' => $request->input('kategori', 'Gayrimenkul'),
            'metrekare' => $request->input('metrekare', 0),
            'lokasyon' => $this->getLocation($request)
        ];

        $suggestions = $this->ollamaService->suggestPrice($propertyData);

        // ✅ Context7: Settings'ten gerçek model adını al
        $currentModel = $this->getCurrentModel();

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions,
            'model' => $currentModel
        ]);
    }

    /**
     * Toplu İlan Analizi
     * POST /admin/ilanlar/ai/bulk-analyze
     */
    public function bulkAnalyze(Request $request): JsonResponse
    {
        $request->validate([
            'ilan_ids' => 'required|array',
            'ilan_ids.*' => 'exists:ilanlar,id'
        ]);

        try {
            $ilanIds = $request->input('ilan_ids');
            $analysisType = $request->input('type', 'comprehensive'); // comprehensive, price, title, seo

            $results = [];
            foreach ($ilanIds as $ilanId) {
                $ilan = \App\Models\Ilan::with(['kategori', 'il', 'ilce', 'ilanSahibi'])->find($ilanId);

                if (!$ilan) {
                    continue;
                }

                $analysis = $this->analyzeSingleListing($ilan, $analysisType);
                $results[] = [
                    'ilan_id' => $ilanId,
                    'baslik' => $ilan->baslik,
                    'analysis' => $analysis
                ];
            }

            return response()->json([
                'success' => true,
                'results' => $results,
                'count' => count($results),
                'type' => $analysisType
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Toplu analiz başarısız',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tek ilan analizi
     */
    protected function analyzeSingleListing($ilan, string $type): array
    {
        $analysis = [];

        switch ($type) {
            case 'price':
                $analysis = $this->analyzePrice($ilan);
                break;
            case 'title':
                $analysis = $this->analyzeTitle($ilan);
                break;
            case 'seo':
                $analysis = $this->analyzeSEO($ilan);
                break;
            case 'comprehensive':
            default:
                $analysis = [
                    'price' => $this->analyzePrice($ilan),
                    'title' => $this->analyzeTitle($ilan),
                    'seo' => $this->analyzeSEO($ilan),
                    'recommendations' => $this->getRecommendations($ilan)
                ];
                break;
        }

        return $analysis;
    }

    /**
     * Fiyat analizi
     */
    protected function analyzePrice($ilan): array
    {
        $currentPrice = $ilan->fiyat ?? 0;
        $suggestedPrice = $currentPrice;
        $confidence = 0.7;

        // Basit fiyat analizi (gerçek implementasyonda market data kullanılmalı)
        if ($ilan->metrekare) {
            $pricePerSqm = $currentPrice / $ilan->metrekare;
            $suggestedPrice = $currentPrice * (1 + (rand(-10, 10) / 100)); // ±10% varyasyon
        }

        return [
            'current_price' => $currentPrice,
            'suggested_price' => round($suggestedPrice, 2),
            'price_per_sqm' => $ilan->metrekare ? round($currentPrice / $ilan->metrekare, 2) : null,
            'confidence' => $confidence,
            'recommendation' => $suggestedPrice > $currentPrice ? 'Fiyat artırılabilir' : 'Fiyat uygun görünüyor'
        ];
    }

    /**
     * Başlık analizi ve optimizasyonu
     */
    protected function analyzeTitle($ilan): array
    {
        $currentTitle = $ilan->baslik ?? '';
        $suggestedTitles = [];

        // Basit başlık önerileri
        if ($ilan->kategori && $ilan->il) {
            $suggestedTitles[] = $ilan->kategori->name . ' - ' . $ilan->il->il_adi;
            if ($ilan->ilce) {
                $suggestedTitles[] = $ilan->kategori->name . ' ' . $ilan->ilce->ilce_adi . ', ' . $ilan->il->il_adi;
            }
        }

        return [
            'current_title' => $currentTitle,
            'suggested_titles' => $suggestedTitles,
            'current_length' => strlen($currentTitle),
            'seo_score' => $this->calculateSEOScore($currentTitle),
            'recommendation' => strlen($currentTitle) < 30 ? 'Başlık kısa, daha detaylı olabilir' : 'Başlık uygun görünüyor'
        ];
    }

    /**
     * SEO analizi
     */
    protected function analyzeSEO($ilan): array
    {
        $title = $ilan->baslik ?? '';
        $description = $ilan->aciklama ?? '';

        $seoScore = $this->calculateSEOScore($title);
        $descriptionScore = strlen($description) > 100 ? 1.0 : strlen($description) / 100;

        return [
            'title_seo_score' => $seoScore,
            'description_length' => strlen($description),
            'description_score' => $descriptionScore,
            'overall_score' => ($seoScore + $descriptionScore) / 2,
            'recommendations' => [
                strlen($title) < 30 ? 'Başlık daha uzun olmalı' : null,
                strlen($description) < 100 ? 'Açıklama daha detaylı olmalı' : null
            ]
        ];
    }

    /**
     * SEO skoru hesapla
     */
    protected function calculateSEOScore(string $text): float
    {
        $score = 0.5; // Base score

        // Uzunluk kontrolü
        if (strlen($text) >= 30 && strlen($text) <= 60) {
            $score += 0.2;
        }

        // Kelime sayısı
        $wordCount = str_word_count($text);
        if ($wordCount >= 5 && $wordCount <= 10) {
            $score += 0.2;
        }

        // Özel karakter kontrolü
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $text)) {
            $score += 0.1;
        }

        return min($score, 1.0);
    }

    /**
     * Genel öneriler
     */
    protected function getRecommendations($ilan): array
    {
        $recommendations = [];

        if (!$ilan->aciklama || strlen($ilan->aciklama) < 100) {
            $recommendations[] = 'Açıklama eksik veya çok kısa';
        }

        if (!$ilan->fotograflar || $ilan->fotograflar->count() < 3) {
            $recommendations[] = 'En az 3 fotoğraf eklenmeli';
        }

        if (!$ilan->metrekare) {
            $recommendations[] = 'Metrekare bilgisi eksik';
        }

        return $recommendations;
    }

    /**
     * Health check
     */
    public function health(): JsonResponse
    {
        $isHealthy = $this->ollamaService->isHealthy();

        return response()->json([
            'success' => $isHealthy,
            'model' => config('ai.ollama_model'),
            'endpoint' => config('ai.ollama_api_url'),
            'status' => $isHealthy ? 'online' : 'offline'
        ]);
    }

    /**
     * Lokasyon string'i oluştur
     */
    protected function getLocation(Request $request): string
    {
        $parts = array_filter([
            $request->input('il'),
            $request->input('ilce'),
            $request->input('mahalle')
        ]);

        return implode(', ', $parts) ?: 'Bodrum';
    }

    /**
     * Fiyat formatla
     */
    protected function formatPrice(?string $amount, ?string $currency): string
    {
        if (!$amount) {
            return '';
        }

        $symbols = [
            'TRY' => '₺',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£'
        ];

        $formatted = number_format((float) $amount, 0, ',', '.');
        $symbol = $symbols[$currency ?? 'TRY'] ?? '₺';

        return $formatted . ' ' . $symbol;
    }

    /**
     * Mevcut AI model'ini settings'ten al
     * Öncelik: ai_default_model > ollama_model > config > default
     */
    protected function getCurrentModel(): string
    {
        return Cache::remember('ai_current_model', 300, function () {
            // Önce ai_default_model kontrol et (genel model ayarı)
            $defaultModel = Setting::where('key', 'ai_default_model')->value('value');
            if ($defaultModel) {
                return $defaultModel;
            }

            // Sonra ollama_model kontrol et (Ollama özel ayarı)
            $ollamaModel = Setting::where('key', 'ollama_model')->value('value');
            if ($ollamaModel) {
                return $ollamaModel;
            }

            // Son olarak config'ten al
            return config('ai.ollama_model', 'gemma2:2b');
        });
    }
}
