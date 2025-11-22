<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * AI Konuşma Model
 *
 * Context7 Standardı: C7-AI-CONVERSATION-MODEL-2025-11-20
 *
 * AI ile yapılan konuşmaları yönetir.
 * Messages JSON formatında saklanır.
 */
class AIConversation extends Model
{
    use HasFactory;

    protected $table = 'ai_conversations';

    protected $fillable = [
        'user_id',
        'channel',
        'messages',
        'status',
    ];

    protected $casts = [
        'messages' => 'array',
    ];

    /**
     * Kullanıcı ilişkisi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mesajlar ilişkisi
     */
    public function aiMessages(): HasMany
    {
        return $this->hasMany(AIMessage::class, 'conversation_id');
    }

    /**
     * Scope: Aktif konuşmalar
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Kapatılmış konuşmalar
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope: Arşivlenmiş konuşmalar
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Scope: Kanal bazlı
     */
    public function scopeByChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Konuşmayı kapat
     */
    public function close(): bool
    {
        return $this->update([
            'status' => 'closed',
        ]);
    }

    /**
     * Konuşmayı arşivle
     */
    public function archive(): bool
    {
        return $this->update([
            'status' => 'archived',
        ]);
    }
}
