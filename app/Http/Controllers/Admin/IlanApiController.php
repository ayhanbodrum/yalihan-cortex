<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Cache\CacheHelper;
use App\Services\Response\ResponseService;
use Illuminate\Support\Facades\DB;

class IlanApiController extends Controller
{
    protected function recordRequest(string $name): void
    {
        $minute = now()->format('YmdHi');
        $rpm = (int) CacheHelper::get('api', 'rpm', 0, ['minute' => $minute]);
        CacheHelper::put('api', 'rpm', $rpm + 1, 'very_short', ['minute' => $minute]);
    }

    public function health()
    {
        $this->recordRequest('health');

        return ResponseService::success([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
        ], 'Sağlık kontrolü');
    }

    public function stats()
    {
        $this->recordRequest('stats');
        $stats = [
            'ilan_count' => DB::table('ilanlar')->count(),
            'kategori_count' => DB::table('ilan_kategorileri')->count(),
            'feature_count' => DB::table('features')->count(),
        ];

        return ResponseService::success($stats, 'İstatistikler');
    }

    public function performance()
    {
        $this->recordRequest('performance');
        $minute = now()->format('YmdHi');
        $sum = (int) CacheHelper::get('api', 'duration_sum', 0, ['minute' => $minute]);
        $count = (int) CacheHelper::get('api', 'duration_count', 0, ['minute' => $minute]);
        $rpm = (int) CacheHelper::get('api', 'rpm', 0, ['minute' => $minute]);
        $succ = (int) CacheHelper::get('api', 'success_count', 0, ['minute' => $minute]);
        $err = (int) CacheHelper::get('api', 'error_count', 0, ['minute' => $minute]);

        $avg = $count > 0 ? (int) round($sum / $count) : 0;
        $total = $succ + $err;
        $successRate = $total > 0 ? round(($succ / $total) * 100, 2) : 0;
        $errorRate = $total > 0 ? round(($err / $total) * 100, 2) : 0;

        // Son 5 dakika istatistikleri ve pencere üretici
        $minutesWindow = function (int $n) {
            $arr = [];
            for ($i = 0; $i < $n; $i++) {
                $arr[] = now()->copy()->subMinutes($i)->format('YmdHi');
            }

            return $arr;
        };
        $windowMinutes = $minutesWindow(5);
        $winSum = 0;
        $winCount = 0;
        $winRpm = 0;
        foreach ($windowMinutes as $m) {
            $winSum += (int) CacheHelper::get('api', 'duration_sum', 0, ['minute' => $m]);
            $winCount += (int) CacheHelper::get('api', 'duration_count', 0, ['minute' => $m]);
            $winRpm += (int) CacheHelper::get('api', 'rpm', 0, ['minute' => $m]);
        }
        $winAvg = $winCount > 0 ? (int) round($winSum / $winCount) : 0;

        // Percentile (yaklaşık) hesaplama: bucket sayımları üzerinden
        $bucketKeys = ['b_0_100', 'b_100_200', 'b_200_500', 'b_500_1000', 'b_1000_plus'];
        $bucketBounds = [100, 200, 500, 1000, 1500];
        $bucketCounts = [];
        foreach ($bucketKeys as $idx => $bk) {
            $bucketCounts[$idx] = 0;
            foreach ($windowMinutes as $m) {
                $bucketCounts[$idx] += (int) CacheHelper::get('api', $bk, 0, ['minute' => $m]);
            }
        }
        $totalBuckets = array_sum($bucketCounts);
        $calcPercentile = function (float $p) use ($bucketCounts, $bucketBounds, $totalBuckets) {
            if ($totalBuckets === 0) {
                return 0;
            }
            $threshold = $totalBuckets * $p;
            $running = 0;
            foreach ($bucketCounts as $i => $c) {
                $running += $c;
                if ($running >= $threshold) {
                    return $bucketBounds[$i];
                }
            }

            return end($bucketBounds);
        };
        $p95 = (int) $calcPercentile(0.95);
        $p99 = (int) $calcPercentile(0.99);

        // 15/60 dk trend RPM ve ortalama
        $win15 = $minutesWindow(15);
        $rpm15 = 0;
        $sum15 = 0;
        $count15 = 0;
        foreach ($win15 as $m) {
            $rpm15 += (int) CacheHelper::get('api', 'rpm', 0, ['minute' => $m]);
            $sum15 += (int) CacheHelper::get('api', 'duration_sum', 0, ['minute' => $m]);
            $count15 += (int) CacheHelper::get('api', 'duration_count', 0, ['minute' => $m]);
        }
        $avg15 = $count15 > 0 ? (int) round($sum15 / $count15) : 0;
        $win60 = $minutesWindow(60);
        $rpm60 = 0;
        $sum60 = 0;
        $count60 = 0;
        foreach ($win60 as $m) {
            $rpm60 += (int) CacheHelper::get('api', 'rpm', 0, ['minute' => $m]);
            $sum60 += (int) CacheHelper::get('api', 'duration_sum', 0, ['minute' => $m]);
            $count60 += (int) CacheHelper::get('api', 'duration_count', 0, ['minute' => $m]);
        }
        $avg60 = $count60 > 0 ? (int) round($sum60 / $count60) : 0;

        $bootTs = (int) CacheHelper::get('system', 'boot_ts', 0);
        $uptime = $bootTs > 0 ? now()->timestamp - $bootTs : 0;

        $data = [
            'avg_response_time_ms' => $avg,
            'requests_per_minute' => $rpm,
            'last_5m_avg_response_ms' => $winAvg,
            'last_5m_rpm' => $winRpm,
            'p95_ms' => $p95,
            'p99_ms' => $p99,
            'last_15m_rpm' => $rpm15,
            'last_15m_avg_ms' => $avg15,
            'last_60m_rpm' => $rpm60,
            'last_60m_avg_ms' => $avg60,
            'success_rate' => $successRate,
            'error_rate' => $errorRate,
            'uptime' => $uptime,
        ];

        return ResponseService::success($data, 'Performans');
    }

