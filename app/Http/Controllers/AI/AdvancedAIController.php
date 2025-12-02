<?php

declare(strict_types=1);

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Models\AiLog;
use App\Models\Ilan;
use App\Models\Talep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Advanced AI Controller
 *
 * Context7 Standard: C7-AI-DASHBOARD-CONTROLLER-2025-11-30
 *
 * AI Command Center Dashboard için controller
 */
class AdvancedAIController extends Controller
{
    /**
     * AI Performance Dashboard
     *
     * Context7: AI Command Center ana sayfası
     */
    public function performanceDashboard(Request $request)
    {
        // System Health
        $systemHealth = $this->getSystemHealth();

        // Opportunity Stream (Son 24 saatte yüksek skorlu eşleşmeler)
        $opportunityStream = $this->getOpportunityStream();

        // Usage Stats (Bugünkü aktiviteler)
        $usageStats = $this->getUsageStats();

        // Queue Worker Status
        $queueStatus = $this->getQueueWorkerStatus();

        // Telegram Notification Stats
        $telegramStats = $this->getTelegramNotificationStats();

        return view('admin.ai.dashboard', [
            'systemHealth' => $systemHealth,
            'opportunityStream' => $opportunityStream,
            'usageStats' => $usageStats,
            'queueStatus' => $queueStatus,
            'telegramStats' => $telegramStats,
        ]);
    }

    /**
     * System Health Check
     *
     * Context7: Cortex Brain (Laravel), LLM Engine (Ollama), Knowledge Base (AnythingLLM) durumları
     */
    public function systemHealth(): array
    {
        return $this->getSystemHealth();
    }

    /**
     * Usage Statistics
     *
     * Context7: Bugünkü AI aktivite istatistikleri
     */
    public function usageStatistics(): array
    {
        return $this->getUsageStats();
    }

    /**
     * Health Check API Endpoint
     *
     * Context7 Standard: C7-HEALTH-CHECK-API-2025-12-01
     * Monitoring araçları için health check endpoint
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function healthCheck(): \Illuminate\Http\JsonResponse
    {
        $systemHealth = $this->getSystemHealth();
        $queueStatus = $this->getQueueWorkerStatus();
        $telegramStats = $this->getTelegramNotificationStats();

        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'services' => [
                'laravel' => 'ok',
                'ollama' => $systemHealth['ollama']['status'] ?? 'unknown',
                'anythingllm' => $systemHealth['anythingllm']['status'] ?? 'unknown',
                'queue' => $queueStatus['status'] ?? 'unknown',
                'telegram' => $telegramStats['configured'] ? 'ok' : 'not_configured',
            ],
            'details' => [
                'system_health' => $systemHealth,
                'queue_status' => $queueStatus,
                'telegram_stats' => $telegramStats,
            ],
        ]);
    }

    /**
     * System Health API Endpoint
     *
     * Context7 Standard: C7-HEALTH-CHECK-API-2025-12-01
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function systemHealthApi(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->getSystemHealth());
    }

    /**
     * Queue Health API Endpoint
     *
     * Context7 Standard: C7-HEALTH-CHECK-API-2025-12-01
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function queueHealth(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->getQueueWorkerStatus());
    }

    /**
     * Telegram Health API Endpoint
     *
     * Context7 Standard: C7-HEALTH-CHECK-API-2025-12-01
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function telegramHealth(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->getTelegramNotificationStats());
    }

    /**
     * Performance Report
     *
     * Context7: Detaylı performans raporu
     */
    public function performanceReport()
    {
        // TODO: Detaylı rapor oluştur
        return response()->json(['message' => 'Performance report - to be implemented']);
    }

    /**
     * Features Overview
     *
     * Context7: AI özelliklerinin genel bakışı
     */
    public function featuresOverview()
    {
        // TODO: Features overview oluştur
        return response()->json(['message' => 'Features overview - to be implemented']);
    }

    /**
     * Get System Health Status
     *
     * @return array
     */
    private function getSystemHealth(): array
    {
        // Cortex Brain (Laravel) - Her zaman UP
        $cortexBrain = [
            'name' => 'Cortex Brain',
            'description' => 'Laravel Application',
            'status' => 'online',
            'url' => config('app.url'),
        ];

        // LLM Engine (Ollama)
        $ollamaStatus = $this->checkOllamaHealth();
        $llmEngine = [
            'name' => 'LLM Engine',
            'description' => 'Ollama Local AI',
            'status' => $ollamaStatus['status'],
            'url' => env('OLLAMA_URL', 'http://ollama:11434'),
            'response_time' => $ollamaStatus['response_time'] ?? null,
        ];

        // Knowledge Base (AnythingLLM)
        $anythingLlmStatus = $this->checkAnythingLlmHealth();
        $knowledgeBase = [
            'name' => 'Knowledge Base',
            'description' => 'AnythingLLM RAG',
            'status' => $anythingLlmStatus['status'],
            'url' => env('ANYTHINGLLM_URL', 'http://localhost:3001'),
            'response_time' => $anythingLlmStatus['response_time'] ?? null,
        ];

        return [
            'cortex_brain' => $cortexBrain,
            'llm_engine' => $llmEngine,
            'knowledge_base' => $knowledgeBase,
        ];
    }

