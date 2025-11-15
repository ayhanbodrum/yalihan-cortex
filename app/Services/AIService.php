<?php

namespace App\Services;

use App\Models\KategoriYayinTipiFieldDependency;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use App\Models\AiLog;
use App\Services\Cache\CacheHelper;

class AIService
{
    protected $provider;
    protected $config;
    protected $defaultProvider = 'openai';

    public function __construct()
    {
        $this->provider = $this->getActiveProvider();
        $this->config = $this->getProviderConfig();
    }

    /**
     * Analyze data with AI
     *
     * @param mixed $data
     * @param array $context
     * @return array
     */
    public function analyze(mixed $data, array $context = []): array
    {
        $prompt = $this->buildAnalysisPrompt($data, $context);
        return $this->makeRequest('analyze', $prompt, $context);
    }

    /**
     * Get AI suggestions
     *
     * @param mixed $context
     * @param string $type
     * @return array
     */
    public function suggest(mixed $context, string $type = 'general'): array
    {
        $prompt = $this->buildSuggestionPrompt($context, $type);
        return $this->makeRequest('suggest', $prompt, $context);
    }

    /**
     * Generate content with AI
     *
     * @param string $prompt
     * @param array $options
     * @return array
     */
    public function generate(string $prompt, array $options = []): array
    {
        return $this->makeRequest('generate', $prompt, $options);
    }

    // ═══════════════════════════════════════════════════════════
    // 🤖 AI-POWERED 2D MATRIX FIELD SUGGESTION
    // ═══════════════════════════════════════════════════════════

    /**
     * Konut özellikleri hibrit sıralama sistemi
     *
     * @param string $kategoriSlug Kategori (konut, arsa, yazlik)
     * @param array $context Ek bağlam
     * @return array Hibrit sıralama verileri
     */
    public function getKonutHibritSiralama(string $kategoriSlug = 'konut', array $context = []): array
    {
        // ✅ STANDARDIZED: Using CacheHelper with standard key format
        return CacheHelper::remember(
            'ai',
            'konut_hibrit_siralama',
            'medium', // 1 hour
            function () use ($kategoriSlug) {
                return \App\Models\KonutOzellikHibritSiralama::active()
                    ->ordered()
                    ->get()
                    ->toArray();
            },
            ['kategori' => $kategoriSlug]
        );
    }

    /**
     * Hibrit skor hesaplama
     *
     * @param int $kullanimSikligi Kullanım sıklığı
     * @param float $aiOneri AI öneri yüzdesi
     * @param float $kullaniciTercih Kullanıcı tercih yüzdesi
     * @return float Hibrit skor
     */
    public function calculateHibritSkor(int $kullanimSikligi, float $aiOneri, float $kullaniciTercih): float
    {
        // Normalize kullanım sıklığı (0-100 arası)
        $normalizedKullanim = min(100, ($kullanimSikligi / 6)); // 600 kullanım = 100 puan

        // Hibrit skor hesaplama: %40 kullanım + %30 AI + %30 kullanıcı
        $hibritSkor = ($normalizedKullanim * 0.4) + ($aiOneri * 0.3) + ($kullaniciTercih * 0.3);

        return round($hibritSkor, 2);
    }

    /**
     * Önem seviyesi belirleme
     *
     * @param float $hibritSkor Hibrit skor
     * @return string Önem seviyesi
     */
    public function determineOnemSeviyesi(float $hibritSkor): string
    {
        if ($hibritSkor >= 80) return 'cok_onemli';
        if ($hibritSkor >= 60) return 'onemli';
        if ($hibritSkor >= 40) return 'orta_onemli';
        return 'dusuk_onemli';
    }

    /**
     * AI ile özellik önerisi
     *
     * @param string $kategoriSlug Kategori
     * @param array $mevcutOzellikler Mevcut özellikler
     * @return array AI önerileri
     */
    public function suggestKonutOzellikleri($kategoriSlug = 'konut', $mevcutOzellikler = [])
    {
        $hibritSiralama = $this->getKonutHibritSiralama($kategoriSlug);

        // Mevcut olmayan özellikleri filtrele
        $oneriOzellikleri = array_filter($hibritSiralama, function($ozellik) use ($mevcutOzellikler) {
            return !in_array($ozellik->ozellik_slug, $mevcutOzellikler);
        });

        // Hibrit skoruna göre sırala
        usort($oneriOzellikleri, function($a, $b) {
            return $b->hibrit_skor <=> $a->hibrit_skor;
        });

        return array_slice($oneriOzellikleri, 0, 5); // İlk 5 öneri
    }

