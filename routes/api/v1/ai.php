<?php

use App\Http\Controllers\Admin\IlanKategoriController;
use App\Http\Controllers\Api\AdminAIController;
use App\Http\Controllers\Api\AIContentController;
use App\Http\Controllers\Api\AIController;
use App\Http\Controllers\Api\AiHealthController;
use App\Http\Controllers\Api\IlanAIController;
use App\Http\Controllers\Api\PlanNotesController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| AI API Routes (v1)
|--------------------------------------------------------------------------
|
| AI-powered API endpoints
| Includes content generation, analysis, and suggestions
|
*/

// Admin AI Routes (Requires Authentication)
Route::prefix('admin/ai')->name('api.ai.admin.')->middleware(['auth'])->group(function () {
    // Core AI Operations
    Route::post('/analyze', [AIController::class, 'analyze'])->name('analyze');
    Route::post('/suggest', [AIController::class, 'suggest'])->name('suggest');
    Route::post('/generate', [AIController::class, 'generate'])->name('generate');
    Route::get('/health', [AIController::class, 'healthCheck'])->name('health');
    Route::get('/providers', [AIController::class, 'getProviders'])->name('providers');
    Route::post('/switch-provider', [AIController::class, 'switchProvider'])->name('switch-provider');
    Route::get('/stats', [AIController::class, 'getStats'])->name('stats');
    Route::get('/logs', [AIController::class, 'getLogs'])->name('logs');

    // Admin AI Controller
    Route::post('/chat', [AdminAIController::class, 'chat'])->name('chat');
    Route::post('/price/predict', [AdminAIController::class, 'pricePredict'])->name('price.predict');
    Route::post('/suggest-features', [AdminAIController::class, 'suggestFeatures'])->name('suggest-features');
    Route::get('/analytics', [AdminAIController::class, 'analytics'])->name('analytics');

    // Plan Notes Query
    Route::post('/plan-notes/query', [PlanNotesController::class, 'query'])->name('plan-notes.query');

    // Talepler Create - AI Assistant Endpoints
    Route::post('/suggest-price', [AIController::class, 'suggestPrice'])->name('suggest-price');
    Route::post('/find-matches', [AIController::class, 'findMatches'])->name('find-matches');
    Route::post('/generate-description', [AIController::class, 'generateDescription'])->name('generate-description');

    // Voice-to-CRM: Sesli komut ile hızlı kayıt (Context7: C7-VOICE-TO-CRM-2025-11-27)
    Route::post('/voice-to-crm', [AIController::class, 'voiceToCrm'])->name('voice-to-crm');
});

// AI Feedback API (Context7: C7-AI-FEEDBACK-2025-11-25)
// Danışman geri bildirim sistemi - AI öğrenme döngüsü için
Route::prefix('ai')->name('api.ai.feedback.')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/feedback/{logId}', [AIController::class, 'submitFeedback'])
        ->name('submit')
        ->where('logId', '[0-9]+');
});

// Churn Risk API
Route::get('/ai/churn-risk/{kisiId}', [AIController::class, 'getChurnRisk'])
    ->middleware(['auth:sanctum'])
    ->name('api.ai.churn-risk')
    ->where('kisiId', '[0-9]+');

Route::get('/ai/churn-risk/top/{limit?}', [AIController::class, 'getTopChurnRisks'])
    ->middleware(['auth:sanctum'])
    ->name('api.ai.churn-risk.top')
    ->where('limit', '[0-9]+');

// Negotiation Strategy API (Context7: Pazarlık Stratejisi Analizi)
Route::get('/ai/strategy/{kisiId}', [AIController::class, 'getNegotiationStrategy'])
    ->middleware(['auth:sanctum'])
    ->name('api.ai.strategy')
    ->where('kisiId', '[0-9]+');

// AI Assist Routes for İlan Creation
Route::prefix('admin/ai-assist')->name('api.ai.assist.')->group(function () {
    Route::post('/auto-categorize', [IlanAIController::class, 'autoDetectCategory'])->name('auto-categorize');
    Route::post('/price-suggest', [IlanAIController::class, 'suggestOptimalPrice'])->name('price-suggest');
    Route::post('/description-generate', [IlanAIController::class, 'generateDescription'])->name('description-generate');
    Route::post('/seo-optimize', [IlanAIController::class, 'optimizeForSEO'])->name('seo-optimize');
    Route::post('/image-analyze', [IlanAIController::class, 'analyzeUploadedImages'])->name('image-analyze');
});

