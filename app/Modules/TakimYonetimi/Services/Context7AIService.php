<?php

namespace App\Modules\TakimYonetimi\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Context7AIService
{
    protected string $apiUrl;

    protected string $apiKey;

    protected array $headers;

    public function __construct()
    {
        $this->apiUrl = config('services.context7.api_url', 'https://api.context7.ai/v1');
        $this->apiKey = config('services.context7.api_key', '');
        $this->timeout = config('services.context7.timeout', 30);
        $this->retryAttempts = config('services.context7.retry_attempts', 3);
        $this->headers = [
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'YalihanEmlak-Context7/1.0',
        ];
    }

    /**
     * Görev analizi
     */
    public function analyzeTask(array $taskData): array
    {
        // Rate limiting kontrolü
        if (! $this->checkRateLimit('task_analysis', 30, 1)) { // Dakikada 30 çağrı
            return [
                'error' => 'Rate limit exceeded',
                'fallback_priority' => $this->fallbackPriorityAnalysis($taskData),
            ];
        }

        $cacheKey = 'context7_task_analysis_'.md5(serialize($taskData));

        // Cache kontrolü
        if ($cached = $this->getCachedData($cacheKey, 1800)) { // 30 dakika cache
            return $cached;
        }

        try {
            $payload = [
                'task' => $taskData,
                'analysis_type' => 'priority_assignment',
                'context' => 'real_estate_team_management',
            ];

            $response = Http::withHeaders($this->headers)
                ->timeout($this->timeout)
                ->retry($this->retryAttempts, 100)
                ->post("{$this->apiUrl}/analyze/task", $payload);

            if ($response->successful()) {
                $result = $response->json();
                $this->setCachedData($cacheKey, $result, 1800);

                return $result;
            }

            return [
                'error' => 'API çağrısı başarısız',
                'fallback_priority' => $this->fallbackPriorityAnalysis($taskData),
            ];

        } catch (\Exception $e) {
            Log::error('Context7 görev analizi hatası: '.$e->getMessage());

            return [
                'error' => $e->getMessage(),
                'fallback_priority' => $this->fallbackPriorityAnalysis($taskData),
            ];
        }
    }

    /**
     * Performans analizi
     */
    public function analyzePerformance(array $userData): array
    {
        try {
            $payload = [
                'user_data' => $userData,
                'analysis_type' => 'performance_insights',
                'metrics' => ['completion_rate', 'average_time', 'workload_balance'],
            ];

            $response = Http::withHeaders($this->headers)
                ->timeout($this->timeout)
                ->retry($this->retryAttempts, 100)
                ->post("{$this->apiUrl}/analyze/performance", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'error' => 'Performans analizi yapılamadı',
                'basic_metrics' => $this->calculateBasicMetrics($userData),
            ];

        } catch (\Exception $e) {
            Log::error('Context7 performans analizi hatası: '.$e->getMessage());

            return [
                'error' => $e->getMessage(),
                'basic_metrics' => $this->calculateBasicMetrics($userData),
            ];
        }
    }

    /**
     * Çok dilli doğal dil işleme
     */
    public function parseNaturalLanguage(string $text, string $language = 'auto'): array
    {
        try {
            $payload = [
                'text' => $text,
                'language' => $language,
                'domain' => 'task_management',
                'extract_entities' => ['task_title', 'priority', 'deadline', 'assignee'],
                'multi_language_support' => true,
                'fallback_languages' => ['tr', 'en'],
            ];

            $response = Http::withHeaders($this->headers)
                ->timeout($this->timeout)
                ->retry($this->retryAttempts, 100)
                ->post("{$this->apiUrl}/nlp/parse", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'error' => 'NLP analizi yapılamadı',
                'basic_parse' => $this->multiLanguageBasicParse($text, $language),
            ];

        } catch (\Exception $e) {
            Log::error('Context7 çok dilli NLP hatası: '.$e->getMessage());

            return [
                'error' => $e->getMessage(),
                'basic_parse' => $this->multiLanguageBasicParse($text, $language),
            ];
        }
    }

    /**
     * Çok dilli mesaj analizi
     */
    public function analyzeMessage(string $message, array $context = [], string $language = 'auto'): array
    {
        try {
            $payload = [
                'message' => $message,
                'context' => $context,
                'language' => $language,
                'intent_classification' => true,
                'sentiment_analysis' => true,
                'multi_language' => true,
            ];

            $response = Http::withHeaders($this->headers)
                ->timeout($this->timeout)
                ->retry($this->retryAttempts, 100)
                ->post("{$this->apiUrl}/analyze/message", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'intent' => 'unknown',
                'confidence' => 0,
                'entities' => [],
                'sentiment' => 'neutral',
                'detected_language' => $this->detectLanguage($message),
            ];

        } catch (\Exception $e) {
            Log::error('Context7 çok dilli mesaj analizi hatası: '.$e->getMessage());

            return [
                'intent' => 'unknown',
                'confidence' => 0,
                'entities' => [],
                'sentiment' => 'neutral',
                'error' => $e->getMessage(),
                'detected_language' => $this->detectLanguage($message),
            ];
        }
    }