    // ═══════════════════════════════════════════════════════════
    // 🤖 AI-POWERED 2D MATRIX FIELD SUGGESTION
    // ═══════════════════════════════════════════════════════════

    /**
     * AI ile tek field için öneri
     *
     * @param string $kategoriSlug Kategori (konut, arsa, yazlik)
     * @param string $yayinTipi Yayın Tipi (Satılık, Kiralık, Sezonluk)
     * @param string $fieldSlug Field slug (ada_no, gunluk_fiyat)
     * @param array $context Form context (diğer field değerleri)
     * @return mixed AI önerisi
     */
    public function suggestFieldValue(string $kategoriSlug, string $yayinTipi, string $fieldSlug, array $context = [])
    {
        // Cache key
        $cacheKey = "ai_field_suggest_{$kategoriSlug}_{$yayinTipi}_{$fieldSlug}_" . md5(json_encode($context));

        return Cache::remember($cacheKey, 3600, function () use ($kategoriSlug, $yayinTipi, $fieldSlug, $context) {
            $prompt = $this->buildFieldSuggestionPrompt($kategoriSlug, $yayinTipi, $fieldSlug, $context);
            return $this->makeRequest('suggest_field', $prompt, compact('kategoriSlug', 'yayinTipi', 'fieldSlug', 'context'));
        });
    }

    /**
     * AI ile tüm field'ları otomatik doldur
     *
     * @param string $kategoriSlug
     * @param string $yayinTipi
     * @param array $existingData Mevcut form verileri
     * @return array Field slug => AI value
     */
    public function autoFillFields(string $kategoriSlug, string $yayinTipi, array $existingData = []): array
    {
        // AI-enabled field'ları getir
        $aiFields = KategoriYayinTipiFieldDependency::where('kategori_slug', $kategoriSlug)
            ->where('yayin_tipi', $yayinTipi)
            ->where('ai_auto_fill', 1)
            ->where('status', 1) // ✅ Context7: enabled → status
            ->get();

        $suggestions = [];

        foreach ($aiFields as $field) {
            try {
                $value = $this->suggestFieldValue($kategoriSlug, $yayinTipi, $field->field_slug, $existingData);
                $suggestions[$field->field_slug] = $value;
            } catch (\Exception $e) {
                Log::warning("AI auto-fill failed for {$field->field_slug}: " . $e->getMessage());
            }
        }

        return $suggestions;
    }

