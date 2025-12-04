<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Health Check API Routes (v1)
|--------------------------------------------------------------------------
|
| Context7 Standard: C7-HEALTH-API-2025-12-04
| API system health and status endpoints
|
| Prefix: /api/v1/health
| No authentication required (public endpoint)
|
*/

Route::prefix('health')->name('api.health.')->group(function () {
    /**
     * GET /api/v1/health
     * Basic API health status
     * Response: { success, message, timestamp }
     */
    Route::get('/', function () {
        return response()->json([
            'success' => true,
            'message' => 'API is healthy',
            'timestamp' => now()->toISOString(),
            'version' => 'v1',
        ]);
    })->name('index');

    /**
     * GET /api/v1/health/database
     * Database connectivity check
     */
    Route::get('/database', function () {
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();

            return response()->json([
                'success' => true,
                'service' => 'database',
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'service' => 'database',
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    })->name('database');

    /**
     * GET /api/v1/health/cache
     * Cache service check
     */
    Route::get('/cache', function () {
        try {
            \Illuminate\Support\Facades\Cache::put('health-check', true, 1);
            $value = \Illuminate\Support\Facades\Cache::get('health-check');

            return response()->json([
                'success' => true,
                'service' => 'cache',
                'status' => $value ? 'healthy' : 'unhealthy',
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'service' => 'cache',
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    })->name('cache');

    /**
     * GET /api/v1/health/full
     * Complete system health report
     */
    Route::get('/full', function () {
        $health = [
            'success' => true,
            'timestamp' => now()->toISOString(),
            'services' => [
                'api' => ['status' => 'healthy'],
                'database' => ['status' => 'unknown'],
                'cache' => ['status' => 'unknown'],
            ],
        ];

        // Database check
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            $health['services']['database']['status'] = 'healthy';
        } catch (\Exception $e) {
            $health['services']['database']['status'] = 'unhealthy';
            $health['services']['database']['error'] = $e->getMessage();
            $health['success'] = false;
        }

        // Cache check
        try {
            \Illuminate\Support\Facades\Cache::put('health-check', true, 1);
            $value = \Illuminate\Support\Facades\Cache::get('health-check');
            $health['services']['cache']['status'] = $value ? 'healthy' : 'unhealthy';
        } catch (\Exception $e) {
            $health['services']['cache']['status'] = 'unhealthy';
            $health['services']['cache']['error'] = $e->getMessage();
            $health['success'] = false;
        }

        return response()->json($health, $health['success'] ? 200 : 500);
    })->name('full');
});