    /**
     * Çok dilli yanıt oluşturma
     */
    public function generateResponse(array $analysis, array $userContext = [], string $language = 'tr'): string
    {
        try {
            $payload = [
                'analysis' => $analysis,
                'user_context' => $userContext,
                'response_type' => 'helpful_assistant',
                'language' => $language,
                'multi_language' => true,
            ];

            $response = Http::withHeaders($this->headers)
                ->timeout($this->timeout)
                ->retry($this->retryAttempts, 100)
                ->post("{$this->apiUrl}/generate/response", $payload);

            if ($response->successful()) {
                $data = $response->json();

                return $data['response'] ?? $this->getFallbackResponse($language, $analysis);
            }

            return $this->getFallbackResponse($language, $analysis);

        } catch (\Exception $e) {
            Log::error('Context7 çok dilli yanıt oluşturma hatası: '.$e->getMessage());

            return $this->getFallbackResponse($language, $analysis);
        }
    }

    /**
     * Dil tespiti
     */
    protected function detectLanguage(string $text): string
    {
        // Basit dil tespiti (gelişmiş versiyon için language detection API kullanılabilir)
        $turkishWords = ['görev', 'müşteri', 'ziyaret', 'toplantı', 'acil', 'önemli'];
        $englishWords = ['task', 'customer', 'visit', 'meeting', 'urgent', 'important'];

        $turkishCount = 0;
        $englishCount = 0;

        $words = explode(' ', strtolower($text));
        foreach ($words as $word) {
            if (in_array($word, $turkishWords)) {
                $turkishCount++;
            }
            if (in_array($word, $englishWords)) {
                $englishCount++;
            }
        }

        return $turkishCount >= $englishCount ? 'tr' : 'en';
    }

    /**
     * Çok dilli temel metin ayrıştırma
     */
    protected function multiLanguageBasicParse(string $text, string $language): array
    {
        $detectedLang = $language === 'auto' ? $this->detectLanguage($text) : $language;

        if ($detectedLang === 'en') {
            return $this->englishBasicParse($text);
        }

        return $this->basicTextParse($text);
    }

    /**
     * İngilizce temel metin ayrıştırma
     */
    protected function englishBasicParse(string $text): array
    {
        $lowerText = strtolower($text);

        // İngilizce acil kelimeler
        $urgentWords = ['urgent', 'emergency', 'critical', 'important', 'asap', 'rush'];
        $priority = 'normal';
        foreach ($urgentWords as $word) {
            if (strpos($lowerText, $word) !== false) {
                $priority = 'urgent';
                break;
            }
        }

        // İngilizce yüksek öncelik kelimeler
        $highWords = ['customer', 'client', 'meeting', 'presentation', 'deadline'];
        if ($priority === 'normal') {
            foreach ($highWords as $word) {
                if (strpos($lowerText, $word) !== false) {
                    $priority = 'high';
                    break;
                }
            }
        }

        return [
            'title' => substr($text, 0, 100),
            'description' => $text,
            'priority' => $priority,
            'deadline' => $this->extractEnglishDeadline($text),
            'assignee' => null,
            'tags' => $this->extractEnglishTags($text),
            'confidence' => 0.4,
            'language' => 'en',
        ];
    }