    /**
     * AI ile akıllı hesaplama
     * Örnek: Günlük fiyattan haftalık/aylık hesapla
     * Örnek: Satış fiyatından m² fiyatı hesapla
     *
     * @param string $sourceField Kaynak field (gunluk_fiyat)
     * @param mixed $sourceValue Kaynak değer (500)
     * @param string $targetField Hedef field (haftalik_fiyat)
     * @param array $context Hesaplama context'i
     * @return mixed Hesaplanan değer
     */
    public function smartCalculate(string $sourceField, $sourceValue, string $targetField, array $context = [])
    {
        $prompt = "
Hesaplama Görevi:
- Kaynak Field: {$sourceField} = {$sourceValue}
- Hedef Field: {$targetField}
- Context: " . json_encode($context) . "

Türkiye emlak sektörü standartlarına göre hesapla.

Örnekler:
- Günlük fiyat 500 TL → Haftalık fiyat = 500 × 7 × 0.85 (haftalık indirim) = 2,975 TL
- Günlük fiyat 500 TL → Aylık fiyat = 500 × 30 × 0.70 (aylık indirim) = 10,500 TL
- Yaz sezonu 500 TL → Ara sezon = 500 × 0.70 (-%30) = 350 TL
- Yaz sezonu 500 TL → Kış sezonu = 500 × 0.50 (-%50) = 250 TL
- Satış fiyatı 1,000,000 TL + Alan 100 m² → m² fiyatı = 10,000 TL/m²

Sadece hesaplanan sayısal değeri döndür (birim olmadan).
";

        try {
            $result = $this->makeRequest('calculate', $prompt, compact('sourceField', 'sourceValue', 'targetField', 'context'));
            return $result['value'] ?? null;
        } catch (\Exception $e) {
            Log::warning("AI smart calculate failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Build field suggestion prompt
     */
    private function buildFieldSuggestionPrompt(string $kategoriSlug, string $yayinTipi, string $fieldSlug, array $context): string
    {
        // Kategori özel prompt'lar
        $categoryContext = [
            'arsa' => [
                'ada_no' => 'Lokasyon bilgisinden ve TKGM verilerinden ada numarasını öner.',
                'parsel_no' => 'Lokasyon bilgisinden ve TKGM verilerinden parsel numarasını öner.',
                'imar_statusu' => 'Arsa konumu ve çevresindeki yapılaşmaya göre imar durumu öner.',
                'kaks' => 'İmar durumuna ve lokasyona göre KAKS değeri öner (örn: 1.25, 1.50).',
                'taks' => 'İmar durumuna göre TAKS değeri öner (örn: 0.30, 0.40).',
                'gabari' => 'Bölgenin yapılaşma karakterine göre gabari öner (örn: 9.50m).',
            ],
            'yazlik' => [
                'gunluk_fiyat' => 'Lokasyon, metrekare ve özelliklere göre günlük fiyat öner.',
                'haftalik_fiyat' => 'Günlük fiyattan haftalık fiyat hesapla (7 gün × %85 indirim).',
                'aylik_fiyat' => 'Günlük fiyattan aylık fiyat hesapla (30 gün × %70 indirim).',
                'yaz_sezonu_fiyat' => 'Piyasa verilerine göre yaz sezonu fiyatı öner.',
                'ara_sezon_fiyat' => 'Yaz sezonu fiyatından %70 olarak hesapla.',
                'kis_sezonu_fiyat' => 'Yaz sezonu fiyatından %50 olarak hesapla.',
                'minimum_konaklama' => 'Sezona ve bölgeye göre minimum konaklama öner (3-7 gün).',
                'maksimum_misafir' => 'Metrekareye göre maksimum misafir sayısı öner (m²/15).',
                'denize_uzaklik' => 'Google Maps API ile denize uzaklığı hesapla.',
            ],
            'konut' => [
                'esyali' => 'İlan fotoğraflarından ve açıklamadan eşyalı durumu belirle.',
                'm2_fiyati' => 'Satış fiyatı / Metrekare ile hesapla.',
            ],
        ];

        $fieldContext = $categoryContext[$kategoriSlug][$fieldSlug] ?? "Bu field için uygun değer öner.";

        $prompt = "
🎯 Emlak İlan Field Suggestion

Kategori: {$kategoriSlug}
Yayın Tipi: {$yayinTipi}
Field: {$fieldSlug}

Context:
" . json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "

Görev: {$fieldContext}

Sadece önerilen değeri döndür (açıklama veya birim olmadan).
Örnek: Ada no için → 1234
Örnek: Günlük fiyat için → 500
Örnek: İmar durumu için → İmarlı
";

        return $prompt;
    }

    public function healthCheck()
    {
        try {
            $response = $this->makeRequest('health', 'test', []);
            return [
                'status' => 'healthy',
                'provider' => $this->provider,
                'response_time' => $response['duration'] ?? 0
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'provider' => $this->provider,
                'error' => $e->getMessage()
            ];
        }
    }

    protected function makeRequest($action, $prompt, $options = [])
    {
        $startTime = microtime(true);

        try {
            $response = $this->callProvider($action, $prompt, $options);
            $duration = microtime(true) - $startTime;

            $this->logRequest($action, $prompt, $response, $duration);

            return $this->formatResponse($response, $duration);

        } catch (\Exception $e) {
            $duration = microtime(true) - $startTime;
            $this->logError($action, $prompt, $e->getMessage(), $duration);
            throw $e;
        }
    }

    protected function callProvider($action, $prompt, $options)
    {
        switch ($this->provider) {
            case 'openai':
                return $this->callOpenAI($action, $prompt, $options);
            case 'google':
                return $this->callGoogle($action, $prompt, $options);
            case 'claude':
                return $this->callClaude($action, $prompt, $options);
            case 'deepseek':
                return $this->callDeepSeek($action, $prompt, $options);
            case 'minimax':
                return $this->callMiniMax($action, $prompt, $options);
            case 'ollama':
                return $this->callOllama($action, $prompt, $options);
            default:
                throw new \Exception("Unsupported AI provider: {$this->provider}");
        }
    }

    protected function callOpenAI($action, $prompt, $options)
    {
        $apiKey = $this->config['openai_api_key'] ?? '';
        $model = $this->config['openai_model'] ?? 'gpt-3.5-turbo';

        if (empty($apiKey)) {
            throw new \Exception('OpenAI API key not configured');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => $options['max_tokens'] ?? 1000,
            'temperature' => $options['temperature'] ?? 0.7,
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI API error: ' . $response->body());
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? '';
    }

    protected function callGoogle($action, $prompt, $options)
    {
        $apiKey = $this->config['google_api_key'] ?? '';
        $model = $this->config['google_model'] ?? 'gemini-pro';

        if (empty($apiKey)) {
            throw new \Exception('Google API key not configured');
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'maxOutputTokens' => $options['max_tokens'] ?? 1000,
                'temperature' => $options['temperature'] ?? 0.7,
            ]
        ]);

        if (!$response->successful()) {
            throw new \Exception('Google API error: ' . $response->body());
        }

        $data = $response->json();
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }

    protected function callClaude($action, $prompt, $options)
    {
        $apiKey = $this->config['claude_api_key'] ?? '';
        $model = $this->config['claude_model'] ?? 'claude-3-sonnet-20240229';

        if (empty($apiKey)) {
            throw new \Exception('Claude API key not configured');
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
            'anthropic-version' => '2023-06-01',
        ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
            'model' => $model,
            'max_tokens' => $options['max_tokens'] ?? 1000,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ]
        ]);

        if (!$response->successful()) {
            throw new \Exception('Claude API error: ' . $response->body());
        }

        $data = $response->json();
        return $data['content'][0]['text'] ?? '';
    }

