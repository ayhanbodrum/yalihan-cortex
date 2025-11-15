<?php

namespace App\Services\Cache;

/**
 * Cache Helper Functions
 *
 * Provides convenient helper functions for common cache operations
 */
class CacheHelper
{
    /**
     * Get cache service instance
     *
     * @return CacheService
     */
    public static function cache(): CacheService
    {
        return app(CacheService::class);
    }

    /**
     * Quick cache key generation
     *
     * @param string $namespace
     * @param string $key
     * @param array $params
     * @return string
     */
    public static function key(string $namespace, string $key, array $params = []): string
    {
        return static::cache()->key($namespace, $key, $params);
    }

    /**
     * Quick cache remember
     *
     * @param string $namespace
     * @param string $key
     * @param int|string $ttl
     * @param callable $callback
     * @param array $params
     * @return mixed
     */
    public static function remember(string $namespace, string $key, $ttl, callable $callback, array $params = [])
    {
        $cacheKey = static::key($namespace, $key, $params);
        return static::cache()->remember($cacheKey, $ttl, $callback);
    }

    /**
     * Quick cache get
     *
     * @param string $namespace
     * @param string $key
     * @param mixed $default
     * @param array $params
     * @return mixed
     */
    public static function get(string $namespace, string $key, $default = null, array $params = [])
    {
        $cacheKey = static::key($namespace, $key, $params);
        return static::cache()->get($cacheKey, $default);
    }

    /**
     * Quick cache put
     *
     * @param string $namespace
     * @param string $key
     * @param mixed $value
     * @param int|string $ttl
     * @param array $params
     * @return bool
     */
    public static function put(string $namespace, string $key, $value, $ttl = 'medium', array $params = []): bool
    {
        $cacheKey = static::key($namespace, $key, $params);
        return static::cache()->put($cacheKey, $value, $ttl);
    }

    /**
     * Quick cache forget
     *
     * @param string $namespace
     * @param string $key
     * @param array $params
     * @return bool
     */
    public static function forget(string $namespace, string $key, array $params = []): bool
    {
        $cacheKey = static::key($namespace, $key, $params);
        return static::cache()->forget($cacheKey);
    }

    /**
     * Invalidate namespace
     *
     * @param string $namespace
     * @param array $params
     * @return int
     */
    public static function invalidate(string $namespace, array $params = []): int
    {
        return static::cache()->invalidateNamespace($namespace, $params);
    }

    /**
     * Invalidate model cache
     *
     * @param string $modelName
     * @param int|null $modelId
     * @return void
     */
    public static function invalidateModel(string $modelName, ?int $modelId = null): void
    {
        static::cache()->invalidateModel($modelName, $modelId);
    }
}