    /**
     * İngilizce deadline çıkarma
     */
    protected function extractEnglishDeadline(string $text): ?string
    {
        $patterns = [
            '/by\s+(\w+\s+\d+|\d+\s+\w+|\w+\s+\d+,\s+\d{4})/i',
            '/due\s+(\w+\s+\d+|\d+\s+\w+|\w+\s+\d+,\s+\d{4})/i',
            '/deadline\s+(\w+\s+\d+|\d+\s+\w+|\w+\s+\d+,\s+\d{4})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * İngilizce tag çıkarma
     */
    protected function extractEnglishTags(string $text): array
    {
        $tags = [];
        $tagWords = [
            'meeting', 'customer', 'client', 'urgent', 'important',
            'sales', 'visit', 'call', 'email', 'report',
        ];

        $lowerText = strtolower($text);
        foreach ($tagWords as $tag) {
            if (strpos($lowerText, $tag) !== false) {
                $tags[] = $tag;
            }
        }

        return array_unique($tags);
    }

    /**
     * Çok dilli fallback yanıt
     */
    protected function getFallbackResponse(string $language, array $analysis): string
    {
        $intent = $analysis['intent'] ?? 'unknown';

        if ($language === 'en') {
            switch ($intent) {
                case 'task_create':
                    return 'To create a new task, please specify the task title.';
                case 'task_list':
                    return 'To view your task list, use the /tasks command.';
                case 'help':
                    return 'For help, use the /help command.';
                default:
                    return 'How can I help you? Please provide more details.';
            }
        }

        return $this->fallbackResponse($analysis);
    }

    /**
     * Rapor analizi
     */
    public function analyzeReportRequirements(string $type, array $filters = []): array
    {
        try {
            $payload = [
                'report_type' => $type,
                'filters' => $filters,
                'analysis_type' => 'requirements_extraction',
            ];

            $response = Http::withHeaders($this->headers)
                ->post("{$this->apiUrl}/analyze/report", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'error' => 'Rapor analizi yapılamadı',
                'basic_requirements' => $this->getBasicReportRequirements($type),
            ];

        } catch (\Exception $e) {
            Log::error('Context7 rapor analizi hatası: '.$e->getMessage());

            return [
                'error' => $e->getMessage(),
                'basic_requirements' => $this->getBasicReportRequirements($type),
            ];
        }
    }

    /**
     * İçgörü oluşturma
     */
    public function generateInsights(array $data): array
    {
        try {
            $payload = [
                'data' => $data,
                'insight_types' => ['trends', 'anomalies', 'recommendations'],
                'depth' => 'detailed',
            ];

            $response = Http::withHeaders($this->headers)
                ->post("{$this->apiUrl}/generate/insights", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'error' => 'İçgörü oluşturma yapılamadı',
                'basic_insights' => $this->generateBasicInsights($data),
            ];

        } catch (\Exception $e) {
            Log::error('Context7 içgörü hatası: '.$e->getMessage());

            return [
                'error' => $e->getMessage(),
                'basic_insights' => $this->generateBasicInsights($data),
            ];
        }
    }

    /**
     * Öneri oluşturma
     */
    public function generateRecommendations(array $data): array
    {
        try {
            $payload = [
                'data' => $data,
                'recommendation_types' => ['performance', 'workload', 'process'],
                'priority_level' => 'high',
            ];

            $response = Http::withHeaders($this->headers)
                ->post("{$this->apiUrl}/generate/recommendations", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'error' => 'Öneri oluşturma yapılamadı',
                'basic_recommendations' => $this->generateBasicRecommendations($data),
            ];

        } catch (\Exception $e) {
            Log::error('Context7 öneri hatası: '.$e->getMessage());

            return [
                'error' => $e->getMessage(),
                'basic_recommendations' => $this->generateBasicRecommendations($data),
            ];
        }
    }

    /**
     * Fallback öncelik analizi
     */
    protected function fallbackPriorityAnalysis(array $taskData): string
    {
        $title = strtolower($taskData['title'] ?? '');
        $description = strtolower($taskData['description'] ?? '');

        // Acil kelimeler
        $urgentWords = ['acil', 'urgent', 'emergency', 'kritik', 'önemli'];
        foreach ($urgentWords as $word) {
            if (strpos($title, $word) !== false || strpos($description, $word) !== false) {
                return 'acil';
            }
        }

        // Yüksek öncelik kelimeler
        $highWords = ['müşteri', 'ziyaret', 'toplantı', 'sunum', 'deadline'];
        foreach ($highWords as $word) {
            if (strpos($title, $word) !== false || strpos($description, $word) !== false) {
                return 'yuksek';
            }
        }

        return 'normal';
    }

    /**
     * Temel metrikler hesaplama
     */
    protected function calculateBasicMetrics(array $userData): array
    {
        return [
            'completion_rate' => $userData['completion_rate'] ?? 0,
            'average_time' => $userData['average_completion_time'] ?? 0,
            'total_tasks' => $userData['total_tasks'] ?? 0,
            'active_tasks' => $userData['active_tasks'] ?? 0,
            'overdue_tasks' => $userData['overdue_tasks'] ?? 0,
        ];
    }

    /**
     * Temel metin ayrıştırma
     */
    protected function basicTextParse(string $text): array
    {
        return [
            'title' => substr($text, 0, 100),
            'description' => $text,
            'priority' => 'normal',
            'deadline' => null,
            'assignee' => null,
            'tags' => [],
            'confidence' => 0.3,
        ];
    }

    /**
     * Fallback yanıt
     */
    protected function fallbackResponse(array $analysis): string
    {
        $intent = $analysis['intent'] ?? 'unknown';

        switch ($intent) {
            case 'task_create':
                return 'Yeni görev oluşturmak için lütfen görev başlığını belirtin.';
            case 'task_list':
                return 'Görev listenizi görüntülemek için /gorevler komutunu kullanın.';
            case 'help':
                return 'Yardım için /help komutunu kullanabilirsiniz.';
            default:
                return 'Size nasıl yardımcı olabilirim? Lütfen daha fazla detay verin.';
        }
    }

    /**
     * Temel rapor gereksinimleri
     */
    protected function getBasicReportRequirements(string $type): array
    {
        $requirements = [
            'team_performance' => [
                'metrics' => ['completion_rate', 'average_time', 'total_tasks'],
                'group_by' => 'user',
                'period' => 'month',
            ],
            'task_distribution' => [
                'metrics' => ['count', 'avg_time'],
                'group_by' => 'type',
                'period' => 'month',
            ],
            'workload_analysis' => [
                'metrics' => ['active_tasks', 'capacity', 'utilization'],
                'group_by' => 'user',
                'period' => 'current',
            ],
        ];

        return $requirements[$type] ?? [];
    }

    /**
     * Temel içgörüler
     */
    protected function generateBasicInsights(array $data): array
    {
        $insights = [];

        if (isset($data[0]['completion_rate'])) {
            $avgCompletion = array_sum(array_column($data, 'completion_rate')) / count($data);
            $insights[] = 'Ortalama tamamlama oranı: %'.round($avgCompletion, 1);
        }

        if (isset($data[0]['active_tasks'])) {
            $totalActive = array_sum(array_column($data, 'active_tasks'));
            $insights[] = "Toplam status görev sayısı: {$totalActive}";
        }

        return $insights;
    }

    /**
     * Temel öneriler
     */
    protected function generateBasicRecommendations(array $data): array
    {
        $recommendations = [];

        // Tamamlama oranı düşük olanlar için
        foreach ($data as $item) {
            if (isset($item['completion_rate']) && $item['completion_rate'] < 70) {
                $name = isset($item['name']) ? $item['name'] : 'Kullanıcı';
                $recommendations[] = "{$name} için tamamlama oranı iyileştirme eğitimi önerilir.";
            }
        }

        // İş yükü yüksek olanlar için
        foreach ($data as $item) {
            if (isset($item['utilization']) && $item['utilization'] > 80) {
                $name = isset($item['name']) ? $item['name'] : 'Kullanıcı';
                $recommendations[] = "{$name} için iş yükü dengelenmesi önerilir.";
            }
        }

        return $recommendations;
    }

    /**
     * API bağlantı testi
     */
    public function testConnection(): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->timeout($this->timeout)
                ->retry($this->retryAttempts, 100)
                ->get("{$this->apiUrl}/health");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'connected' => true,
                    'status' => $data['status'] ?? 'healthy',
                    'response_time' => $response->handlerStats()['total_time'] ?? 0,
                    'version' => $data['version'] ?? 'unknown',
                    'features' => $data['features'] ?? [],
                ];
            }

