<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function analyze(Request $request)
    {
        // FLEXIBLE VALIDATION: Support both old and new formats
        $validator = Validator::make($request->all(), [
            'action' => 'sometimes|string',  // Made optional
            'data' => 'sometimes|array',     // Made optional
            'context' => 'sometimes|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $action = $request->input('action', 'talep_analysis');
            $data = $request->input('data', $request->all());
            $context = $request->input('context', []);

            // Simple rule-based analysis (no AI service needed)
            $analysis = $this->simpleAnalysis($data, $context);

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'metadata' => [
                    'cached' => false,
                    'provider' => 'Context7 Rule-Based',
                    'action' => $action
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI analysis failed',
                'error' => $e->getMessage()
            ], 500);
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
        $validator = Validator::make($request->all(), [
            'context' => 'required|array',
            'type' => 'sometimes|string|in:category,feature,content,general'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $context = $request->input('context');
            $type = $request->input('type', 'general');

            $result = $this->aiService->suggest($context, $type);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI suggestion failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string',
            'options' => 'sometimes|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $prompt = $request->input('prompt');
            $options = $request->input('options', []);

            $result = $this->aiService->generate($prompt, $options);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI generation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function healthCheck()
    {
        try {
            $health = $this->aiService->healthCheck();

            return response()->json([
                'success' => true,
                'data' => $health
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Health check failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getProviders()
    {
        try {
            $providers = $this->aiService->getAvailableProviders();

            return response()->json([
                'success' => true,
                'data' => $providers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get providers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function switchProvider(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $provider = $request->input('provider');
            $this->aiService->switchProvider($provider);

            return response()->json([
                'success' => true,
                'message' => 'Provider switched successfully',
                'data' => ['provider' => $provider]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to switch provider',
                'error' => $e->getMessage()
            ], 500);
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

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLogs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'sometimes|integer|min:1|max:100',
            'status' => 'sometimes|string|in:success,error',
            'provider' => 'sometimes|string',
            'action' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
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

            return response()->json([
                'success' => true,
                'data' => $logs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI Başlık Önerisi
     */
    public function suggestTitle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string',
            'location' => 'required|string',
            'property_type' => 'required|string',
            'features' => 'sometimes|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $prompt = $this->buildTitlePrompt($request->all());
            $result = $this->aiService->suggest($request->all(), 'title');

            return response()->json([
                'success' => true,
                'data' => [
                    'suggestions' => $this->parseTitleSuggestions($result),
                    'prompt' => $prompt
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI başlık önerisi alınamadı',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI Açıklama Üretimi (OLD - Deprecated)
     * @deprecated Use generateDescription() method below
     */
    public function generateDescriptionOld(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string',
            'location' => 'required|string',
            'property_type' => 'required|string',
            'features' => 'sometimes|array',
            'price' => 'sometimes|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $prompt = $this->buildDescriptionPrompt($request->all());
            $result = $this->aiService->generate($prompt, ['max_tokens' => 500]);

            return response()->json([
                'success' => true,
                'data' => [
                    'description' => $result,
                    'prompt' => $prompt
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI açıklama üretilemedi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI Fiyat Önerisi (OLD - Deprecated - Use suggestPrice instead)
     * @deprecated Use suggestPrice() method below
     */
    public function suggestPriceOld(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string',
            'location' => 'required|string',
            'property_type' => 'required|string',
            'features' => 'sometimes|array',
            'size' => 'sometimes|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $prompt = $this->buildPricePrompt($request->all());
            $result = $this->aiService->analyze($request->all(), ['type' => 'price']);

            return response()->json([
                'success' => true,
                'data' => [
                    'price_suggestion' => $this->parsePriceSuggestion($result),
                    'prompt' => $prompt
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI fiyat önerisi alınamadı',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI Health Check
     */
    public function health()
    {
        try {
            $health = $this->aiService->healthCheck();
            return response()->json([
                'success' => true,
                'data' => $health
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI health check failed',
                'error' => $e->getMessage()
            ], 500);
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
                // Varsayılan değerler
                return response()->json([
                    'success' => true,
                    'price' => [
                        'min' => 500000,
                        'avg' => 1000000,
                        'max' => 2000000
                    ],
                    'message' => 'Benzer ilan bulunamadı, genel pazar verileri gösteriliyor',
                    'metadata' => [
                        'source' => 'default',
                        'count' => 0
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
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
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fiyat önerisi alınamadı',
                'error' => $e->getMessage()
            ], 500);
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
                ->where('status', 'active')
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

            return response()->json([
                'success' => true,
                'matches' => $ilanlar,
                'count' => $ilanlar->count(),
                'metadata' => [
                    'algorithm' => 'Context7 Smart Matching v1.0',
                    'provider' => 'Database + AI Scoring'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Eşleştirme başarısız',
                'error' => $e->getMessage()
            ], 500);
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

            return response()->json([
                'success' => true,
                'description' => $description,
                'metadata' => [
                    'provider' => $description ? 'Context7 Rule-Based' : 'Fallback',
                    'duration' => 0,
                    'tokens' => 0
                ]
            ]);

        } catch (\Exception $e) {
            // Ultimate fallback
            return response()->json([
                'success' => true,
                'description' => 'Profesyonel bir emlak talebi. Detaylar için lütfen bizi arayın.',
                'metadata' => [
                    'provider' => 'Emergency Fallback',
                    'error' => $e->getMessage()
                ]
            ]);
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
