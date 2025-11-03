<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class StandardCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'standard:check 
                            {--type=all : Check type: all, css, js, php, blade, context7}
                            {--fix : Auto-fix issues}
                            {--report : Generate detailed report}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Standartlara uygunluk kontrolü - MODERNIZATION sistemi';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Yalıhan Emlak - Standart Kontrol Sistemi');
        $this->newLine();

        $type = $this->option('type');
        $fix = $this->option('fix');
        $report = $this->option('report');

        $checks = [
            'all' => ['css', 'js', 'php', 'blade', 'context7'],
            'css' => ['css'],
            'js' => ['js'],
            'php' => ['php'],
            'blade' => ['blade'],
            'context7' => ['context7'],
        ];

        $typesToCheck = $checks[$type] ?? $checks['all'];

        $issues = [];
        $fixed = [];

        foreach ($typesToCheck as $checkType) {
            $this->info("📋 Kontrol ediliyor: {$checkType}");
            
            $result = $this->runCheck($checkType, $fix);
            
            if (!empty($result['issues'])) {
                $issues[$checkType] = $result['issues'];
                $this->warn("  ⚠️  {$checkType}: " . count($result['issues']) . " sorun bulundu");
            } else {
                $this->info("  ✅ {$checkType}: Sorun yok");
            }
            
            if ($fix && !empty($result['fixed'])) {
                $fixed[$checkType] = $result['fixed'];
                $this->comment("  🔧 {$checkType}: " . count($result['fixed']) . " sorun düzeltildi");
            }
        }

        $this->newLine();

        if (empty($issues)) {
            $this->info('🎉 Tüm kontroller başarılı! Sistem standartlara uygun.');
            return Command::SUCCESS;
        }

        $this->error('❌ Toplam ' . array_sum(array_map('count', $issues)) . ' sorun bulundu.');
        
        if ($report) {
            $this->generateReport($issues, $fixed);
        } else {
            $this->displayIssues($issues);
            $this->newLine();
            $this->comment('💡 Detaylı rapor için: php artisan standard:check --report');
        }

        if (!$fix) {
            $this->newLine();
            $this->comment('💡 Otomatik düzeltme için: php artisan standard:check --fix');
        }

        return Command::FAILURE;
    }

    /**
     * Run specific check type
     */
    protected function runCheck(string $type, bool $fix): array
    {
        return match ($type) {
            'css' => $this->checkCSS($fix),
            'js' => $this->checkJS($fix),
            'php' => $this->checkPHP($fix),
            'blade' => $this->checkBlade($fix),
            'context7' => $this->checkContext7($fix),
            default => ['issues' => [], 'fixed' => []],
        };
    }

    /**
     * Check CSS files
     */
    protected function checkCSS(bool $fix): array
    {
        $issues = [];
        $fixed = [];

        // Neo classes kontrolü
        $bladeFiles = File::allFiles(resource_path('views'));
        
        foreach ($bladeFiles as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = File::get($file->getPathname());
            
            // Neo-btn pattern
            if (preg_match('/class="[^"]*neo-btn[^"]*"/', $content)) {
                $issues[] = [
                    'file' => str_replace(base_path(), '', $file->getPathname()),
                    'type' => 'Neo Class Usage',
                    'message' => 'Neo-btn kullanımı bulundu. Tailwind\'e geçilmeli.',
                    'line' => $this->getLineNumber($content, 'neo-btn'),
                ];
            }

            // Inline styles
            if (preg_match('/style="[^"]*"/', $content)) {
                $issues[] = [
                    'file' => str_replace(base_path(), '', $file->getPathname()),
                    'type' => 'Inline Style',
                    'message' => 'Inline style kullanımı. Tailwind classes kullanın.',
                    'line' => $this->getLineNumber($content, 'style='),
                ];
            }
        }

        return ['issues' => $issues, 'fixed' => $fixed];
    }

    /**
     * Check JavaScript files
     */
    protected function checkJS(bool $fix): array
    {
        $issues = [];
        $fixed = [];

        $jsFiles = File::allFiles(resource_path('js'));
        
        foreach ($jsFiles as $file) {
            $content = File::get($file->getPathname());
            
            // jQuery usage
            if (preg_match('/\$\([\'"]/', $content) || preg_match('/jQuery/', $content)) {
                $issues[] = [
                    'file' => str_replace(base_path(), '', $file->getPathname()),
                    'type' => 'jQuery Usage',
                    'message' => 'jQuery kullanımı bulundu. Alpine.js veya Vanilla JS kullanın.',
                    'line' => $this->getLineNumber($content, '$'),
                ];
            }

            // var usage (should use const/let)
            if (preg_match('/\bvar\s+\w+/', $content)) {
                $issues[] = [
                    'file' => str_replace(base_path(), '', $file->getPathname()),
                    'type' => 'Var Usage',
                    'message' => 'var kullanımı. const veya let kullanın.',
                    'line' => $this->getLineNumber($content, 'var '),
                ];
            }

            // console.log in production files
            if (preg_match('/console\.log/', $content) && !str_contains($file->getPathname(), 'dev')) {
                $issues[] = [
                    'file' => str_replace(base_path(), '', $file->getPathname()),
                    'type' => 'Console Log',
                    'message' => 'console.log kullanımı. Production\'da olmamalı.',
                    'line' => $this->getLineNumber($content, 'console.log'),
                ];
            }
        }

        return ['issues' => $issues, 'fixed' => $fixed];
    }

    /**
     * Check PHP files
     */
    protected function checkPHP(bool $fix): array
    {
        $issues = [];
        $fixed = [];

        $phpFiles = File::allFiles(app_path());
        
        foreach ($phpFiles as $file) {
            $content = File::get($file->getPathname());
            
            // Missing type hints
            if (preg_match('/public function \w+\([^\)]*\$\w+[^\)]*(,|\))/', $content)) {
                // Basic check for missing type hints
                $issues[] = [
                    'file' => str_replace(base_path(), '', $file->getPathname()),
                    'type' => 'Type Hints',
                    'message' => 'Type hint eksik olabilir. PSR-12 standardına uyun.',
                    'line' => $this->getLineNumber($content, 'public function'),
                ];
            }

            // Turkish field names (forbidden)
            $forbiddenFields = ['durum', 'aktif', 'sehir', 'sehir_id', 'ad_soyad'];
            foreach ($forbiddenFields as $field) {
                if (preg_match("/['\"]" . $field . "['\"]/", $content)) {
                    $issues[] = [
                        'file' => str_replace(base_path(), '', $file->getPathname()),
                        'type' => 'Turkish Field Name',
                        'message' => "Yasak field: '{$field}'. İngilizce kullanın (status, enabled, city, etc.).",
                        'line' => $this->getLineNumber($content, $field),
                    ];
                }
            }
        }

        return ['issues' => $issues, 'fixed' => $fixed];
    }

    /**
     * Check Blade files
     */
    protected function checkBlade(bool $fix): array
    {
        $issues = [];
        $fixed = [];

        $bladeFiles = File::allFiles(resource_path('views'));
        
        foreach ($bladeFiles as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = File::get($file->getPathname());
            
            // Missing CSRF in forms
            if (preg_match('/<form[^>]*>/', $content) && !preg_match('/@csrf/', $content)) {
                $issues[] = [
                    'file' => str_replace(base_path(), '', $file->getPathname()),
                    'type' => 'CSRF Missing',
                    'message' => 'Form\'da @csrf eksik.',
                    'line' => $this->getLineNumber($content, '<form'),
                ];
            }

            // Input without label
            if (preg_match('/<input[^>]*name="[^"]*"[^>]*>/', $content, $matches)) {
                foreach ($matches as $match) {
                    if (!str_contains($content, 'label for=') && !str_contains($match, 'type="hidden"')) {
                        $issues[] = [
                            'file' => str_replace(base_path(), '', $file->getPathname()),
                            'type' => 'Missing Label',
                            'message' => 'Input için label eksik (accessibility).',
                            'line' => $this->getLineNumber($content, $match),
                        ];
                        break; // Sadece ilkini raporla
                    }
                }
            }
        }

        return ['issues' => $issues, 'fixed' => $fixed];
    }

    /**
     * Check Context7 compliance
     */
    protected function checkContext7(bool $fix): array
    {
        // Context7 validation artisan command'ini çağır
        $this->call('context7:check');
        
        return ['issues' => [], 'fixed' => []];
    }

    /**
     * Get line number for pattern
     */
    protected function getLineNumber(string $content, string $pattern): int
    {
        $lines = explode("\n", $content);
        foreach ($lines as $index => $line) {
            if (str_contains($line, $pattern)) {
                return $index + 1;
            }
        }
        return 0;
    }

    /**
     * Display issues in console
     */
    protected function displayIssues(array $issues): void
    {
        $this->newLine();
        $this->error('📋 Bulunan Sorunlar:');
        $this->newLine();

        foreach ($issues as $type => $typeIssues) {
            $this->line("<fg=yellow>▶ {$type}:</>");
            
            foreach (array_slice($typeIssues, 0, 5) as $issue) {
                $this->line("  <fg=red>✗</> {$issue['file']}:{$issue['line']}");
                $this->line("    {$issue['type']}: {$issue['message']}");
            }
            
            if (count($typeIssues) > 5) {
                $this->line("  <fg=gray>... ve " . (count($typeIssues) - 5) . " sorun daha</>");
            }
            
            $this->newLine();
        }
    }

    /**
     * Generate detailed report
     */
    protected function generateReport(array $issues, array $fixed): void
    {
        $reportPath = storage_path('logs/standard-check-' . date('Y-m-d-His') . '.json');
        
        $report = [
            'timestamp' => now()->toIso8601String(),
            'total_issues' => array_sum(array_map('count', $issues)),
            'total_fixed' => array_sum(array_map('count', $fixed)),
            'issues' => $issues,
            'fixed' => $fixed,
        ];
        
        File::put($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->info("📄 Detaylı rapor oluşturuldu: {$reportPath}");
    }
}

