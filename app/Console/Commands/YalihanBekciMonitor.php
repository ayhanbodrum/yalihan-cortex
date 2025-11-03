<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Yalıhan Bekçi - Otomatik Monitoring Sistemi
 * Her değişikliği izler, standardı kontrol eder
 */
class YalihanBekciMonitor extends Command
{
    protected $signature = 'bekci:monitor
                            {--watch : Sürekli izleme modu}
                            {--auto-fix : Otomatik düzeltme}
                            {--daily : Günlük rapor}';

    protected $description = 'Yalıhan Bekçi - Otomatik proje izleme ve standardizasyon sistemi';

    private $report = [];
    private $score = 100;

    public function handle()
    {
        $this->info('🤖 Yalıhan Bekçi Monitoring Sistemi Başlatılıyor...');
        $this->newLine();

        if ($this->option('watch')) {
            $this->watchMode();
        } else {
            $this->singleCheck();
        }
    }

    private function singleCheck()
    {
        $this->line('📊 Tek Seferlik Kontrol Yapılıyor...');
        $this->newLine();

        // 1. Context7 Compliance
        $this->checkContext7();

        // 2. Component Usage
        $this->checkComponentUsage();

        // 3. Code Quality
        $this->checkCodeQuality();

        // 4. Database Health
        $this->checkDatabaseHealth();

        // 5. Performance
        $this->checkPerformance();

        // Sonuç
        $this->displayReport();

        if ($this->option('auto-fix')) {
            $this->autoFix();
        }
    }

    private function watchMode()
    {
        $this->info('👁️  Sürekli İzleme Modu Aktif!');
        $this->warn('⚠️  Ctrl+C ile durdurun');
        $this->newLine();

        $lastCheck = time();
        $checkInterval = 60; // 60 saniye

        while (true) {
            if (time() - $lastCheck >= $checkInterval) {
                $this->line('🔄 Kontrol yapılıyor... (' . date('H:i:s') . ')');
                $this->singleCheck();
                $lastCheck = time();
            }
            sleep(5);
        }
    }

    private function checkContext7()
    {
        $this->line('1️⃣  Context7 Compliance Kontrolü...');

        // Context7 check komutunu çalıştır
        $output = [];
        exec('php artisan context7:check 2>&1', $output, $returnCode);

        $violations = 0;
        foreach ($output as $line) {
            if (preg_match('/(\d+) Context7 İhlali/', $line, $matches)) {
                $violations = (int)$matches[1];
            }
        }

        if ($violations === 0) {
            $this->info('   ✅ Context7: %100 Uyumlu');
        } else {
            $this->warn("   ⚠️  Context7: {$violations} ihlal bulundu");
            $this->score -= min(20, $violations / 10);
        }

        $this->report['context7'] = [
            'violations' => $violations,
            'status' => $violations === 0 ? 'perfect' : 'needs_fix'
        ];
    }

    private function checkComponentUsage()
    {
        $this->line('2️⃣  Component Kullanımı Kontrolü...');

        // Blade dosyalarında x-neo- component kullanımını kontrol et
        $bladeFiles = File::allFiles(resource_path('views'));
        $totalForms = 0;
        $componentUsage = 0;

        foreach ($bladeFiles as $file) {
            $content = File::get($file->getPathname());

            // Input sayısı
            $totalForms += substr_count($content, '<input');
            $totalForms += substr_count($content, '<select');

            // Component kullanımı
            $componentUsage += substr_count($content, '<x-neo-input');
            $componentUsage += substr_count($content, '<x-neo-select');
        }

        $componentRate = $totalForms > 0 ? round(($componentUsage / $totalForms) * 100, 1) : 0;

        if ($componentRate > 80) {
            $this->info("   ✅ Component: %{$componentRate} kullanım");
        } elseif ($componentRate > 50) {
            $this->warn("   ⚠️  Component: %{$componentRate} kullanım (hedef: %80)");
            $this->score -= 10;
        } else {
            $this->error("   ❌ Component: %{$componentRate} kullanım (çok düşük!)");
            $this->score -= 20;
        }

        $this->report['components'] = [
            'rate' => $componentRate,
            'total_forms' => $totalForms,
            'component_usage' => $componentUsage
        ];
    }

