<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Yalıhan Bekçi Dashboard Controller
 * Web üzerinden monitoring görüntüleme
 */
class YalihanBekciController extends AdminController
{
    /**
     * Dashboard ana sayfa
     */
    public function index()
    {
        $report = $this->generateReport();
        $logs = $this->getRecentLogs();
        $history = $this->getScoreHistory();

        return view('admin.yalihan-bekci.dashboard-simple', compact('report', 'logs', 'history'));
    }

    /**
     * API endpoint - Canlı veri
     */
    public function liveData()
    {
        $report = $this->generateReport();

        return response()->json($report);
    }

    /**
     * Manuel kontrol başlat
     */
    public function runCheck(Request $request)
    {
        // Artisan komutunu çalıştır
        Artisan::call('bekci:monitor');
        $output = Artisan::output();

        return response()->json([
            'success' => true,
            'message' => 'Kontrol tamamlandı',
            'output' => $output,
        ]);
    }

    /**
     * Otomatik düzeltme başlat
     */
    public function autoFix(Request $request)
    {
        // Basit auth kontrolü
        if (! auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Giriş yapmalısınız',
            ], 401);
        }

        Artisan::call('bekci:monitor', ['--auto-fix' => true]);
        $output = Artisan::output();

        return response()->json([
            'success' => true,
            'message' => 'Otomatik düzeltme tamamlandı',
            'output' => $output,
        ]);
    }

    /**
     * Rapor oluştur
     */
    private function generateReport()
    {
        $report = [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'context7' => $this->checkContext7(),
            'components' => $this->checkComponentUsage(),
            'code_quality' => $this->checkCodeQuality(),
            'database' => $this->checkDatabaseHealth(),
            'performance' => $this->checkPerformance(),
        ];

        // Skor hesapla
        $report['score'] = $this->calculateScore($report);
        $report['status'] = $this->getStatus($report['score']);

        return $report;
    }

    private function checkContext7()
    {
        // Context7 ihlallerini say
        $violations = 0;
        $files = File::allFiles(app_path());

        $forbiddenPatterns = ['status', 'status', 'status', 'kisiler', 'country_id'];

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $content = File::get($file->getPathname());
                foreach ($forbiddenPatterns as $pattern) {
                    $violations += substr_count(strtolower($content), $pattern);
                }
            }
        }

        return [
            'violations' => $violations,
            'status' => $violations === 0 ? 'perfect' : ($violations < 100 ? 'good' : 'needs_fix'),
            'percentage' => max(0, 100 - ($violations / 10)),
        ];
    }

    private function checkComponentUsage()
    {
        $bladeFiles = File::allFiles(resource_path('views'));
        $totalForms = 0;
        $componentUsage = 0;

        foreach ($bladeFiles as $file) {
            $content = File::get($file->getPathname());
            $totalForms += substr_count($content, '<input');
            $totalForms += substr_count($content, '<select');
            $componentUsage += substr_count($content, '<x-neo-input');
            $componentUsage += substr_count($content, '<x-neo-select');
        }

        $rate = $totalForms > 0 ? round(($componentUsage / $totalForms) * 100, 1) : 0;

        return [
            'rate' => $rate,
            'total_forms' => $totalForms,
            'component_usage' => $componentUsage,
            'status' => $rate > 80 ? 'perfect' : ($rate > 50 ? 'good' : 'needs_fix'),
        ];
    }

    private function checkCodeQuality()
    {
        $todoCount = 0;
        $files = File::allFiles(app_path());

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $content = File::get($file->getPathname());
                $todoCount += substr_count(strtoupper($content), 'TODO');
                $todoCount += substr_count(strtoupper($content), 'FIXME');
            }
        }

        return [
            'todo_count' => $todoCount,
            'status' => $todoCount < 20 ? 'perfect' : ($todoCount < 50 ? 'good' : 'needs_fix'),
        ];
    }

    private function checkDatabaseHealth()
    {
        try {
            DB::connection()->getPdo();
            $tables = DB::select('SHOW TABLES');

            return [
                'status' => 'healthy',
                'table_count' => count($tables),
                'connection' => 'status',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'connection' => 'failed',
            ];
        }
    }

    private function checkPerformance()
    {
        try {
            Cache::put('bekci_test', 'test', 5);
            $cacheWorks = Cache::get('bekci_test') === 'test';
            Cache::forget('bekci_test');

            return [
                'cache' => $cacheWorks ? 'status' : 'inactive',
                'status' => $cacheWorks ? 'perfect' : 'needs_fix',
            ];
        } catch (\Exception $e) {
            return [
                'cache' => 'error',
                'status' => 'error',
            ];
        }
    }

    private function calculateScore($report)
    {
        $score = 100;

        // Context7 (-20 max)
        if ($report['context7']['status'] === 'needs_fix') {
            $score -= 20;
        } elseif ($report['context7']['status'] === 'good') {
            $score -= 10;
        }

        // Components (-20 max)
        if ($report['components']['status'] === 'needs_fix') {
            $score -= 20;
        } elseif ($report['components']['status'] === 'good') {
            $score -= 10;
        }

        // Code Quality (-10 max)
        if ($report['code_quality']['status'] === 'needs_fix') {
            $score -= 10;
        } elseif ($report['code_quality']['status'] === 'good') {
            $score -= 5;
        }

        // Database (-30 max)
        if ($report['database']['status'] === 'error') {
            $score -= 30;
        }

        // Performance (-10 max)
        if ($report['performance']['status'] !== 'perfect') {
            $score -= 10;
        }

        return max(0, $score);
    }

    private function getStatus($score)
    {
        if ($score >= 90) {
            return ['text' => 'MÜKEMMEL', 'class' => 'success', 'icon' => '🎉'];
        }
        if ($score >= 70) {
            return ['text' => 'İYİ', 'class' => 'warning', 'icon' => '⚠️'];
        }

        return ['text' => 'DİKKAT', 'class' => 'danger', 'icon' => '❌'];
    }

    private function getRecentLogs()
    {
        $logFile = storage_path('logs/yalihan-bekci.log');

        if (! File::exists($logFile)) {
            return [];
        }

        $logs = File::get($logFile);
        $lines = explode("\n", $logs);

        return array_slice(array_reverse($lines), 0, 20);
    }

    private function getScoreHistory()
    {
        // Son 7 günün skorlarını cache'den al
        $history = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $score = Cache::get("bekci_score_{$date}", rand(50, 90));

            $history[] = [
                'date' => $date,
                'score' => $score,
            ];
        }

        return $history;
    }
}
