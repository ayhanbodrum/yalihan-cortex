<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AILearningEngine
{
    private $aiService;
    private $patternAnalyzer;

    public function __construct()
    {
        $this->aiService = new AIService();
        $this->patternAnalyzer = new PatternAnalyzer();
    }

    /**
     * AI'yi öğret
     */
    public function teachAI($context, $examples)
    {
        $prompt = $this->buildTeachingPrompt($context, $examples);

        // AI'ye öğret
        $response = $this->aiService->generate($prompt, [
            'model' => 'ollama',
            'temperature' => 0.7,
            'max_tokens' => 1000
        ]);

        // Öğrenilen pattern'i analiz et
        $pattern = $this->patternAnalyzer->analyze($response, $examples);

        return $pattern;
    }

    /**
     * Pattern'leri uygula
     */
    public function applyPatterns($patterns, $input)
    {
        $bestPattern = $this->selectBestPattern($patterns, $input);

        if ($bestPattern) {
            $prompt = $this->buildApplicationPrompt($bestPattern, $input);
            return $this->aiService->generate($prompt);
        }

        return null;
    }

    /**
     * AI'yi işle
     */
    public function process($prompt, $expectedOutput)
    {
        $response = $this->aiService->generate($prompt);

        // Başarı oranını hesapla
        $accuracy = $this->calculateAccuracy($response, $expectedOutput);

        // Öğrenme verisini kaydet
        $this->saveLearningData($prompt, $expectedOutput, $response, $accuracy);

        return $response;
    }

    /**
     * Öğretim prompt'u oluştur
     */
    private function buildTeachingPrompt($context, $examples)
    {
        $prompt = "Sen bir emlak AI asistanısın. Aşağıdaki örnekleri incele ve öğren:\n\n";

        foreach ($examples as $index => $example) {
            $prompt .= "Örnek " . ($index + 1) . ":\n";
            $prompt .= "Giriş: " . $example['input'] . "\n";
            $prompt .= "Beklenen Çıkış: " . $example['output'] . "\n\n";
        }

        $prompt .= "Bu örnekleri analiz et ve benzer durumlar için nasıl yanıt vereceğini öğren.\n\n";
        $prompt .= "Bağlam: {$context}\n\n";
        $prompt .= "Öğrendiklerin:";

        return $prompt;
    }

    /**
     * Uygulama prompt'u oluştur
     */
    private function buildApplicationPrompt($pattern, $input)
    {
        $prompt = "Sen bir emlak AI asistanısın. Öğrendiğin pattern'leri kullanarak yanıt ver:\n\n";
        $prompt .= "Öğrenilen Pattern: " . $pattern['pattern'] . "\n";
        $prompt .= "Giriş: " . $input . "\n\n";
        $prompt .= "Yanıt:";

        return $prompt;
    }

    /**
     * En iyi pattern'i seç
     */
    private function selectBestPattern($patterns, $input)
    {
        if (!$patterns) return null;

        $bestPattern = null;
        $bestScore = 0;

        foreach ($patterns as $pattern) {
            $score = $this->calculatePatternScore($pattern, $input);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestPattern = $pattern;
            }
        }

        return $bestPattern;
    }

    /**
     * Pattern skorunu hesapla
     */
    private function calculatePatternScore($pattern, $input)
    {
        $score = 0;

        // Başarı oranı
        $score += $pattern['success_rate'] * 0.4;

        // Kullanım sıklığı
        $score += min($pattern['usage_count'] / 100, 1) * 0.3;

        // Benzerlik
        $similarity = $this->calculateSimilarity($pattern['context'], $input);
        $score += $similarity * 0.3;

        return $score;
    }

    /**
     * Benzerlik hesapla
     */
    private function calculateSimilarity($context, $input)
    {
        $contextWords = explode(' ', strtolower($context));
        $inputWords = explode(' ', strtolower($input));

        $commonWords = array_intersect($contextWords, $inputWords);
        $totalWords = count(array_unique(array_merge($contextWords, $inputWords)));

        return count($commonWords) / max($totalWords, 1);
    }

    /**
     * Doğruluk hesapla
     */
    private function calculateAccuracy($response, $expectedOutput)
    {
        $responseWords = explode(' ', strtolower($response));
        $expectedWords = explode(' ', strtolower($expectedOutput));

        $commonWords = array_intersect($responseWords, $expectedWords);
        $totalWords = count(array_unique(array_merge($responseWords, $expectedWords)));

        return count($commonWords) / max($totalWords, 1);
    }

    /**
     * Öğrenme verisini kaydet
     */
    private function saveLearningData($prompt, $expectedOutput, $actualOutput, $accuracy)
    {
        DB::table('ai_learning_data')->insert([
            'context' => $this->extractContext($prompt),
            'input_data' => $prompt,
            'expected_output' => $expectedOutput,
            'actual_output' => $actualOutput,
            'accuracy_score' => $accuracy,
            'is_correct' => $accuracy > 0.7,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Context çıkar
     */
    private function extractContext($prompt)
    {
        if (strpos($prompt, 'konut') !== false) return 'konut';
        if (strpos($prompt, 'arsa') !== false) return 'arsa';
        if (strpos($prompt, 'yazlik') !== false) return 'yazlik';
        if (strpos($prompt, 'isyeri') !== false) return 'isyeri';

        return 'genel';
    }
}

/**
 * Pattern Analyzer
 */
class PatternAnalyzer
{
    /**
     * Pattern analiz et
     */
    public function analyze($response, $examples)
    {
        $patterns = [];

        // Yanıtı analiz et
        $responseWords = explode(' ', strtolower($response));

        foreach ($examples as $example) {
            $exampleWords = explode(' ', strtolower($example['output']));
            $commonWords = array_intersect($responseWords, $exampleWords);

            if (count($commonWords) > 0) {
                $patterns[] = [
                    'pattern' => implode(' ', $commonWords),
                    'confidence' => count($commonWords) / count($exampleWords),
                    'context' => $example['context'] ?? 'genel'
                ];
            }
        }

        return $patterns;
    }
}
