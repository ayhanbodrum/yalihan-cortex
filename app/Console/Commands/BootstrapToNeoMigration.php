<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Bootstrap to Neo Design System Migration
 * Otomatik class dönüşümü
 */
class BootstrapToNeoMigration extends Command
{
    protected $signature = 'bootstrap:migrate
                            {--scan : Sadece tara, değiştirme}
                            {--dry-run : Test modu}
                            {--fix : Otomatik düzelt}';

    protected $description = 'Bootstrap class\'larını Neo Design System\'e migre et';

    private $conversions = [
        // Button classes
        'btn-primary' => 'neo-btn neo-btn-primary',
        'btn-secondary' => 'neo-btn neo-btn-secondary',
        'btn-success' => 'neo-btn neo-btn-success',
        'btn-danger' => 'neo-btn neo-btn-danger',
        'btn-warning' => 'neo-btn neo-btn-warning',
        'btn-info' => 'neo-btn neo-btn-info',

        // Form classes
        'form-control' => 'neo-input',
        'form-select' => 'neo-select',

        // Card classes (dikkatli!)
        // 'card-header' => 'neo-card-header',
        // 'card-body' => 'neo-card-body',
    ];

    private $changes = [];
    private $dryRun = false;

    public function handle()
    {
        $this->info('🔧 Bootstrap → Neo Migration Başlıyor...');
        $this->newLine();

        $this->dryRun = $this->option('dry-run');

        if ($this->option('scan')) {
            $this->scanFiles();
            $this->displayResults();
        } elseif ($this->option('fix')) {
            $this->migrateFiles();
            $this->displayResults();
        } else {
            $this->info('Kullanım:');
            $this->line('  --scan     : Dosyaları tara ve rapor ver');
            $this->line('  --dry-run  : Test modu (değişiklik yapma)');
            $this->line('  --fix      : Otomatik düzelt');
            $this->newLine();
            $this->line('Örnek:');
            $this->line('  php artisan bootstrap:migrate --scan');
            $this->line('  php artisan bootstrap:migrate --fix');
        }
    }

    private function scanFiles()
    {
        $this->line('🔍 Dosyalar taranıyor...');

        $files = File::allFiles(resource_path('views/admin'));
        $bar = $this->output->createProgressBar(count($files));

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $this->scanFile($file);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function scanFile($file)
    {
        $content = file_get_contents($file->getPathname());
        $relativePath = str_replace(resource_path() . '/', '', $file->getPathname());

        foreach ($this->conversions as $old => $new) {
            // Check if file contains the old class
            if (preg_match_all('/class=["\'][^"\']*' . preg_quote($old, '/') . '[^"\']*["\']/', $content, $matches)) {
                foreach ($matches[0] as $match) {
                    $this->changes[] = [
                        'file' => $relativePath,
                        'old' => $old,
                        'new' => $new,
                        'context' => $match,
                    ];
                }
            }
        }
    }

    private function migrateFiles()
    {
        $this->line('🔧 Dosyalar migre ediliyor...');
        $this->newLine();

        $files = File::allFiles(resource_path('views/admin'));
        $totalChanges = 0;

        $bar = $this->output->createProgressBar(count($files));

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $changes = $this->migrateFile($file);
                $totalChanges += $changes;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ {$totalChanges} değişiklik yapıldı!");
    }

    private function migrateFile($file)
    {
        $content = file_get_contents($file->getPathname());
        $originalContent = $content;
        $changeCount = 0;

        foreach ($this->conversions as $old => $new) {
            // Strategy: Add new class, keep old for compatibility
            // "btn-primary" → "neo-btn neo-btn-primary btn-primary"
            // Later we can remove old classes

            $pattern = '/class=(["\'])([^"\']*?)' . preg_quote($old, '/') . '([^"\']*?)\1/';

            $content = preg_replace_callback($pattern, function($matches) use ($old, $new, &$changeCount) {
                $quote = $matches[1];
                $before = $matches[2];
                $after = $matches[3];

                // Don't modify if already has neo- version
                if (strpos($matches[0], 'neo-btn') !== false || strpos($matches[0], 'neo-input') !== false) {
                    return $matches[0];
                }

                $changeCount++;

                // Add Neo class before old class (for specificity)
                return 'class=' . $quote . trim($before . ' ' . $new . ' ' . $old . ' ' . $after) . $quote;
            }, $content);
        }

        if ($changeCount > 0 && !$this->dryRun) {
            $relativePath = str_replace(resource_path() . '/', '', $file->getPathname());
            $this->changes[] = [
                'file' => $relativePath,
                'changes' => $changeCount,
            ];

            file_put_contents($file->getPathname(), $content);
        }

        return $changeCount;
    }

    private function displayResults()
    {
        $this->newLine();

        if (empty($this->changes)) {
            $this->info('✅ Hiç değişiklik gerekmedi!');
            return;
        }

        if ($this->option('scan')) {
            $this->warn('📊 Bulunan Bootstrap class\'ları:');
            $this->newLine();

            $grouped = [];
            foreach ($this->changes as $change) {
                $old = $change['old'];
                if (!isset($grouped[$old])) {
                    $grouped[$old] = 0;
                }
                $grouped[$old]++;
            }

            foreach ($grouped as $old => $count) {
                $new = $this->conversions[$old];
                $this->line("   {$old} → {$new} ({$count} kullanım)");
            }

            $this->newLine();
            $this->info("Toplam: " . count($this->changes) . " değişiklik gerekiyor");
            $this->newLine();
            $this->line("Düzeltmek için:");
            $this->line("  php artisan bootstrap:migrate --fix");
        } else {
            $this->info('✅ Migration tamamlandı!');
            $this->newLine();

            $fileCount = count(array_unique(array_column($this->changes, 'file')));
            $totalChanges = array_sum(array_column($this->changes, 'changes'));

            $this->line("📁 Güncellenen dosya: {$fileCount}");
            $this->line("🔧 Toplam değişiklik: {$totalChanges}");

            $this->newLine();
            $this->warn('⚠️  Önemli:');
            $this->line('  • Eski class\'lar uyumluluk için korundu');
            $this->line('  • Visual test yapın!');
            $this->line('  • Git diff kontrol edin');
        }
    }
}
