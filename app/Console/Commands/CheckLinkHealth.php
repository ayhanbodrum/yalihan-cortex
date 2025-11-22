<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckLinkHealth extends Command
{
    protected $signature = 'link:health-check';
    protected $description = 'Tanımlı linklerin sağlık kontrolünü yapar ve hataları raporlar';

    public function handle(): int
    {
        $baseUrl = rtrim(config('app.url') ?: 'http://127.0.0.1:8000', '/');
        $paths = config('link_health.urls', ['/','/login','/admin/adres-yonetimi']);

        $timeout = (int) config('link_health.timeout', 10);
        $failures = [];

        foreach ($paths as $path) {
            $url = $baseUrl . (str_starts_with($path, '/') ? $path : '/' . $path);
            try {
                $resp = Http::timeout($timeout)->get($url);
                $status = $resp->status();
                if ($status >= 400) {
                    $failures[] = ['url' => $url, 'status' => $status];
                }
            } catch (\Throwable $e) {
                $failures[] = ['url' => $url, 'error' => $e->getMessage()];
            }
        }

        if (!empty($failures)) {
            Log::channel('daily')->warning('Link health failures', [
                'failures' => $failures,
                'timestamp' => now()->toISOString(),
            ]);
            $this->error('Bazı linklerde hata bulundu. Ayrıntılar günlüklerde.');
            return self::FAILURE;
        }

        $this->info('Tüm linkler sağlıklı.');
        return self::SUCCESS;
    }
}