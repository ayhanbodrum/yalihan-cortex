<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Yalıhan Bekçi - Otomatik Enforcement Sistemi
 * Yasaklı teknolojileri tespit eder ve engeller
 */
class YalihanBekciEnforce extends Command
{
    protected $signature = 'bekci:enforce
                            {--scan : Dosyaları tara}
                            {--fix : Otomatik düzelt}
                            {--watch : Sürekli izle}';

    protected $description = 'Yalıhan Bekçi - Yasaklı teknolojileri tespit ve engelle';

    private $violations = [];
    private $forbiddenPatterns = [
        'select2' => [
            'patterns' => [
                '/select2\.min\.js/',
                '/cdn\.jsdelivr\.net.*select2/',
                '/\$\([\'"].*[\'"]\.select2\(/',
                '/import.*select2/',
            ],
            'replacement' => 'Context7 Live Search',
            'severity' => 'critical',
        ],
        'jquery' => [
            'patterns' => [
                '/jquery\.min\.js/',
                '/cdn\.jsdelivr\.net.*jquery/',
                '/\$\.ajax\(/',
                '/import.*jquery/',
            ],
            'replacement' => 'Vanilla JS fetch()',
            'severity' => 'high',
        ],
        'react-select' => [
            'patterns' => [
                '/react-select/',
                '/import.*ReactSelect/',
                '/from [\'"]react-select[\'"]/',
            ],
            'replacement' => 'Context7 Live Search',
            'severity' => 'critical',
        ],
        'choices-js' => [
            'patterns' => [
                '/choices\.js/',
                '/new Choices\(/',
                '/import.*Choices/',
            ],
            'replacement' => 'Context7 Live Search',
            'severity' => 'critical',
        ],
        'bootstrap-classes' => [
            'patterns' => [
                '/class=[\'"].*btn-primary/',
                '/class=[\'"].*btn-secondary/',
                '/class=[\'"].*form-control/',
            ],
            'replacement' => 'Neo Design System (neo-btn, neo-input)',
            'severity' => 'medium',
        ],
    ];

    public function handle()
    {
        $this->info('🛡️ Yalıhan Bekçi Enforcement Sistemi Başlatılıyor...');
        $this->newLine();

        if ($this->option('watch')) {
            $this->watchMode();
        } else {
            $this->scanFiles();
            $this->displayViolations();

            if ($this->option('fix')) {
                $this->autoFix();
            }
        }
    }

    private function scanFiles()
    {
        $this->line('🔍 Dosyalar taranıyor...');
        $this->newLine();

        $directories = [
            'resources/views',
            'resources/js',
            'public/js',
            'public/css',
        ];

        foreach ($directories as $dir) {
            if (!File::isDirectory(base_path($dir))) {
                continue;
            }

            $files = File::allFiles(base_path($dir));
            foreach ($files as $file) {
                $this->scanFile($file);
            }
        }

        $this->info("✅ {$this->countFiles()} dosya tarandı");
    }

    private function scanFile($file)
    {
        $content = file_get_contents($file->getPathname());
        $relativePath = str_replace(base_path() . '/', '', $file->getPathname());

        foreach ($this->forbiddenPatterns as $tech => $config) {
            foreach ($config['patterns'] as $pattern) {
                if (preg_match($pattern, $content, $matches)) {
                    $this->violations[] = [
                        'file' => $relativePath,
                        'tech' => $tech,
                        'pattern' => $pattern,
                        'match' => $matches[0] ?? '',
                        'replacement' => $config['replacement'],
                        'severity' => $config['severity'],
                        'line' => $this->findLineNumber($content, $matches[0] ?? ''),
                    ];
                }
            }
        }
    }

    private function findLineNumber($content, $search)
    {
        $lines = explode("\n", $content);
        foreach ($lines as $num => $line) {
            if (strpos($line, $search) !== false) {
                return $num + 1;
            }
        }
        return 0;
    }

