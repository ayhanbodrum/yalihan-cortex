<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

/**
 * API Endpoint Health Check
 *
 * Context7 Standard: C7-API-HEALTH-CHECK-2025-12-03
 *
 * Tüm API endpoint'lerini test eder ve sağlık durumunu raporlar.
 *
 * @version 1.0.0
 * @since 2025-12-03
 */
class TestApiEndpoints extends Command
{
    protected $signature = 'api:test-endpoints {--base-url=http://localhost:8000}';
    protected $description = 'Test all API endpoints for health';

    public function handle()
    {
        $baseUrl = $this->option('base-url');
        $this->info("🧪 Testing API endpoints (Base URL: {$baseUrl})...");

        $routes = Route::getRoutes();
        $testResults = [];
        $total = 0;
        $passed = 0;
        $failed = 0;

        foreach ($routes as $route) {
            $uri = $route->uri();
            if (!str_starts_with($uri, 'api/')) {
                continue;
            }

            $methods = $route->methods();
            if (!in_array('GET', $methods)) {
                continue; // Only test GET endpoints for now
            }

            $total++;
            $fullUrl = rtrim($baseUrl, '/') . '/' . ltrim($uri, '/');

            // Replace route parameters with test values
            $fullUrl = preg_replace('/\{[^}]+\}/', '1', $fullUrl);
            $fullUrl = preg_replace('/\{[^}]+\?\}/', '', $fullUrl);

            try {
                $response = Http::timeout(5)->get($fullUrl);
                $status = $response->status();

                if ($status >= 200 && $status < 400) {
                    $passed++;
                    $testResults[] = [
                        'uri' => $uri,
                        'status' => '✅',
                        'http_status' => $status,
                    ];
                } else {
                    $failed++;
                    $testResults[] = [
                        'uri' => $uri,
                        'status' => '❌',
                        'http_status' => $status,
                    ];
                }
            } catch (\Exception $e) {
                $failed++;
                $testResults[] = [
                    'uri' => $uri,
                    'status' => '❌',
                    'error' => $e->getMessage(),
                ];
            }
        }

        // Report results
        $this->newLine();
        $this->info("📊 Test Results:");
        $this->info("   Total: {$total}");
        $this->info("   ✅ Passed: {$passed}");
        $this->info("   ❌ Failed: {$failed}");
        $this->newLine();

        if ($failed > 0) {
            $this->error('Failed endpoints:');
            foreach ($testResults as $result) {
                if ($result['status'] === '❌') {
                    $this->line("   {$result['status']} {$result['uri']}");
                    if (isset($result['error'])) {
                        $this->line("      Error: {$result['error']}");
                    } elseif (isset($result['http_status'])) {
                        $this->line("      HTTP Status: {$result['http_status']}");
                    }
                }
            }
        }

        return $failed > 0 ? 1 : 0;
    }
}
