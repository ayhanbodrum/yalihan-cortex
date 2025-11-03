<?php

namespace App\Services;

use App\Models\IlanKategori;
use App\Models\Ilan;
use App\Services\AIService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AICategorySuggestionService
{
    protected $aiService;
    protected $cachePrefix = 'ai_category_suggestions';

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * AI-Powered kategori önerileri sistemi
     */
    public function suggestCategories($title, $description = '', $features = [])
    {
        $cacheKey = $this->generateCacheKey($title, $description, $features);

        // Cache'den kontrol et
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        try {
            // AI analizi yap
            $suggestions = $this->performAIAnalysis($title, $description, $features);

            // Trend analizi ekle
            $trendAnalysis = $this->analyzeTrends($suggestions);

            // Performans metrikleri hesapla
            $performanceMetrics = $this->calculatePerformanceMetrics($suggestions);

            // SEO skorlama yap
            $seoScoring = $this->calculateSEOScores($suggestions);

            $result = [
                'suggestions' => $suggestions,
                'trend_analysis' => $trendAnalysis,
                'performance_metrics' => $performanceMetrics,
                'seo_scoring' => $seoScoring,
                'confidence_score' => $this->calculateConfidenceScore($suggestions),
                'generated_at' => now(),
                'cache_duration' => 3600 // 1 saat cache
            ];

            // Cache'e kaydet
            Cache::put($cacheKey, $result, 3600);

            return $result;
        } catch (\Exception $e) {
            Log::error('AI Category Suggestion failed: ' . $e->getMessage());

            // Fallback: basit kategori eşleştirme
            return $this->fallbackCategoryMatching($title, $description);
        }
    }

    /**
     * AI analizi gerçekleştir
     */
    protected function performAIAnalysis($title, $description, $features)
    {
        $prompt = $this->buildAnalysisPrompt($title, $description, $features);

        $aiResponse = $this->aiService->generate($prompt, 'json');

        $analysis = json_decode($aiResponse, true);

        if (!$analysis) {
            throw new \Exception('AI response could not be parsed');
        }

        // Kategori önerilerini doğrula ve genişlet
        return $this->validateAndEnhanceSuggestions($analysis);
    }

    /**
     * AI analiz prompt'u oluştur
     */
    protected function buildAnalysisPrompt($title, $description, $features)
    {
        $availableCategories = $this->getAvailableCategories();

        $prompt = "Bir emlak ilanı için en uygun kategorileri öner. Analiz ettiğin içeriğe göre kategori önerilerini JSON formatında döndür.

İlan Bilgileri:
- Başlık: {$title}
- Açıklama: {$description}
- Özellikler: " . implode(', ', $features) . "

Mevcut Kategoriler:
" . implode(', ', $availableCategories) . "

Analiz kriterleri:
1. İlan içeriğinin ana özelliklerini tespit et
2. Emlak türünü belirle (daire, villa, arsa, ofis, dükkan vb.)
3. Kullanım amacını analiz et (satılık, kiralık, turistik vb.)
4. Lokasyon özelliklerini değerlendir
5. Fiyat seviyesini tahmin et

JSON formatında şu bilgileri döndür:
{
  \"primary_category\": \"Ana kategori\",
  \"secondary_categories\": [\"İkincil kategori 1\", \"İkincil kategori 2\"],
  \"confidence_scores\": {
    \"primary\": 0.95,
    \"secondary\": [0.85, 0.75]
  },
  \"reasoning\": \"Kategori seçim gerekçesi\",
  \"keywords\": [\"anahtar\", \"kelimeler\"],
  \"market_segment\": \"Pazar segmenti\",
  \"price_range_estimate\": \"Fiyat aralığı tahmini\"
}";

        return $prompt;
    }

    /**
     * Mevcut kategorileri getir
     */
    protected function getAvailableCategories()
    {
        return Cache::remember('available_categories', 3600, function () {
            return IlanKategori::where('status', 'active')
                ->orderBy('order')
                ->pluck('name')
                ->toArray();
        });
    }

    /**
     * Önerileri doğrula ve genişlet
     */
    protected function validateAndEnhanceSuggestions($analysis)
    {
        $suggestions = [];

        // Ana kategori doğrula
        if (isset($analysis['primary_category'])) {
            $primaryCategory = $this->findCategoryByName($analysis['primary_category']);
            if ($primaryCategory) {
                $suggestions['primary'] = [
                    'category' => $primaryCategory,
                    'confidence' => $analysis['confidence_scores']['primary'] ?? 0.8,
                    'reasoning' => $analysis['reasoning'] ?? 'AI analizi'
                ];
            }
        }

        // İkincil kategoriler doğrula
        if (isset($analysis['secondary_categories'])) {
            $secondaryCategories = [];
            foreach ($analysis['secondary_categories'] as $index => $categoryName) {
                $category = $this->findCategoryByName($categoryName);
                if ($category) {
                    $secondaryCategories[] = [
                        'category' => $category,
                        'confidence' => $analysis['confidence_scores']['secondary'][$index] ?? 0.7,
                        'reasoning' => 'İkincil kategori önerisi'
                    ];
                }
            }
            $suggestions['secondary'] = $secondaryCategories;
        }

        // Ek bilgiler ekle
        $suggestions['metadata'] = [
            'keywords' => $analysis['keywords'] ?? [],
            'market_segment' => $analysis['market_segment'] ?? null,
            'price_range_estimate' => $analysis['price_range_estimate'] ?? null,
            'analysis_timestamp' => now()
        ];

        return $suggestions;
    }

    /**
     * İsim ile kategori bul
     */
    protected function findCategoryByName($name)
    {
        return IlanKategori::where('name', 'LIKE', "%{$name}%")
            ->where('status', 'active')
            ->first();
    }

    /**
     * Trend analizi yap
     */
    protected function analyzeTrends($suggestions)
    {
        $trends = [];

        if (isset($suggestions['primary']['category'])) {
            $categoryId = $suggestions['primary']['category']->id;

            // Son 30 günlük trend analizi
            $trends['popularity'] = $this->calculateCategoryPopularity($categoryId);
            $trends['growth'] = $this->calculateCategoryGrowth($categoryId);
            $trends['seasonal'] = $this->calculateSeasonalTrend($categoryId);
        }

        return $trends;
    }

    /**
     * Kategori popülerliği hesapla
     */
    protected function calculateCategoryPopularity($categoryId)
    {
        return Cache::remember("category_popularity_{$categoryId}", 1800, function () use ($categoryId) {
            $totalIlanlar = Ilan::count();
            $categoryIlanlar = Ilan::where('kategori_id', $categoryId)->count();

            return $totalIlanlar > 0 ? ($categoryIlanlar / $totalIlanlar) * 100 : 0;
        });
    }

    /**
     * Kategori büyüme oranı hesapla
     */
    protected function calculateCategoryGrowth($categoryId)
    {
        return Cache::remember("category_growth_{$categoryId}", 1800, function () use ($categoryId) {
            $last30Days = Ilan::where('kategori_id', $categoryId)
                ->where('created_at', '>=', now()->subDays(30))
                ->count();

            $previous30Days = Ilan::where('kategori_id', $categoryId)
                ->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])
                ->count();

            if ($previous30Days == 0) {
                return $last30Days > 0 ? 100 : 0;
            }

            return (($last30Days - $previous30Days) / $previous30Days) * 100;
        });
    }

    /**
     * Mevsimsel trend hesapla
     */
    protected function calculateSeasonalTrend($categoryId)
    {
        $currentMonth = now()->month;
        $seasonalData = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthCount = Ilan::where('kategori_id', $categoryId)
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', now()->year)
                ->count();

            $seasonalData[] = [
                'month' => $i,
                'count' => $monthCount,
                'is_current' => $i == $currentMonth
            ];
        }

        return $seasonalData;
    }

    /**
     * Performans metrikleri hesapla
     */
    protected function calculatePerformanceMetrics($suggestions)
    {
        $metrics = [];

        if (isset($suggestions['primary']['category'])) {
            $categoryId = $suggestions['primary']['category']->id;

            $metrics['average_view_time'] = $this->getAverageViewTime($categoryId);
            $metrics['conversion_rate'] = $this->getConversionRate($categoryId);
            $metrics['engagement_score'] = $this->getEngagementScore($categoryId);
            $metrics['search_volume'] = $this->getSearchVolume($categoryId);
        }

        return $metrics;
    }

    /**
     * Ortalama görüntülenme süresi
     */
    protected function getAverageViewTime($categoryId)
    {
        // Bu veri gerçek uygulamada analytics sisteminden gelecek
        return Cache::remember("avg_view_time_{$categoryId}", 3600, function () use ($categoryId) {
            return rand(30, 300); // Örnek veri (saniye)
        });
    }

    /**
     * Dönüşüm oranı
     */
    protected function getConversionRate($categoryId)
    {
        return Cache::remember("conversion_rate_{$categoryId}", 3600, function () use ($categoryId) {
            // Gerçek uygulamada analytics'ten gelecek
            return rand(5, 25) / 100; // 5-25% arası
        });
    }

    /**
     * Etkileşim skoru
     */
    protected function getEngagementScore($categoryId)
    {
        return Cache::remember("engagement_score_{$categoryId}", 3600, function () use ($categoryId) {
            // Gerçek uygulamada analytics'ten gelecek
            return rand(60, 95); // 60-95 arası skor
        });
    }

    /**
     * Arama hacmi
     */
    protected function getSearchVolume($categoryId)
    {
        return Cache::remember("search_volume_{$categoryId}", 3600, function () use ($categoryId) {
            // Gerçek uygulamada search logs'tan gelecek
            return rand(100, 5000); // Aylık arama sayısı
        });
    }

    /**
     * SEO skorlama yap
     */
    protected function calculateSEOScores($suggestions)
    {
        $seoScores = [];

        if (isset($suggestions['primary']['category'])) {
            $category = $suggestions['primary']['category'];

            $seoScores['keyword_density'] = $this->calculateKeywordDensity($category);
            $seoScores['search_competition'] = $this->calculateSearchCompetition($category);
            $seoScores['local_seo_potential'] = $this->calculateLocalSEOPotential($category);
            $seoScores['content_optimization'] = $this->calculateContentOptimization($category);
        }

        return $seoScores;
    }

    /**
     * Anahtar kelime yoğunluğu
     */
    protected function calculateKeywordDensity($category)
    {
        // Kategori adının arama terimlerinde geçme sıklığı
        return Cache::remember("keyword_density_{$category->id}", 3600, function () use ($category) {
            // Gerçek uygulamada search analytics'ten gelecek
            return rand(70, 95); // 70-95% arası
        });
    }

    /**
     * Arama rekabeti
     */
    protected function calculateSearchCompetition($category)
    {
        return Cache::remember("search_competition_{$category->id}", 3600, function () use ($category) {
            // Gerçek uygulamada market analysis'ten gelecek
            return rand(30, 80); // 30-80% arası rekabet
        });
    }

    /**
     * Yerel SEO potansiyeli
     */
    protected function calculateLocalSEOPotential($category)
    {
        return Cache::remember("local_seo_{$category->id}", 3600, function () use ($category) {
            // Gerçek uygulamada location-based analytics'ten gelecek
            return rand(60, 90); // 60-90% arası potansiyel
        });
    }

    /**
     * İçerik optimizasyonu
     */
    protected function calculateContentOptimization($category)
    {
        return Cache::remember("content_optimization_{$category->id}", 3600, function () use ($category) {
            // Gerçek uygulamada content analysis'ten gelecek
            return rand(75, 95); // 75-95% arası optimizasyon
        });
    }

    /**
     * Güven skoru hesapla
     */
    protected function calculateConfidenceScore($suggestions)
    {
        $totalScore = 0;
        $factorCount = 0;

        if (isset($suggestions['primary']['confidence'])) {
            $totalScore += $suggestions['primary']['confidence'] * 0.5;
            $factorCount += 0.5;
        }

        if (isset($suggestions['secondary'])) {
            $secondaryCount = count($suggestions['secondary']);
            if ($secondaryCount > 0) {
                $avgSecondaryConfidence = array_sum(array_column($suggestions['secondary'], 'confidence')) / $secondaryCount;
                $totalScore += $avgSecondaryConfidence * 0.3;
                $factorCount += 0.3;
            }
        }

        // Trend analizi skorunu ekle
        if (isset($suggestions['trend_analysis']['popularity'])) {
            $popularityScore = min($suggestions['trend_analysis']['popularity'] / 100, 1);
            $totalScore += $popularityScore * 0.2;
            $factorCount += 0.2;
        }

        return $factorCount > 0 ? $totalScore / $factorCount : 0.5;
    }

    /**
     * Fallback kategori eşleştirme
     */
    protected function fallbackCategoryMatching($title, $description)
    {
        $text = strtolower($title . ' ' . $description);
        $categories = $this->getAvailableCategories();

        $matches = [];
        foreach ($categories as $category) {
            $keywords = $this->getCategoryKeywords($category);
            $score = 0;

            foreach ($keywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    $score += 1;
                }
            }

            if ($score > 0) {
                $matches[] = [
                    'category' => $category,
                    'score' => $score,
                    'confidence' => min($score / count($keywords), 1)
                ];
            }
        }

        // Skorlara göre sırala
        usort($matches, function ($a, $b) {
            return $b['score'] - $a['score'];
        });

        return [
            'suggestions' => [
                'primary' => isset($matches[0]) ? $matches[0] : null,
                'secondary' => array_slice($matches, 1, 3),
                'metadata' => [
                    'method' => 'fallback',
                    'generated_at' => now()
                ]
            ],
            'confidence_score' => isset($matches[0]) ? $matches[0]['confidence'] : 0.3
        ];
    }

    /**
     * Kategori anahtar kelimeleri
     */
    protected function getCategoryKeywords($category)
    {
        $keywordMap = [
            'Satılık Daire' => ['daire', 'apartman', 'satılık', 'ev'],
            'Kiralık Daire' => ['daire', 'apartman', 'kiralık', 'kira'],
            'Satılık Villa' => ['villa', 'müstakil', 'satılık', 'ev'],
            'Kiralık Villa' => ['villa', 'müstakil', 'kiralık', 'kira'],
            'Satılık Arsa' => ['arsa', 'arazi', 'satılık', 'tarla'],
            'Kiralık Ofis' => ['ofis', 'işyeri', 'kiralık', 'kira'],
            'Satılık Dükkan' => ['dükkan', 'mağaza', 'satılık', 'işyeri']
        ];

        return $keywordMap[$category] ?? [strtolower($category)];
    }

    /**
     * Cache key oluştur
     */
    protected function generateCacheKey($title, $description, $features)
    {
        $key = md5($title . $description . implode('', $features));
        return "{$this->cachePrefix}_{$key}";
    }

    /**
     * Otomatik etiketleme (ML tabanlı)
     */
    public function autoTagging($content)
    {
        $prompt = "Bu emlak ilanı içeriğini analiz et ve uygun etiketleri öner:

İçerik: {$content}

Aşağıdaki etiket kategorilerinden uygun olanları seç:
- Lokasyon: şehir, ilçe, mahalle
- Emlak türü: daire, villa, arsa, ofis, dükkan
- Özellikler: yeni, ikinci el, tadilatlı, sıfır
- Fiyat seviyesi: ekonomik, orta, lüks
- Durum: satılık, kiralık, kiralık-satılık

JSON formatında döndür:
{
  \"tags\": {
    \"location\": [\"etiket1\", \"etiket2\"],
    \"property_type\": [\"etiket1\", \"etiket2\"],
    \"features\": [\"etiket1\", \"etiket2\"],
    \"price_level\": [\"etiket1\"],
    \"status\": [\"etiket1\"]
  },
  \"confidence\": 0.85
}";

        try {
            $response = $this->aiService->generate($prompt, 'json');
            return json_decode($response, true);
        } catch (\Exception $e) {
            Log::error('Auto tagging failed: ' . $e->getMessage());
            return ['tags' => [], 'confidence' => 0];
        }
    }

    /**
     * Kategori performans raporu
     */
    public function getCategoryPerformanceReport($categoryId = null)
    {
        $query = IlanKategori::query();

        if ($categoryId) {
            $query->where('id', $categoryId);
        }

        $categories = $query->where('status', 'active')->get();

        $report = [];
        foreach ($categories as $category) {
            $report[] = [
                'category' => $category,
                'total_listings' => Ilan::where('kategori_id', $category->id)->count(),
                'popularity_score' => $this->calculateCategoryPopularity($category->id),
                'growth_rate' => $this->calculateCategoryGrowth($category->id),
                'seo_score' => $this->calculateSEOScores(['primary' => ['category' => $category]])['keyword_density'] ?? 0,
                'recommendations' => $this->generateCategoryRecommendations($category)
            ];
        }

        return $report;
    }

    /**
     * Kategori önerileri oluştur
     */
    protected function generateCategoryRecommendations($category)
    {
        $recommendations = [];

        $popularity = $this->calculateCategoryPopularity($category->id);
        $growth = $this->calculateCategoryGrowth($category->id);

        if ($popularity < 10) {
            $recommendations[] = 'Bu kategori düşük popülerlikte. İçerik optimizasyonu yapın.';
        }

        if ($growth < -10) {
            $recommendations[] = 'Kategori büyüme oranı düşük. Trend analizi yapın.';
        }

        if ($popularity > 50 && $growth > 20) {
            $recommendations[] = 'Kategori çok popüler ve büyüyor. Yatırım fırsatı!';
        }

        return $recommendations;
    }
}
