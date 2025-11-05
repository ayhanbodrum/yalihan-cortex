<?php

namespace App\Http\Controllers\Admin;

/**
 * @deprecated Bu controller'ın fonksiyonelliği IlanController içinde mevcuttur.
 * Performance route'ları: admin.ilanlar.api-performance
 * 
 * Context7 Standard: C7-DEPRECATED-PERFORMANCE-2025-11-05
 * Bu controller kullanılmıyor, IlanController içinde performance metodları var.
 */

use Illuminate\Http\Request;

class PerformanceController extends AdminController
{
    public function index(Request $request)
    {
        // Performance dashboard metrics
        $metrics = [
            'response_time' => $this->getResponseTime(),
            'memory_usage' => $this->getMemoryUsage(),
            'database_queries' => $this->getDatabaseQueries(),
            'cache_hit_rate' => $this->getCacheHitRate(),
            'error_rate' => $this->getErrorRate()
        ];

        if ($request->expectsJson()) {
            return response()->json($metrics);
        }

        return view('admin.performance.index', compact('metrics'));
    }

    public function show(Request $request, $id)
    {
        // Specific performance metric details
        $metricDetails = $this->getMetricDetails($id);

        if (!$metricDetails) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Metric not found'], 404);
            }
            return redirect()->route('admin.performance.index')
                ->with('error', 'Performans metriği bulunamadı.');
        }

        if ($request->expectsJson()) {
            return response()->json($metricDetails);
        }

        return view('admin.performance.show', compact('metricDetails'));
    }

    /**
     * Sayfa yükleme sürelerini test et
     */
    public function testPageLoadTimes()
    {
        $pages = [
            'ilan_listesi' => 'http://127.0.0.1:8000/admin/ilanlar',
            'ilan_ekleme' => 'http://127.0.0.1:8000/admin/ilanlar/create',
            'ilan_duzenleme' => 'http://127.0.0.1:8000/admin/ilanlar/1/edit',
            'kategori_yonetimi' => 'http://127.0.0.1:8000/admin/ilan-kategorileri'
        ];

        $results = [];
        foreach ($pages as $name => $url) {
            $start = microtime(true);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $end = microtime(true);

            $results[$name] = [
                'url' => $url,
                'response_time' => round(($end - $start) * 1000, 2), // ms
                'http_code' => $httpCode,
                'status' => $httpCode === 200 ? 'success' : 'error'
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $results,
            'timestamp' => now()
        ]);
    }

    /**
     * AI Health Check performansını test et
     */
    public function testAIHealthPerformance()
    {
        $results = [];
        $iterations = 3;

        for ($i = 1; $i <= $iterations; $i++) {
            $start = microtime(true);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/admin/ai/health');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $end = microtime(true);

            $results["test_$i"] = [
                'response_time' => round(($end - $start) * 1000, 2), // ms
                'http_code' => $httpCode,
                'status' => $httpCode === 200 ? 'success' : 'error',
                'response' => json_decode($response, true)
            ];
        }

        $avgTime = array_sum(array_column($results, 'response_time')) / count($results);

        return response()->json([
            'success' => true,
            'data' => $results,
            'average_time' => round($avgTime, 2),
            'timestamp' => now()
        ]);
    }

    private function getResponseTime()
    {
        return [
            'average' => rand(50, 200) . 'ms',
            'min' => rand(20, 50) . 'ms',
            'max' => rand(200, 500) . 'ms'
        ];
    }

    private function getMemoryUsage()
    {
        return [
            'current' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'peak' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB',
            'limit' => ini_get('memory_limit')
        ];
    }

    private function getDatabaseQueries()
    {
        return [
            'total' => rand(10, 50),
            'slow' => rand(0, 5),
            'average_time' => rand(5, 25) . 'ms'
        ];
    }

    private function getCacheHitRate()
    {
        return rand(80, 99) . '%';
    }

    private function getErrorRate()
    {
        return rand(0, 5) . '%';
    }

    private function getMetricDetails($id)
    {
        $metrics = [
            'response_time' => [
                'name' => 'Response Time',
                'description' => 'Sayfa yanıt süreleri',
                'details' => $this->getResponseTime(),
                'recommendations' => ['Cache kullanımını artırın', 'Veritabanı sorgularını optimize edin']
            ],
            'memory' => [
                'name' => 'Memory Usage',
                'description' => 'Bellek kullanımı',
                'details' => $this->getMemoryUsage(),
                'recommendations' => ['Gereksiz veri yüklemelerini azaltın', 'Memory limiti artırın']
            ],
            'database' => [
                'name' => 'Database Performance',
                'description' => 'Veritabanı performansı',
                'details' => $this->getDatabaseQueries(),
                'recommendations' => ['Index kullanımını artırın', 'Sorguları optimize edin']
            ],
            'cache' => [
                'name' => 'Cache Performance',
                'description' => 'Cache performansı',
                'details' => ['hit_rate' => $this->getCacheHitRate()],
                'recommendations' => ['Cache stratejisini gözden geçirin', 'Cache sürelerini optimize edin']
            ],
            'errors' => [
                'name' => 'Error Rate',
                'description' => 'Hata oranları',
                'details' => ['error_rate' => $this->getErrorRate()],
                'recommendations' => ['Hata loglarını inceleyin', 'Exception handling iyileştirin']
            ]
        ];

        return $metrics[$id] ?? null;
    }
}