// İlan AI Routes (Context7: C7-ILAN-AI-API-2025-11-30)
Route::prefix('ai')->name('api.ai.')->middleware(['auth'])->group(function () {
    Route::post('/fetch-tkgm', [IlanAIController::class, 'fetchTkgm'])->name('fetch-tkgm');
    Route::post('/calculate-m2-price', [IlanAIController::class, 'calculateM2Price'])->name('calculate-m2-price');
    Route::post('/analyze-construction', [IlanAIController::class, 'analyzeConstruction'])->name('analyze-construction');
    Route::post('/calculate-seasonal-price', [IlanAIController::class, 'calculateSeasonalPrice'])->name('calculate-seasonal-price');
    Route::post('/start-video-render/{ilanId}', [AIController::class, 'startVideoRender'])
        ->name('start-video-render') // Group prefix otomatik ekler: api.ai.start-video-render
        ->where('ilanId', '[0-9]+');
    Route::get('/video-status/{ilanId}', [AIController::class, 'getVideoStatus'])
        ->name('video-status') // Group prefix otomatik ekler: api.ai.video-status
        ->where('ilanId', '[0-9]+');
});

// AI Analysis API
Route::post('/admin/ilan-kategorileri/ai-analysis', [IlanKategoriController::class, 'aiAnalysis'])
    ->name('api.ai.analysis');

// Public AI Routes (Rate Limited)
Route::prefix('public-ai')->name('api.ai.public.')->group(function () {
    Route::middleware('throttle:10,1')->post('/ilan-arama', function (\Illuminate\Http\Request $request) {
        try {
            $validated = $request->validate([
                'query' => 'required|string|max:500',
                'location' => 'nullable|string|max:100',
                'budget_min' => 'nullable|numeric|min:0',
                'budget_max' => 'nullable|numeric|min:0',
            ]);

            // Basit ilan arama (AI olmadan)
            $ilanlar = \App\Models\Ilan::where('status', 'active')
                ->where('yayinlandi', true)
                ->when($validated['location'] ?? null, function ($query, $location) {
                    return $query->whereHas('city', function ($q) use ($location) {
                        $q->where('ad', 'like', "%{$location}%");
                    })->orWhereHas('ilce', function ($q) use ($location) {
                        $q->where('ad', 'like', "%{$location}%");
                    });
                })
                ->when($validated['budget_min'] ?? null, function ($query, $budget) {
                    return $query->where('fiyat', '>=', $budget);
                })
                ->when($validated['budget_max'] ?? null, function ($query, $budget) {
                    return $query->where('fiyat', '<=', $budget);
                })
                ->limit(20)
                ->get(['id', 'baslik', 'fiyat', 'il_id', 'ilce_id']);

            return response()->json([
                'success' => true,
                'query' => $validated['query'],
                'results' => $ilanlar->map(function ($ilan) {
                    return [
                        'id' => $ilan->id,
                        'title' => $ilan->baslik,
                        'price' => $ilan->fiyat,
                        'location' => [
                            'city' => $ilan->il->ad ?? '',
                            'district' => $ilan->ilce->ad ?? '',
                        ],
                    ];
                }),
                'count' => $ilanlar->count(),
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Arama yapılamadı: ' . $e->getMessage(),
            ], 500);
        }
    })->name('ilan-arama');
});

