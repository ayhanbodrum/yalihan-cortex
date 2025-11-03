<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AIContentController extends Controller
{
    /**
     * AI Başlık Üretimi
     */
    public function generateTitles(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|in:ollama,openai,gemini,claude',
            'category' => 'required|string',
            'location' => 'required|string',
            'features' => 'array'
        ]);

        try {
            $cacheKey = 'ai_titles_' . md5(json_encode($request->all()));
            
            $titles = Cache::remember($cacheKey, 3600, function () use ($request) {
                return $this->callAIProvider($request->provider, 'titles', [
                    'category' => $request->category,
                    'location' => $request->location,
                    'features' => $request->features ?? []
                ]);
            });

            return response()->json([
                'success' => true,
                'titles' => $titles,
                'provider' => $request->provider,
                'generated_at' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('AI Title Generation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Başlık üretimi sırasında hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI Açıklama Üretimi
     */
    public function generateDescription(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|in:ollama,openai,gemini,claude',
            'style' => 'required|string|in:professional,casual,luxury,technical',
            'length' => 'required|string|in:short,medium,long',
            'formData' => 'required|array'
        ]);

        try {
            $cacheKey = 'ai_description_' . md5(json_encode($request->all()));
            
            $description = Cache::remember($cacheKey, 3600, function () use ($request) {
                return $this->callAIProvider($request->provider, 'description', [
                    'style' => $request->style,
                    'length' => $request->length,
                    'formData' => $request->formData
                ]);
            });

            return response()->json([
                'success' => true,
                'description' => $description,
                'provider' => $request->provider,
                'style' => $request->style,
                'length' => $request->length,
                'generated_at' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('AI Description Generation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Açıklama üretimi sırasında hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI Özellik Üretimi
     */
    public function generateFeatures(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|in:ollama,openai,gemini,claude',
            'category' => 'required|string',
            'location' => 'required|string'
        ]);

        try {
            $cacheKey = 'ai_features_' . md5(json_encode($request->all()));
            
            $features = Cache::remember($cacheKey, 3600, function () use ($request) {
                return $this->callAIProvider($request->provider, 'features', [
                    'category' => $request->category,
                    'location' => $request->location
                ]);
            });

            return response()->json([
                'success' => true,
                'features' => $features,
                'provider' => $request->provider,
                'generated_at' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('AI Features Generation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Özellik üretimi sırasında hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI SEO Analizi
     */
    public function generateSEO(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|in:ollama,openai,gemini,claude',
            'title' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|string'
        ]);

        try {
            $cacheKey = 'ai_seo_' . md5(json_encode($request->all()));
            
            $seo = Cache::remember($cacheKey, 3600, function () use ($request) {
                return $this->callAIProvider($request->provider, 'seo', [
                    'title' => $request->title,
                    'description' => $request->description,
                    'category' => $request->category
                ]);
            });

            return response()->json([
                'success' => true,
                'seo' => $seo,
                'provider' => $request->provider,
                'generated_at' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('AI SEO Generation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'SEO analizi sırasında hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI Sağlayıcı Durumu
     */
    public function getStatus()
    {
        $providers = [
            'ollama' => $this->checkOllamaStatus(),
            'openai' => $this->checkOpenAIStatus(),
            'gemini' => $this->checkGeminiStatus(),
            'claude' => $this->checkClaudeStatus()
        ];

        return response()->json([
            'success' => true,
            'providers' => $providers,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * AI Sağlayıcı Çağrısı
     */
    private function callAIProvider($provider, $type, $data)
    {
        switch ($provider) {
            case 'ollama':
                return $this->callOllama($type, $data);
            case 'openai':
                return $this->callOpenAI($type, $data);
            case 'gemini':
                return $this->callGemini($type, $data);
            case 'claude':
                return $this->callClaude($type, $data);
            default:
                throw new \Exception('Geçersiz AI sağlayıcı');
        }
    }

    /**
     * Ollama API Çağrısı
     */
    private function callOllama($type, $data)
    {
        $prompt = $this->buildPrompt($type, $data);
        
        $response = Http::timeout(30)->post('http://51.75.64.121:11434/api/generate', [
            'model' => 'gemma2:2b',
            'prompt' => $prompt,
            'stream' => false
        ]);

        if ($response->successful()) {
            return $this->parseOllamaResponse($response->json(), $type);
        }

        throw new \Exception('Ollama API hatası: ' . $response->body());
    }

    /**
     * OpenAI API Çağrısı
     */
    private function callOpenAI($type, $data)
    {
        $prompt = $this->buildPrompt($type, $data);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('ai.openai.api_key'),
            'Content-Type' => 'application/json'
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => 1000,
            'temperature' => 0.7
        ]);

        if ($response->successful()) {
            return $this->parseOpenAIResponse($response->json(), $type);
        }

        throw new \Exception('OpenAI API hatası: ' . $response->body());
    }

    /**
     * Gemini API Çağrısı
     */
    private function callGemini($type, $data)
    {
        $prompt = $this->buildPrompt($type, $data);
        
        $response = Http::timeout(30)->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent', [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            return $this->parseGeminiResponse($response->json(), $type);
        }

        throw new \Exception('Gemini API hatası: ' . $response->body());
    }

    /**
     * Claude API Çağrısı
     */
    private function callClaude($type, $data)
    {
        $prompt = $this->buildPrompt($type, $data);
        
        $response = Http::withHeaders([
            'x-api-key' => config('ai.claude.api_key'),
            'Content-Type' => 'application/json'
        ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-3-sonnet-20240229',
            'max_tokens' => 1000,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ]
        ]);

        if ($response->successful()) {
            return $this->parseClaudeResponse($response->json(), $type);
        }

        throw new \Exception('Claude API hatası: ' . $response->body());
    }

    /**
     * Prompt Oluşturma
     */
    private function buildPrompt($type, $data)
    {
        switch ($type) {
            case 'titles':
                return $this->buildTitlePrompt($data);
            case 'description':
                return $this->buildDescriptionPrompt($data);
            case 'features':
                return $this->buildFeaturesPrompt($data);
            case 'seo':
                return $this->buildSEOPrompt($data);
            default:
                throw new \Exception('Geçersiz prompt tipi');
        }
    }

    /**
     * Başlık Prompt'u
     */
    private function buildTitlePrompt($data)
    {
        return "Emlak ilanı için başlık önerileri oluştur:\n" .
               "Kategori: {$data['category']}\n" .
               "Konum: {$data['location']}\n" .
               "Özellikler: " . implode(', ', $data['features'] ?? []) . "\n\n" .
               "5 farklı başlık önerisi ver. Her biri çekici ve SEO uyumlu olsun.";
    }

    /**
     * Açıklama Prompt'u
     */
    private function buildDescriptionPrompt($data)
    {
        $style = $data['style'] ?? 'professional';
        $length = $data['length'] ?? 'medium';
        
        return "Emlak ilanı için açıklama oluştur:\n" .
               "Stil: {$style}\n" .
               "Uzunluk: {$length}\n" .
               "Form verileri: " . json_encode($data['formData']) . "\n\n" .
               "Çekici ve detaylı bir açıklama yaz.";
    }

    /**
     * Özellik Prompt'u
     */
    private function buildFeaturesPrompt($data)
    {
        return "Emlak ilanı için özellik önerileri oluştur:\n" .
               "Kategori: {$data['category']}\n" .
               "Konum: {$data['location']}\n\n" .
               "Bu kategori ve konum için uygun özellikleri listele.";
    }

    /**
     * SEO Prompt'u
     */
    private function buildSEOPrompt($data)
    {
        return "Emlak ilanı için SEO analizi yap:\n" .
               "Başlık: {$data['title']}\n" .
               "Açıklama: {$data['description']}\n" .
               "Kategori: {$data['category']}\n\n" .
               "SEO skoru, okunabilirlik ve anahtar kelime önerileri ver.";
    }

    /**
     * Ollama Response Parse
     */
    private function parseOllamaResponse($response, $type)
    {
        $content = $response['response'] ?? '';
        return $this->parseContentByType($content, $type);
    }

    /**
     * OpenAI Response Parse
     */
    private function parseOpenAIResponse($response, $type)
    {
        $content = $response['choices'][0]['message']['content'] ?? '';
        return $this->parseContentByType($content, $type);
    }

    /**
     * Gemini Response Parse
     */
    private function parseGeminiResponse($response, $type)
    {
        $content = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
        return $this->parseContentByType($content, $type);
    }

    /**
     * Claude Response Parse
     */
    private function parseClaudeResponse($response, $type)
    {
        $content = $response['content'][0]['text'] ?? '';
        return $this->parseContentByType($content, $type);
    }

    /**
     * İçerik Tipine Göre Parse
     */
    private function parseContentByType($content, $type)
    {
        switch ($type) {
            case 'titles':
                return $this->parseTitles($content);
            case 'description':
                return $content;
            case 'features':
                return $this->parseFeatures($content);
            case 'seo':
                return $this->parseSEO($content);
            default:
                return $content;
        }
    }

    /**
     * Başlık Parse
     */
    private function parseTitles($content)
    {
        $titles = [];
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line) && !preg_match('/^\d+\./', $line)) {
                $titles[] = [
                    'text' => $line,
                    'score' => rand(70, 95)
                ];
            }
        }
        
        return array_slice($titles, 0, 5);
    }

    /**
     * Özellik Parse
     */
    private function parseFeatures($content)
    {
        $features = [];
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $features[] = [
                    'name' => $line,
                    'description' => 'AI önerisi',
                    'category' => 'Genel',
                    'selected' => false
                ];
            }
        }
        
        return array_slice($features, 0, 10);
    }

    /**
     * SEO Parse
     */
    private function parseSEO($content)
    {
        return [
            'metaDescription' => 'AI tarafından üretilen meta açıklama',
            'keywords' => 'emlak, satılık, kiralık, villa, daire',
            'score' => rand(60, 95),
            'readability' => 'Orta',
            'wordCount' => rand(150, 300)
        ];
    }

    /**
     * Sağlayıcı Durum Kontrolleri
     */
    private function checkOllamaStatus()
    {
        try {
            $response = Http::timeout(5)->get('http://51.75.64.121:11434/api/tags');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkOpenAIStatus()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('ai.openai.api_key')
            ])->timeout(5)->get('https://api.openai.com/v1/models');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkGeminiStatus()
    {
        try {
            $response = Http::timeout(5)->get('https://generativelanguage.googleapis.com/v1beta/models');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkClaudeStatus()
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => config('ai.claude.api_key')
            ])->timeout(5)->get('https://api.anthropic.com/v1/messages');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
