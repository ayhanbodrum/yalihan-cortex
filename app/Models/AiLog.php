<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AI Log Model
 *
 * Context7 Standardı: C7-AI-LOG-MODEL-2025-10-14
 *
 * AI provider kullanımlarını ve analytics'i takip eder
 */
class AiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'request_type',
        'content_type',
        'content_id',
        'status',
        'response_time',
        'cost',
        'tokens_used',
        'request_data',
        'response_data',
        'error_message',
        'user_id',
        'model',
        'version',
        'ip_address',
    ];

    protected $casts = [
        'response_time' => 'integer',
        'cost' => 'decimal:6',
        'tokens_used' => 'integer',
        'request_data' => 'array',
        'response_data' => 'array',
    ];

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Successful requests
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope: Failed requests
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'error', 'timeout']);
    }

    /**
     * Scope: By provider
     */
    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope: Recent (last N days)
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get average response time
     */
    public static function averageResponseTime(?string $provider = null): float
    {
        $query = static::successful();

        if ($provider) {
            $query->byProvider($provider);
        }

        return $query->avg('response_time') ?? 0;
    }

    /**
     * Get total cost
     */
    public static function totalCost(?string $provider = null): float
    {
        $query = static::query();

        if ($provider) {
            $query->byProvider($provider);
        }

        return $query->sum('cost') ?? 0;
    }

    /**
     * Get success rate percentage
     */
    public static function successRate(?string $provider = null): float
    {
        $query = static::query();

        if ($provider) {
            $query->byProvider($provider);
        }

        $total = $query->count();

        if ($total === 0) {
            return 0;
        }

        $successful = $query->where('status', 'success')->count();

        return round(($successful / $total) * 100, 2);
    }

    /**
     * Get provider usage statistics
     */
    public static function providerUsage(int $days = 30): array
    {
        return static::recent($days)
            ->selectRaw('provider, count(*) as count')
            ->groupBy('provider')
            ->pluck('count', 'provider')
            ->toArray();
    }
}
