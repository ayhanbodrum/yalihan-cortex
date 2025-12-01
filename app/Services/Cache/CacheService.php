<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Standardized Cache Service
 *
 * Context7 Cache Standardization
 * Provides consistent cache key formatting, TTL management, and invalidation strategies
 */
class CacheService
{
    /**
     * Cache key prefix
     */
    protected string $prefix = 'emlak_pro';

    /**
     * Default TTL values (in seconds)
     */
    protected array $ttl = [
        'very_short' => 60,      // 1 minute
        'short' => 300,          // 5 minutes
        'medium' => 3600,        // 1 hour
        'long' => 86400,         // 1 day
        'very_long' => 604800,   // 1 week
    ];

    /**
     * Cache tags for grouped invalidation
     */
    protected array $tags = [
        'features' => 'feature_cache',
        'categories' => 'category_cache',
        'listings' => 'ilan_cache',
        'demands' => 'talep_cache',
        'statistics' => 'stats_cache',
        'search' => 'search_cache',
        'ai' => 'ai_cache',
        'prices' => 'price_cache',
        'dashboard' => 'dashboard_cache',
    ];

    public function __construct()
    {
        $this->prefix = config('redis-cache.cache_prefix', 'emlak_pro');
        $this->ttl = array_merge($this->ttl, config('redis-cache.ttl', []));
    }

    /**
     * Generate standardized cache key
     *
     * Format: {prefix}:{namespace}:{key}:{params?}
     * Example: emlak_pro:ilan:stats:active
     *
     * @param  string  $namespace  Cache namespace (e.g., 'ilan', 'category', 'ai')
     * @param  string  $key  Cache key
     * @param  array  $params  Optional parameters for key uniqueness
     * @return string Standardized cache key
     */
    public function key(string $namespace, string $key, array $params = []): string
    {
        $parts = [$this->prefix, $namespace, $key];

        if (! empty($params)) {
            // Sort params for consistent key generation
            ksort($params);
            $paramString = implode(':', array_map(function ($k, $v) {
                return $k.'='.(is_array($v) ? md5(json_encode($v)) : $v);
            }, array_keys($params), $params));
            $parts[] = $paramString;
        }

        return implode(':', $parts);
    }

    /**
     * Get value from cache
     *
     * @param  string  $key  Cache key
     * @param  mixed  $default  Default value if not found
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $startTime = microtime(true);
        $value = Cache::get($key, $default);
        $duration = (microtime(true) - $startTime) * 1000;

        if (config('redis-cache.monitoring.log_hits', false) && $value !== null) {
            Log::debug('Cache Hit', [
                'key' => $key,
                'duration_ms' => round($duration, 2),
            ]);
        }

        if (config('redis-cache.monitoring.log_misses', true) && $value === null) {
            Log::debug('Cache Miss', [
                'key' => $key,
                'duration_ms' => round($duration, 2),
            ]);
        }

        return $value;
    }

    /**
     * Store value in cache
     *
     * @param  string  $key  Cache key
     * @param  mixed  $value  Value to cache
     * @param  int|string|null  $ttl  TTL in seconds or preset name (e.g., 'short', 'medium')
     */
    public function put(string $key, $value, $ttl = null): bool
    {
        $ttl = $this->resolveTtl($ttl ?? 'medium');

        return Cache::put($key, $value, $ttl);
    }

    /**
     * Remember value in cache (get or compute)
     *
     * @param  string  $key  Cache key
     * @param  int|string|null  $ttl  TTL in seconds or preset name
     * @param  callable  $callback  Callback to compute value if not cached
     * @return mixed
     */
    public function remember(string $key, $ttl, callable $callback)
    {
        $ttl = $this->resolveTtl($ttl ?? 'medium');

        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Check if key exists in cache
     *
     * @param  string  $key  Cache key
     */
    public function has(string $key): bool
    {
        return Cache::has($key);
    }

    /**
     * Remove value from cache
     *
     * @param  string  $key  Cache key
     */
    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    /**
     * Invalidate cache by namespace
     *
     * @param  string  $namespace  Namespace to invalidate
     * @param  array  $params  Optional parameters to match specific keys
     * @return int Number of keys invalidated
     */
    public function invalidateNamespace(string $namespace, array $params = []): int
    {
        $pattern = $this->key($namespace, '*', $params);

        // Note: Redis pattern matching requires implementation
        // For now, we'll use a tag-based approach or manual key tracking
        return $this->invalidateByTag($this->getTagForNamespace($namespace));
    }

    /**
     * Invalidate cache by tag
     *
     * @param  string  $tag  Tag name
     * @return int Number of keys invalidated
     */
    public function invalidateByTag(string $tag): int
    {
        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\TaggedCache) {
                Cache::tags([$tag])->flush();

                return 1; // Tag-based flush
            }
        } catch (\Exception $e) {
            Log::warning('Tag-based cache invalidation not available', [
                'tag' => $tag,
                'error' => $e->getMessage(),
            ]);
        }

        return 0;
    }

    /**
     * Invalidate related caches when model changes
     *
     * @param  string  $modelName  Model name (e.g., 'Ilan', 'Category')
     * @param  int|null  $modelId  Optional model ID for specific invalidation
     */
    public function invalidateModel(string $modelName, ?int $modelId = null): void
    {
        $namespaceMap = [
            'Ilan' => 'ilan',
            'IlanKategori' => 'category',
            'Feature' => 'feature',
            'Talep' => 'talep',
            'Kisi' => 'kisi',
            'Price' => 'price',
        ];

        $namespace = $namespaceMap[$modelName] ?? strtolower($modelName);

        // Invalidate specific model cache
        if ($modelId) {
            $this->forget($this->key($namespace, "model:{$modelId}"));
        }

        // Invalidate related caches
        $this->invalidateNamespace($namespace);
        $this->invalidateByTag($this->getTagForNamespace($namespace));

        // Invalidate statistics
        $this->forget($this->key('stats', "{$namespace}:stats"));
    }

    /**
     * Get TTL value (resolve preset names to seconds)
     *
     * @param  int|string  $ttl  TTL in seconds or preset name
     * @return int TTL in seconds
     */
    protected function resolveTtl($ttl): int
    {
        if (is_string($ttl) && isset($this->ttl[$ttl])) {
            return $this->ttl[$ttl];
        }

        if (is_numeric($ttl)) {
            return (int) $ttl;
        }

        return $this->ttl['medium']; // Default to medium
    }

    /**
     * Get tag for namespace
     *
     * @param  string  $namespace  Namespace
     * @return string Tag name
     */
    protected function getTagForNamespace(string $namespace): string
    {
        return $this->tags[$namespace] ?? "{$namespace}_cache";
    }

    /**
     * Clear all cache
     */
    public function flush(): bool
    {
        return Cache::flush();
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        // This would require Redis-specific implementation
        // For now, return basic info
        return [
            'prefix' => $this->prefix,
            'ttl_presets' => $this->ttl,
            'tags' => array_keys($this->tags),
        ];
    }
}
