<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Context7 Compliance Checker
 * Yalıhan Bekçi - Otomatik Kontrol Sistemi
 */
class Context7Check extends Command
{
    protected $signature = 'context7:check
                            {--fix : Otomatik düzeltme yap}
                            {--report : Detaylı rapor oluştur}
                            {--path= : Belirli bir path kontrol et}';

    protected $description = 'Context7 compliance kontrolü - Yasak field name ve pattern tespiti';

    // Context7 Yasak Field Names - Context7 Authority'dan alınacak
    private $forbiddenFields = [];

    private $violations = [];
    private $fixedCount = 0;

    public function handle()
    {
        $this->info('🔍 Context7 Compliance Kontrolü Başlıyor...');
        $this->newLine();

        $path = $this->option('path') ?? 'app';

        // 1. PHP Dosyalarını Kontrol Et
        $this->checkPHPFiles($path);

        // 2. Blade Dosyalarını Kontrol Et
        $this->checkBladeFiles();

        // 3. Migration Dosyalarını Kontrol Et
        $this->checkMigrations();

        // 4. Sonuçları Göster
        $this->displayResults();

        // 5. Rapor Oluştur
        if ($this->option('report')) {
            $this->generateReport();
        }

        // 6. Otomatik Düzelt
        if ($this->option('fix')) {
            $this->autoFix();
        }

        return $this->violations ? Command::FAILURE : Command::SUCCESS;
    }

    private function checkPHPFiles($path)
    {
        $this->info("📁 PHP Dosyaları Kontrol Ediliyor: {$path}/");

        $files = File::allFiles(base_path($path));
        $phpFiles = array_filter($files, fn($file) => $file->getExtension() === 'php');

        $bar = $this->output->createProgressBar(count($phpFiles));
        $bar->start();

        foreach ($phpFiles as $file) {
            $this->checkFile($file->getPathname(), 'php');
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function checkBladeFiles()
    {
        $this->info('📁 Blade Dosyaları Kontrol Ediliyor...');

        $files = File::allFiles(resource_path('views'));
        $bladeFiles = array_filter($files, fn($file) => $file->getExtension() === 'php');

        foreach ($bladeFiles as $file) {
            $this->checkFile($file->getPathname(), 'blade');
        }

        $this->newLine();
    }

    private function checkMigrations()
    {
        $this->info('📁 Migration Dosyaları Kontrol Ediliyor...');

        $files = File::allFiles(database_path('migrations'));

        foreach ($files as $file) {
            $this->checkFile($file->getPathname(), 'migration');
        }

        $this->newLine();
    }

    private function checkFile($filePath, $type)
    {
        $content = File::get($filePath);
        $relativePath = str_replace(base_path() . '/', '', $filePath);

        foreach ($this->forbiddenFields as $forbidden => $correct) {
            // Regex pattern'ler
            $patterns = [
                "/['\\\"]" . $forbidden . "['\\\"]/",  // String kullanımı: 'status'
                "/->" . $forbidden . "\\b/",            // Property erişimi: ->status
                "/\\$" . $forbidden . "\\b/",           // Variable: $status
                "/->" . $forbidden . "\\(/",            // Method call: ->status()
            ];

            foreach ($patterns as $pattern) {
                if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                    foreach ($matches[0] as $match) {
                        $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;

                        // Eğer Context7 comment'i varsa skip et
                        $lineContent = explode("\n", $content)[$line - 1] ?? '';
                        if (strpos($lineContent, 'Context7') !== false ||
                            strpos($lineContent, 'YASAK') !== false ||
                            strpos($lineContent, '→') !== false) {
                            continue;
                        }

                        $this->violations[] = [
                            'file' => $relativePath,
                            'line' => $line,
                            'type' => $type,
                            'forbidden' => $forbidden,
                            'correct' => $correct,
                            'context' => trim($lineContent)
                        ];
                    }
                }
            }
        }
    }

    private function displayResults()
    {
        if (empty($this->violations)) {
            $this->info('✅ Context7 İhlali Bulunamadı!');
            $this->newLine();
            $this->info('🎉 Tebrikler! Proje %100 Context7 uyumlu.');
            return;
        }

        $this->error('❌ ' . count($this->violations) . ' Context7 İhlali Bulundu!');
        $this->newLine();

        // Grup by forbidden field
        $grouped = collect($this->violations)->groupBy('forbidden');

        foreach ($grouped as $field => $violations) {
            $this->warn("🚫 {$field} → ✅ {$violations[0]['correct']} ({$violations->count()} kullanım)");

            foreach ($violations->take(5) as $violation) {
                $this->line("   📄 {$violation['file']}:{$violation['line']}");
                $this->line("      {$violation['context']}");
            }

            if ($violations->count() > 5) {
                $this->line("   ... ve " . ($violations->count() - 5) . " kullanım daha");
            }

            $this->newLine();
        }

        $this->info('💡 İpucu: --fix parametresi ile otomatik düzeltme yapabilirsiniz:');
        $this->line('   php artisan context7:check --fix');
    }

    private function generateReport()
    {
        $reportPath = base_path('CONTEXT7_COMPLIANCE_REPORT_' . date('Y-m-d') . '.md');

        $report = "# 🔍 Context7 Compliance Report\n\n";
        $report .= "**Tarih:** " . date('Y-m-d H:i:s') . "\n";
        $report .= "**Toplam İhlal:** " . count($this->violations) . "\n\n";
        $report .= "---\n\n";

        $grouped = collect($this->violations)->groupBy('forbidden');

        foreach ($grouped as $field => $violations) {
            $report .= "## 🚫 {$field} → ✅ {$violations[0]['correct']}\n\n";
            $report .= "**Kullanım Sayısı:** {$violations->count()}\n\n";

            foreach ($violations as $violation) {
                $report .= "- `{$violation['file']}:{$violation['line']}`\n";
                $report .= "  ```\n  {$violation['context']}\n  ```\n\n";
            }

            $report .= "---\n\n";
        }

        File::put($reportPath, $report);
        $this->info("📄 Rapor oluşturuldu: {$reportPath}");
    }

    private function autoFix()
    {
        if (empty($this->violations)) {
            $this->info('✅ Düzeltilecek ihlal yok.');
            return;
        }

        $this->warn('🔧 Otomatik düzeltme başlıyor...');
        $this->newLine();

        if (!$this->confirm('Bu işlem dosyaları değiştirecek. Devam etmek istiyor musunuz?', false)) {
            $this->info('❌ İptal edildi.');
            return;
        }

        $grouped = collect($this->violations)->groupBy('file');

        foreach ($grouped as $file => $violations) {
            $filePath = base_path($file);
            $content = File::get($filePath);
            $originalContent = $content;

            foreach ($violations as $violation) {
                // Basit string replacement (dikkatli!)
                $forbidden = $violation['forbidden'];
                $correct = $violation['correct'];

                // Sadece exact match'leri değiştir
                $patterns = [
                    "'$forbidden'" => "'$correct'",
                    "\"$forbidden\"" => "\"$correct\"",
                    "->$forbidden" => "->$correct",
                    "\$$forbidden" => "\$$correct",
                ];

                foreach ($patterns as $find => $replace) {
                    $content = str_replace($find, $replace, $content);
                }
            }

            if ($content !== $originalContent) {
                File::put($filePath, $content);
                $this->info("✅ Düzeltildi: {$file}");
                $this->fixedCount++;
            }
        }

        $this->newLine();
        $this->info("🎉 {$this->fixedCount} dosya düzeltildi!");
        $this->warn('⚠️  Değişiklikleri kontrol edin ve test edin!');
    }
}
