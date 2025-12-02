<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Check Queue Worker Command
 *
 * Context7 Standard: C7-QUEUE-WORKER-ALERT-2025-12-01
 *
 * Queue worker durumunu kontrol eder ve durdurulmuşsa bildirim gönderir.
 * Cron job ile her 5 dakikada bir çalıştırılmalıdır.
 */
class CheckQueueWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:check-worker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check queue worker status and send alert if stopped';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $status = $this->getQueueWorkerStatus();

            if ($status['status'] === 'stopped') {
                $this->sendAlert($status);
                $this->error('Queue worker durdurulmuş! Bildirim gönderildi.');
                return Command::FAILURE;
            }

            $this->info('Queue worker çalışıyor.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('CheckQueueWorker: Hata oluştu', [
                'error' => $e->getMessage(),
            ]);
            $this->error('Hata: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Get queue worker status
     *
     * @return array
     */
    private function getQueueWorkerStatus(): array
    {
        // Son 5 dakikada işlenen job sayısı
        $processedJobs = DB::table('jobs')
            ->where('queue', 'cortex-notifications')
            ->whereNotNull('reserved_at')
            ->where('reserved_at', '>=', now()->subMinutes(5))
            ->count();

        // Bekleyen job sayısı
        $pendingJobs = DB::table('jobs')
            ->where('queue', 'cortex-notifications')
            ->whereNull('reserved_at')
            ->count();

        // Eğer son 5 dakikada hiç job işlenmemişse ve bekleyen job varsa, worker durdurulmuş olabilir
        if ($processedJobs === 0 && $pendingJobs > 0) {
            return [
                'status' => 'stopped',
                'pending_jobs' => $pendingJobs,
                'processed_jobs_last_5min' => $processedJobs,
            ];
        }

        return [
            'status' => 'running',
            'pending_jobs' => $pendingJobs,
            'processed_jobs_last_5min' => $processedJobs,
        ];
    }

    /**
     * Send alert if queue worker is stopped
     *
     * @param array $status
     * @return void
     */
    private function sendAlert(array $status): void
    {
        // Alert throttling: Aynı alert'i 1 saat içinde tekrar gönderme
        $alertKey = 'queue:worker:alert:sent';
        if (Cache::has($alertKey)) {
            Log::info('CheckQueueWorker: Alert zaten gönderilmiş, throttling aktif', [
                'alert_key' => $alertKey,
            ]);
            return;
        }

        try {
            $telegramService = app(TelegramService::class);
            $message = $this->buildAlertMessage($status);

            // Telegram bildirimi gönder
            $sent = $telegramService->sendCriticalAlert([
                'type' => 'system_alert',
                'score' => 100,
                'ilan_id' => null,
                'talep_id' => null,
                'message' => $message,
            ]);

            if ($sent) {
                // Alert gönderildi, 1 saat boyunca tekrar gönderme
                Cache::put($alertKey, true, now()->addHour());

                Log::info('CheckQueueWorker: Queue worker alert gönderildi', [
                    'status' => $status,
                ]);
            } else {
                Log::warning('CheckQueueWorker: Queue worker alert gönderilemedi', [
                    'status' => $status,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('CheckQueueWorker: Alert gönderme hatası', [
                'error' => $e->getMessage(),
                'status' => $status,
            ]);
        }
    }

    /**
     * Build alert message
     *
     * @param array $status
     * @return string
     */
    private function buildAlertMessage(array $status): string
    {
        $message = "🚨 *QUEUE WORKER DURDURULMUŞ* 🚨\n\n";
        $message .= "⚠️ *Durum:* Queue worker çalışmıyor!\n\n";
        $message .= "📊 *Detaylar:*\n";
        $message .= "• Bekleyen işler: {$status['pending_jobs']}\n";
        $message .= "• Son 5 dakikada işlenen: {$status['processed_jobs_last_5min']}\n\n";
        $message .= "🔧 *Çözüm:*\n";
        $message .= "```bash\n";
        $message .= "php artisan queue:work --queue=cortex-notifications --tries=3\n";
        $message .= "```\n\n";
        $message .= "💡 *Not:* Bu bildirim 1 saat içinde tekrar gönderilmeyecektir.";

        return $message;
    }
}

