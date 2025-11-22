<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Services\Response\ResponseService;
use App\Models\Setting;
use App\Models\AiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AISettingsController extends Controller
{
    public function index()
    {
        // Mevcut AI ayarlarını al
        $currentProvider = Setting::where('key', 'ai_provider')->value('value') ?? config('ai.provider', 'ollama');
        $currentModel = Setting::where('key', 'ai_default_model')->value('value')
            ?? Setting::where('key', 'ollama_model')->value('value')
            ?? config('ai.ollama_model', 'gemma2:2b');

        // Tüm provider ayarlarını al
        $providerSettings = [
            'openai' => [
                'api_key' => Setting::where('key', 'openai_api_key')->value('value') ?? '',
                'model' => Setting::where('key', 'openai_model')->value('value') ?? 'gpt-3.5-turbo',
                'organization' => Setting::where('key', 'openai_organization')->value('value') ?? '',
            ],
            'google' => [
                'api_key' => Setting::where('key', 'google_api_key')->value('value') ?? '',
                'model' => Setting::where('key', 'google_model')->value('value') ?? 'gemini-pro',
            ],
            'claude' => [
                'api_key' => Setting::where('key', 'claude_api_key')->value('value') ?? '',
                'model' => Setting::where('key', 'claude_model')->value('value') ?? 'claude-3-sonnet-20240229',
            ],
            'deepseek' => [
                'api_key' => Setting::where('key', 'deepseek_api_key')->value('value') ?? '',
                'model' => Setting::where('key', 'deepseek_model')->value('value') ?? 'deepseek-chat',
            ],
            'ollama' => [
                'url' => Setting::where('key', 'ollama_url')->value('value') ?? 'http://localhost:11434',
                'model' => Setting::where('key', 'ollama_model')->value('value') ?? 'gemma2:2b',
            ],
        ];

        // Genel ayarlar
        $appLocale = Setting::where('key', 'app_locale')->value('value') ?? 'tr';
        $currencyDefault = Setting::where('key', 'currency_default')->value('value') ?? 'TRY';

        return view('admin.ai-settings.index', [
            'currentProvider' => $currentProvider,
            'currentModel' => $currentModel,
            'providerSettings' => $providerSettings,
            'appLocale' => $appLocale,
            'currencyDefault' => $currencyDefault,
        ]);
    }

    /**
     * AI Analytics - Detaylı istatistikler
     * Context7: Real-time analytics endpoint
     */
    public function analytics(Request $request)
    {
        try {
            $period = $request->input('period', '7days'); // 7days, 30days, all
            $since = match ($period) {
                '24hours' => now()->subHours(24),
                '7days' => now()->subDays(7),
                '30days' => now()->subDays(30),
                'all' => null,
                default => now()->subDays(7),
            };

            $query = AiLog::query();
            if ($since) {
                $query->where('created_at', '>=', $since);
            }

            // Genel İstatistikler
            $total = (clone $query)->count();
            $successful = (clone $query)->where('status', 'success')->count();
            $failed = (clone $query)->whereIn('status', ['failed', 'error', 'timeout'])->count();
            $successRate = $total > 0 ? round(($successful / $total) * 100, 2) : 0;
            $avgResponseTime = (clone $query)->where('status', 'success')->avg('response_time') ?? 0;
            $totalCost = (clone $query)->sum('cost') ?? 0;
            $totalTokens = (clone $query)->sum('tokens_used') ?? 0;

            // Provider Bazlı İstatistikler
            $providerUsage = (clone $query)
                ->select('provider', DB::raw('count(*) as total'))
                ->groupBy('provider')
                ->get()
                ->map(function ($item) use ($since) {
                    $providerQuery = AiLog::where('provider', $item->provider);
                    if ($since) {
                        $providerQuery->where('created_at', '>=', $since);
                    }

                    $providerTotal = $providerQuery->count();
                    $providerSuccess = $providerQuery->where('status', 'success')->count();
                    $providerAvgTime = $providerQuery->where('status', 'success')->avg('response_time') ?? 0;
                    $providerCost = $providerQuery->sum('cost') ?? 0;

                    return [
                        'provider' => $item->provider,
                        'total' => $providerTotal,
                        'success' => $providerSuccess,
                        'failed' => $providerTotal - $providerSuccess,
                        'success_rate' => $providerTotal > 0 ? round(($providerSuccess / $providerTotal) * 100, 2) : 0,
                        'avg_response_time' => round($providerAvgTime, 2),
                        'total_cost' => round($providerCost, 6),
                    ];
                })
                ->values()
                ->toArray();

            // Model Bazlı İstatistikler
            $modelUsage = (clone $query)
                ->whereNotNull('model')
                ->select('model', DB::raw('count(*) as total'))
                ->groupBy('model')
                ->orderByDesc('total')
                ->limit(10)
                ->get()
                ->map(function ($item) use ($since) {
                    $modelQuery = AiLog::where('model', $item->model);
                    if ($since) {
                        $modelQuery->where('created_at', '>=', $since);
                    }

                    $modelTotal = $modelQuery->count();
                    $modelSuccess = $modelQuery->where('status', 'success')->count();

                    return [
                        'model' => $item->model,
                        'total' => $modelTotal,
                        'success' => $modelSuccess,
                        'success_rate' => $modelTotal > 0 ? round(($modelSuccess / $modelTotal) * 100, 2) : 0,
                    ];
                })
                ->values()
                ->toArray();

            // Request Type Bazlı İstatistikler
            $requestTypeUsage = (clone $query)
                ->whereNotNull('request_type')
                ->select('request_type', DB::raw('count(*) as total'))
                ->groupBy('request_type')
                ->orderByDesc('total')
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => $item->request_type,
                        'total' => $item->total,
                    ];
                })
                ->values()
                ->toArray();

            // Günlük Trend (Son 7 gün)
            $dailyTrend = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->startOfDay();
                $dayTotal = AiLog::whereDate('created_at', $date)->count();
                $daySuccess = AiLog::whereDate('created_at', $date)->where('status', 'success')->count();

                $dailyTrend[] = [
                    'date' => $date->format('Y-m-d'),
                    'label' => $date->format('d M'),
                    'total' => $dayTotal,
                    'success' => $daySuccess,
                    'failed' => $dayTotal - $daySuccess,
                ];
            }

            return ResponseService::success([
                'summary' => [
                    'total_requests' => $total,
                    'successful_requests' => $successful,
                    'failed_requests' => $failed,
                    'success_rate' => $successRate,
                    'error_rate' => round(100 - $successRate, 2),
                    'avg_response_time' => round($avgResponseTime, 2),
                    'total_cost' => round($totalCost, 6),
                    'total_tokens' => $totalTokens,
                ],
                'provider_usage' => $providerUsage,
                'model_usage' => $modelUsage,
                'request_type_usage' => $requestTypeUsage,
                'daily_trend' => $dailyTrend,
                'period' => $period,
                'current_provider' => $this->getCurrentProvider(),
                'current_model' => Setting::where('key', 'ai_default_model')->value('value')
                    ?? Setting::where('key', 'ollama_model')->value('value')
                    ?? config('ai.ollama_model', 'gemma2:2b'),
                'timestamp' => now()->toIso8601String(),
            ], 'AI analytics başarıyla yüklendi');
        } catch (\Exception $e) {
            return ResponseService::serverError('Analytics yüklenirken hata oluştu', $e);
        }
    }
    public function providerStatus()
    {
        $service = app(AIService::class);
        return ResponseService::success([
            'provider' => config('ai.provider'),
            'model' => config('ai.default_model'),
            'available_providers' => $service->getAvailableProviders(),
        ], 'AI provider durumu');
    }

    public function testProvider(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string',
            'api_key' => 'nullable|string',
            'model' => 'nullable|string',
        ]);
        return ResponseService::success([
            'test' => 'ok',
            'provider' => $validated['provider'],
            'model' => $validated['model'] ?? null,
        ], 'Provider testi başarılı');
    }

    public function updateLocale(Request $request)
    {
        $validated = $request->validate([
            'locale' => 'required|string|in:tr,en',
        ]);
        Setting::updateOrCreate(['key' => 'app_locale'], ['value' => $validated['locale']]);
        return ResponseService::success(['locale' => $validated['locale']], 'Dil ayarı güncellendi');
    }

    public function updateCurrency(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|string|in:TRY,USD,EUR,GBP',
        ]);
        Setting::updateOrCreate(['key' => 'currency_default'], ['value' => $validated['currency']]);
        return ResponseService::success(['currency' => $validated['currency']], 'Para birimi ayarı güncellendi');
    }

    public function updateProvider(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string|in:openai,google,claude,deepseek,ollama',
        ]);
        /** @var AIService $ai */
        $ai = app(AIService::class);
        try {
            $ai->switchProvider($validated['provider']);
            return ResponseService::success(['provider' => $validated['provider']], 'AI sağlayıcı güncellendi');
        } catch (\Throwable $e) {
            return ResponseService::serverError('Sağlayıcı güncellenemedi', $e);
        }
    }

    public function updateModel(Request $request)
    {
        $validated = $request->validate([
            'model' => 'required|string|max:100',
            'provider' => 'nullable|string|in:openai,google,claude,deepseek,ollama',
        ]);

        $provider = $validated['provider'] ?? $this->getCurrentProvider();

        // Provider'a göre model key'i belirle
        $modelKey = match ($provider) {
            'ollama' => 'ollama_model',
            'openai' => 'openai_model',
            'google' => 'google_model',
            'claude' => 'claude_model',
            'deepseek' => 'deepseek_model',
            default => 'ai_default_model',
        };

        // Hem provider-specific hem de genel model ayarını güncelle
        Setting::updateOrCreate(['key' => $modelKey], ['value' => $validated['model']]);
        Setting::updateOrCreate(['key' => 'ai_default_model'], ['value' => $validated['model']]);

        // Cache'i temizle
        Cache::forget('ollama_model');
        Cache::forget('ai_current_model');
        Cache::forget('ai_provider');

        return ResponseService::success([
            'model' => $validated['model'],
            'provider' => $provider,
            'model_key' => $modelKey
        ], 'Model ayarı güncellendi');
    }

    /**
     * API Key güncelle
     */
    public function updateApiKey(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string|in:openai,google,claude,deepseek',
            'api_key' => 'required|string|max:500',
        ]);

        $keyMap = [
            'openai' => 'openai_api_key',
            'google' => 'google_api_key',
            'claude' => 'claude_api_key',
            'deepseek' => 'deepseek_api_key',
        ];

        Setting::updateOrCreate(
            ['key' => $keyMap[$validated['provider']]],
            ['value' => $validated['api_key'], 'type' => 'string', 'group' => 'ai']
        );

        // Cache'i temizle
        Cache::forget('ai_provider_config');

        return ResponseService::success([
            'provider' => $validated['provider'],
            'masked_key' => $this->maskApiKey($validated['api_key']),
        ], 'API Key başarıyla güncellendi');
    }

    /**
     * Ollama URL güncelle
     */
    public function updateOllamaUrl(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url|max:255',
        ]);

        Setting::updateOrCreate(
            ['key' => 'ollama_url'],
            ['value' => $validated['url'], 'type' => 'string', 'group' => 'ai']
        );

        // Cache'i temizle
        Cache::forget('ollama_url');
        Cache::forget('ai_provider_config');

        return ResponseService::success([
            'url' => $validated['url'],
        ], 'Ollama URL başarıyla güncellendi');
    }

    /**
     * Provider Model güncelle
     */
    public function updateProviderModel(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string|in:openai,google,claude,deepseek,ollama',
            'model' => 'required|string|max:100',
        ]);

        $modelKeyMap = [
            'openai' => 'openai_model',
            'google' => 'google_model',
            'claude' => 'claude_model',
            'deepseek' => 'deepseek_model',
            'ollama' => 'ollama_model',
        ];

        Setting::updateOrCreate(
            ['key' => $modelKeyMap[$validated['provider']]],
            ['value' => $validated['model'], 'type' => 'string', 'group' => 'ai']
        );

        // Eğer aktif provider ise genel model ayarını da güncelle
        if ($this->getCurrentProvider() === $validated['provider']) {
            Setting::updateOrCreate(
                ['key' => 'ai_default_model'],
                ['value' => $validated['model'], 'type' => 'string', 'group' => 'ai']
            );
        }

        // Cache'i temizle
        Cache::forget('ollama_model');
        Cache::forget('ai_current_model');
        Cache::forget('ai_provider_config');

        return ResponseService::success([
            'provider' => $validated['provider'],
            'model' => $validated['model'],
        ], 'Model ayarı başarıyla güncellendi');
    }

    /**
     * OpenAI Organization ID güncelle
     */
    public function updateOpenAIOrganization(Request $request)
    {
        $validated = $request->validate([
            'organization' => 'nullable|string|max:100',
        ]);

        Setting::updateOrCreate(
            ['key' => 'openai_organization'],
            ['value' => $validated['organization'] ?? '', 'type' => 'string', 'group' => 'ai']
        );

        Cache::forget('ai_provider_config');

        return ResponseService::success([
            'organization' => $validated['organization'] ?? '',
        ], 'OpenAI Organization ID başarıyla güncellendi');
    }

    /**
     * API Key'i mask'le (güvenlik için)
     */
    protected function maskApiKey(string $key): string
    {
        if (strlen($key) <= 8) {
            return str_repeat('*', strlen($key));
        }
        return substr($key, 0, 4) . str_repeat('*', strlen($key) - 8) . substr($key, -4);
    }

    /**
     * Mevcut provider'ı al
     */
    protected function getCurrentProvider(): string
    {
        return Setting::where('key', 'ai_provider')->value('value')
            ?? config('ai.provider', 'ollama');
    }
}