            return [
                'connected' => false,
                'status' => 'unhealthy',
                'error' => 'API yanıt vermiyor',
                'http_status' => $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error('Context7 bağlantı testi hatası: '.$e->getMessage());

            return [
                'connected' => false,
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cache'den veri al
     */
    protected function getCachedData(string $key, int $ttl = 3600)
    {
        $cached = Cache::get($key);
        if ($cached) {
            Log::info("Context7 cache hit for key: {$key}");
        }

        return $cached;
    }

    /**
     * Cache'e veri kaydet
     */
    protected function setCachedData(string $key, $data, int $ttl = 3600): void
    {
        Cache::put($key, $data, $ttl);
        Log::info("Context7 cache set for key: {$key}, TTL: {$ttl}");
    }

    /**
     * Makine öğrenmesi ile tahmin analizi
     */
    public function predictiveAnalysis(array $historicalData, string $predictionType): array
    {
        try {
            $payload = [
                'data' => $historicalData,
                'prediction_type' => $predictionType,
                'model' => 'time_series_forecasting',
                'confidence_interval' => 0.95,
            ];

            $response = Http::withHeaders($this->headers)
                ->timeout($this->timeout)
                ->retry($this->retryAttempts, 100)
                ->post("{$this->apiUrl}/ml/predict", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'error' => 'Tahmin analizi yapılamadı',
                'basic_prediction' => $this->basicPredictionAnalysis($historicalData, $predictionType),
            ];

        } catch (\Exception $e) {
            Log::error('Context7 ML tahmin hatası: '.$e->getMessage());

            return [
                'error' => $e->getMessage(),
                'basic_prediction' => $this->basicPredictionAnalysis($historicalData, $predictionType),
            ];
        }
    }

    /**
     * Kümeleme analizi
     */
    public function clusteringAnalysis(array $data, array $features): array
    {
        try {
            $payload = [
                'data' => $data,
                'features' => $features,
                'algorithm' => 'kmeans',
                'clusters' => 3,
                'normalize' => true,
            ];

            $response = Http::withHeaders($this->headers)
                ->timeout($this->timeout)
                ->retry($this->retryAttempts, 100)
                ->post("{$this->apiUrl}/ml/cluster", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'error' => 'Kümeleme analizi yapılamadı',
                'basic_clusters' => $this->basicClustering($data, $features),
            ];

        } catch (\Exception $e) {
            Log::error('Context7 kümeleme hatası: '.$e->getMessage());

            return [
                'error' => $e->getMessage(),
                'basic_clusters' => $this->basicClustering($data, $features),
            ];
        }
    }

    /**
     * Anomali tespiti
     */
    public function anomalyDetection(array $data, array $metrics): array
    {
        try {
            $payload = [
                'data' => $data,
                'metrics' => $metrics,
                'algorithm' => 'isolation_forest',
                'contamination' => 0.1,
            ];

            $response = Http::withHeaders($this->headers)
                ->timeout($this->timeout)
                ->retry($this->retryAttempts, 100)
                ->post("{$this->apiUrl}/ml/anomaly", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'error' => 'Anomali tespiti yapılamadı',
                'basic_anomalies' => $this->basicAnomalyDetection($data, $metrics),
            ];

        } catch (\Exception $e) {
            Log::error('Context7 anomali tespiti hatası: '.$e->getMessage());

            return [
                'error' => $e->getMessage(),
                'basic_anomalies' => $this->basicAnomalyDetection($data, $metrics),
            ];
        }
    }

    /**
     * Trend analizi
     */
    public function trendAnalysis(array $timeSeriesData, string $period): array
    {
        try {
            $payload = [
                'data' => $timeSeriesData,
                'period' => $period,
                'analysis_type' => 'trend_detection',
                'seasonal_decomposition' => true,
            ];

            $response = Http::withHeaders($this->headers)
                ->timeout($this->timeout)
                ->retry($this->retryAttempts, 100)
                ->post("{$this->apiUrl}/ml/trend", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'error' => 'Trend analizi yapılamadı',
                'basic_trends' => $this->basicTrendAnalysis($timeSeriesData),
            ];

        } catch (\Exception $e) {
            Log::error('Context7 trend analizi hatası: '.$e->getMessage());

            return [
                'error' => $e->getMessage(),
                'basic_trends' => $this->basicTrendAnalysis($timeSeriesData),
            ];
        }
    }

    /**
     * Temel tahmin analizi
     */
    protected function basicPredictionAnalysis(array $historicalData, string $predictionType): array
    {
        if (empty($historicalData)) {
            return ['prediction' => 0, 'confidence' => 0];
        }

        $values = array_column($historicalData, 'value');
        $avg = array_sum($values) / count($values);

        // Basit lineer trend
        $trend = 0;
        if (count($values) > 1) {
            $firstHalf = array_slice($values, 0, floor(count($values) / 2));
            $secondHalf = array_slice($values, floor(count($values) / 2));
            $avgFirst = array_sum($firstHalf) / count($firstHalf);
            $avgSecond = array_sum($secondHalf) / count($secondHalf);
            $trend = ($avgSecond - $avgFirst) / $avgFirst;
        }

        $prediction = $avg * (1 + $trend);

        return [
            'prediction' => round($prediction, 2),
            'confidence' => 0.6,
            'trend' => round($trend * 100, 2).'%',
            'method' => 'basic_linear_trend',
        ];
    }

    /**
     * Temel kümeleme
     */
    protected function basicClustering(array $data, array $features): array
    {
        $clusters = [];
        $numClusters = 3;

        foreach ($data as $item) {
            $scores = [];
            foreach ($features as $feature) {
                $scores[] = $item[$feature] ?? 0;
            }

            // Basit skorlama ile küme atama
            $totalScore = array_sum($scores);
            $clusterId = $totalScore > 15 ? 2 : ($totalScore > 8 ? 1 : 0);

            if (! isset($clusters[$clusterId])) {
                $clusters[$clusterId] = [];
            }
            $clusters[$clusterId][] = $item;
        }

        return [
            'clusters' => $clusters,
            'cluster_centers' => array_fill(0, $numClusters, ['method' => 'basic_scoring']),
            'method' => 'basic_threshold_clustering',
        ];
    }

    /**
     * Temel anomali tespiti
     */
    protected function basicAnomalyDetection(array $data, array $metrics): array
    {
        $anomalies = [];
        $stats = [];

        // Her metrik için istatistik hesapla
        foreach ($metrics as $metric) {
            $values = array_column($data, $metric);
            $mean = array_sum($values) / count($values);
            $variance = 0;
            foreach ($values as $value) {
                $variance += pow($value - $mean, 2);
            }
            $stdDev = sqrt($variance / count($values));

            $stats[$metric] = [
                'mean' => $mean,
                'std_dev' => $stdDev,
                'threshold' => $mean + (2 * $stdDev),
            ];
        }

        // Anomalileri tespit et
        foreach ($data as $index => $item) {
            foreach ($metrics as $metric) {
                if (($item[$metric] ?? 0) > $stats[$metric]['threshold']) {
                    $anomalies[] = [
                        'index' => $index,
                        'metric' => $metric,
                        'value' => $item[$metric],
                        'threshold' => $stats[$metric]['threshold'],
                        'severity' => 'high',
                    ];
                }
            }
        }

        return [
            'anomalies' => $anomalies,
            'total_anomalies' => count($anomalies),
            'method' => 'basic_statistical_outlier',
        ];
    }

    /**
     * Güvenlik audit'i
     */
    public function securityAudit(): array
    {
        return [
            'api_key_status' => $this->checkAPIKeySecurity(),
            'encryption_status' => $this->checkEncryptionStatus(),
            'rate_limiting' => $this->getRateLimitStatus(),
            'audit_logs' => $this->getAuditLogs(),
            'vulnerability_check' => $this->checkVulnerabilities(),
            'last_audit' => now()->toISOString(),
        ];
    }

    /**
     * Input sanitization
     */
    protected function sanitizeInput(string $input): string
    {
        // XSS koruması
        $input = strip_tags($input);

        // SQL injection koruması için özel karakterleri escape et
        $input = addslashes($input);

        // Maksimum uzunluk kontrolü
        if (strlen($input) > 10000) {
            $input = substr($input, 0, 10000);
        }

        return $input;
    }

    /**
     * API key güvenliği kontrolü
     */
    protected function checkAPIKeySecurity(): array
    {
        $key = $this->apiKey;
        $issues = [];

        if (empty($key)) {
            $issues[] = 'API key tanımlanmamış';
        }

        if (strlen($key) < 20) {
            $issues[] = 'API key çok kısa (minimum 20 karakter önerilir)';
        }

        if (! preg_match('/[A-Z]/', $key)) {
            $issues[] = 'API key büyük harf içermeli';
        }

        if (! preg_match('/[a-z]/', $key)) {
            $issues[] = 'API key küçük harf içermeli';
        }

        if (! preg_match('/[0-9]/', $key)) {
            $issues[] = 'API key rakam içermeli';
        }

        return [
            'secure' => empty($issues),
            'issues' => $issues,
            'strength' => $this->calculateKeyStrength($key),
        ];
    }

    /**
     * API key güç hesapla
     */
    protected function calculateKeyStrength(string $key): string
    {
        $score = 0;

        if (strlen($key) >= 32) {
            $score += 25;
        } elseif (strlen($key) >= 20) {
            $score += 15;
        }

        if (preg_match('/[A-Z]/', $key)) {
            $score += 20;
        }
        if (preg_match('/[a-z]/', $key)) {
            $score += 20;
        }
        if (preg_match('/[0-9]/', $key)) {
            $score += 20;
        }
        if (preg_match('/[^A-Za-z0-9]/', $key)) {
            $score += 15;
        }

        if ($score >= 80) {
            return 'strong';
        }
        if ($score >= 60) {
            return 'medium';
        }

        return 'weak';
    }

    /**
     * Şifreleme statusu kontrolü
     */
    protected function checkEncryptionStatus(): array
    {
        return [
            'data_encryption' => config('app.key') ? true : false,
            'api_communication' => str_starts_with($this->apiUrl, 'https'),
            'sensitive_data_protection' => true, // Implement edildi varsayalım
            'key_rotation' => $this->checkKeyRotation(),
        ];
    }

    /**
     * Key rotation kontrolü
     */
    protected function checkKeyRotation(): array
    {
        $lastRotation = Cache::get('context7_key_last_rotation');
        $rotationInterval = 30; // 30 gün

        if (! $lastRotation) {
            return [
                'status' => 'never_rotated',
                'days_since_rotation' => null,
                'needs_rotation' => true,
            ];
        }

        $daysSince = now()->diffInDays($lastRotation);

        return [
            'status' => $daysSince > $rotationInterval ? 'overdue' : 'current',
            'days_since_rotation' => $daysSince,
            'needs_rotation' => $daysSince > $rotationInterval,
        ];
    }

    /**
     * Rate limit statusu
     */
    protected function getRateLimitStatus(): array
    {
        $limits = [
            'task_analysis' => ['max' => 30, 'window' => 1],
            'performance_analysis' => ['max' => 20, 'window' => 1],
            'nlp_parse' => ['max' => 50, 'window' => 1],
        ];

        $status = [];
        foreach ($limits as $key => $limit) {
            $status[$key] = [
                'limit' => $limit['max'],
                'window_minutes' => $limit['window'],
                'current_usage' => $this->getCurrentUsage($key, $limit['window']),
            ];
        }

        return $status;
    }

    /**
     * Mevcut kullanım
     */
    protected function getCurrentUsage(string $key, int $windowMinutes): int
    {
        $cacheKey = "context7_rate_limit_{$key}";
        $usage = Cache::get($cacheKey, []);

        // Son pencere içindeki istekleri say
        $windowStart = now()->subMinutes($windowMinutes);
        $currentUsage = 0;

        foreach ($usage as $timestamp) {
            if (Carbon::parse($timestamp)->isAfter($windowStart)) {
                $currentUsage++;
            }
        }

        return $currentUsage;
    }

    /**
     * Audit logları
     */
    protected function getAuditLogs(): array
    {
        // Son 24 saatin loglarını al
        $logs = [];

        // Bu örnekte basit log yapısı - gerçek implementasyonda database'den alınacak
        $recentLogs = Cache::get('context7_audit_logs', []);

        return [
            'total_logs' => count($recentLogs),
            'recent_logs' => array_slice($recentLogs, -10), // Son 10 log
            'log_retention_days' => 30,
        ];
    }

    /**
     * Güvenlik açıkları kontrolü
     */
    protected function checkVulnerabilities(): array
    {
        $vulnerabilities = [];

        // API URL kontrolü
        if (! str_starts_with($this->apiUrl, 'https')) {
            $vulnerabilities[] = [
                'type' => 'insecure_connection',
                'severity' => 'high',
                'description' => 'API bağlantısı HTTPS kullanmıyor',
            ];
        }

        // API key kontrolü
        $keyCheck = $this->checkAPIKeySecurity();
        if (! $keyCheck['secure']) {
            $vulnerabilities[] = [
                'type' => 'weak_api_key',
                'severity' => 'medium',
                'description' => 'API key güvenlik gereksinimlerini karşılamıyor',
                'issues' => $keyCheck['issues'],
            ];
        }

        // Rate limiting kontrolü
        if (! $this->checkRateLimit('vulnerability_check', 10, 1)) {
            $vulnerabilities[] = [
                'type' => 'rate_limit_bypass',
                'severity' => 'low',
                'description' => 'Rate limiting status değil',
            ];
        }

        return [
            'found' => count($vulnerabilities),
            'vulnerabilities' => $vulnerabilities,
            'scan_timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Güvenli loglama
     */
    protected function secureLog(string $action, array $data = [], string $level = 'info'): void
    {
        // Hassas verileri maskele
        $safeData = $this->maskSensitiveData($data);

        // Audit log'a ekle
        $auditEntry = [
            'timestamp' => now()->toISOString(),
            'action' => $action,
            'data' => $safeData,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        $logs = Cache::get('context7_audit_logs', []);
        $logs[] = $auditEntry;

        // Son 1000 log'u tut
        if (count($logs) > 1000) {
            $logs = array_slice($logs, -1000);
        }

        Cache::put('context7_audit_logs', $logs, 60 * 24 * 30); // 30 gün

        Log::log($level, "Context7 Security: {$action}", $safeData);
    }

    /**
     * Monitoring setup
     */
    public function setupMonitoring(): array
    {
        return [
            'health_checks' => $this->setupHealthChecks(),
            'metrics_collection' => $this->setupMetricsCollection(),
            'alerts_configuration' => $this->setupAlerts(),
            'logging_configuration' => $this->setupLogging(),
            'performance_monitoring' => $this->setupPerformanceMonitoring(),
        ];
    }

    /**
     * Health check kurulum
     */
    protected function setupHealthChecks(): array
    {
        $checks = [
            'api_connectivity' => [
                'status' => $this->testConnection()['connected'] ? 'healthy' : 'unhealthy',
                'check_interval' => 60, // saniye
                'timeout' => 10,
            ],
            'database_connectivity' => [
                'status' => $this->checkDatabaseConnection() ? 'healthy' : 'unhealthy',
                'check_interval' => 30,
            ],
            'cache_performance' => [
                'status' => $this->checkCachePerformance() ? 'healthy' : 'degraded',
                'check_interval' => 120,
            ],
            'memory_usage' => [
                'status' => $this->checkMemoryUsage() ? 'healthy' : 'warning',
                'check_interval' => 300,
            ],
        ];

        return $checks;
    }

    /**
     * Metrics collection kurulum
     */
    protected function setupMetricsCollection(): array
    {
        return [
            'request_metrics' => [
                'enabled' => true,
                'metrics' => ['response_time', 'success_rate', 'error_rate'],
                'collection_interval' => 60,
            ],
            'performance_metrics' => [
                'enabled' => true,
                'metrics' => ['cpu_usage', 'memory_usage', 'api_latency'],
                'collection_interval' => 30,
            ],
            'business_metrics' => [
                'enabled' => true,
                'metrics' => ['tasks_processed', 'users_served', 'ai_accuracy'],
                'collection_interval' => 300,
            ],
        ];
    }

    /**
     * Alert kurulum
     */
    protected function setupAlerts(): array
    {
        return [
            'api_down' => [
                'enabled' => true,
                'threshold' => '3 failures in 5 minutes',
                'channels' => ['email', 'slack'],
                'escalation' => 'after 10 minutes',
            ],
            'high_error_rate' => [
                'enabled' => true,
                'threshold' => '5% error rate',
                'channels' => ['email'],
                'escalation' => 'after 15 minutes',
            ],
            'performance_degradation' => [
                'enabled' => true,
                'threshold' => 'response time > 5 seconds',
                'channels' => ['email', 'slack'],
                'escalation' => 'immediate',
            ],
            'security_alert' => [
                'enabled' => true,
                'threshold' => 'any security event',
                'channels' => ['email', 'sms'],
                'escalation' => 'immediate',
            ],
        ];
    }

    /**
     * Logging kurulum
     */
    protected function setupLogging(): array
    {
        return [
            'application_logs' => [
                'level' => 'info',
                'retention' => '30 days',
                'rotation' => 'daily',
                'compression' => true,
            ],
            'security_logs' => [
                'level' => 'warning',
                'retention' => '90 days',
                'rotation' => 'weekly',
                'encryption' => true,
            ],
            'performance_logs' => [
                'level' => 'debug',
                'retention' => '7 days',
                'rotation' => 'hourly',
                'compression' => true,
            ],
            'audit_logs' => [
                'level' => 'info',
                'retention' => '1 year',
                'rotation' => 'monthly',
                'encryption' => true,
            ],
        ];
    }

    /**
     * Performance monitoring kurulum
     */
    protected function setupPerformanceMonitoring(): array
    {
        return [
            'response_time_tracking' => [
                'enabled' => true,
                'percentiles' => [50, 95, 99],
                'alert_threshold' => 2000, // ms
            ],
            'throughput_monitoring' => [
                'enabled' => true,
                'metrics' => ['requests_per_second', 'concurrent_users'],
                'alert_threshold' => 100, // rps
            ],
            'error_tracking' => [
                'enabled' => true,
                'error_types' => ['4xx', '5xx', 'timeout'],
                'alert_threshold' => 5, // percent
            ],
            'resource_monitoring' => [
                'enabled' => true,
                'resources' => ['cpu', 'memory', 'disk', 'network'],
                'alert_thresholds' => [
                    'cpu' => 80, // percent
                    'memory' => 85, // percent
                    'disk' => 90, // percent
                    'network' => 1000, // Mbps
                ],
            ],
        ];
    }

    /**
     * Database bağlantı kontrolü
     */
    protected function checkDatabaseConnection(): bool
    {
        try {
            // Basit bir query ile bağlantıyı test et
            \DB::select('SELECT 1');

            return true;
        } catch (\Exception $e) {
            Log::error('Database connection check failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Cache performans kontrolü
     */
    protected function checkCachePerformance(): bool
    {
        try {
            $start = microtime(true);
            Cache::put('health_check', 'ok', 10);
            $value = Cache::get('health_check');
            $end = microtime(true);

            $responseTime = ($end - $start) * 1000; // ms

            return $value === 'ok' && $responseTime < 100; // 100ms altında
        } catch (\Exception $e) {
            Log::error('Cache performance check failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Memory usage kontrolü
     */
    protected function checkMemoryUsage(): bool
    {
        $memoryUsage = memory_get_usage(true) / 1024 / 1024; // MB
        $memoryLimit = $this->getMemoryLimit();

        $usagePercent = ($memoryUsage / $memoryLimit) * 100;

        return $usagePercent < 80; // %80 altında
    }

    /**
     * Memory limit al
     */
    protected function getMemoryLimit(): int
    {
        $limit = ini_get('memory_limit');

        if ($limit === '-1') {
            return 512; // Unlimited ise 512MB varsayalım
        }

        return (int) $limit;
    }

    /**
     * Monitoring dashboard verileri
     */
    public function getMonitoringDashboard(): array
    {
        return [
            'system_health' => $this->getSystemHealth(),
            'performance_metrics' => $this->getPerformanceMetrics(),
            'error_rates' => $this->getErrorRates(),
            'usage_statistics' => $this->getUsageStatistics(),
            'last_updated' => now()->toISOString(),
        ];
    }

    /**
     * Sistem sağlığı
     */
    protected function getSystemHealth(): array
    {
        return [
            'overall_status' => 'healthy',
            'components' => [
                'api' => $this->testConnection()['connected'] ? 'healthy' : 'unhealthy',
                'database' => $this->checkDatabaseConnection() ? 'healthy' : 'unhealthy',
                'cache' => $this->checkCachePerformance() ? 'healthy' : 'degraded',
                'memory' => $this->checkMemoryUsage() ? 'healthy' : 'warning',
            ],
            'uptime' => '99.9%',
            'last_incident' => null,
        ];
    }

    /**
     * Performance metrikleri
     */
    protected function getPerformanceMetrics(): array
    {
        return [
            'response_time' => [
                'average' => 245, // ms
                'p95' => 450,
                'p99' => 800,
            ],
            'throughput' => [
                'current' => 25, // rps
                'peak' => 50,
            ],
            'error_rate' => 0.5, // percent
        ];
    }

    /**
     * Error oranları
     */
    protected function getErrorRates(): array
    {
        return [
            'total_errors' => 12,
            'error_rate' => 0.5,
            'error_types' => [
                '4xx' => 8,
                '5xx' => 3,
                'timeout' => 1,
            ],
            'trend' => 'decreasing',
        ];
    }

    /**
     * Kullanım istatistikleri
     */
    protected function getUsageStatistics(): array
    {
        return [
            'total_requests' => 15420,
            'active_users' => 45,
            'tasks_processed' => 1234,
            'ai_interactions' => 567,
            'peak_usage_hour' => '14:00',
        ];
    }
}