// AI Suggestions API (Public - Rate Limited)
Route::prefix('ai')->name('api.ai.')->middleware(['throttle:30,1'])->group(function () {
    // AI Health Check
    Route::get('/health', [AiHealthController::class, 'health'])->name('health');

    // Health Check API Endpoints (Context7: C7-HEALTH-CHECK-API-2025-12-01)
    // Monitoring araçları için (Prometheus, Grafana, UptimeRobot)
    Route::prefix('health')->name('health.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AI\AdvancedAIController::class, 'healthCheck'])->name('check');
        Route::get('/system', [\App\Http\Controllers\AI\AdvancedAIController::class, 'systemHealthApi'])->name('system');
        Route::get('/queue', [\App\Http\Controllers\AI\AdvancedAIController::class, 'queueHealth'])->name('queue');
        Route::get('/telegram', [\App\Http\Controllers\AI\AdvancedAIController::class, 'telegramHealth'])->name('telegram');
    });

    // AI İlan Analizi
    Route::post('/analyze-listing', function (Request $request) {
        try {
            $data = $request->all();
            sleep(1); // Simulated AI analysis delay

            $analysis = [
                'price_analysis' => [
                    'suggested_price' => rand(800000, 1500000),
                    'market_comparison' => 'Bölge ortalamalarına uygun',
                    'price_confidence' => rand(75, 95) . '%',
                ],
                'title_suggestions' => [
                    'Merkezi Konumda Satılık ' . ($data['category'] ?? 'Daire'),
                    'Yatırım Fırsatı ' . ($data['category'] ?? 'Emlak'),
                    'Özel Tasarım ' . ($data['category'] ?? 'Konut'),
                ],
                'description_improvements' => [
                    'Lokasyon avantajları vurgulanabilir',
                    'Yatırım potansiyeli eklenebilir',
                    'Çevre imkanları detaylandırılabilir',
                ],
                'seo_keywords' => [
                    'satılık daire',
                    'merkezi konum',
                    'yatırım fırsatı',
                    'modern tasarım',
                ],
                'market_insights' => [
                    'Bu bölgede talep yüksek',
                    'Benzer ilanlar 15 gün içinde satılıyor',
                    'Fiyat artış trendi: %8 (yıllık)',
                ],
            ];

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'message' => 'AI analizi tamamlandı',
                'processing_time' => '1.2s',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI analizi sırasında hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    })->name('analyze-listing');

    Route::post('/suggestions', function (Request $request) {
        return response()->json([
            'success' => true,
            'suggestions' => [
                'title' => 'AI ile ilan başlığı önerisi',
                'description' => 'AI ile ilan açıklaması önerisi',
                'keywords' => ['emlak', 'satılık', 'kiralık', 'ev', 'daire'],
            ],
            'message' => 'AI suggestions endpoint aktif (Context7 uyumlu)',
        ]);
    })->name('suggestions');
});

// AI Content Generation API Routes - Admin Only
Route::prefix('ai')->name('api.ai.content.')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/generate-titles', [AIContentController::class, 'generateTitles'])->name('generate-titles');
    Route::post('/generate-description', [AIContentController::class, 'generateDescription'])->name('generate-description');
    Route::post('/generate-features', [AIContentController::class, 'generateFeatures'])->name('generate-features');
    Route::post('/generate-seo', [AIContentController::class, 'generateSEO'])->name('generate-seo');
    Route::get('/status', [AIContentController::class, 'getStatus'])->name('status');
});

// Environment Analysis API Routes (AI-Powered)
Route::prefix('environment')->name('api.environment.')->middleware(['throttle:120,1'])->group(function () {
    Route::get('/analyze', [\App\Http\Controllers\Api\EnvironmentAnalysisController::class, 'analyze'])
        ->name('analyze');
    Route::get('/category/{category}', [\App\Http\Controllers\Api\EnvironmentAnalysisController::class, 'analyzeCategory'])
        ->name('category');
    Route::post('/value-prediction', [\App\Http\Controllers\Api\EnvironmentAnalysisController::class, 'predictLocationValue'])
        ->name('value-prediction');
});

// AI Chat Endpoints (Context7: C7-AI-CHAT-API-2025-12-04)
Route::prefix('chat')->name('api.chat.')->middleware('throttle:30,1')->group(function () {
    Route::post('/message', [\App\Http\Controllers\Api\AIChatController::class, 'chat'])->name('message');
    Route::post('/generate-description', [\App\Http\Controllers\Api\AIChatController::class, 'generateDescription'])->name('generate-description');
    Route::post('/suggest-tags', [\App\Http\Controllers\Api\AIChatController::class, 'suggestTags'])->name('suggest-tags');
    Route::post('/analyze-demand', [\App\Http\Controllers\Api\AIChatController::class, 'analyzeDemand'])->name('analyze-demand');
    Route::post('/find-matching-properties', [\App\Http\Controllers\Api\AIChatController::class, 'findMatchingProperties'])->name('find-matching-properties');
});