    public function context7Rules()
    {
        $this->recordRequest('context7-rules');
        $path = base_path('.context7/authority.json');
        if (! file_exists($path)) {
            return ResponseService::notFound('Context7 authority.json bulunamadı');
        }
        $json = json_decode(file_get_contents($path), true);
        $forbidden = isset($json['forbidden_patterns']) && is_array($json['forbidden_patterns']) ? count($json['forbidden_patterns']) : 0;
        $required = isset($json['required_patterns']) && is_array($json['required_patterns']) ? count($json['required_patterns']) : 0;
        $version = $json['version'] ?? null;

        return ResponseService::success([
            'version' => $version,
            'forbidden_count' => $forbidden,
            'required_count' => $required,
        ], 'Context7 kural özeti');
    }

    public function metrics()
    {
        $this->recordRequest('metrics');
        $rpm = (int) CacheHelper::get('api', 'rpm', 0, ['minute' => now()->format('YmdHi')]);
        $windowMinutes = [];
        for ($i = 0; $i < 5; $i++) {
            $windowMinutes[] = now()->copy()->subMinutes($i)->format('YmdHi');
        }
        $winRpm = 0;
        foreach ($windowMinutes as $m) {
            $winRpm += (int) CacheHelper::get('api', 'rpm', 0, ['minute' => $m]);
        }
        // 15/60 dk trend RPM
        $win15 = [];
        for ($i = 0; $i < 15; $i++) {
            $win15[] = now()->copy()->subMinutes($i)->format('YmdHi');
        }
        $rpm15 = 0;
        foreach ($win15 as $m) {
            $rpm15 += (int) CacheHelper::get('api', 'rpm', 0, ['minute' => $m]);
        }
        $win60 = [];
        for ($i = 0; $i < 60; $i++) {
            $win60[] = now()->copy()->subMinutes($i)->format('YmdHi');
        }
        $rpm60 = 0;
        foreach ($win60 as $m) {
            $rpm60 += (int) CacheHelper::get('api', 'rpm', 0, ['minute' => $m]);
        }

        $data = [
            'requests_per_minute' => $rpm,
            'last_5m_rpm' => $winRpm,
            'last_15m_rpm' => $rpm15,
            'last_60m_rpm' => $rpm60,
            'active_users' => 0,
            'queue_jobs' => 0,
        ];

        return ResponseService::success($data, 'Metrikler');
    }

    public function cacheStats()
    {
        $this->recordRequest('cache-stats');
        $data = [
            'enabled' => config('api.context7.cache_ttl', null) !== null,
            'entries' => 0,
            'driver' => config('cache.default'),
        ];

        return ResponseService::success($data, 'Cache istatistikleri');
    }

    public function dbPerformance()
    {
        $this->recordRequest('db-performance');
        $data = [
            'connections' => 1,
            'slow_queries' => 0,
        ];

        return ResponseService::success($data, 'Veritabanı performansı');
    }
}
