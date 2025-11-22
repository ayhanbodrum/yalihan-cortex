<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AiLog;

class SystemMonitorController extends AdminController
{
    /**
     * AI Monitor Dashboard
     */
    public function index()
    {
        // AI Summary (Son 24 saat)
        $aiSummary = $this->getAISummary();

        // AI Models (Son 7 gün)
        $aiModels = $this->getAIModels();

        // MCP Status (boş collection - API'den gelecek)
        $mcpStatus = collect([]);

        // API Status (boş array - API'den gelecek)
        $apiStatus = [];

        // Self Healing (boş collection - API'den gelecek)
        $selfHealing = collect([]);

        // Recent Errors (Son 10 hata)
        $recentErrors = $this->getRecentErrors();

        // Overall Status
        $overall = $this->getOverallStatus($aiSummary);

        return view('admin.ai-monitor.index', [
            'aiSummary' => $aiSummary,
            'aiModels' => $aiModels,
            'mcpStatus' => $mcpStatus,
            'apiStatus' => $apiStatus,
            'selfHealing' => $selfHealing,
            'recentErrors' => $recentErrors,
            'overall' => $overall,
        ]);
    }

    /**
     * AI Summary (Son 24 saat)
     */
    protected function getAISummary(): array
    {
        $since = now()->subHours(24);

        $total = AiLog::where('created_at', '>=', $since)->count();
        $success = AiLog::where('created_at', '>=', $since)
            ->where('status', 'success')
            ->count();
        $failed = AiLog::where('created_at', '>=', $since)
            ->where('status', 'error')
            ->count();

        $avgResponse = AiLog::where('created_at', '>=', $since)
            ->where('status', 'success')
            ->avg('response_time') ?? 0;

        $avgTokens = AiLog::where('created_at', '>=', $since)
            ->where('status', 'success')
            ->avg('tokens_used') ?? 0;

        $successRate = $total > 0 ? round(($success / $total) * 100, 2) : 0;
        $errorRate = $total > 0 ? round(($failed / $total) * 100, 2) : 0;

        return [
            'total' => $total,
            'success' => $success,
            'failed' => $failed,
            'success_rate' => $successRate,
            'error_rate' => $errorRate,
            'avg_response_ms' => round($avgResponse, 2), // Already in ms
            'avg_tokens' => round($avgTokens, 0),
            'total_cost' => 0, // TODO: Calculate from tokens
        ];
    }

    /**
     * AI Models (Son 7 gün)
     */
    protected function getAIModels(): array
    {
        $since = now()->subDays(7);

        $models = AiLog::where('created_at', '>=', $since)
            ->select('model', DB::raw('count(*) as total'))
            ->groupBy('model')
            ->get()
            ->map(function ($log) use ($since) {
                $model = $log->model ?? 'unknown';
                $total = $log->total;

                $success = AiLog::where('created_at', '>=', $since)
                    ->where('model', $model)
                    ->where('status', 'success')
                    ->count();

                $failed = AiLog::where('created_at', '>=', $since)
                    ->where('model', $model)
                    ->where('status', 'error')
                    ->count();

                $avgResponse = AiLog::where('created_at', '>=', $since)
                    ->where('model', $model)
                    ->where('status', 'success')
                    ->avg('response_time') ?? 0;

                return [
                    'model' => $model,
                    'total' => $total,
                    'success' => $success,
                    'failed' => $failed,
                    'success_rate' => $total > 0 ? round(($success / $total) * 100, 2) : 0,
                    'error_rate' => $total > 0 ? round(($failed / $total) * 100, 2) : 0,
                    'avg_response_ms' => round($avgResponse, 2), // Already in ms
                    'total_cost' => 0, // TODO: Calculate from tokens
                ];
            })
            ->toArray();

        return $models;
    }

    /**
     * Recent Errors (Son 10)
     */
    protected function getRecentErrors()
    {
        return AiLog::where('status', 'error')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->request_type ?? 'unknown',
                    'provider' => $log->provider ?? 'unknown',
                    'error' => $log->error_message ?? ($log->response_data['error'] ?? 'Unknown error'),
                    'timestamp' => $log->created_at->toIso8601String(),
                ];
            });
    }

    /**
     * Overall Status
     */
    protected function getOverallStatus(array $aiSummary): array
    {
        $successRate = $aiSummary['success_rate'] ?? 0;
        $errorRate = $aiSummary['error_rate'] ?? 0;

        if ($successRate >= 90 && $errorRate < 10) {
            $level = 'green';
            $message = 'Sistem sağlıklı';
        } elseif ($successRate >= 70 && $errorRate < 20) {
            $level = 'yellow';
            $message = 'Bazı uyarılar var';
        } else {
            $level = 'red';
            $message = 'Kritik sorunlar tespit edildi';
        }

        return [
            'level' => $level,
            'message' => $message,
            'success_rate' => $successRate,
            'error_rate' => $errorRate,
        ];
    }

    /**
     * MCP Status API
     */
    public function apiMcpStatus(Request $request)
    {
        // TODO: Implement MCP status check
        return response()->json(['data' => []], 200);
    }

    /**
     * API Status API
     */
    public function apiApiStatus(Request $request)
    {
        // TODO: Implement API status check
        return response()->json(['data' => []], 200);
    }

    /**
     * Self Healing API
     */
    public function apiSelfHealing(Request $request)
    {
        // TODO: Implement self-healing status
        return response()->json(['data' => []], 200);
    }

    /**
     * Recent Errors API
     */
    public function apiRecentErrors(Request $request)
    {
        $errors = $this->getRecentErrors();
        return response()->json(['data' => $errors], 200);
    }

    /**
     * AI Health API
     */
    public function apiAiHealth(Request $request)
    {
        $aiSummary = $this->getAISummary();
        $overall = $this->getOverallStatus($aiSummary);

        return response()->json([
            'data' => [
                'summary' => $aiSummary,
                'overall' => $overall,
                'timestamp' => now()->toIso8601String(),
            ]
        ], 200);
    }

    /**
     * Context7 Rules API
     */
    public function getContext7Rules(Request $request)
    {
        // TODO: Load Context7 rules from authority.json
        return response()->json(['data' => []], 200);
    }
    public function apiPagesHealth(Request $request)
    {
        $data = [];
        return response()->json(['data' => $data], 200);
    }

    public function apiCodeHealth(Request $request)
    {
        $data = [
            'total_issues' => 0,
            'health_score' => 100,
            'issues' => [],
        ];
        return response()->json(['data' => $data], 200);
    }

    public function apiDuplicates(Request $request)
    {
        return response()->json(['data' => []], 200);
    }

    public function apiDuplicateFiles(Request $request)
    {
        return $this->apiDuplicates($request);
    }

    public function apiConflicts(Request $request)
    {
        return response()->json(['data' => []], 200);
    }

    public function apiConflictFiles(Request $request)
    {
        return $this->apiConflicts($request);
    }

    public function apiConflictingRoutes(Request $request)
    {
        return $this->apiConflicts($request);
    }

    public function runContext7Fix(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Öneriler hazır',
            'suggestions' => [],
            'action' => 'noop',
        ], 200);
    }

    public function applySuggestion(Request $request)
    {
        return response()->json([
            'manual_required' => true,
        ], 200);
    }
}
