<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ilan;
use App\Models\Kisi;
use App\Models\Talep;
use App\Services\AI\YalihanCortex;
use App\Services\AIService;
use App\Services\Logging\LogService;
use App\Services\Response\ResponseService;
use App\Traits\ValidatesApiRequests;
use Illuminate\Http\Request;

class AIController extends Controller
{
    use ValidatesApiRequests;

    /**
     * Yalihan Cortex - Merkezi Zeka Sistemi
     * Context7: Tüm AI işlemleri YalihanCortex üzerinden yönetilir
     */
    protected YalihanCortex $cortex;

    /**
     * AI Service (İçerik üretimi için)
     */
    protected AIService $aiService;

    public function __construct(YalihanCortex $cortex, AIService $aiService)
    {
        $this->cortex = $cortex;
        $this->aiService = $aiService;
    }

    public function analyze(Request $request)
    {
        // ✅ REFACTORED: Using ValidatesApiRequests trait
        $validated = $this->validateRequestWithResponse($request, [
            'action' => 'sometimes|string',  // Made optional
            'data' => 'sometimes|array',     // Made optional
            'context' => 'sometimes|array',
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
                    'action' => $action,
                ],
            ], 'AI analysis completed successfully');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('AI analysis failed', $e);
        }
    }

    /**
     * Churn Risk Analizi
     * Context7: YalihanCortex üzerinden yönetilir
     */
    public function getChurnRisk(int $kisiId)
    {
        try {
            $kisi = Kisi::find($kisiId);
            if (! $kisi) {
                return ResponseService::notFound('Kişi bulunamadı');
            }

            // ✅ REFACTORED: YalihanCortex üzerinden churn analizi
            $cortexResult = $this->cortex->calculateChurnRisk($kisi);

            // Hata durumu kontrolü
            if (isset($cortexResult['success']) && !$cortexResult['success']) {
                return ResponseService::serverError(
                    $cortexResult['error'] ?? 'Churn riski hesaplanamadı',
                    new \Exception($cortexResult['error'] ?? 'Unknown error')
                );
            }

            return ResponseService::success([
                'kisi_id' => $cortexResult['kisi_id'],
                'risk' => [
                    'score' => $cortexResult['risk_score'],
                    'level' => $cortexResult['risk_level'],
                    'breakdown' => $cortexResult['breakdown'],
                    'recommendation' => $cortexResult['recommendation'],
                ],
                'metadata' => array_merge($cortexResult['metadata'] ?? [], [
                    'provider' => 'YalihanCortex',
                    'normalized' => true,
                ]),
            ], 'Churn riski hesaplandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('Churn riski hesaplanamadı', $e);
        }
    }

    /**
     * Top Churn Risks Analizi
     * Context7: YalihanCortex üzerinden yönetilir
     */
    public function getTopChurnRisks(int $limit = 10)
    {
        try {
            $user = auth()->user();

            // ✅ REFACTORED: YalihanCortex üzerinden top churn risks analizi
            $cortexResult = $this->cortex->getTopChurnRisks($limit, $user->id ?? null);

            // Hata durumu kontrolü
            if (isset($cortexResult['success']) && !$cortexResult['success']) {
                return ResponseService::serverError(
                    $cortexResult['error'] ?? 'Top churn risk listesi oluşturulamadı',
                    new \Exception($cortexResult['error'] ?? 'Unknown error')
                );
            }

            return ResponseService::success([
                'customers' => $cortexResult['customers'] ?? [],
                'count' => $cortexResult['count'] ?? 0,
                'metadata' => array_merge($cortexResult['metadata'] ?? [], [
                    'provider' => 'YalihanCortex',
                    'normalized' => true,
                ]),
            ], 'Top churn risk listesi oluşturuldu');
        } catch (\Exception $e) {
            return ResponseService::serverError('Top churn risk listesi oluşturulamadı', $e);
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
            'suggestion' => $suggestion,
        ];
    }

    public function suggest(Request $request)
    {
        // ✅ REFACTORED: Using ValidatesApiRequests trait
        $validated = $this->validateRequestWithResponse($request, [
            'context' => 'required|array',
            'type' => 'sometimes|string|in:category,feature,content,general',
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
            'options' => 'sometimes|array',
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

    /**
     * Pazarlama videosu render sürecini başlatır.
     */
    public function startVideoRender(int $ilanId)
    {
        try {
            $ilan = Ilan::find($ilanId);
            if (! $ilan) {
                return ResponseService::notFound('İlan bulunamadı');
            }

            $ilan->video_status = 'queued';
            $ilan->video_last_frame = 0;
            $ilan->save();

            \App\Jobs\RenderMarketingVideo::dispatch($ilan->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'ilan_id' => $ilan->id,
                    'video_status' => $ilan->video_status,
                ],
                'message' => 'Video render işlemi kuyruğa alındı',
            ], 202);
        } catch (\Exception $e) {
            return ResponseService::serverError('Video render işlemi başlatılamadı', $e);
        }
    }

    /**
     * Video durumunu döndürür (polling için).
     */
    public function getVideoStatus(int $ilanId)
    {
        try {
            $ilan = Ilan::find($ilanId);
            if (! $ilan) {
                return ResponseService::notFound('İlan bulunamadı');
            }

            return ResponseService::success([
                'ilan_id' => $ilan->id,
                'video_status' => $ilan->video_status ?? 'none',
                'video_last_frame' => (int) ($ilan->video_last_frame ?? 0),
                'video_url' => $ilan->video_url,
            ], 'Video durumu getirildi');
        } catch (\Exception $e) {
            return ResponseService::serverError('Video durumu alınamadı', $e);
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
            'provider' => 'required|string',
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
                'failed_requests' => \App\Models\AiLog::whereIn('status', ['failed', 'error', 'timeout'])->count(),
                'average_response_time' => \App\Models\AiLog::avg('response_time'),
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
            'action' => 'sometimes|string',
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
                'features' => $features,
            ];

            $prompt = $this->buildTitlePrompt($normalizedData);
            $result = $this->aiService->suggest($normalizedData, 'title');

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success([
                'suggestions' => $this->parseTitleSuggestions($result),
                'prompt' => $prompt,
            ], 'AI başlık önerileri başarıyla oluşturuldu');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('AI başlık önerisi alınamadı', $e);
        }
    }

    /**
     * AI Açıklama Üretimi (OLD - Deprecated)
     *
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
            'price' => 'sometimes|numeric',
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
                'prompt' => $prompt,
            ], 'AI açıklama başarıyla oluşturuldu (deprecated method)');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('AI açıklama üretilemedi', $e);
        }
    }

    /**
     * AI Fiyat Önerisi (OLD - Deprecated - Use suggestPrice instead)
     *
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
            'size' => 'sometimes|numeric',
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
                'prompt' => $prompt,
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
            'Özellikler: ' . implode(', ', $data['features'] ?? []) . "\n" .
            '3 farklı başlık önerisi ver.';
    }

    private function buildDescriptionPrompt($data)
    {
        return "Emlak ilanı için açıklama yaz:\n" .
            "Kategori: {$data['category']}\n" .
            "Konum: {$data['location']}\n" .
            "Mülk Tipi: {$data['property_type']}\n" .
            'Özellikler: ' . implode(', ', $data['features'] ?? []) . "\n" .
            'Fiyat: ' . ($data['price'] ?? 'Belirtilmemiş') . "\n" .
            'Profesyonel ve çekici bir açıklama yaz.';
    }

    private function buildPricePrompt($data)
    {
        return "Emlak ilanı için fiyat analizi yap:\n" .
            "Kategori: {$data['category']}\n" .
            "Konum: {$data['location']}\n" .
            "Mülk Tipi: {$data['property_type']}\n" .
            'Büyüklük: ' . ($data['size'] ?? 'Belirtilmemiş') . " m²\n" .
            'Özellikler: ' . implode(', ', $data['features'] ?? []) . "\n" .
            'Piyasa analizi yaparak fiyat önerisi ver.';
    }

    private function parseTitleSuggestions($result)
    {
        // AI'dan gelen başlık önerilerini parse et
        $lines = explode("\n", $result);
        $suggestions = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (! empty($line) && ! preg_match('/^\d+\./', $line)) {
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

            if (! $stats || $stats->count == 0) {
                // ✅ REFACTORED: Using ResponseService - Varsayılan değerler
                return ResponseService::success([
                    'price' => [
                        'min' => 500000,
                        'avg' => 1000000,
                        'max' => 2000000,
                    ],
                    'metadata' => [
                        'source' => 'default',
                        'count' => 0,
                    ],
                ], 'Benzer ilan bulunamadı, genel pazar verileri gösteriliyor');
            }

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success([
                'price' => [
                    'min' => round($stats->min, -3), // Round to thousands
                    'avg' => round($stats->avg, -3),
                    'max' => round($stats->max, -3),
                ],
                'metadata' => [
                    'source' => 'database',
                    'count' => $stats->count,
                    'provider' => 'Context7 Market Analysis',
                ],
            ], 'Fiyat önerisi başarıyla oluşturuldu');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('Fiyat önerisi alınamadı', $e);
        }
    }

    /**
     * AI İlan Eşleştirme
     * Context7: Talep kriterlerine göre uygun ilanları bul
     *
     * Yeni SmartPropertyMatcherAI servisi kullanılıyor (2025-11-24)
     */
    public function findMatches(Request $request)
    {
        try {
            // Talep ID varsa veritabanından bul, yoksa geçici nesne oluştur
            $talepId = $request->input('talep_id');

            if ($talepId) {
                // Veritabanından Talep'i bul
                $talep = Talep::with(['il', 'ilce', 'mahalle', 'altKategori'])->find($talepId);

                if (! $talep) {
                    return ResponseService::notFound('Talep bulunamadı');
                }
            } else {
                // Request verileriyle geçici Talep nesnesi oluştur
                $talep = new Talep;
                $talep->fill([
                    'alt_kategori_id' => $request->input('kategori_id') ?? $request->input('alt_kategori_id'),
                    'il_id' => $request->input('il_id'),
                    'ilce_id' => $request->input('ilce_id'),
                    'mahalle_id' => $request->input('mahalle_id'),
                    'min_fiyat' => $request->input('min_fiyat'),
                    'max_fiyat' => $request->input('max_fiyat'),
                    'min_metrekare' => $request->input('min_metrekare'),
                    'max_metrekare' => $request->input('max_metrekare'),
                    'aranan_ozellikler_json' => $request->input('aranan_ozellikler') ?? $request->input('aranan_ozellikler_json'),
                    'metadata' => $request->input('metadata'),
                ]);

                // Koordinatlar metadata içinde olabilir
                if ($request->has('latitude') || $request->has('lat')) {
                    $metadata = $talep->metadata ?? [];
                    $metadata['latitude'] = $request->input('latitude') ?? $request->input('lat');
                    $metadata['longitude'] = $request->input('longitude') ?? $request->input('lng');
                    $talep->metadata = $metadata;
                }
            }

            // ✅ YalihanCortex ile zenginleştirilmiş eşleştirme
            $cortexResult = $this->cortex->matchForSale($talep);

            // Sonuçları formatla - KÂR ODAKLI ZEKÂ: Action Score, Match Score ve Churn Score ayrı ayrı
            $formattedMatches = collect($cortexResult['matches'] ?? [])->map(function ($match) {
                return [
                    'id' => $match['ilan_id'],
                    'baslik' => $match['baslik'],
                    'title' => $match['baslik'],
                    'price' => $match['fiyat'],
                    'para_birimi' => $match['para_birimi'],
                    // 3 ayrı skor (0-100 arası)
                    'match_score' => round($match['match_score'] ?? 0, 2), // 0-100 arası Match skoru
                    'churn_score' => round($match['churn_score'] ?? 0, 2), // 0-100 arası Churn skoru
                    'action_score' => round($match['action_score'] ?? 0, 2), // 0-100+ arası Action skoru (birleşik)
                    // Normalize edilmiş skorlar (0-1 arası, geriye dönük uyumluluk için)
                    'score' => round(($match['action_score'] ?? 0) / 100, 2), // Action score normalize edilmiş
                    'match_level' => $match['match_level'],
                    'priority' => $match['priority'],
                    'reasons' => $match['reasons'] ?? [],
                    'breakdown' => $match['breakdown'] ?? [],
                ];
            });

            // ✅ REFACTORED: Using ResponseService with YalihanCortex
            return ResponseService::success([
                'matches' => $formattedMatches,
                'count' => $formattedMatches->count(),
                'churn_analysis' => $cortexResult['churn_analysis'] ?? null,
                'recommendations' => $cortexResult['recommendations'] ?? [],
                'metadata' => array_merge($cortexResult['metadata'] ?? [], [
                    'algorithm' => 'YalihanCortex v1.0',
                    'provider' => 'Context7 AI Brain System',
                    'scoring_system' => 'Action Score (Match + Churn * 0.5)',
                    'filter_threshold' => 85, // action_score > 85
                    'max_results' => 5, // İlk 5 ilan
                    'talep_id' => $talepId,
                ]),
            ], 'İlan eşleştirmesi başarıyla tamamlandı');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('Eşleştirme başarısız', $e);
        }
    }

    /**
     * İlan lokasyonunu formatla
     *
     * @param  \App\Models\Ilan  $ilan
     */
    private function formatLocation($ilan): string
    {
        $parts = [];

        if ($ilan->mahalle && $ilan->mahalle->mahalle_adi) {
            $parts[] = $ilan->mahalle->mahalle_adi;
        }

        if ($ilan->ilce && $ilan->ilce->ilce_adi) {
            $parts[] = $ilan->ilce->ilce_adi;
        }

        if ($ilan->il && $ilan->il->il_adi) {
            $parts[] = $ilan->il->il_adi;
        }

        return implode(', ', $parts);
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
                    'temperature' => 0.7,
                ]);

                $description = $result['data'] ?? null;
            } catch (\Exception $aiError) {
                // AI failed, use fallback
                $description = null;
            }

            // Fallback: Rule-based description generation
            if (! $description) {
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
                    'tokens' => 0,
                ],
            ], 'AI açıklama başarıyla oluşturuldu');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService - Ultimate fallback
            return ResponseService::success([
                'description' => 'Profesyonel bir emlak talebi. Detaylar için lütfen bizi arayın.',
                'metadata' => [
                    'provider' => 'Emergency Fallback',
                    'error' => $e->getMessage(),
                ],
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
        $parts[] = 'İlginize teşekkür ederiz, detaylı bilgi için iletişime geçebilirsiniz.';

        return implode(' ', $parts);
    }

    /**
     * AI Feedback Submission
     * Context7: C7-AI-FEEDBACK-2025-11-25
     * Danışman geri bildirimi: "İşe Yaradı/Yaramadı" + rating + reason
     *
     * ✅ REFACTORED: YalihanCortex üzerinden yönetilir
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitFeedback(Request $request, int $logId)
    {
        // ✅ REFACTORED: Using ValidatesApiRequests trait
        $validated = $this->validateRequestWithResponse($request, [
            'rating' => 'required|integer|min:1|max:5',
            'feedback_type' => 'required|string|in:positive,negative,neutral',
            'reason' => 'nullable|string|max:1000',
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            $user = $request->user();

            // ✅ REFACTORED: YalihanCortex üzerinden feedback kaydet
            $cortexResult = $this->cortex->submitFeedback(
                $logId,
                $request->only(['rating', 'feedback_type', 'reason']),
                $user->id
            );

            // Hata durumu kontrolü
            if (! ($cortexResult['success'] ?? false)) {
                $errorCode = $cortexResult['code'] ?? 500;
                if ($errorCode === 403) {
                    return ResponseService::error(
                        $cortexResult['error'] ?? 'Yetkiniz yok',
                        403
                    );
                }
                if ($errorCode === 404 || str_contains($cortexResult['error'] ?? '', 'bulunamadı')) {
                    return ResponseService::notFound($cortexResult['error'] ?? 'AI log kaydı bulunamadı');
                }
                return ResponseService::serverError(
                    $cortexResult['error'] ?? 'Feedback kaydedilemedi',
                    new \Exception($cortexResult['error'] ?? 'Unknown error')
                );
            }

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success([
                'log_id' => $cortexResult['log_id'],
                'rating' => $cortexResult['rating'],
                'feedback_type' => $cortexResult['feedback_type'],
                'message' => $cortexResult['message'] ?? 'Geri bildirim başarıyla kaydedildi. AI öğrenme döngüsüne katkı sağladınız!',
                'metadata' => $cortexResult['metadata'] ?? [],
            ], 'Geri bildirim başarıyla kaydedildi. AI öğrenme döngüsüne katkı sağladınız!');
        } catch (\Exception $e) {
            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('Geri bildirim kaydedilemedi', $e);
        }
    }

    /**
     * Get negotiation strategy for a customer
     *
     * Context7: YalihanCortex üzerinden pazarlık stratejisi analizi
     *
     * @param Request $request
     * @param int $kisiId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNegotiationStrategy(Request $request, int $kisiId)
    {
        try {
            $kisi = Kisi::findOrFail($kisiId);

            $result = $this->cortex->getNegotiationStrategy($kisi);

            return ResponseService::success($result, 'Pazarlık stratejisi başarıyla oluşturuldu.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseService::error('Kişi bulunamadı.', 404);
        } catch (\Exception $e) {
            LogService::error(
                'Negotiation strategy API failed',
                [
                    'kisi_id' => $kisiId,
                    'error' => $e->getMessage(),
                ],
                $e,
                LogService::CHANNEL_AI
            );

            return ResponseService::error('Pazarlık stratejisi oluşturulurken bir hata oluştu: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Sesli komut ile hızlı kayıt oluşturma
     * Context7: C7-VOICE-TO-CRM-2025-11-27
     *
     * Telegram/WhatsApp sesli mesajdan gelen metni parse edip
     * Kisi ve Talep draft kayıtları oluşturur
     *
     * POST /api/v1/admin/ai/voice-to-crm
     *
     * Body:
     * {
     *   "text": "Yeni talep, Ahmet Yılmaz, 10 milyon TL, Bodrum Yalıkavak'ta villa arıyor.",
     *   "danisman_id": 1
     * }
     */
    public function voiceToCrm(Request $request)
    {
        // ✅ REFACTORED: Using ValidatesApiRequests trait
        $validated = $this->validateRequestWithResponse($request, [
            'text' => 'required|string|min:10|max:2000',
            'danisman_id' => 'nullable|integer|exists:users,id',
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            $user = $request->user();
            $danismanId = $validated['danisman_id'] ?? $user->id;
            $text = $validated['text'];

            // ✅ YalihanCortex üzerinden voice-to-crm işlemi
            $cortexResult = $this->cortex->createDraftFromText($text, $danismanId);

            // Hata durumu kontrolü
            if (!($cortexResult['success'] ?? false)) {
                return ResponseService::serverError(
                    $cortexResult['error'] ?? 'Sesli komut kaydı oluşturulamadı',
                    new \Exception($cortexResult['error'] ?? 'Unknown error')
                );
            }

            return ResponseService::success([
                'kisi_id' => $cortexResult['kisi_id'],
                'talep_id' => $cortexResult['talep_id'],
                'kisi' => $cortexResult['kisi'],
                'talep' => $cortexResult['talep'],
                'message' => '✅ Kayıt alındı. Formu daha sonra doldurabilirsiniz.',
                'metadata' => $cortexResult['metadata'] ?? [],
            ], 'Sesli komut başarıyla işlendi');
        } catch (\Exception $e) {
            return ResponseService::serverError('Sesli komut işlenirken hata oluştu', $e);
        }
    }
}