    /**
     * Check Ollama Health
     *
     * @return array
     */
    private function checkOllamaHealth(): array
    {
        $ollamaUrl = env('OLLAMA_URL', 'http://ollama:11434');
        $startTime = microtime(true);

        try {
            $response = Http::timeout(2)
                ->get(rtrim($ollamaUrl, '/') . '/api/tags');

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($response->successful()) {
                return [
                    'status' => 'online',
                    'response_time' => $responseTime,
                ];
            }

            return [
                'status' => 'offline',
                'response_time' => $responseTime,
            ];
        } catch (\Exception $e) {
            Log::warning('Ollama health check failed', [
                'url' => $ollamaUrl,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'offline',
                'response_time' => null,
            ];
        }
    }

    /**
     * Check AnythingLLM Health
     *
     * @return array
     */
    private function checkAnythingLlmHealth(): array
    {
        $anythingLlmUrl = env('ANYTHINGLLM_URL', 'http://localhost:3001');
        $anythingLlmKey = env('ANYTHINGLLM_KEY');
        $startTime = microtime(true);

        if (empty($anythingLlmKey)) {
            return [
                'status' => 'not_configured',
                'response_time' => null,
            ];
        }

        try {
            $response = Http::timeout(2)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $anythingLlmKey,
                ])
                ->get(rtrim($anythingLlmUrl, '/') . '/api/system/health');

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($response->successful()) {
                return [
                    'status' => 'online',
                    'response_time' => $responseTime,
                ];
            }

            return [
                'status' => 'offline',
                'response_time' => $responseTime,
            ];
        } catch (\Exception $e) {
            Log::warning('AnythingLLM health check failed', [
                'url' => $anythingLlmUrl,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'offline',
                'response_time' => null,
            ];
        }
    }

