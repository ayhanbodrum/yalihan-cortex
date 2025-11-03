<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AISettingsController extends AdminController
{
    /**
     * AI Analytics Dashboard
     * Context7: Real-time AI metrics and cost tracking
     */
    public function analytics(Request $request)
    {
        $analytics = Cache::remember('ai.analytics', 300, function () {
            $logs = \App\Models\AiLog::where('created_at', '>=', now()->subDays(30))->get();

            $totalRequests = $logs->count();
            $successfulRequests = $logs->where('success', true)->count();
            $totalCost = $logs->sum('cost');
            $avgResponseTime = $logs->avg('response_time');

            // Provider usage breakdown
            $providerUsage = [];
            foreach ($logs->groupBy('provider') as $provider => $providerLogs) {
                $providerRequests = $providerLogs->count();
                $providerUsage[$provider] = [
                    'requests' => $providerRequests,
                    'percentage' => $totalRequests > 0 ? ($providerRequests / $totalRequests) * 100 : 0,
                    'success_rate' => $providerLogs->where('success', true)->count() / $providerRequests * 100,
                    'avg_time' => $providerLogs->avg('response_time'),
                    'cost' => $providerLogs->sum('cost'),
                ];
            }

            return [
                'total_requests' => $totalRequests,
                'success_rate' => $totalRequests > 0 ? ($successfulRequests / $totalRequests) * 100 : 0,
                'avg_response_time' => round($avgResponseTime),
                'total_cost' => $totalCost,
                'provider_usage' => $providerUsage,
                'recent_logs' => \App\Models\AiLog::latest()->limit(20)->get(),
            ];
        });

        return view('admin.ai-settings.analytics', compact('analytics'));
    }

    public function index(Request $request)
    {
        $keys = [
            // Provider Settings
            'ai_provider',

            // API Keys
            'google_api_key',
            'google_model',
            'openai_api_key',
            'openai_model',
            'claude_api_key',
            'claude_model',
            'deepseek_api_key',
            'deepseek_model',
            'ollama_url',
            'ollama_model',

            // Legacy Settings
            'ai_anythingllm_url',
            'ai_anythingllm_api_key',
            'ai_anythingllm_timeout',

            // Default Settings
            'ai_default_tone',
            'ai_default_variant_count',
            'ai_default_ab_test',
            'ai_default_languages',

            // Legacy API Keys (keep for backward compatibility)
            'ai_openai_api_key',
            'ai_gemini_api_key',
            'ai_claude_api_key',
        ];

        $settings = Setting::query()->whereIn('key', $keys)->pluck('value', 'key')->toArray();

        // API Keys'i decrypt et (güvenlik için)
        $apiKeyFields = [
            'google_api_key',
            'openai_api_key',
            'claude_api_key',
            'deepseek_api_key',
            'ai_anythingllm_api_key',
            'ai_openai_api_key',
            'ai_gemini_api_key',
            'ai_claude_api_key'
        ];

        foreach ($apiKeyFields as $key) {
            if (isset($settings[$key]) && !empty($settings[$key])) {
                try {
                    // Decrypt etmeyi dene
                    $decrypted = decrypt($settings[$key]);
                    $settings[$key] = $decrypted;
                } catch (\Exception $e) {
                    // Eğer decrypt edilemezse, muhtemelen plain text'tir (eski veriler)
                    // Plain text olarak bırak, boş yapma
                    // $settings[$key] = ''; // Bu satırı kaldırdık
                }
            }
        }

        // AI Provider Statistics for View
        $statistics = [
            'active_providers' => count(array_filter($settings, function ($value, $key) {
                return str_contains($key, 'api_key') && !empty($value);
            }, ARRAY_FILTER_USE_BOTH)),
            'active_provider' => 'anythingllm', // Default to AnythingLLM for initial display
        ];

        return view('admin.ai-settings.index', [
            'settings' => $settings,
            'statistics' => $statistics,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            // Provider Settings
            'ai_provider' => 'nullable|string|in:google,openai,anthropic,deepseek,ollama',

            // API Keys
            'google_api_key' => 'nullable|string',
            'google_model' => 'nullable|string|in:gemini-pro,gemini-pro-vision,gemini-1.5-pro',
            'openai_api_key' => 'nullable|string',
            'openai_model' => 'nullable|string|in:gpt-4,gpt-4-turbo,gpt-3.5-turbo',
            'claude_api_key' => 'nullable|string',
            'claude_model' => 'nullable|string|in:claude-3-opus-20240229,claude-3-sonnet-20240229,claude-3-haiku-20240307',
            'deepseek_api_key' => 'nullable|string',
            'deepseek_model' => 'nullable|string|in:deepseek-chat,deepseek-coder',
            'ollama_url' => 'nullable|url',
            'ollama_model' => 'nullable|string|in:qwen2.5:3b,qwen2.5:latest,gemma2:2b,phi3:mini,nomic-embed-text:latest,llama2,llama3,mistral,codellama',

            // Legacy Settings (keep for backward compatibility)
            'ai_anythingllm_url' => 'nullable|url',
            'ai_anythingllm_api_key' => 'nullable|string',
            'ai_anythingllm_timeout' => 'nullable|integer|min:5|max:120',

            // Default Settings
            'ai_default_tone' => 'nullable|string|in:seo,kurumsal,hizli_satis,luks',
            'ai_default_variant_count' => 'nullable|integer|min:1|max:5',
            'ai_default_ab_test' => 'nullable|boolean',
            'ai_default_languages' => 'nullable|array',
            'ai_default_languages.*' => 'in:EN,RU,DE',
        ]);

        // API Keys'i şifrele
        $apiKeyFields = [
            'google_api_key',
            'openai_api_key',
            'claude_api_key',
            'deepseek_api_key',
            'ai_anythingllm_api_key'
        ];

        foreach ($data as $key => $value) {
            // API key'leri şifrele
            if (in_array($key, $apiKeyFields) && !empty($value)) {
                $value = encrypt($value);
            }

            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget('services.anythingllm');

        return redirect()->back()->with('success', 'AI ayarları kaydedildi.');
    }

    /**
     * Test AI Provider Connection
     * Context7: Real-time provider health check
     */
    public function testProvider(Request $request)
    {
        $provider = $request->input('provider');
        $startTime = microtime(true);

        $result = [
            'success' => false,
            'provider' => $provider,
            'message' => '',
            'response_time' => 0,
            'details' => [],
        ];

        try {
            switch ($provider) {
                case 'anythingllm':
                    $result = $this->testAnythingLLM();
                    break;

                case 'openai':
                    $result = $this->testOpenAIConnection();
                    break;

                case 'gemini':
                case 'google':
                    $result = $this->testGeminiConnection();
                    break;

                case 'claude':
                case 'anthropic':
                    $result = $this->testClaudeConnection();
                    break;

                default:
                    $result['message'] = 'Bilinmeyen provider';
            }

            $result['response_time'] = round((microtime(true) - $startTime) * 1000, 2);

            // Log the test result
            $this->logConnectionTest($provider, $result);
        } catch (\Exception $e) {
            $result['success'] = false;
            $result['message'] = $e->getMessage();
            $result['response_time'] = round((microtime(true) - $startTime) * 1000, 2);

            $this->logConnectionTest($provider, $result, $e);
        }

        return response()->json($result);
    }

    /**
     * Test AnythingLLM Connection
     */
    private function testAnythingLLM(): array
    {
        $url = Setting::where('key', 'ai_anythingllm_url')->value('value');
        $apiKey = Setting::where('key', 'ai_anythingllm_api_key')->value('value');
        $timeout = Setting::where('key', 'ai_anythingllm_timeout')->value('value') ?? 30;

        if (!$url || !$apiKey) {
            return [
                'success' => false,
                'message' => 'AnythingLLM URL veya API Key tanımlanmamış',
                'details' => ['url' => $url ? 'Tanımlı' : 'Eksik', 'api_key' => $apiKey ? 'Tanımlı' : 'Eksik']
            ];
        }

        try {
            $response = Http::timeout($timeout)
                ->withHeaders(['Authorization' => 'Bearer ' . $apiKey])
                ->get($url . '/api/v1/workspace');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'AnythingLLM bağlantısı başarılı',
                    'details' => [
                        'status' => $response->status(),
                        'url' => $url,
                        'response' => 'OK'
                    ]
                ];
            }

            return [
                'success' => false,
                'message' => 'AnythingLLM yanıt vermedi: ' . $response->status(),
                'details' => ['status' => $response->status()]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Bağlantı hatası: ' . $e->getMessage(),
                'details' => ['error' => $e->getMessage()]
            ];
        }
    }

    /**
     * Test OpenAI Connection (Legacy - for provider status)
     */
    private function testOpenAIConnection(): array
    {
        $apiKey = Setting::where('key', 'ai_openai_api_key')->value('value');

        if (!$apiKey) {
            return [
                'success' => false,
                'message' => 'OpenAI API Key tanımlanmamış',
                'details' => []
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => 'Bearer ' . $apiKey])
                ->get('https://api.openai.com/v1/models');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'OpenAI bağlantısı başarılı',
                    'details' => ['status' => 200, 'models' => count($response->json('data', []))]
                ];
            }

            return [
                'success' => false,
                'message' => 'OpenAI yanıt vermedi: ' . $response->status(),
                'details' => ['status' => $response->status()]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Bağlantı hatası: ' . $e->getMessage(),
                'details' => ['error' => $e->getMessage()]
            ];
        }
    }

    /**
     * Test Google Gemini Connection (Legacy - for provider status)
     */
    private function testGeminiConnection(): array
    {
        $apiKey = Setting::where('key', 'ai_google_api_key')->value('value');

        if (!$apiKey) {
            return [
                'success' => false,
                'message' => 'Gemini API Key tanımlanmamış',
                'details' => []
            ];
        }

        try {
            $response = Http::timeout(10)
                ->get('https://generativelanguage.googleapis.com/v1beta/models', [
                    'key' => $apiKey
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Gemini bağlantısı başarılı',
                    'details' => ['status' => 200, 'models' => count($response->json('models', []))]
                ];
            }

            return [
                'success' => false,
                'message' => 'Gemini yanıt vermedi: ' . $response->status(),
                'details' => ['status' => $response->status()]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Bağlantı hatası: ' . $e->getMessage(),
                'details' => ['error' => $e->getMessage()]
            ];
        }
    }

    /**
     * Test Claude Connection (Legacy - for provider status)
     */
    private function testClaudeConnection(): array
    {
        $apiKey = Setting::where('key', 'ai_claude_api_key')->value('value');

        if (!$apiKey) {
            return [
                'success' => false,
                'message' => 'Claude API Key tanımlanmamış',
                'details' => []
            ];
        }

        try {
            // Claude API test endpoint yok, sadece API key varlığını kontrol et
            return [
                'success' => true,
                'message' => 'Claude API Key tanımlanmış (Test endpoint yok)',
                'details' => ['api_key' => 'Configured']
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage(),
                'details' => ['error' => $e->getMessage()]
            ];
        }
    }

    /**
     * Log AI Connection Test
     * Context7: Centralized AI connection logging
     */
    private function logConnectionTest(string $provider, array $result, ?\Exception $exception = null): void
    {
        $logData = [
            'timestamp' => now()->toDateTimeString(),
            'provider' => $provider,
            'success' => $result['success'],
            'message' => $result['message'],
            'response_time' => $result['response_time'] ?? 0,
            'details' => $result['details'] ?? [],
        ];

        if ($exception) {
            $logData['exception'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }

        // Log to dedicated AI connections log
        Log::channel('single')->info('AI Connection Test', $logData);

        // Also write to dedicated ai_connections.log
        $logFile = storage_path('logs/ai_connections.log');
        $logEntry = sprintf(
            "[%s] %s - %s | Response: %sms | Details: %s\n",
            $logData['timestamp'],
            strtoupper($provider),
            $result['success'] ? 'SUCCESS ✅' : 'FAILED ❌',
            $logData['response_time'],
            json_encode($logData['details'])
        );

        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    /**
     * Get Provider Status (for live updates)
     */
    public function getProviderStatus(Request $request)
    {
        $providers = ['anythingllm', 'openai', 'gemini', 'claude'];
        $statuses = [];

        foreach ($providers as $provider) {
            $cacheKey = "ai_provider_status_{$provider}";
            $statuses[$provider] = Cache::get($cacheKey, [
                'status' => 'unknown',
                'last_check' => null,
                'message' => 'Henüz test edilmedi'
            ]);
        }

        return response()->json($statuses);
    }

    /**
     * Test AI Query with selected provider
     * Context7: Real-time AI testing with live message
     */
    public function testQuery(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|in:google,openai,anthropic,deepseek,ollama',
            'message' => 'required|string|max:1000',
        ]);

        $provider = $request->provider;
        $message = $request->message;
        $startTime = microtime(true);

        try {
            // Test based on provider
            switch ($provider) {
                case 'ollama':
                    $response = $this->testOllama($message);
                    break;
                case 'google':
                    $response = $this->testGoogleGemini($message);
                    break;
                case 'openai':
                    $response = $this->testOpenAI($message);
                    break;
                case 'anthropic':
                    $response = $this->testClaude($message);
                    break;
                case 'deepseek':
                    $response = $this->testDeepSeek($message);
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Desteklenmeyen provider',
                    ], 400);
            }

            $responseTime = round((microtime(true) - $startTime) * 1000); // ms

            return response()->json([
                'success' => true,
                'response' => $response['text'] ?? 'Yanıt alındı',
                'message' => 'Test başarılı',
                'tokens' => $response['tokens'] ?? null,
                'cost' => $response['cost'] ?? null,
                'response_time' => $responseTime,
            ]);
        } catch (\Exception $e) {
            // Log API errors for debugging
            Log::info("AI Test Query Error [{$provider}]", [
                'message' => $e->getMessage(),
                'provider' => $provider
            ]);

            // Return 400 for API errors (insufficient balance, invalid key, etc.)
            // Return 500 only for server errors
            $statusCode = 400;
            if (strpos($e->getMessage(), 'sunucusuna bağlanılamıyor') !== false) {
                $statusCode = 500; // Connection errors are server errors
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'response_time' => round((microtime(true) - $startTime) * 1000),
            ], $statusCode);
        }
    }

    /**
     * Test Ollama Local
     */
    private function testOllama($message)
    {
        $url = Setting::where('key', 'ai_ollama_url')->value('value') ?? 'http://51.75.64.121:11434';
        $model = Setting::where('key', 'ai_ollama_model')->value('value') ?? 'gemma2:2b';

        try {
            // First, check if Ollama is reachable
            $healthCheck = Http::timeout(5)->get("{$url}/api/tags");

            if (!$healthCheck->successful()) {
                throw new \Exception("Ollama sunucusuna erişilemiyor. Lütfen sunucunun çalıştığından ve {$url} adresinin doğru olduğundan emin olun.");
            }

            // Send the actual request
            $response = Http::timeout(60)->post("{$url}/api/generate", [
                'model' => $model,
                'prompt' => $message,
                'stream' => false,
            ]);

            if (!$response->successful()) {
                throw new \Exception("Ollama yanıt hatası: {$response->status()}. Model '{$model}' yüklü mü kontrol edin.");
            }

            $data = $response->json();

            return [
                'text' => $data['response'] ?? 'Yanıt alınamadı',
                'tokens' => null,
                'cost' => 0,
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \Exception("Ollama sunucusuna bağlanılamıyor ({$url}). Sunucu çalışıyor mu? IP adresi doğru mu?");
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Test Google Gemini
     */
    private function testGoogleGemini($message)
    {
        $apiKey = Setting::where('key', 'google_api_key')->value('value');

        if (!$apiKey) {
            throw new \Exception("Google Gemini API key bulunamadı");
        }

        // Decrypt API key
        try {
            $apiKey = decrypt($apiKey);
        } catch (\Exception $e) {
            // Key might be plain text (old data)
        }

        $model = Setting::where('key', 'ai_google_model')->value('value') ?? 'gemini-pro';

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $message]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 1000,
                        'temperature' => 0.7
                    ]
                ]);

            if (!$response->successful()) {
                throw new \Exception("Google Gemini API hatası: {$response->status()} - " . $response->body());
            }

            $data = $response->json();
            $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Yanıt alınamadı';

            // Token count estimation (Google doesn't always provide exact counts)
            $tokens = strlen($content) / 4; // Rough estimation
            $cost = round(($tokens * 0.0005), 6); // Google Gemini pricing

            return [
                'text' => $content,
                'tokens' => round($tokens),
                'cost' => $cost,
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \Exception("Google Gemini sunucusuna bağlanılamıyor. İnternet bağlantınızı kontrol edin.");
        } catch (\Exception $e) {
            throw new \Exception("Google Gemini API hatası: " . $e->getMessage());
        }
    }

    /**
     * Test OpenAI GPT
     */
    private function testOpenAI($message)
    {
        $apiKey = Setting::where('key', 'ai_openai_api_key')->value('value');

        if (!$apiKey) {
            throw new \Exception("OpenAI API key bulunamadı");
        }

        // Decrypt API key
        try {
            $apiKey = decrypt($apiKey);
        } catch (\Exception $e) {
            // Key might be plain text (old data)
        }

        $model = Setting::where('key', 'ai_openai_model')->value('value') ?? 'gpt-3.5-turbo';

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $message
                        ]
                    ],
                    'max_tokens' => 1000,
                    'temperature' => 0.7
                ]);

            if (!$response->successful()) {
                throw new \Exception("OpenAI API hatası: {$response->status()} - " . $response->body());
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? 'Yanıt alınamadı';
            $tokens = $data['usage']['total_tokens'] ?? null;

            // OpenAI pricing (GPT-3.5-turbo: $0.0015/1K input, $0.002/1K output)
            $cost = $tokens ? round(($tokens * 0.000002), 6) : null;

            return [
                'text' => $content,
                'tokens' => $tokens,
                'cost' => $cost,
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \Exception("OpenAI sunucusuna bağlanılamıyor. İnternet bağlantınızı kontrol edin.");
        } catch (\Exception $e) {
            throw new \Exception("OpenAI API hatası: " . $e->getMessage());
        }
    }

    /**
     * Test Anthropic Claude
     */
    private function testClaude($message)
    {
        $apiKey = Setting::where('key', 'ai_claude_api_key')->value('value');

        if (!$apiKey) {
            throw new \Exception("Claude API key bulunamadı");
        }

        // Decrypt API key
        try {
            $apiKey = decrypt($apiKey);
        } catch (\Exception $e) {
            // Key might be plain text (old data)
        }

        $model = Setting::where('key', 'ai_claude_model')->value('value') ?? 'claude-3-sonnet-20240229';

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                    'anthropic-version' => '2023-06-01'
                ])
                ->post('https://api.anthropic.com/v1/messages', [
                    'model' => $model,
                    'max_tokens' => 1000,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $message
                        ]
                    ]
                ]);

            if (!$response->successful()) {
                throw new \Exception("Claude API hatası: {$response->status()} - " . $response->body());
            }

            $data = $response->json();
            $content = $data['content'][0]['text'] ?? 'Yanıt alınamadı';
            $tokens = $data['usage']['total_tokens'] ?? null;

            // Claude pricing (Claude 3 Sonnet: $3/1M input, $15/1M output)
            $cost = $tokens ? round(($tokens * 0.000015), 6) : null;

            return [
                'text' => $content,
                'tokens' => $tokens,
                'cost' => $cost,
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \Exception("Claude sunucusuna bağlanılamıyor. İnternet bağlantınızı kontrol edin.");
        } catch (\Exception $e) {
            throw new \Exception("Claude API hatası: " . $e->getMessage());
        }
    }

    /**
     * Test DeepSeek
     */
    private function testDeepSeek($message)
    {
        $apiKey = Setting::where('key', 'ai_deepseek_api_key')->value('value');

        if (!$apiKey) {
            throw new \Exception("DeepSeek API key bulunamadı");
        }

        // Decrypt API key
        try {
            $apiKey = decrypt($apiKey);
        } catch (\Exception $e) {
            // Key might be plain text (old data)
        }

        $model = Setting::where('key', 'ai_deepseek_model')->value('value') ?? 'deepseek-chat';

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->post('https://api.deepseek.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $message
                        ]
                    ],
                    'max_tokens' => 1000,
                    'temperature' => 0.7
                ]);

            if (!$response->successful()) {
                throw new \Exception("DeepSeek API hatası: {$response->status()} - " . $response->body());
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? 'Yanıt alınamadı';
            $tokens = $data['usage']['total_tokens'] ?? null;

            // DeepSeek pricing: ~$0.14 per 1M input tokens, $0.28 per 1M output tokens
            $cost = $tokens ? round(($tokens * 0.00014), 6) : null;

            return [
                'text' => $content,
                'tokens' => $tokens,
                'cost' => $cost,
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \Exception("DeepSeek sunucusuna bağlanılamıyor. İnternet bağlantınızı kontrol edin.");
        } catch (\Exception $e) {
            throw new \Exception("DeepSeek API hatası: " . $e->getMessage());
        }
    }

    /**
     * Test Ollama Connection (Backend Proxy)
     */
    public function testOllamaConnection(Request $request)
    {
        $request->validate([
            'ollama_url' => 'required|url'
        ]);

        $ollamaUrl = $request->ollama_url;

        try {
            // Test Ollama connection
            $response = Http::timeout(10)->get("{$ollamaUrl}/api/tags");

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => 'Ollama bağlantısı başarılı',
                    'models' => $data['models'] ?? [],
                    'server_url' => $ollamaUrl
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Ollama yanıt hatası: {$response->status()}",
                    'models' => []
                ], 400);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => "Ollama sunucusuna bağlanılamıyor: {$e->getMessage()}",
                'models' => []
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Bağlantı hatası: {$e->getMessage()}",
                'models' => []
            ], 400);
        }
    }

    /**
     * Ollama model listesini getir
     */
    public function getOllamaModels(Request $request)
    {
        try {
            $aiService = app(\App\Services\AIService::class);
            $modelsData = $aiService->getOllamaModels();
            $recommendations = $aiService->getModelRecommendations();

            if (!$modelsData['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $modelsData['error'],
                    'models' => []
                ], 500);
            }

            // Model listesini öneri bilgileriyle zenginleştir
            $enrichedModels = [];
            foreach ($modelsData['models'] as $model) {
                $modelName = $model['name'];
                $recommendation = $recommendations[$modelName] ?? null;

                $enrichedModels[] = [
                    'name' => $modelName,
                    'display_name' => $recommendation['title'] ?? $modelName,
                    'description' => $recommendation['description'] ?? 'Model açıklaması mevcut değil',
                    'size' => $model['size'],
                    'family' => $model['family'],
                    'parameter_size' => $model['parameter_size'],
                    'performance' => $recommendation['performance'] ?? 'Bilinmiyor',
                    'speed' => $recommendation['speed'] ?? 'Bilinmiyor',
                    'memory' => $recommendation['memory'] ?? $model['size'],
                    'recommended' => $recommendation['recommended'] ?? false,
                    'quantization' => $model['quantization'],
                    'modified_at' => $model['modified_at']
                ];
            }

            // Önerilen modelleri önce sırala
            usort($enrichedModels, function ($a, $b) {
                if ($a['recommended'] && !$b['recommended']) return -1;
                if (!$a['recommended'] && $b['recommended']) return 1;
                return 0;
            });

            return response()->json([
                'success' => true,
                'models' => $enrichedModels,
                'server_url' => $modelsData['server_url'],
                'total_models' => count($enrichedModels),
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('Ollama model listesi hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Model listesi alınamadı: ' . $e->getMessage(),
                'models' => []
            ], 500);
        }
    }
}