    protected function callDeepSeek($action, $prompt, $options)
    {
        $apiKey = $this->config['deepseek_api_key'] ?? '';
        $model = $this->config['deepseek_model'] ?? 'deepseek-chat';

        if (empty($apiKey)) {
            throw new \Exception('DeepSeek API key not configured');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.deepseek.com/v1/chat/completions', [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => $options['max_tokens'] ?? 1000,
            'temperature' => $options['temperature'] ?? 0.7,
        ]);

        if (!$response->successful()) {
            throw new \Exception('DeepSeek API error: ' . $response->body());
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? '';
    }

    protected function callMiniMax($action, $prompt, $options)
    {
        $apiKey = $this->config['minimax_api_key'] ?? '';
        $model = $this->config['minimax_model'] ?? 'minimax-m2';

        if (empty($apiKey)) {
            throw new \Exception('MiniMax API key not configured');
        }

        // MiniMax API v2 endpoint
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post('https://api.minimax.chat/v1/text/chatcompletion_v2', [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? 2000,
            'stream' => false,
        ]);

        if (!$response->successful()) {
            $errorBody = $response->body();
            Log::error('MiniMax API error', [
                'status' => $response->status(),
                'body' => $errorBody,
            ]);
            throw new \Exception('MiniMax API error: ' . $errorBody);
        }

        $data = $response->json();

        // MiniMax response format: { "choices": [{ "message": { "content": "..." } }] }
        if (isset($data['choices'][0]['message']['content'])) {
            return $data['choices'][0]['message']['content'];
        }

        // Fallback: try alternative response format
        if (isset($data['reply'])) {
            return $data['reply'];
        }

        throw new \Exception('Unexpected MiniMax API response format');
    }