    /**
     * Get Opportunity Stream
     *
     * Context7: Son 24 saatte SmartPropertyMatcherAI tarafından yakalanan yüksek skorlu eşleşmeler
     *
     * @return array
     */
    private function getOpportunityStream(): array
    {
        // ai_logs tablosundan SmartPropertyMatcherAI loglarını çek
        $logs = AiLog::where('request_type', 'like', '%SmartPropertyMatcherAI%')
            ->orWhere('request_data->service', 'SmartPropertyMatcherAI')
            ->where('created_at', '>=', now()->subHours(24))
            ->where('status', 'success')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $opportunities = [];

        foreach ($logs as $log) {
            $requestData = $log->request_data ?? [];
            $responseData = $log->response_data ?? [];

            // Eşleşme bilgilerini çıkar
            $ilanId = $requestData['ilan_id'] ?? $responseData['ilan_id'] ?? null;
            $talepId = $requestData['talep_id'] ?? $responseData['talep_id'] ?? null;
            $score = $responseData['score'] ?? $requestData['score'] ?? null;

            if ($ilanId && $score >= 80) {
                $ilan = Ilan::find($ilanId);
                if ($ilan) {
                    $opportunities[] = [
                        'id' => $log->id,
                        'type' => 'ilan_match',
                        'ilan_id' => $ilanId,
                        'ilan_baslik' => $ilan->baslik,
                        'score' => $score,
                        'created_at' => $log->created_at,
                        'time_ago' => $log->created_at->diffForHumans(),
                    ];
                }
            }

            if ($talepId && $score >= 80) {
                $talep = Talep::find($talepId);
                if ($talep) {
                    $opportunities[] = [
                        'id' => $log->id,
                        'type' => 'talep_match',
                        'talep_id' => $talepId,
                        'talep_baslik' => $talep->baslik,
                        'score' => $score,
                        'created_at' => $log->created_at,
                        'time_ago' => $log->created_at->diffForHumans(),
                    ];
                }
            }
        }

        // Score'a göre sırala
        usort($opportunities, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $opportunities;
    }

    /**
     * Get Usage Statistics
     *
     * Context7: Bugünkü AI aktivite istatistikleri
     *
     * @return array
     */
    private function getUsageStats(): array
    {
        $today = now()->startOfDay();

        // İmar Analizi
        $imarAnalizi = AiLog::where('request_type', 'analyze-construction')
            ->orWhere('request_type', 'like', '%imar%')
            ->where('created_at', '>=', $today)
            ->where('status', 'success')
            ->count();

        // İlan Açıklaması
        $ilanAciklama = AiLog::where('request_type', 'like', '%description%')
            ->orWhere('request_type', 'like', '%aciklama%')
            ->where('created_at', '>=', $today)
            ->where('status', 'success')
            ->count();

        // Fiyat Hesaplama
        $fiyatHesaplama = AiLog::where('request_type', 'like', '%price%')
            ->orWhere('request_type', 'like', '%fiyat%')
            ->orWhere('request_type', 'like', '%pricing%')
            ->where('created_at', '>=', $today)
            ->where('status', 'success')
            ->count();

        // Toplam Token Kullanımı
        $totalTokens = AiLog::where('created_at', '>=', $today)
            ->where('status', 'success')
            ->sum('tokens_used') ?? 0;

        // Toplam İstek Sayısı
        $totalRequests = AiLog::where('created_at', '>=', $today)
            ->count();

        // Başarı Oranı
        $successCount = AiLog::where('created_at', '>=', $today)
            ->where('status', 'success')
            ->count();

        $successRate = $totalRequests > 0
            ? round(($successCount / $totalRequests) * 100, 2)
            : 0;

        return [
            'imar_analizi' => $imarAnalizi,
            'ilan_aciklama' => $ilanAciklama,
            'fiyat_hesaplama' => $fiyatHesaplama,
            'total_tokens' => $totalTokens,
            'total_requests' => $totalRequests,
            'success_rate' => $successRate,
            'formatted_tokens' => number_format($totalTokens / 1000000, 2) . 'M',
        ];
    }

    /**
     * Get Queue Worker Status
     *
     * Context7: Queue worker'ın çalışıp çalışmadığını kontrol et
     *
     * @return array
     */
    private function getQueueWorkerStatus(): array
    {
        // Process kontrolü (basit - pgrep benzeri)
        $queueWorkerRunning = false;
        $queueName = 'cortex-notifications';

        try {
            // jobs tablosunda bekleyen iş sayısı
            $pendingJobs = DB::table('jobs')
                ->where('queue', $queueName)
                ->count();

            // Son 5 dakikada işlenen iş sayısı
            $processedJobs = DB::table('jobs')
                ->where('queue', $queueName)
                ->whereNotNull('reserved_at')
                ->where('reserved_at', '>=', now()->subMinutes(5))
                ->count();

            // Queue worker'ın çalışıp çalışmadığını tahmin et
            // Eğer son 5 dakikada iş işlendiyse, worker çalışıyor demektir
            $queueWorkerRunning = $processedJobs > 0 || $pendingJobs === 0;

            // Başarısız iş sayısı (failed_jobs tablosundan)
            $failedJobs = DB::table('failed_jobs')
                ->where('queue', $queueName)
                ->where('failed_at', '>=', now()->subHours(24))
                ->count();

            return [
                'status' => $queueWorkerRunning ? 'running' : 'stopped',
                'queue_name' => $queueName,
                'pending_jobs' => $pendingJobs,
                'processed_last_5min' => $processedJobs,
                'failed_last_24h' => $failedJobs,
                'last_check' => now()->toDateTimeString(),
            ];
        } catch (\Exception $e) {
            Log::warning('Queue worker status check failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'unknown',
                'queue_name' => $queueName,
                'pending_jobs' => 0,
                'processed_last_5min' => 0,
                'failed_last_24h' => 0,
                'last_check' => now()->toDateTimeString(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get Telegram Notification Statistics
     *
     * Context7: Telegram bildirim istatistikleri
     *
     * @return array
     */
    private function getTelegramNotificationStats(): array
    {
        $today = now()->startOfDay();

        // Bugün gönderilen bildirim sayısı
        $sentToday = AiLog::where('request_type', 'notification_sent')
            ->where('created_at', '>=', $today)
            ->where('status', 'success')
            ->count();

        // Son 24 saatte gönderilen bildirim sayısı
        $sentLast24h = AiLog::where('request_type', 'notification_sent')
            ->where('created_at', '>=', now()->subHours(24))
            ->where('status', 'success')
            ->count();

        // Başarısız bildirim sayısı (son 24 saat)
        $failedLast24h = AiLog::where('request_type', 'notification_sent')
            ->where('created_at', '>=', now()->subHours(24))
            ->where('status', 'failed')
            ->count();

        // Toplam bildirim sayısı (son 24 saat)
        $totalLast24h = $sentLast24h + $failedLast24h;

        // Başarı oranı
        $successRate = $totalLast24h > 0
            ? round(($sentLast24h / $totalLast24h) * 100, 2)
            : 0;

        // Bot token kontrolü
        $botTokenConfigured = !empty(env('TELEGRAM_BOT_TOKEN', ''));

        // Admin chat ID kontrolü
        $adminChatIdConfigured = !empty(env('TELEGRAM_ADMIN_CHAT_ID', ''));

        return [
            'sent_today' => $sentToday,
            'sent_last_24h' => $sentLast24h,
            'failed_last_24h' => $failedLast24h,
            'success_rate' => $successRate,
            'bot_token_configured' => $botTokenConfigured,
            'admin_chat_id_configured' => $adminChatIdConfigured,
            'is_configured' => $botTokenConfigured && $adminChatIdConfigured,
        ];
    }
}
