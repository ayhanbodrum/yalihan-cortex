<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogView extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_post_id',
        'user_id',
        'ip_address',
        'user_agent',
        'referrer',
        'reading_time_seconds',
        'completed_reading',
    ];

    protected $casts = [
        'reading_time_seconds' => 'integer',
        'completed_reading' => 'boolean',
    ];

    // Relationships
    public function post(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    public function scopeByPost($query, $postId)
    {
        return $query->where('blog_post_id', $postId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByIp($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    public function scopeCompleted($query)
    {
        return $query->where('completed_reading', true);
    }

    public function scopeWithReadingTime($query, $minSeconds = null)
    {
        $query = $query->whereNotNull('reading_time_seconds');

        if ($minSeconds) {
            $query->where('reading_time_seconds', '>=', $minSeconds);
        }

        return $query;
    }

    public function scopeFromReferrer($query, $referrer)
    {
        return $query->where('referrer', 'like', "%{$referrer}%");
    }

    public function scopeUniqueVisitors($query)
    {
        return $query->distinct('ip_address');
    }

    public function scopeRegisteredUsers($query)
    {
        return $query->whereNotNull('user_id');
    }

    public function scopeGuestUsers($query)
    {
        return $query->whereNull('user_id');
    }

    // Accessors
    public function getIsRegisteredUserAttribute()
    {
        return ! is_null($this->user_id);
    }

    public function getIsGuestAttribute()
    {
        return is_null($this->user_id);
    }

    public function getReadingTimeMinutesAttribute()
    {
        return $this->reading_time_seconds ? round($this->reading_time_seconds / 60, 1) : null;
    }

    public function getReadingTimeFormattedAttribute()
    {
        if (! $this->reading_time_seconds) {
            return null;
        }

        $minutes = floor($this->reading_time_seconds / 60);
        $seconds = $this->reading_time_seconds % 60;

        if ($minutes > 0) {
            return $minutes.'m '.$seconds.'s';
        }

        return $seconds.'s';
    }

    public function getReferrerDomainAttribute()
    {
        if (! $this->referrer) {
            return null;
        }

        $parsed = parse_url($this->referrer);

        return $parsed['host'] ?? null;
    }

    public function getIsDirectTrafficAttribute()
    {
        return is_null($this->referrer) || empty($this->referrer);
    }

    public function getIsSocialTrafficAttribute()
    {
        if (! $this->referrer) {
            return false;
        }

        $socialDomains = [
            'facebook.com', 'twitter.com', 'instagram.com', 'linkedin.com',
            'youtube.com', 'tiktok.com', 'pinterest.com', 'reddit.com',
        ];

        $domain = $this->getReferrerDomainAttribute();

        foreach ($socialDomains as $socialDomain) {
            if (str_contains($domain, $socialDomain)) {
                return true;
            }
        }

        return false;
    }

    public function getIsSearchTrafficAttribute()
    {
        if (! $this->referrer) {
            return false;
        }

        $searchDomains = [
            'google.com', 'bing.com', 'yahoo.com', 'duckduckgo.com',
            'yandex.com', 'baidu.com',
        ];

        $domain = $this->getReferrerDomainAttribute();

        foreach ($searchDomains as $searchDomain) {
            if (str_contains($domain, $searchDomain)) {
                return true;
            }
        }

        return false;
    }

    public function getBrowserAttribute()
    {
        if (! $this->user_agent) {
            return 'Unknown';
        }

        // Simple browser detection
        if (str_contains($this->user_agent, 'Chrome')) {
            return 'Chrome';
        } elseif (str_contains($this->user_agent, 'Firefox')) {
            return 'Firefox';
        } elseif (str_contains($this->user_agent, 'Safari')) {
            return 'Safari';
        } elseif (str_contains($this->user_agent, 'Edge')) {
            return 'Edge';
        } elseif (str_contains($this->user_agent, 'Opera')) {
            return 'Opera';
        }

        return 'Other';
    }

    public function getDeviceTypeAttribute()
    {
        if (! $this->user_agent) {
            return 'Unknown';
        }

        if (str_contains($this->user_agent, 'Mobile') || str_contains($this->user_agent, 'Android')) {
            return 'Mobile';
        } elseif (str_contains($this->user_agent, 'Tablet') || str_contains($this->user_agent, 'iPad')) {
            return 'Tablet';
        }

        return 'Desktop';
    }

    // Static methods for analytics
    public static function getPopularPosts($limit = 10, $days = 30)
    {
        return static::select('blog_post_id')
            ->with('post')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('blog_post_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit($limit)
            ->get()
            ->pluck('post')
            ->filter();
    }

    public static function getTrafficSources($days = 30)
    {
        return static::select('referrer')
            ->where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('referrer')
            ->groupBy('referrer')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(20)
            ->get()
            ->map(function ($view) {
                $parsed = parse_url($view->referrer);

                return [
                    'domain' => $parsed['host'] ?? 'Unknown',
                    'full_url' => $view->referrer,
                    'views' => static::where('referrer', $view->referrer)->count(),
                ];
            });
    }

    public static function getViewsByDateRange($startDate, $endDate)
    {
        return static::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as views')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public static function getAverageReadingTime($postId = null, $days = 30)
    {
        $query = static::where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('reading_time_seconds');

        if ($postId) {
            $query->where('blog_post_id', $postId);
        }

        return $query->avg('reading_time_seconds');
    }

    public static function getCompletionRate($postId = null, $days = 30)
    {
        $query = static::where('created_at', '>=', now()->subDays($days));

        if ($postId) {
            $query->where('blog_post_id', $postId);
        }

        $total = $query->count();
        $completed = $query->where('completed_reading', true)->count();

        return $total > 0 ? ($completed / $total) * 100 : 0;
    }

    // Methods
    public function markAsCompleted()
    {
        $this->completed_reading = true;
        $this->save();
    }

    public function updateReadingTime($seconds)
    {
        $this->reading_time_seconds = $seconds;
        $this->save();
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::created(function ($view) {
            // You could trigger analytics events here
            // For example, update post view count
            $view->post->incrementViews();
        });
    }
}
