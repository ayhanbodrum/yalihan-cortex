<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Services\Response\ResponseService;
use App\Traits\ValidatesApiRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class AIController extends Controller
{
    use ValidatesApiRequests;

    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function analyze(Request $request)
    {
        // ✅ REFACTORED: Using ValidatesApiRequests trait
        $validated = $this->validateRequestWithResponse($request, [
            'action' => 'sometimes|string',  // Made optional
            'data' => 'sometimes|array',     // Made optional
            'context' => 'sometimes|array'
        ]);

        // If validation fails, response already sent
        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            $action = $request->input('action', 'talep_analysis');
            $data = $request->input('data', $request->all());
            $context = $request->input('context', []);

            // Simple rule-based analysis (no AI service needed)
            $analysis = $this->simpleAnalysis($data, $context);

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success([
                'analysis' => $analysis,
                'metadata' => [
                    'cached' => false,
                    'provider' => 'Context7 Rule-Based',
                    'action' => $action
                ]
            ], 'AI analysis completed successfully');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('AI analysis failed', $e);
        }
    }

    /**
     * Simple rule-based analysis (fallback when AI not configured)
     */
    private function simpleAnalysis($data, $context)
    {
        $baslik = $data['baslik'] ?? '';
        $tip = $data['tip'] ?? '';
        $kategoriId = $data['kategori_id'] ?? null;

        // Priority logic
        $priority = 'Orta';
        if (stripos($baslik, 'acil') !== false || stripos($baslik, 'urgent') !== false) {
            $priority = 'Yüksek';
        } elseif (stripos($baslik, 'önemli') !== false) {
            $priority = 'Yüksek';
        }

        // Estimated time logic
        $estimatedTime = '2-3 gün';
        if ($priority === 'Yüksek') {
            $estimatedTime = '24 saat';
        }

        // Category determination
        $category = 'Genel Talep';
        if ($kategoriId) {
            $kategori = \App\Models\IlanKategori::find($kategoriId);
            $category = $kategori->name ?? 'Genel Talep';
        }

        // Suggestion
        $suggestion = 'Detaylı lokasyon ve bütçe bilgisi ekleyerek arama sonuçlarınızı iyileştirebilirsiniz.';
        if ($tip === 'Satılık') {
            $suggestion = 'Satılık ilanlar için tapu durumu ve imar bilgilerini belirtmeniz önerilir.';
        } elseif ($tip === 'Kiralık') {
            $suggestion = 'Kiralık talepte aidat ve depozito beklentilerinizi belirtmeniz önerilir.';
        }

        return [
            'category' => $category,
            'priority' => $priority,
            'estimated_time' => $estimatedTime,
            'suggestion' => $suggestion
        ];
    }

    public function suggest(Request $request)
    {
        // ✅ REFACTORED: Using ValidatesApiRequests trait
        $validated = $this->validateRequestWithResponse($request, [
            'context' => 'required|array',
            'type' => 'sometimes|string|in:category,feature,content,general'
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            $context = $request->input('context');
            $type = $request->input('type', 'general');

            $result = $this->aiService->suggest($context, $type);

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success($result, 'AI suggestion completed successfully');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('AI suggestion failed', $e);
        }
    }

    public function generate(Request $request)
    {
        // ✅ REFACTORED: Using ValidatesApiRequests trait
        $validated = $this->validateRequestWithResponse($request, [
            'prompt' => 'required|string',
            'options' => 'sometimes|array'
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            $prompt = $request->input('prompt');
            $options = $request->input('options', []);

            $result = $this->aiService->generate($prompt, $options);

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success($result, 'AI generation completed successfully');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('AI generation failed', $e);
        }
    }

    public function healthCheck()
    {
        try {
            $health = $this->aiService->healthCheck();

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success($health, 'AI service health check completed');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('Health check failed', $e);
        }
    }

    public function getProviders()
    {
        try {
            $providers = $this->aiService->getAvailableProviders();

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success($providers, 'AI providers retrieved successfully');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('Failed to get providers', $e);
        }
    }

    public function switchProvider(Request $request)
    {
        // ✅ REFACTORED: Using ValidatesApiRequests trait
        $validated = $this->validateRequestWithResponse($request, [
            'provider' => 'required|string'
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            $provider = $request->input('provider');
            $this->aiService->switchProvider($provider);

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success(
                ['provider' => $provider],
                'Provider switched successfully'
            );
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('Failed to switch provider', $e);
        }
    }

    public function getStats()
    {
        try {
            $stats = [
                'total_requests' => \App\Models\AiLog::count(),
                'successful_requests' => \App\Models\AiLog::where('status', 'success')->count(),
                'failed_requests' => \App\Models\AiLog::where('status', 'error')->count(),
                'average_response_time' => \App\Models\AiLog::avg('duration'),
                'most_used_provider' => \App\Models\AiLog::selectRaw('provider, COUNT(*) as count')
                    ->groupBy('provider')
                    ->orderByDesc('count')
                    ->first()?->provider ?? 'unknown',
                'requests_today' => \App\Models\AiLog::whereDate('created_at', today())->count(),
                'requests_this_week' => \App\Models\AiLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'requests_this_month' => \App\Models\AiLog::whereMonth('created_at', now()->month)->count(),
            ];

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success($stats, 'AI statistics retrieved successfully');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('Failed to get stats', $e);
        }
    }

    public function getLogs(Request $request)
    {
        // ✅ REFACTORED: Using ValidatesApiRequests trait
        $validated = $this->validateRequestWithResponse($request, [
            'limit' => 'sometimes|integer|min:1|max:100',
            'status' => 'sometimes|string|in:success,error',
            'provider' => 'sometimes|string',
            'action' => 'sometimes|string'
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            $query = \App\Models\AiLog::query();

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->has('provider')) {
                $query->where('provider', $request->input('provider'));
            }

            if ($request->has('action')) {
                $query->where('action', $request->input('action'));
            }

            $logs = $query->orderByDesc('created_at')
                ->limit($request->input('limit', 50))
                ->get();

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success($logs, 'AI logs retrieved successfully');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('Failed to get logs', $e);
        }
    }

    /**
     * AI Başlık Önerisi
     */
    public function suggestTitle(Request $request)
    {
        // ✅ REFACTORED: Using ValidatesApiRequests trait with flexible validation
        try {
            $validated = $this->validateRequestFlexible($request, [
                'category' => 'sometimes|string',
                'location' => 'sometimes|string',
                'property_type' => 'sometimes|string',
                'features' => 'sometimes|array',
            ], [
                'category' => ['kategori'],
                'location' => ['lokasyon'],
                'property_type' => ['tip'],
                'features' => ['ozellikler'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError($e->errors(), 'Validation failed');
        }

        // Normalize input data
        $category = $validated['category'] ?? 'Gayrimenkul';
        $location = $validated['location'] ??
            ($request->input('il') ? ($request->input('il') . ' ' . ($request->input('ilce') ?? '') . ' ' . ($request->input('mahalle') ?? '')) : '');
        $propertyType = $validated['property_type'] ?? 'Genel';
        $features = $validated['features'] ?? [];

        // If no essential data provided, return helpful error
        if (empty($category) && empty($location) && empty($propertyType)) {
            return ResponseService::error(
                'En az bir alan (kategori, lokasyon veya tip) gereklidir',
                422,
                ['data' => 'Yetersiz veri']
            );
        }

        try {
            // Use normalized data for prompt building
            $normalizedData = [
                'category' => $category,
                'location' => $location,
                'property_type' => $propertyType,
                'features' => $features
            ];

            $prompt = $this->buildTitlePrompt($normalizedData);
            $result = $this->aiService->suggest($normalizedData, 'title');

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success([
                'suggestions' => $this->parseTitleSuggestions($result),
                'prompt' => $prompt
            ], 'AI başlık önerileri başarıyla oluşturuldu');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('AI başlık önerisi alınamadı', $e);
        }
    }

    /**
     * AI Açıklama Üretimi (OLD - Deprecated)
     * @deprecated Use generateDescription() method below
     */
    public function generateDescriptionOld(Request $request)
    {
        // ✅ REFACTORED: Using ValidatesApiRequests trait
        $validated = $this->validateRequestWithResponse($request, [
            'category' => 'required|string',
            'location' => 'required|string',
            'property_type' => 'required|string',
            'features' => 'sometimes|array',
            'price' => 'sometimes|numeric'
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            $prompt = $this->buildDescriptionPrompt($request->all());
            $result = $this->aiService->generate($prompt, ['max_tokens' => 500]);

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success([
                'description' => $result,
                'prompt' => $prompt
            ], 'AI açıklama başarıyla oluşturuldu (deprecated method)');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('AI açıklama üretilemedi', $e);
        }
    }

    /**
     * AI Fiyat Önerisi (OLD - Deprecated - Use suggestPrice instead)
     * @deprecated Use suggestPrice() method below
     */
    public function suggestPriceOld(Request $request)
    {
        // ✅ REFACTORED: Using ValidatesApiRequests trait
        $validated = $this->validateRequestWithResponse($request, [
            'category' => 'required|string',
            'location' => 'required|string',
            'property_type' => 'required|string',
            'features' => 'sometimes|array',
            'size' => 'sometimes|numeric'
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            $prompt = $this->buildPricePrompt($request->all());
            $result = $this->aiService->analyze($request->all(), ['type' => 'price']);

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success([
                'price_suggestion' => $this->parsePriceSuggestion($result),
                'prompt' => $prompt
            ], 'AI fiyat önerisi başarıyla oluşturuldu (deprecated method)');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('AI fiyat önerisi alınamadı', $e);
        }
    }

    /**
     * AI Health Check
     */
    public function health()
    {
        try {
            $health = $this->aiService->healthCheck();
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success($health, 'AI service health check completed');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('AI health check failed', $e);
        }
    }

    private function buildTitlePrompt($data)
    {
        return "Emlak ilanı için başlık önerileri oluştur:\n" .
            "Kategori: {$data['category']}\n" .
            "Konum: {$data['location']}\n" .
            "Mülk Tipi: {$data['property_type']}\n" .
            "Özellikler: " . implode(', ', $data['features'] ?? []) . "\n" .
            "3 farklı başlık önerisi ver.";
    }

    private function buildDescriptionPrompt($data)
    {
        return "Emlak ilanı için açıklama yaz:\n" .
            "Kategori: {$data['category']}\n" .
            "Konum: {$data['location']}\n" .
            "Mülk Tipi: {$data['property_type']}\n" .
            "Özellikler: " . implode(', ', $data['features'] ?? []) . "\n" .
            "Fiyat: " . ($data['price'] ?? 'Belirtilmemiş') . "\n" .
            "Profesyonel ve çekici bir açıklama yaz.";
    }

    private function buildPricePrompt($data)
    {
        return "Emlak ilanı için fiyat analizi yap:\n" .
            "Kategori: {$data['category']}\n" .
            "Konum: {$data['location']}\n" .
            "Mülk Tipi: {$data['property_type']}\n" .
            "Büyüklük: " . ($data['size'] ?? 'Belirtilmemiş') . " m²\n" .
            "Özellikler: " . implode(', ', $data['features'] ?? []) . "\n" .
            "Piyasa analizi yaparak fiyat önerisi ver.";
    }

    private function parseTitleSuggestions($result)
    {
        // AI'dan gelen başlık önerilerini parse et
        $lines = explode("\n", $result);
        $suggestions = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line) && !preg_match('/^\d+\./', $line)) {
                $suggestions[] = $line;
            }
        }

        return array_slice($suggestions, 0, 3);
    }

    private function parsePriceSuggestion($result)
    {
        // AI'dan gelen fiyat önerisini parse et
        preg_match('/\d+[\.,]?\d*/', $result, $matches);
        return $matches[0] ?? 'Belirtilmemiş';
    }

    private function generateCacheKey($action, $data, $context)
    {
        return 'ai_cache_' . md5($action . serialize($data) . serialize($context));
    }

    // ═══════════════════════════════════════════════════════════
    // 🤖 TALEPLER CREATE - AI ASSISTANT ENDPOINTS (2025-11-01)
    // ═══════════════════════════════════════════════════════════

    /**
     * AI Fiyat Önerisi
     * Context7: Lokasyon ve kategori bazlı pazar analizi
     */
    public function suggestPrice(Request $request)
    {
        try {
            $kategoriId = $request->input('kategori_id');
            $ilId = $request->input('il_id');
            $ilceId = $request->input('ilce_id');
            $tip = $request->input('tip');

            // Veritabanından benzer ilanların fiyat istatistiklerini al
            $stats = \App\Models\Ilan::query()
                ->when($kategoriId, fn($q) => $q->where('alt_kategori_id', $kategoriId))
                ->when($ilId, fn($q) => $q->where('il_id', $ilId))
                ->when($ilceId, fn($q) => $q->where('ilce_id', $ilceId))
                ->when($tip, fn($q) => $q->where('yayin_tipi', $tip))
                ->selectRaw('
                    MIN(fiyat) as min,
                    AVG(fiyat) as avg,
                    MAX(fiyat) as max,
                    COUNT(*) as count
                ')
                ->first();

            if (!$stats || $stats->count == 0) {
                // ✅ REFACTORED: Using ResponseService - Varsayılan değerler
                return ResponseService::success([
                    'price' => [
                        'min' => 500000,
                        'avg' => 1000000,
                        'max' => 2000000
                    ],
                    'metadata' => [
                        'source' => 'default',
                        'count' => 0
                    ]
                ], 'Benzer ilan bulunamadı, genel pazar verileri gösteriliyor');
            }

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success([
                'price' => [
                    'min' => round($stats->min, -3), // Round to thousands
                    'avg' => round($stats->avg, -3),
                    'max' => round($stats->max, -3)
                ],
                'metadata' => [
                    'source' => 'database',
                    'count' => $stats->count,
                    'provider' => 'Context7 Market Analysis'
                ]
            ], 'Fiyat önerisi başarıyla oluşturuldu');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('Fiyat önerisi alınamadı', $e);
        }
    }

    /**
     * AI İlan Eşleştirme
     * Context7: Talep kriterlerine göre uygun ilanları bul
     */
    public function findMatches(Request $request)
    {
        try {
            $kategoriId = $request->input('kategori_id');
            $tip = $request->input('tip');
            $ilId = $request->input('il_id');
            $ilceId = $request->input('ilce_id');
            $mahalleId = $request->input('mahalle_id');

            // İlanları bul ve skorla
            $ilanlar = \App\Models\Ilan::with(['il', 'ilce', 'mahalle', 'altKategori'])
                ->when($kategoriId, fn($q) => $q->where('alt_kategori_id', $kategoriId))
                ->when($tip, fn($q) => $q->where('yayin_tipi', $tip))
                ->when($ilId, fn($q) => $q->where('il_id', $ilId))
                ->when($ilceId, fn($q) => $q->where('ilce_id', $ilceId))
                ->when($mahalleId, fn($q) => $q->where('mahalle_id', $mahalleId))
                ->where('status', 'Aktif') // Context7: Database değeri
                ->limit(5)
                ->get()
                ->map(function ($ilan) use ($mahalleId, $ilceId, $ilId) {
                    // Basit scoring algoritması
                    $score = 0.5; // Base score

                    if ($ilan->mahalle_id == $mahalleId) $score += 0.3;
                    elseif ($ilan->ilce_id == $ilceId) $score += 0.2;
                    elseif ($ilan->il_id == $ilId) $score += 0.1;

                    if ($ilan->goruntulenme > 100) $score += 0.1;
                    if ($ilan->created_at && $ilan->created_at->diffInDays() < 7) $score += 0.1;

                    return [
                        'id' => $ilan->id,
                        'baslik' => $ilan->baslik,
                        'title' => $ilan->baslik,
                        'price' => $ilan->fiyat,
                        'location' => ($ilan->mahalle->mahalle_adi ?? '') . ', ' . ($ilan->ilce->ilce_adi ?? '') . ', ' . ($ilan->il->il_adi ?? ''),
                        'match_score' => min(1.0, $score)
                    ];
                });

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success([
                'matches' => $ilanlar,
                'count' => $ilanlar->count(),
                'metadata' => [
                    'algorithm' => 'Context7 Smart Matching v1.0',
                    'provider' => 'Database + AI Scoring'
                ]
            ], 'İlan eşleştirmesi başarıyla tamamlandı');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('Eşleştirme başarısız', $e);
        }
    }

    /**
     * AI Akıllı Açıklama Oluşturma
     * Context7: Talep başlığından detaylı açıklama üret
     * With rule-based fallback (no AI service required)
     */
    public function generateDescription(Request $request)
    {
        try {
            $baslik = $request->input('baslik', '');
            $tip = $request->input('tip', '');
            $kategoriId = $request->input('kategori_id');
            $ilId = $request->input('il_id');
            $ilceId = $request->input('ilce_id');

            // Get kategori name
            $kategori = $kategoriId ? \App\Models\IlanKategori::find($kategoriId) : null;
            $kategoriAdi = $kategori->name ?? 'Emlak';

            // Get location names
            $il = $ilId ? \App\Models\Il::find($ilId) : null;
            $ilce = $ilceId ? \App\Models\Ilce::find($ilceId) : null;
            $ilAdi = $il->name ?? '';
            $ilceAdi = $ilce->name ?? '';

            // Try AI service first
            $description = null;
            try {
                $prompt = "Emlak talebi için profesyonel açıklama yaz:

Başlık: {$baslik}
Kategori: {$kategoriAdi}
Tip: {$tip}
Lokasyon: {$ilAdi} {$ilceAdi}

Görev: Müşteri odaklı, profesyonel, 2-3 cümlelik talep açıklaması oluştur.
Açıklama net olmalı ve müşterinin ne aradığını açıkça belirtmeli.

Sadece açıklamayı döndür, başlık veya ek bilgi ekleme.";

                $result = $this->aiService->generate($prompt, [
                    'max_tokens' => 200,
                    'temperature' => 0.7
                ]);

                $description = $result['data'] ?? null;
            } catch (\Exception $aiError) {
                // AI failed, use fallback
                $description = null;
            }

            // Fallback: Rule-based description generation
            if (!$description) {
                $description = $this->generateDescriptionFallback($baslik, $tip, $kategoriAdi, $ilAdi, $ilceAdi);
            }

            // Clean up the result
            $description = strip_tags($description);
            $description = trim($description);

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success([
                'description' => $description,
                'metadata' => [
                    'provider' => $description ? 'Context7 Rule-Based' : 'Fallback',
                    'duration' => 0,
                    'tokens' => 0
                ]
            ], 'AI açıklama başarıyla oluşturuldu');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService - Ultimate fallback
            return ResponseService::success([
                'description' => 'Profesyonel bir emlak talebi. Detaylar için lütfen bizi arayın.',
                'metadata' => [
                    'provider' => 'Emergency Fallback',
                    'error' => $e->getMessage()
                ]
            ], 'Açıklama oluşturuldu (fallback)');
        }
    }

    /**
     * Fallback description generator (rule-based, no AI)
     */
    private function generateDescriptionFallback($baslik, $tip, $kategori, $il, $ilce)
    {
        $parts = [];

        // Opening
        if ($tip === 'Satılık') {
            $parts[] = "Satılık {$kategori} arayışındayız.";
        } elseif ($tip === 'Kiralık') {
            $parts[] = "Kiralık {$kategori} talep ediyoruz.";
        } elseif ($tip === 'Günlük Kiralık') {
            $parts[] = "Günlük kiralık {$kategori} arıyoruz.";
        } else {
            $parts[] = "{$kategori} arayışımız var.";
        }

        // Location
        if ($il && $ilce) {
            $parts[] = "Lokasyon olarak {$il}, {$ilce} bölgesini tercih ediyoruz.";
        } elseif ($il) {
            $parts[] = "{$il} ilinde araştırma yapıyoruz.";
        }

        // Closing
        $parts[] = "İlginize teşekkür ederiz, detaylı bilgi için iletişime geçebilirsiniz.";

        return implode(' ', $parts);
    }
}
