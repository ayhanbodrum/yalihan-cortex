<?php

/**
 * Context7 Sistem İzleyici
 *
 * Sistem performansını, Context7 uyumluluğunu ve
 * genel sağlık durumunu izler ve raporlar.
 */
class Context7SystemMonitor
{
    private $metrics = [];

    private $alerts = [];

    public function __construct()
    {
        echo "🔍 Context7 Sistem İzleyici başlatılıyor...\n";
    }

    /**
     * Sistem sağlık durumunu kontrol et
     */
    public function checkSystemHealth()
    {
        $health = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'performance' => $this->checkPerformance(),
            'context7' => $this->checkContext7Compliance(),
        ];

        $overall = array_sum($health) / count($health);

        $this->metrics['health'] = [
            'overall' => round($overall, 2),
            'details' => $health,
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        return $overall;
    }

    /**
     * Veritabanı bağlantısını kontrol et
     */
    private function checkDatabase()
    {
        try {
            // Basit veritabanı bağlantı testi
            $pdo = new PDO('mysql:host=localhost;dbname=yalihanemlak_ultra', 'root', '');

            return 100;
        } catch (\Exception $e) {
            $this->alerts[] = 'Veritabanı bağlantı hatası: '.$e->getMessage();

            return 0;
        }
    }

    /**
     * Cache durumunu kontrol et
     */
    private function checkCache()
    {
        try {
            // Basit cache testi
            $cacheFile = 'storage/framework/cache/test.cache';
            file_put_contents($cacheFile, 'ok');
            $result = file_get_contents($cacheFile);
            unlink($cacheFile);

            return $result === 'ok' ? 100 : 50;
        } catch (\Exception $e) {
            $this->alerts[] = 'Cache hatası: '.$e->getMessage();

            return 0;
        }
    }

    /**
     * Storage durumunu kontrol et
     */
    private function checkStorage()
    {
        $storagePath = getcwd().'/storage';
        $freeSpace = disk_free_space($storagePath);
        $totalSpace = disk_total_space($storagePath);
        $usagePercent = (($totalSpace - $freeSpace) / $totalSpace) * 100;

        if ($usagePercent > 90) {
            $this->alerts[] = "Storage kullanımı %90'ın üzerinde!";

            return 50;
        } elseif ($usagePercent > 80) {
            $this->alerts[] = "Storage kullanımı %80'in üzerinde";

            return 75;
        }

        return 100;
    }

    /**
     * Performans metriklerini kontrol et
     */
    private function checkPerformance()
    {
        $startTime = microtime(true);

        // Basit performans testi
        try {
            // Dosya okuma testi
            $testFile = 'composer.json';
            if (file_exists($testFile)) {
                $content = file_get_contents($testFile);
                $endTime = microtime(true);
                $responseTime = ($endTime - $startTime) * 1000; // ms

                if ($responseTime > 1000) {
                    $this->alerts[] = "Yavaş dosya okuma: {$responseTime}ms";

                    return 50;
                } elseif ($responseTime > 500) {
                    $this->alerts[] = "Orta hızda dosya okuma: {$responseTime}ms";

                    return 75;
                }

                return 100;
            }

            return 75; // Dosya yok ama sistem çalışıyor
        } catch (\Exception $e) {
            $this->alerts[] = 'Performans testi hatası: '.$e->getMessage();

            return 0;
        }
    }

    /**
     * Context7 uyumluluğunu kontrol et
     */
    private function checkContext7Compliance()
    {
        $violations = 0;
        $total = 0;

        // Controller kontrolü
        $controllers = glob('app/Http/Controllers/**/*.php');
        foreach ($controllers as $controller) {
            $total++;
            $content = file_get_contents($controller);
            if (strpos($content, '$status') !== false && strpos($content, '$status =') === false) {
                $violations++;
            }
        }

        // View kontrolü
        $views = glob('resources/views/**/*.blade.php');
        foreach ($views as $view) {
            $total++;
            $content = file_get_contents($view);
            if (strpos($content, 'btn-') !== false || strpos($content, 'card-') !== false) {
                $violations++;
            }
        }

        $compliance = max(0, 100 - (($violations / max($total, 1)) * 100));

        if ($compliance < 95) {
            $this->alerts[] = "Context7 uyumluluk %95'in altında: %{$compliance}";
        }

        return $compliance;
    }