    protected function callOllama($action, $prompt, $options)
    {
        $url = $this->config['ollama_url'] ?? 'http://localhost:11434';
        $model = $this->config['ollama_model'] ?? 'llama2';

        // Debug: Model seçimini kontrol et
        Log::info('Ollama Config:', ['url' => $url, 'model' => $model]);

        $response = Http::timeout(120)->post("{$url}/api/generate", [
            'model' => $model,
            'prompt' => $prompt,
            'stream' => false,
            'options' => [
                'temperature' => $options['temperature'] ?? 0.7,
                'num_predict' => $options['max_tokens'] ?? 1000,
            ]
        ]);

        if (!$response->successful()) {
            throw new \Exception('Ollama API error: ' . $response->body());
        }

        $data = $response->json();
        return $data['response'] ?? '';
    }

    protected function buildAnalysisPrompt($data, $context)
    {
        $basePrompt = "Analiz et ve öneriler sun:";

        if (isset($context['type'])) {
            switch ($context['type']) {
                case 'category':
                    $basePrompt = "Kategori analizi yap ve optimizasyon önerileri sun:";
                    break;
                case 'feature':
                    $basePrompt = "Özellik analizi yap ve öneriler sun:";
                    break;
                case 'content':
                    $basePrompt = "İçerik analizi yap ve iyileştirme önerileri sun:";
                    break;
            }
        }

        return $basePrompt . "\n\n" . json_encode($data, JSON_PRETTY_PRINT);
    }

