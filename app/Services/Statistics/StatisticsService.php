<?php

namespace App\Services\Statistics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Statistics Service
 *
 * Context7 Standardı: C7-STATISTICS-SERVICE-2025-11-11
 *
 * Standart statistics hesaplama için service
 * Code duplication'ı azaltmak ve tutarlı statistics logic sağlamak için oluşturuldu
 *
 * @package App\Services\Statistics
 */
class StatisticsService
{
    /**
     * Model için temel istatistikleri getir
     *
     * @param string|Model $model Model class name veya instance
     * @param array $options Seçenekler
     *   - status_field: Status field adı (varsayılan: 'status')
     *   - cache_key: Cache key (varsayılan: otomatik oluşturulur)
     *   - cache_ttl: Cache TTL saniye (varsayılan: 3600)
     *   - additional_stats: Ek istatistikler (closure array)
     * @return array
     */
    public static function getModelStats($model, array $options = []): array
    {
        $modelClass = is_string($model) ? $model : get_class($model);
        $modelInstance = is_string($model) ? new $model : $model;

        $statusField = $options['status_field'] ?? 'status';
        $cacheKey = $options['cache_key'] ?? 'stats_' . str_replace('\\', '_', $modelClass);
        $cacheTtl = $options['cache_ttl'] ?? 3600;

        return Cache::remember($cacheKey, $cacheTtl, function () use ($modelInstance, $statusField, $options) {
            $stats = [
                'total' => $modelInstance->count(),
            ];

            // Status field varsa aktif/pasif sayılarını ekle
            if ($modelInstance->getConnection()->getSchemaBuilder()->hasColumn($modelInstance->getTable(), $statusField)) {
                $stats['active'] = (clone $modelInstance)->where($statusField, true)->count();
                $stats['inactive'] = (clone $modelInstance)->where($statusField, false)->count();
            }

            // Ek istatistikler varsa ekle
            if (isset($options['additional_stats']) && is_array($options['additional_stats'])) {
                foreach ($options['additional_stats'] as $key => $closure) {
                    if (is_callable($closure)) {
                        $stats[$key] = $closure($modelInstance);
                    }
                }
            }

            return $stats;
        });
    }

    /**
     * Model için aylık istatistikleri getir
     *
     * @param string|Model $model Model class name veya instance
     * @param array $options Seçenekler
     *   - date_field: Tarih field adı (varsayılan: 'created_at')
     *   - months: Kaç ay geriye gidilecek (varsayılan: 12)
     *   - cache_key: Cache key
     *   - cache_ttl: Cache TTL saniye (varsayılan: 3600)
     * @return array
     */
    public static function getMonthlyStats($model, array $options = []): array
    {
        $modelClass = is_string($model) ? $model : get_class($model);
        $modelInstance = is_string($model) ? new $model : $model;

        $dateField = $options['date_field'] ?? 'created_at';
        $months = $options['months'] ?? 12;
        $cacheKey = $options['cache_key'] ?? 'monthly_stats_' . str_replace('\\', '_', $modelClass);
        $cacheTtl = $options['cache_ttl'] ?? 3600;

        return Cache::remember($cacheKey, $cacheTtl, function () use ($modelInstance, $dateField, $months) {
            $stats = [];
            $now = Carbon::now();

            for ($i = $months - 1; $i >= 0; $i--) {
                $month = $now->copy()->subMonths($i);
                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();

                $count = (clone $modelInstance)
                    ->whereBetween($dateField, [$monthStart, $monthEnd])
                    ->count();

                $stats[$month->format('Y-m')] = [
                    'month' => $month->format('F Y'),
                    'count' => $count,
                ];
            }

            return $stats;
        });
    }

    /**
     * Model için günlük istatistikleri getir
     *
     * @param string|Model $model Model class name veya instance
     * @param array $options Seçenekler
     *   - date_field: Tarih field adı (varsayılan: 'created_at')
     *   - days: Kaç gün geriye gidilecek (varsayılan: 30)
     *   - cache_key: Cache key
     *   - cache_ttl: Cache TTL saniye (varsayılan: 1800)
     * @return array
     */
    public static function getDailyStats($model, array $options = []): array
    {
        $modelClass = is_string($model) ? $model : get_class($model);
        $modelInstance = is_string($model) ? new $model : $model;

        $dateField = $options['date_field'] ?? 'created_at';
        $days = $options['days'] ?? 30;
        $cacheKey = $options['cache_key'] ?? 'daily_stats_' . str_replace('\\', '_', $modelClass);
        $cacheTtl = $options['cache_ttl'] ?? 1800;

        return Cache::remember($cacheKey, $cacheTtl, function () use ($modelInstance, $dateField, $days) {
            $stats = [];
            $now = Carbon::now();

            for ($i = $days - 1; $i >= 0; $i--) {
                $day = $now->copy()->subDays($i);
                $dayStart = $day->copy()->startOfDay();
                $dayEnd = $day->copy()->endOfDay();

                $count = (clone $modelInstance)
                    ->whereBetween($dateField, [$dayStart, $dayEnd])
                    ->count();

                $stats[$day->format('Y-m-d')] = [
                    'date' => $day->format('d.m.Y'),
                    'count' => $count,
                ];
            }

            return $stats;
        });
    }

    /**
     * Model için status bazlı istatistikleri getir
     *
     * @param string|Model $model Model class name veya instance
     * @param array $options Seçenekler
     *   - status_field: Status field adı (varsayılan: 'status')
     *   - status_values: Status değerleri array'i (varsayılan: ['active', 'inactive', 'pending'])
     *   - cache_key: Cache key
     *   - cache_ttl: Cache TTL saniye (varsayılan: 3600)
     * @return array
     */
    public static function getStatusStats($model, array $options = []): array
    {
        $modelClass = is_string($model) ? $model : get_class($model);
        $modelInstance = is_string($model) ? new $model : $model;

        $statusField = $options['status_field'] ?? 'status';
        $statusValues = $options['status_values'] ?? ['active', 'inactive', 'pending'];
        $cacheKey = $options['cache_key'] ?? 'status_stats_' . str_replace('\\', '_', $modelClass);
        $cacheTtl = $options['cache_ttl'] ?? 3600;

        return Cache::remember($cacheKey, $cacheTtl, function () use ($modelInstance, $statusField, $statusValues) {
            $stats = [];

            // Status field kontrolü
            if (!$modelInstance->getConnection()->getSchemaBuilder()->hasColumn($modelInstance->getTable(), $statusField)) {
                return $stats;
            }

            foreach ($statusValues as $status) {
                $stats[$status] = (clone $modelInstance)->where($statusField, $status)->count();
            }

            return $stats;
        });
    }

    /**
     * Cache'i temizle
     *
     * @param string|Model $model Model class name veya instance
     * @param string|null $type Stats type (null ise tüm cache'ler temizlenir)
     * @return void
     */
    public static function clearCache($model, ?string $type = null): void
    {
        $modelClass = is_string($model) ? $model : get_class($model);
        $baseKey = str_replace('\\', '_', $modelClass);

        $keys = [
            "stats_{$baseKey}",
            "monthly_stats_{$baseKey}",
            "daily_stats_{$baseKey}",
            "status_stats_{$baseKey}",
        ];

        if ($type) {
            $keys = array_filter($keys, fn($key) => str_starts_with($key, "{$type}_"));
        }

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}