    private function displayViolations()
    {
        $this->newLine();

        if (empty($this->violations)) {
            $this->info('✅ Hiç ihlal bulunamadı! Proje temiz!');
            return;
        }

        $this->warn("❌ {$this->countViolations()} ihlal bulundu!");
        $this->newLine();

        $grouped = $this->groupBySeverity();

        // Critical violations
        if (!empty($grouped['critical'])) {
            $this->error('🔴 KRİTİK İHLALLER (' . count($grouped['critical']) . ')');
            foreach ($grouped['critical'] as $violation) {
                $this->displayViolation($violation);
            }
            $this->newLine();
        }

        // High violations
        if (!empty($grouped['high'])) {
            $this->warn('🟠 YÜKSEK ÖNCELİK (' . count($grouped['high']) . ')');
            foreach ($grouped['high'] as $violation) {
                $this->displayViolation($violation);
            }
            $this->newLine();
        }

        // Medium violations
        if (!empty($grouped['medium'])) {
            $this->line('🟡 ORTA ÖNCELİK (' . count($grouped['medium']) . ')');
            foreach (array_slice($grouped['medium'], 0, 5) as $violation) {
                $this->displayViolation($violation);
            }
            if (count($grouped['medium']) > 5) {
                $this->line('   ... ve ' . (count($grouped['medium']) - 5) . ' tane daha');
            }
        }
    }

    private function displayViolation($violation)
    {
        $this->line("   📄 {$violation['file']}:{$violation['line']}");
        $this->line("      ❌ {$violation['tech']}: {$violation['match']}");
        $this->line("      ✅ Yerine kullan: {$violation['replacement']}");
        $this->newLine();
    }

    private function groupBySeverity()
    {
        $grouped = ['critical' => [], 'high' => [], 'medium' => []];
        foreach ($this->violations as $violation) {
            $grouped[$violation['severity']][] = $violation;
        }
        return $grouped;
    }

    private function autoFix()
    {
        $this->warn('🔧 Otomatik düzeltme başlatılıyor...');
        $this->newLine();

        // Sadece safe pattern'leri düzelt
        $safePatterns = ['bootstrap-classes'];
        $fixed = 0;

        foreach ($this->violations as $violation) {
            if (in_array($violation['tech'], $safePatterns)) {
                if ($this->fixViolation($violation)) {
                    $fixed++;
                }
            }
        }

        $this->info("✅ {$fixed} ihlal otomatik düzeltildi!");
        $this->warn('⚠️  ' . (count($this->violations) - $fixed) . ' ihlal manuel düzeltme gerektirir.');
    }

    private function fixViolation($violation)
    {
        // Bootstrap class replacements
        $replacements = [
            'btn-primary' => 'neo-btn neo-btn-primary',
            'btn-secondary' => 'neo-btn neo-btn-secondary',
            'form-control' => 'neo-input',
        ];

        $file = base_path($violation['file']);
        $content = file_get_contents($file);

        foreach ($replacements as $old => $new) {
            if (strpos($violation['match'], $old) !== false) {
                $content = str_replace($old, $new, $content);
                file_put_contents($file, $content);
                return true;
            }
        }

        return false;
    }

    private function watchMode()
    {
        $this->info('👁️  Sürekli İzleme Modu Aktif!');
        $this->warn('⚠️  Yeni dosya değişikliklerini izliyor...');
        $this->warn('⚠️  Ctrl+C ile durdurun');
        $this->newLine();

        $lastCheck = time();

        while (true) {
            if (time() - $lastCheck >= 30) { // Her 30 saniyede kontrol
                $this->violations = [];
                $this->scanFiles();
                
                if (!empty($this->violations)) {
                    $this->newLine();
                    $this->error('⚠️  YENİ İHLAL TESPİT EDİLDİ! (' . date('H:i:s') . ')');
                    $this->displayViolations();
                }
                
                $lastCheck = time();
            }
            sleep(5);
        }
    }

    private function countFiles()
    {
        $count = 0;
        $directories = ['resources/views', 'resources/js', 'public/js', 'public/css'];
        
        foreach ($directories as $dir) {
            if (File::isDirectory(base_path($dir))) {
                $count += count(File::allFiles(base_path($dir)));
            }
        }
        
        return $count;
    }

    private function countViolations()
    {
        return count($this->violations);
    }
}
