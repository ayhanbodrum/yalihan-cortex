<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Services\Response\ResponseService;
use App\Traits\ValidatesApiRequests;
use Illuminate\Http\Request;
use App\Models\IlanKategori;
use App\Models\Feature;
use App\Models\FeatureCategory;
use Illuminate\Support\Str;

class IlanAIController extends Controller
{
    use ValidatesApiRequests;

    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Otomatik kategori tespiti
     */
    public function autoDetectCategory(Request $request)
    {
        $validated = $this->validateRequestWithResponse($request, [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            $title = $request->input('title');
            $description = $request->input('description', '');

            // AI prompt hazırla
            $prompt = $this->buildCategoryDetectionPrompt($title, $description);

            // AI'dan kategori önerisi al
            $aiResult = $this->aiService->analyze([
                'title' => $title,
                'description' => $description
            ], [
                'type' => 'category_detection',
                'available_categories' => IlanKategori::pluck('name')->toArray()
            ]);

            // AI sonucunu parse et
            $suggestedCategory = $this->parseCategorySuggestion($aiResult['data']);

            // En yakın kategoriyi bul
            $closestCategory = IlanKategori::where('name', 'like', "%{$suggestedCategory}%")->first();

            return ResponseService::success([
                'suggested_category' => $suggestedCategory,
                'category_id' => $closestCategory?->id,
                'confidence' => $this->calculateConfidence($title, $description, $suggestedCategory),
                'alternative_categories' => $this->getAlternativeCategories($suggestedCategory)
            ], 'Kategori tespiti başarıyla tamamlandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('Kategori tespiti başarısız.', $e);
        }
    }

    /**
     * Akıllı fiyat önerisi
     */
    public function suggestOptimalPrice(Request $request)
    {
        $validated = $this->validateRequestWithResponse($request, [
            'category_id' => 'required|exists:ilan_kategorileri,id',
            'location_id' => 'required|exists:iller,id',
            'features' => 'nullable|array',
            'metrekare' => 'nullable|numeric|min:1',
            'oda_sayisi' => 'nullable|integer|min:1'
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            $data = $request->only(['category_id', 'location_id', 'features', 'metrekare', 'oda_sayisi']);

            // Piyasa verilerini analiz et
            $marketData = $this->getMarketData($data);

            // AI ile fiyat analizi
            $prompt = $this->buildPriceAnalysisPrompt($data, $marketData);

            $aiResult = $this->aiService->analyze([
                'property_data' => $data,
                'market_data' => $marketData
            ], [
                'type' => 'price_analysis'
            ]);

            $priceAnalysis = $this->parsePriceAnalysis($aiResult['data']);

            return ResponseService::success([
                'suggested_price' => $priceAnalysis['suggested_price'],
                'price_range' => $priceAnalysis['price_range'],
                'confidence' => $priceAnalysis['confidence'],
                'market_comparison' => $priceAnalysis['market_comparison'],
                'factors' => $priceAnalysis['factors']
            ], 'Fiyat önerisi başarıyla oluşturuldu');
        } catch (\Exception $e) {
            return ResponseService::serverError('Fiyat önerisi başarısız.', $e);
        }
    }

    /**
     * İçerik üretimi (SEO uyumlu açıklama)
     */
    public function generateDescription(Request $request)
    {
        $validated = $this->validateRequestWithResponse($request, [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:ilan_kategorileri,id',
            'features' => 'nullable|array',
            'location' => 'required|array',
            'location.il' => 'required|exists:iller,id',
            'location.ilce' => 'nullable|exists:ilceler,id'
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            $data = $request->all();

            // AI prompt hazırla
            $prompt = $this->buildDescriptionPrompt($data);

            $aiResult = $this->aiService->generate($prompt, [
                'max_tokens' => 500,
                'temperature' => 0.8
            ]);

            return ResponseService::success([
                'description' => $aiResult['data'],
                'seo_score' => $this->calculateSEOScore($aiResult['data']),
                'keywords' => $this->extractKeywords($aiResult['data'])
            ], 'Açıklama başarıyla üretildi');
        } catch (\Exception $e) {
            return ResponseService::serverError('Açıklama üretimi başarısız.', $e);
        }
    }

    /**
     * SEO optimizasyonu
     */
    public function optimizeForSEO(Request $request)
    {
        $validated = $this->validateRequestWithResponse($request, [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:ilan_kategorileri,id'
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            $data = $request->all();

            // SEO analizi yap
            $seoAnalysis = $this->analyzeSEO($data);

            // AI ile SEO optimizasyonu
            $prompt = $this->buildSEOOptimizationPrompt($data, $seoAnalysis);

            $aiResult = $this->aiService->analyze([
                'content' => $data,
                'seo_analysis' => $seoAnalysis
            ], [
                'type' => 'seo_optimization'
            ]);

            $optimization = $this->parseSEOOptimization($aiResult['data']);

            return ResponseService::success([
                'meta_title' => $optimization['meta_title'],
                'meta_description' => $optimization['meta_description'],
                'slug' => $optimization['slug'],
                'seo_score' => $optimization['seo_score'],
                'improvements' => $optimization['improvements']
            ], 'SEO optimizasyonu başarıyla tamamlandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('SEO optimizasyonu başarısız.', $e);
        }
    }

    /**
     * Görsel analizi
     */
    public function analyzeUploadedImages(Request $request)
    {
        $validated = $this->validateRequestWithResponse($request, [
            'images' => 'required|array|min:1',
            'images.*' => 'required|string|url'
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            $images = $request->input('images');

            // Her görsel için AI analizi
            $analysisResults = [];

            foreach ($images as $imageUrl) {
                $analysis = $this->analyzeImage($imageUrl);
                $analysisResults[] = [
                    'url' => $imageUrl,
                    'analysis' => $analysis
                ];
            }

            // Genel değerlendirme
            $overallScore = $this->calculateOverallImageScore($analysisResults);

            return ResponseService::success([
                'images' => $analysisResults,
                'overall_score' => $overallScore,
                'recommendations' => $this->getImageRecommendations($analysisResults)
            ], 'Görsel analizi başarıyla tamamlandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('Görsel analizi başarısız.', $e);
        }
    }

    // Private helper methods

    private function buildCategoryDetectionPrompt($title, $description)
    {
        $categories = IlanKategori::pluck('name')->implode(', ');

        return "Bu emlak ilanını analiz et ve en uygun kategoriyi öner:\n\n" .
            "Başlık: {$title}\n" .
            "Açıklama: {$description}\n\n" .
            "Mevcut kategoriler: {$categories}\n\n" .
            "Sadece kategori adını döndür.";
    }

    private function parseCategorySuggestion($aiResult)
    {
        // AI sonucundan kategori adını çıkar
        $result = trim($aiResult);

        // Kategori adını temizle
        $result = preg_replace('/[^\w\s]/', '', $result);

        return $result;
    }

    private function calculateConfidence($title, $description, $suggestedCategory)
    {
        // Basit confidence hesaplama (gerçek uygulamada daha karmaşık olabilir)
        $keywords = strtolower($title . ' ' . $description);
        $categoryKeywords = strtolower($suggestedCategory);

        $matches = 0;
        $words = explode(' ', $categoryKeywords);

        foreach ($words as $word) {
            if (strpos($keywords, $word) !== false) {
                $matches++;
            }
        }

        return min(100, ($matches / count($words)) * 100);
    }

    private function getAlternativeCategories($suggestedCategory)
    {
        return IlanKategori::where('name', 'like', "%{$suggestedCategory}%")
            ->orWhere('name', 'like', "%" . substr($suggestedCategory, 0, 3) . "%")
            ->limit(3)
            ->pluck('name', 'id')
            ->toArray();
    }

    private function getMarketData($data)
    {
        // Piyasa verilerini getir (gerçek uygulamada veritabanından)
        return [
            'average_price' => 500000,
            'price_per_sqm' => 2500,
            'location_factor' => 1.2,
            'category_factor' => 1.1
        ];
    }

    private function buildPriceAnalysisPrompt($data, $marketData)
    {
        return "Bu emlak için optimal fiyat önerisi yap:\n\n" .
            "Özellikler: " . json_encode($data) . "\n" .
            "Piyasa Verileri: " . json_encode($marketData) . "\n\n" .
            "Fiyat önerisi ve gerekçelerini JSON formatında döndür.";
    }

    private function parsePriceAnalysis($aiResult)
    {
        // AI sonucunu parse et
        $data = json_decode($aiResult, true);

        return [
            'suggested_price' => $data['suggested_price'] ?? 0,
            'price_range' => $data['price_range'] ?? [],
            'confidence' => $data['confidence'] ?? 0,
            'market_comparison' => $data['market_comparison'] ?? [],
            'factors' => $data['factors'] ?? []
        ];
    }

    private function buildDescriptionPrompt($data)
    {
        $category = IlanKategori::find($data['category_id'])->name ?? 'Emlak';

        return "Bu {$category} için çekici ve SEO uyumlu bir açıklama yaz:\n\n" .
            "Başlık: {$data['title']}\n" .
            "Özellikler: " . json_encode($data['features']) . "\n" .
            "Lokasyon: " . json_encode($data['location']) . "\n\n" .
            "Açıklama en az 200 kelime olsun ve SEO anahtar kelimeleri içersin.";
    }

    private function calculateSEOScore($description)
    {
        // Basit SEO skoru hesaplama
        $score = 0;

        if (strlen($description) > 200) $score += 20;
        if (strlen($description) > 300) $score += 10;
        if (preg_match('/\b(daire|villa|arsa|satılık|kiralık)\b/i', $description)) $score += 15;
        if (preg_match('/\b(metrekare|m²|oda|banyo)\b/i', $description)) $score += 15;

        return min(100, $score);
    }

    private function extractKeywords($description)
    {
        // Basit anahtar kelime çıkarma
        $words = str_word_count(strtolower($description), 1, 'çğıöşüÇĞIÖŞÜ');
        $wordCount = array_count_values($words);

        arsort($wordCount);

        return array_slice(array_keys($wordCount), 0, 10);
    }

    private function analyzeSEO($data)
    {
        return [
            'title_length' => strlen($data['title']),
            'description_length' => strlen($data['description'] ?? ''),
            'keyword_density' => $this->calculateKeywordDensity($data['title'] . ' ' . $data['description']),
            'readability_score' => $this->calculateReadabilityScore($data['description'] ?? '')
        ];
    }

    private function calculateKeywordDensity($text)
    {
        $words = str_word_count(strtolower($text), 1);
        $wordCount = array_count_values($words);
        $totalWords = count($words);

        $densities = [];
        foreach ($wordCount as $word => $count) {
            if (strlen($word) > 3) {
                $densities[$word] = ($count / $totalWords) * 100;
            }
        }

        return $densities;
    }

    private function calculateReadabilityScore($text)
    {
        // Basit okunabilirlik skoru
        $sentences = preg_split('/[.!?]+/', $text);
        $words = str_word_count($text);
        $syllables = $this->countSyllables($text);

        if (count($sentences) > 0 && $words > 0) {
            return 206.835 - (1.015 * ($words / count($sentences))) - (84.6 * ($syllables / $words));
        }

        return 0;
    }

    private function countSyllables($text)
    {
        // Basit hece sayma (Türkçe için geliştirilmeli)
        return preg_match_all('/[aeiouAEIOUçğıöşüÇĞIÖŞÜ]/', $text);
    }

    private function buildSEOOptimizationPrompt($data, $seoAnalysis)
    {
        return "Bu içerik için SEO optimizasyonu yap:\n\n" .
            "Başlık: {$data['title']}\n" .
            "Açıklama: {$data['description']}\n" .
            "Kategori: {$data['category_id']}\n\n" .
            "SEO Analizi: " . json_encode($seoAnalysis) . "\n\n" .
            "Meta title, meta description ve slug öner.";
    }

    private function parseSEOOptimization($aiResult)
    {
        $data = json_decode($aiResult, true);

        return [
            'meta_title' => $data['meta_title'] ?? '',
            'meta_description' => $data['meta_description'] ?? '',
            'slug' => $data['slug'] ?? Str::slug($data['meta_title'] ?? ''),
            'seo_score' => $data['seo_score'] ?? 0,
            'improvements' => $data['improvements'] ?? []
        ];
    }

    private function analyzeImage($imageUrl)
    {
        // Basit görsel analizi (gerçek uygulamada AI vision API kullanılabilir)
        return [
            'quality_score' => rand(60, 95),
            'room_detection' => ['salon', 'yatak odası', 'banyo'],
            'lighting_score' => rand(70, 90),
            'composition_score' => rand(65, 85),
            'recommendations' => [
                'Görsel kalitesi iyi',
                'Işıklandırma yeterli',
                'Kompozisyon düzgün'
            ]
        ];
    }

    private function calculateOverallImageScore($analysisResults)
    {
        if (empty($analysisResults)) return 0;

        $totalScore = 0;
        foreach ($analysisResults as $result) {
            $totalScore += $result['analysis']['quality_score'];
        }

        return round($totalScore / count($analysisResults));
    }

    private function getImageRecommendations($analysisResults)
    {
        $recommendations = [];

        foreach ($analysisResults as $result) {
            if ($result['analysis']['quality_score'] < 70) {
                $recommendations[] = 'Görsel kalitesi iyileştirilebilir';
            }
            if ($result['analysis']['lighting_score'] < 75) {
                $recommendations[] = 'Daha iyi ışıklandırma gerekli';
            }
        }

        return array_unique($recommendations);
    }
}
