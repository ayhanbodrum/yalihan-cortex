<?php

namespace App\Services;

use App\Models\AiLog;
use Illuminate\Support\Facades\Cache;

class AiMonitorService
{
    public function summary(int $hours = 24): array
    {
        $key = 'ai_monitor:summary:'.$hours;

        return Cache::remember($key, now()->addMinutes(5), function () use ($hours) {
            $from = now()->subHours($hours);

            $base = AiLog::query()->where('created_at', '>=', $from);
            $total = (clone $base)->count();
            $success = (clone $base)->where('status', 'success')->count();
            $failed = (clone $base)->whereIn('status', ['failed', 'error', 'timeout'])->count();
            $avgResponse = (int) ((clone $base)->whereNotNull('response_time')->avg('response_time') ?? 0);
            $avgTokens = (int) ((clone $base)->whereNotNull('tokens_used')->avg('tokens_used') ?? 0);
            $cost = (float) ((clone $base)->sum('cost') ?? 0);

            $successRate = $total > 0 ? round(($success / $total) * 100, 2) : 0.0;
            $errorRate = $total > 0 ? round(($failed / $total) * 100, 2) : 0.0;

            return [
                'from' => $from->toIso8601String(),
                'to' => now()->toIso8601String(),
                'total' => $total,
                'success' => $success,
                'failed' => $failed,
                'success_rate' => $successRate,
                'error_rate' => $errorRate,
                'avg_response_ms' => $avgResponse,
                'avg_tokens' => $avgTokens,
                'total_cost' => round($cost, 6),
            ];
        });
    }

    public function byModel(int $days = 7): array
    {
        $key = 'ai_monitor:by_model:'.$days;

        return Cache::remember($key, now()->addMinutes(5), function () use ($days) {
            $from = now()->subDays($days);
            $rows = AiLog::query()
                ->where('created_at', '>=', $from)
                ->selectRaw('model, count(*) as total, sum(cost) as cost, avg(response_time) as avg_response')
                ->groupBy('model')
                ->get();

            $data = [];
            foreach ($rows as $row) {
                $model = $row->model ?: 'unknown';
                $total = (int) $row->total;
                $success = AiLog::query()->where('created_at', '>=', $from)->where('model', $row->model)->where('status', 'success')->count();
                $failed = AiLog::query()->where('created_at', '>=', $from)->where('model', $row->model)->whereIn('status', ['failed', 'error', 'timeout'])->count();
                $successRate = $total > 0 ? round(($success / $total) * 100, 2) : 0.0;
                $errorRate = $total > 0 ? round(($failed / $total) * 100, 2) : 0.0;
                $data[] = [
                    'model' => $model,
                    'total' => $total,
                    'success_rate' => $successRate,
                    'error_rate' => $errorRate,
                    'avg_response_ms' => (int) ($row->avg_response ?? 0),
                    'total_cost' => round((float) ($row->cost ?? 0), 6),
                ];
            }

            return $data;
        });
    }

    public function health(): array
    {
        $summary = $this->summary(24);
        $status = 'healthy';
        $reasons = [];

        if ($summary['error_rate'] > 20.0) {
            $status = 'degraded';
            $reasons[] = 'high_error_rate';
        }
        if ($summary['avg_response_ms'] > 3000) {
            $status = 'degraded';
            $reasons[] = 'high_latency';
        }
        if ($summary['total'] === 0) {
            $status = 'idle';
        }

        return [
            'status' => $status,
            'summary' => $summary,
            'reasons' => $reasons,
            'checked_at' => now()->toIso8601String(),
        ];
    }
}
