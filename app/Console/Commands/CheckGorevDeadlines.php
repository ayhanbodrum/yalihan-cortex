<?php

namespace App\Console\Commands;

use App\Events\GorevDeadlineYaklasiyor;
use App\Events\GorevGecikti;
use App\Modules\TakimYonetimi\Models\Gorev;
use App\Services\Logging\LogService;
use Illuminate\Console\Command;

/**
 * Görev Deadline Kontrolü Komutu
 *
 * Context7: Takım Yönetimi Otomasyonu - Temel Event Sistemi
 * Deadline'ı yaklaşan ve geciken görevleri bulup ilgili Event'leri fırlatır.
 * Scheduler'da günlük çalıştırılmalı.
 */
class CheckGorevDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gorevler:check-deadlines
                            {--gun=1 : Deadline yaklaşma kontrolü için gün sayısı (varsayılan: 1)}
                            {--dry-run : Sadece bulunan görevleri göster, event fırlatma}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Görev deadline\'larını kontrol et ve yaklaşan/geciken görevler için event fırlat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $gun = (int) $this->option('gun');
        $dryRun = $this->option('dry-run');

        $this->info("🔍 Görev deadline kontrolü başlatılıyor... (Yaklaşma: {$gun} gün)");

        $startTime = LogService::startTimer('gorev_deadline_check');

        try {
            // Deadline yaklaşan görevler (1 gün içinde)
            $yaklasanGorevler = Gorev::deadlineYaklasan($gun)
                ->where('status', '!=', 'tamamlandi')
                ->where('status', '!=', 'iptal')
                ->get();

            // Geciken görevler
            $gecikenGorevler = Gorev::geciken()
                ->where('status', '!=', 'tamamlandi')
                ->where('status', '!=', 'iptal')
                ->get();

            $this->info("📊 Bulunan görevler:");
            $this->info("  - Deadline yaklaşan: {$yaklasanGorevler->count()}");
            $this->info("  - Geciken: {$gecikenGorevler->count()}");

            $eventCount = 0;

            // Deadline yaklaşan görevler için event fırlat
            foreach ($yaklasanGorevler as $gorev) {
                if (!$gorev->bitis_tarihi) {
                    continue;
                }

                $kalanGun = now()->diffInDays($gorev->bitis_tarihi, false);

                if ($kalanGun >= 0 && $kalanGun <= $gun) {
                    if ($dryRun) {
                        $this->line("  ⚠️  [DRY-RUN] Deadline yaklaşıyor: {$gorev->baslik} (Kalan: {$kalanGun} gün)");
                    } else {
                        event(new GorevDeadlineYaklasiyor($gorev, (int) $kalanGun));
                        $this->line("  ⚠️  Event fırlatıldı: {$gorev->baslik} (Kalan: {$kalanGun} gün)");
                        $eventCount++;
                    }
                }
            }

            // Geciken görevler için event fırlat
            foreach ($gecikenGorevler as $gorev) {
                if (!$gorev->bitis_tarihi || $gorev->geciktiMi() === false) {
                    continue;
                }

                $gecikmeGunu = abs(now()->diffInDays($gorev->bitis_tarihi, false));

                if ($dryRun) {
                    $this->line("  🔴 [DRY-RUN] Geciken: {$gorev->baslik} ({$gecikmeGunu} gün gecikme)");
                } else {
                    event(new GorevGecikti($gorev, (int) $gecikmeGunu));
                    $this->line("  🔴 Event fırlatıldı: {$gorev->baslik} ({$gecikmeGunu} gün gecikme)");
                    $eventCount++;
                }
            }

            $durationMs = LogService::stopTimer($startTime);

            if (!$dryRun) {
                LogService::info('Gorev deadline kontrolü tamamlandı', [
                    'yaklasan_count' => $yaklasanGorevler->count(),
                    'geciken_count' => $gecikenGorevler->count(),
                    'event_count' => $eventCount,
                    'duration_ms' => $durationMs,
                ]);
            }

            $this->info("✅ Kontrol tamamlandı! ({$eventCount} event fırlatıldı)");
            $this->info("⏱️  Süre: {$durationMs}ms");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $durationMs = LogService::stopTimer($startTime);

            LogService::error('Gorev deadline kontrolü hatası', [
                'error' => $e->getMessage(),
                'duration_ms' => $durationMs,
            ], $e);

            $this->error("❌ Hata: {$e->getMessage()}");

            return Command::FAILURE;
        }
    }
}