    /**
     * Sistem metriklerini topla
     */
    public function collectMetrics()
    {
        $this->metrics['system'] = [
            'php_version' => PHP_VERSION,
            'laravel_version' => '10.x',
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'uptime' => $this->getUptime(),
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        $this->metrics['database'] = [
            'connection' => 'mysql',
            'driver' => 'mysql',
            'host' => 'localhost',
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        $this->metrics['cache'] = [
            'driver' => 'file',
            'stores' => ['file', 'redis'],
            'timestamp' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Uptime hesapla
     */
    private function getUptime()
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();

            return [
                'load_1min' => $load[0],
                'load_5min' => $load[1],
                'load_15min' => $load[2],
            ];
        }

        return null;
    }

    /**
     * Rapor oluştur
     */
    public function generateReport()
    {
        $this->collectMetrics();
        $health = $this->checkSystemHealth();

        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'health_score' => $health,
            'metrics' => $this->metrics,
            'alerts' => $this->alerts,
            'recommendations' => $this->getRecommendations($health),
        ];

        // JSON raporu kaydet
        $reportPath = 'storage/logs/context7-system-report.json';
        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT));

        // Console raporu
        $this->displayReport($report);

        return $report;
    }

    /**
     * Öneriler oluştur
     */
    private function getRecommendations($health)
    {
        $recommendations = [];

        if ($health < 80) {
            $recommendations[] = 'Sistem sağlığı düşük. Optimizasyon gerekli.';
        }

        if (count($this->alerts) > 0) {
            $recommendations[] = 'Acil düzeltme gereken '.count($this->alerts).' sorun var.';
        }

        if ($this->metrics['health']['details']['context7'] < 95) {
            $recommendations[] = 'Context7 uyumluluğu artırılmalı.';
        }

        return $recommendations;
    }

    /**
     * Raporu ekranda göster
     */
    private function displayReport($report)
    {
        echo "\n📊 CONTEXT7 SİSTEM RAPORU\n";
        echo "==========================\n";
        echo "🕐 Tarih: {$report['timestamp']}\n";
        echo "💚 Sağlık Skoru: %{$report['health_score']}\n";
        echo "📈 Context7 Uyumluluk: %{$report['metrics']['health']['details']['context7']}\n";
        echo "🗄️ Veritabanı: %{$report['metrics']['health']['details']['database']}\n";
        echo "💾 Cache: %{$report['metrics']['health']['details']['cache']}\n";
        echo "💿 Storage: %{$report['metrics']['health']['details']['storage']}\n";
        echo "⚡ Performans: %{$report['metrics']['health']['details']['performance']}\n";

        if (! empty($report['alerts'])) {
            echo "\n⚠️ UYARILAR:\n";
            foreach ($report['alerts'] as $alert) {
                echo "  - {$alert}\n";
            }
        }

        if (! empty($report['recommendations'])) {
            echo "\n💡 ÖNERİLER:\n";
            foreach ($report['recommendations'] as $rec) {
                echo "  - {$rec}\n";
            }
        }

        echo "\n📁 Detaylı rapor: storage/logs/context7-system-report.json\n";
    }

    /**
     * Sürekli izleme başlat
     */
    public function startContinuousMonitoring($interval = 300) // 5 dakika
    {
        echo "🔄 Sürekli izleme başlatılıyor (her {$interval} saniyede bir)...\n";

        while (true) {
            $this->generateReport();
            sleep($interval);
        }
    }
}

// Script çalıştır
if (php_sapi_name() === 'cli') {
    $monitor = new Context7SystemMonitor;

    if (isset($argv[1]) && $argv[1] === '--continuous') {
        $monitor->startContinuousMonitoring();
    } else {
        $monitor->generateReport();
    }
}
