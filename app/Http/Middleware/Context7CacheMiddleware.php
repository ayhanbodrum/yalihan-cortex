<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Context7CacheService;
use Illuminate\Support\Facades\Log;

/**
 * Context7 Cache Middleware
 *
 * Context7 standartlarına uygun cache middleware.
 * API response'larını otomatik olarak cache'ler.
 */
class Context7CacheMiddleware
{
    protected $cacheService;

    public function __construct(Context7CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $ttl = '3600')
    {
        // Sadece GET request'leri cache'le
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Cache key oluştur
        $cacheKey = $this->generateCacheKey($request);

        // Cache'den kontrol et
        $cached = $this->cacheService->get($cacheKey);

        if ($cached !== null) {
            Log::info('Context7 Cache Hit: ' . $cacheKey);
            return response()->json($cached);
        }

        // Response'u al
        $response = $next($request);

        // Sadece başarılı response'ları cache'le
        if ($response->getStatusCode() === 200) {
            $content = $response->getContent();
            $data = json_decode($content, true);

            if ($data !== null) {
                $this->cacheService->put($cacheKey, $data, (int)$ttl);
                Log::info('Context7 Cache Stored: ' . $cacheKey);
            }
        }

        return $response;
    }

    /**
     * Cache key oluştur
     */
    private function generateCacheKey(Request $request): string
    {
        $uri = $request->getRequestUri();
        $query = $request->getQueryString();
        $user = $request->user();

        $key = 'api:' . md5($uri . ':' . $query);

        if ($user) {
            $key .= ':user:' . $user->id;
        }

        return $key;
    }
}