    protected function buildSuggestionPrompt($context, $type)
    {
        $prompts = [
            'category' => 'Bu kategoriler için öneriler sun:',
            'feature' => 'Bu özellikler için öneriler sun:',
            'content' => 'Bu içerik için öneriler sun:',
            'qr_code' => 'QR kod kullanımı için öneriler sun. İlan bilgilerine göre QR kodun nerede ve nasıl kullanılacağına dair pratik öneriler ver:',
            'navigation' => 'İlan navigasyonu için öneriler sun. Kullanıcı deneyimini iyileştirmek için önceki/sonraki ilan navigasyonu ve benzer ilanlar önerileri ver:',
            'general' => 'Genel öneriler sun:'
        ];

        $basePrompt = $prompts[$type] ?? $prompts['general'];

        // QR Code için özel prompt
        if ($type === 'qr_code' && isset($context['ilan'])) {
            $basePrompt .= "\n\nİlan Bilgileri:\n";
            $basePrompt .= "- Başlık: " . ($context['ilan']['baslik'] ?? 'N/A') . "\n";
            $basePrompt .= "- Kategori: " . ($context['ilan']['kategori'] ?? 'N/A') . "\n";
            $basePrompt .= "- Lokasyon: " . ($context['ilan']['lokasyon'] ?? 'N/A') . "\n";
            $basePrompt .= "- Fiyat: " . ($context['ilan']['fiyat'] ?? 'N/A') . "\n";
            $basePrompt .= "\nQR kod kullanım önerileri:\n";
            $basePrompt .= "- Fiziksel görüntülemelerde nerede kullanılmalı?\n";
            $basePrompt .= "- Print materyallerde nasıl yerleştirilmeli?\n";
            $basePrompt .= "- Sosyal medya paylaşımlarında nasıl kullanılmalı?\n";
            $basePrompt .= "- Mobil kullanıcı deneyimi için öneriler\n";
        }

        // Navigation için özel prompt
        if ($type === 'navigation' && isset($context['ilan'])) {
            $basePrompt .= "\n\nİlan Bilgileri:\n";
            $basePrompt .= "- Başlık: " . ($context['ilan']['baslik'] ?? 'N/A') . "\n";
            $basePrompt .= "- Kategori: " . ($context['ilan']['kategori'] ?? 'N/A') . "\n";
            $basePrompt .= "- Lokasyon: " . ($context['ilan']['lokasyon'] ?? 'N/A') . "\n";
            $basePrompt .= "- Fiyat: " . ($context['ilan']['fiyat'] ?? 'N/A') . "\n";
            $basePrompt .= "\nNavigasyon önerileri:\n";
            $basePrompt .= "- Hangi ilanlar önceki/sonraki olarak gösterilmeli?\n";
            $basePrompt .= "- Benzer ilanlar nasıl belirlenmeli?\n";
            $basePrompt .= "- Kullanıcı deneyimini iyileştirmek için ne yapılmalı?\n";
        }

        return $basePrompt . "\n\n" . json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    protected function formatResponse($response, $duration)
    {
        return [
            'success' => true,
            'data' => $response,
            'metadata' => [
                'provider' => $this->provider,
                'duration' => round($duration, 3),
                'timestamp' => now()->toISOString()
            ]
        ];
    }

    protected function getActiveProvider()
    {
        return Cache::remember('ai_provider', 300, function () {
            return Setting::where('key', 'ai_provider')->value('value') ?? $this->defaultProvider;
        });
    }

    protected function getProviderConfig()
    {
        // ✅ STANDARDIZED: Using CacheHelper
        return CacheHelper::remember('ai', 'config', 'short', function () {
            $keys = [
                'openai_api_key', 'openai_model',
                'google_api_key', 'google_model',
                'claude_api_key', 'claude_model',
                'deepseek_api_key', 'deepseek_model',
                'minimax_api_key', 'minimax_model',
                'ollama_url', 'ollama_model'
            ];

            return Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();
        });
    }

    protected function logRequest($action, $prompt, $response, $duration)
    {
        AiLog::create([
            'action' => $action,
            'provider' => $this->provider,
            'prompt' => $prompt,
            'response' => is_string($response) ? $response : json_encode($response),
            'duration' => $duration,
            'status' => 'success',
            'user_id' => auth()->id()
        ]);
    }

    protected function logError($action, $prompt, $error, $duration)
    {
        AiLog::create([
            'action' => $action,
            'provider' => $this->provider,
            'prompt' => $prompt,
            'response' => $error,
            'duration' => $duration,
            'status' => 'error',
            'user_id' => auth()->id()
        ]);
    }

    public function getAvailableProviders()
    {
        return [
            'openai' => 'OpenAI',
            'google' => 'Google Gemini',
            'claude' => 'Anthropic Claude',
            'deepseek' => 'DeepSeek',
            'ollama' => 'Ollama (Local)'
        ];
    }

    public function switchProvider($provider)
    {
        if (!array_key_exists($provider, $this->getAvailableProviders())) {
            throw new \Exception("Invalid provider: {$provider}");
        }

        Setting::updateOrCreate(
            ['key' => 'ai_provider'],
            ['value' => $provider]
        );

        // ✅ STANDARDIZED: Using CacheHelper
        CacheHelper::forget('ai', 'provider');
        $this->provider = $provider;
        $this->config = $this->getProviderConfig();
    }

    /**
     * Ollama sunucusundan mevcut modelleri çek
     */
    public function getOllamaModels()
    {
        try {
            $ollamaUrl = config('ai.ollama_api_url', 'http://51.75.64.121:11434');

            $response = Http::timeout(10)->get($ollamaUrl . '/api/tags');

            if (!$response->successful()) {
                throw new \Exception('Ollama sunucusuna erişilemiyor');
            }

            $data = $response->json();
            $models = [];

            if (isset($data['models']) && is_array($data['models'])) {
                foreach ($data['models'] as $model) {
                    $models[] = [
                        'name' => $model['name'],
                        'model' => $model['model'],
                        'size' => $this->formatBytes($model['size'] ?? 0),
                        'family' => $model['details']['family'] ?? 'unknown',
                        'parameter_size' => $model['details']['parameter_size'] ?? 'unknown',
                        'quantization' => $model['details']['quantization_level'] ?? 'unknown',
                        'modified_at' => $model['modified_at'] ?? null
                    ];
                }
            }

            return [
                'success' => true,
                'models' => $models,
                'server_url' => $ollamaUrl
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'models' => []
            ];
        }
    }

    /**
     * Byte'ları okunabilir formata çevir
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Model öncelik sıralaması (en iyiden kötüye)
     */
    public function getModelRecommendations()
    {
        return [
            'qwen2.5:latest' => [
                'title' => 'Qwen 2.5 Latest (7.6B)',
                'description' => 'En güçlü model - Kompleks analizler için ideal',
                'performance' => 'Yüksek',
                'speed' => 'Orta',
                'memory' => '4.7 GB',
                'recommended' => true
            ],
            'qwen2.5:3b' => [
                'title' => 'Qwen 2.5 (3B)',
                'description' => 'Hızlı ve verimli - Günlük kullanım için optimal',
                'performance' => 'İyi',
                'speed' => 'Hızlı',
                'memory' => '1.9 GB',
                'recommended' => false
            ],
            'phi3:mini' => [
                'title' => 'Phi-3 Mini (3.8B)',
                'description' => 'Microsoft geliştirmesi - Kod analizi için iyi',
                'performance' => 'Orta',
                'speed' => 'Hızlı',
                'memory' => '2.2 GB',
                'recommended' => false
            ],
            'gemma2:2b' => [
                'title' => 'Gemma 2 (2B)',
                'description' => 'Hafif ve hızlı - Basit görevler için',
                'performance' => 'Temel',
                'speed' => 'Çok Hızlı',
                'memory' => '1.6 GB',
                'recommended' => false
            ]
        ];
    }

    /**
     * AI-Powered Smart Field Generation
     * Kategori seçilince uygun özellikleri önerir
     */
    public function suggestFieldsForCategory($kategoriSlug, $yayinTipi = null, $context = [])
    {
        $cacheKey = "ai_suggest_fields_{$kategoriSlug}_{$yayinTipi}";

        return Cache::remember($cacheKey, 3600, function() use ($kategoriSlug, $yayinTipi, $context) {
            $prompt = $this->buildFieldSuggestionPrompt($kategoriSlug, $yayinTipi, $context);
            return $this->makeRequest('suggest-fields', $prompt, $context);
        });
    }

    /**
     * AI-Powered Property Analysis
     * Mevcut özellikleri analiz eder ve eksikleri önerir
     */
    public function analyzePropertyFeatures($propertyData, $context = [])
    {
        $prompt = $this->buildPropertyAnalysisPrompt($propertyData, $context);
        return $this->makeRequest('analyze-property', $prompt, $context);
    }

    /**
     * AI-Powered Smart Form Generation
     * Kategori bazlı akıllı form field'ları oluşturur
     */
    public function generateSmartForm($kategoriSlug, $yayinTipi, $context = [])
    {
        $cacheKey = "ai_smart_form_{$kategoriSlug}_{$yayinTipi}";

        return Cache::remember($cacheKey, 3600, function() use ($kategoriSlug, $yayinTipi, $context) {
            $prompt = $this->buildSmartFormPrompt($kategoriSlug, $yayinTipi, $context);
            return $this->makeRequest('generate-form', $prompt, $context);
        });
    }


    /**
     * Property Analysis Prompt Builder
     */
    private function buildPropertyAnalysisPrompt($propertyData, $context)
    {
        return "Mevcut emlak özellikleri analizi:\n\n" .
               "Özellikler: " . json_encode($propertyData, JSON_UNESCAPED_UNICODE) . "\n\n" .
               "Bu özellikler için:\n" .
               "1. Eksik olan önemli özellikler neler?\n" .
               "2. Hangi özellikler daha detaylandırılabilir?\n" .
               "3. Bu emlak için hangi özellikler değer katabilir?\n" .
               "4. AI ile otomatik doldurulabilecek özellikler hangileri?\n\n" .
               "Her öneri için önem derecesi ve gerekçe belirt.";
    }

    /**
     * Smart Form Prompt Builder
     */
    private function buildSmartFormPrompt($kategoriSlug, $yayinTipi, $context)
    {
        $kategoriNames = [
            'konut' => 'Konut',
            'arsa' => 'Arsa',
            'yazlik' => 'Yazlık',
            'isyeri' => 'İşyeri'
        ];

        $kategoriName = $kategoriNames[$kategoriSlug] ?? $kategoriSlug;

        return "{$kategoriName} kategorisi için akıllı form oluştur:\n\n" .
               "Form field'ları şu kategorilerde organize et:\n" .
               "1. Altyapı\n" .
               "2. Genel Özellikler\n" .
               "3. Manzara\n" .
               "4. Konum\n\n" .
               "Her field için:\n" .
               "- Field tipi (text, number, boolean, select, textarea)\n" .
               "- Zorunlu mu? (true/false)\n" .
               "- AI önerisi var mı? (true/false)\n" .
               "- AI otomatik doldurma var mı? (true/false)\n" .
               "- Select seçenekleri (eğer select ise)\n" .
               "- Birim (m², km, vs.)\n\n" .
               "JSON formatında döndür.";
    }
}
