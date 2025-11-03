<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIChatLog extends Model
{
    use HasFactory;

    protected $table = 'ai_chat_logs';

    protected $fillable = [
        'session_id',
        'user_id',
        'message_type',
        'message',
        'ai_response',
        'context',
        'ai_model',
        'tokens_used',
        'cost',
        'response_time_ms',
        'one_cikan', // Context7: status → one_cikan
        'error_message',
        'metadata',
        'processed_at',
    ];

    protected $casts = [
        'context' => 'array',
        'metadata' => 'array',
        'tokens_used' => 'integer',
        'cost' => 'decimal:6',
        'response_time_ms' => 'integer',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'one_cikan' => false, // Context7: status → one_cikan
        'tokens_used' => 0,
        'cost' => 0,
        'response_time_ms' => 0,
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByMessageType($query, $type)
    {
        return $query->where('message_type', $type);
    }

    public function scopeByOneCikan($query, $oneCikan)
    {
        // Context7: status yerine one_cikan kullanıldı
        return $query->where('one_cikan', $oneCikan);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUserMessages($query)
    {
        return $query->where('message_type', 'user');
    }

    public function scopeAiMessages($query)
    {
        return $query->where('message_type', 'ai');
    }

    public function scopeSystemMessages($query)
    {
        return $query->where('message_type', 'system');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year);
    }

    public function scopeExpensive($query, $minCost = 0.01)
    {
        return $query->where('cost', '>=', $minCost);
    }

    public function scopeSlow($query, $minTime = 5000)
    {
        return $query->where('response_time_ms', '>=', $minTime);
    }

    // Accessors
    public function getMessagePreviewAttribute()
    {
        return \Illuminate\Support\Str::limit($this->message, 100);
    }

    public function getResponsePreviewAttribute()
    {
        return \Illuminate\Support\Str::limit($this->ai_response, 100);
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Bekliyor',
            'completed' => 'Tamamlandı',
            'failed' => 'Başarısız',
            'timeout' => 'Zaman Aşımı',
        ];

        return $labels[$this->status] ?? 'Bilinmiyor';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'completed' => 'success',
            'failed' => 'danger',
            'timeout' => 'secondary',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getMessageTypeLabelAttribute()
    {
        $labels = [
            'user' => 'Kullanıcı',
            'ai' => 'AI Asistan',
            'system' => 'Sistem',
        ];

        return $labels[$this->message_type] ?? 'Bilinmiyor';
    }

    public function getResponseTimeSecondsAttribute()
    {
        return round($this->response_time_ms / 1000, 2);
    }

    public function getCostFormattedAttribute()
    {
        return number_format($this->cost, 4).' TL';
    }

    public function getProcessingDurationAttribute()
    {
        if (! $this->processed_at) {
            return null;
        }

        return $this->created_at->diffInSeconds($this->processed_at);
    }

    // Methods
    public function markAsCompleted($aiResponse, $tokensUsed = 0, $responseTime = 0, $cost = 0)
    {
        $this->update([
            'ai_response' => $aiResponse,
            'tokens_used' => $tokensUsed,
            'response_time_ms' => $responseTime,
            'cost' => $cost,
            'status' => 'completed',
            'processed_at' => Carbon::now(),
        ]);
    }

    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'processed_at' => Carbon::now(),
        ]);
    }

    public function markAsTimeout()
    {
        $this->update([
            'status' => 'timeout',
            'error_message' => 'Request timed out',
            'processed_at' => Carbon::now(),
        ]);
    }

    public function addContext($key, $value)
    {
        $context = $this->context ?? [];
        $context[$key] = $value;
        $this->update(['context' => $context]);
    }

    public function getContext($key, $default = null)
    {
        return data_get($this->context, $key, $default);
    }

    public function addMetadata($key, $value)
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->update(['metadata' => $metadata]);
    }

    public function getMetadata($key, $default = null)
    {
        return data_get($this->metadata, $key, $default);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isTimeout()
    {
        return $this->status === 'timeout';
    }

    public function isUserMessage()
    {
        return $this->message_type === 'user';
    }

    public function isAiMessage()
    {
        return $this->message_type === 'ai';
    }

    public function isSystemMessage()
    {
        return $this->message_type === 'system';
    }

    // Static methods
    public static function createUserMessage($sessionId, $userId, $message, $context = [])
    {
        return static::create([
            'session_id' => $sessionId,
            'user_id' => $userId,
            'message_type' => 'user',
            'message' => $message,
            'context' => $context,
        ]);
    }

    public static function createAiMessage($sessionId, $userId, $message, $context = [])
    {
        return static::create([
            'session_id' => $sessionId,
            'user_id' => $userId,
            'message_type' => 'ai',
            'message' => $message,
            'context' => $context,
            'status' => 'completed',
            'processed_at' => Carbon::now(),
        ]);
    }

    public static function createSystemMessage($sessionId, $userId, $message, $context = [])
    {
        return static::create([
            'session_id' => $sessionId,
            'user_id' => $userId,
            'message_type' => 'system',
            'message' => $message,
            'context' => $context,
            'status' => 'completed',
            'processed_at' => Carbon::now(),
        ]);
    }

    public static function getSessionHistory($sessionId, $limit = 50)
    {
        return static::bySession($sessionId)
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();
    }

    public static function getUserStats($userId, $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);

        return [
            'total_messages' => static::byUser($userId)->where('created_at', '>=', $startDate)->count(),
            'user_messages' => static::byUser($userId)->userMessages()->where('created_at', '>=', $startDate)->count(),
            'ai_responses' => static::byUser($userId)->aiMessages()->where('created_at', '>=', $startDate)->count(),
            'total_tokens' => static::byUser($userId)->where('created_at', '>=', $startDate)->sum('tokens_used'),
            'total_cost' => static::byUser($userId)->where('created_at', '>=', $startDate)->sum('cost'),
            'avg_response_time' => static::byUser($userId)->completed()->where('created_at', '>=', $startDate)->avg('response_time_ms'),
            'failed_requests' => static::byUser($userId)->failed()->where('created_at', '>=', $startDate)->count(),
        ];
    }

    public static function getSystemStats($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);

        return [
            'total_conversations' => static::where('created_at', '>=', $startDate)->distinct('session_id')->count(),
            'total_messages' => static::where('created_at', '>=', $startDate)->count(),
            'total_users' => static::where('created_at', '>=', $startDate)->distinct('user_id')->count(),
            'total_tokens' => static::where('created_at', '>=', $startDate)->sum('tokens_used'),
            'total_cost' => static::where('created_at', '>=', $startDate)->sum('cost'),
            'avg_response_time' => static::completed()->where('created_at', '>=', $startDate)->avg('response_time_ms'),
            'success_rate' => static::where('created_at', '>=', $startDate)->count() > 0
                ? (static::completed()->where('created_at', '>=', $startDate)->count() / static::where('created_at', '>=', $startDate)->count()) * 100
                : 0,
            'popular_models' => static::where('created_at', '>=', $startDate)
                ->whereNotNull('ai_model')
                ->groupBy('ai_model')
                ->selectRaw('ai_model, count(*) as usage_count')
                ->orderBy('usage_count', 'desc')
                ->limit(5)
                ->pluck('usage_count', 'ai_model'),
        ];
    }

    public static function cleanupOldLogs($days = 90)
    {
        $cutoffDate = Carbon::now()->subDays($days);

        return static::where('created_at', '<', $cutoffDate)->delete();
    }
}