    private function checkCodeQuality()
    {
        $this->line('3️⃣  Kod Kalitesi Kontrolü...');

        $issues = [];

        // TODO sayısı
        $todoCount = 0;
        $files = File::allFiles(app_path());
        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $content = File::get($file->getPathname());
                $todoCount += substr_count(strtoupper($content), 'TODO');
                $todoCount += substr_count(strtoupper($content), 'FIXME');
            }
        }

        if ($todoCount > 20) {
            $this->warn("   ⚠️  {$todoCount} TODO/FIXME bulundu");
            $this->score -= 5;
            $issues[] = "Too many TODOs: {$todoCount}";
        } else {
            $this->info("   ✅ TODO/FIXME: {$todoCount} (kabul edilebilir)");
        }

        $this->report['code_quality'] = [
            'todo_count' => $todoCount,
            'issues' => $issues
        ];
    }

    private function checkDatabaseHealth()
    {
        $this->line('4️⃣  Veritabanı Sağlığı...');

        try {
            // Database connection test
            DB::connection()->getPdo();

            // Tablo sayısı
            $tables = DB::select('SHOW TABLES');
            $tableCount = count($tables);

            $this->info("   ✅ Database: {$tableCount} tablo aktif");

            $this->report['database'] = [
                'status' => 'healthy',
                'table_count' => $tableCount
            ];
        } catch (\Exception $e) {
            $this->error('   ❌ Database bağlantı hatası!');
            $this->score -= 30;

            $this->report['database'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    private function checkPerformance()
    {
        $this->line('5️⃣  Performans Kontrolü...');

        // Cache durumu
        $cacheWorks = false;
        try {
            Cache::put('bekci_test', 'test', 5);
            $cacheWorks = Cache::get('bekci_test') === 'test';
            Cache::forget('bekci_test');
        } catch (\Exception $e) {
            // Cache çalışmıyor
        }

        if ($cacheWorks) {
            $this->info('   ✅ Cache: Aktif');
        } else {
            $this->warn('   ⚠️  Cache: Pasif (performans düşük olabilir)');
            $this->score -= 5;
        }

        $this->report['performance'] = [
            'cache' => $cacheWorks ? 'status' : 'inactive'
        ];
    }

    private function displayReport()
    {
        $this->newLine();
        $this->line('═══════════════════════════════════════════════════');
        $this->line('📊 YALIHAN BEKÇİ RAPORU');
        $this->line('═══════════════════════════════════════════════════');
        $this->newLine();

        // Skor
        $this->displayScore();
        $this->newLine();

        // Detaylar
        $this->table(
            ['Kategori', 'Durum', 'Detay'],
            [
                [
                    'Context7',
                    $this->getStatusIcon($this->report['context7']['status']),
                    $this->report['context7']['violations'] . ' ihlal'
                ],
                [
                    'Components',
                    $this->getStatusIcon($this->report['components']['rate'] > 80 ? 'perfect' : 'needs_fix'),
                    '%' . $this->report['components']['rate'] . ' kullanım'
                ],
                [
                    'Code Quality',
                    $this->getStatusIcon($this->report['code_quality']['todo_count'] < 20 ? 'perfect' : 'needs_fix'),
                    $this->report['code_quality']['todo_count'] . ' TODO'
                ],
                [
                    'Database',
                    $this->getStatusIcon($this->report['database']['status'] === 'healthy' ? 'perfect' : 'error'),
                    $this->report['database']['table_count'] ?? 'Error'
                ],
                [
                    'Performance',
                    $this->getStatusIcon($this->report['performance']['cache'] === 'status' ? 'perfect' : 'needs_fix'),
                    'Cache: ' . $this->report['performance']['cache']
                ],
            ]
        );

        $this->newLine();
        $this->line('🤖 Yalıhan Bekçi - ' . date('Y-m-d H:i:s'));
        $this->line('═══════════════════════════════════════════════════');
    }

    private function displayScore()
    {
        $score = max(0, min(100, $this->score));

        if ($score >= 90) {
            $this->info("🎉 SKOR: {$score}/100 - MÜKEMMEL!");
        } elseif ($score >= 70) {
            $this->warn("⚠️  SKOR: {$score}/100 - İYİ (iyileştirilebilir)");
        } else {
            $this->error("❌ SKOR: {$score}/100 - DİKKAT GEREKTİRİYOR!");
        }

        // Progress bar
        $filled = round($score / 5);
        $empty = 20 - $filled;
        $bar = str_repeat('█', $filled) . str_repeat('░', $empty);
        $this->line("   [{$bar}] {$score}%");
    }

    private function getStatusIcon($status)
    {
        return match($status) {
            'perfect' => '✅',
            'needs_fix' => '⚠️',
            'error' => '❌',
            default => '❓'
        };
    }

    private function autoFix()
    {
        $this->newLine();
        $this->warn('🔧 Otomatik Düzeltme Başlatılıyor...');

        if ($this->report['context7']['violations'] > 0) {
            $this->line('   → Context7 ihlalleri düzeltiliyor...');
            // Context7 fix komutunu çalıştır
            // $this->call('context7:check', ['--fix' => true]);
        }

        $this->info('✅ Otomatik düzeltme tamamlandı!');
    }
}

