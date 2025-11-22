<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AI Mesaj Model
 *
 * Context7 Standardı: C7-AI-MESSAGE-MODEL-2025-11-20
 *
 * AI tarafından üretilen mesaj taslaklarını yönetir.
 * Tüm mesajlar onaylanmadan gönderilmez.
 */
class AIMessage extends Model
{
    use HasFactory;

    protected $table = 'ai_messages';

    protected $fillable = [
        'conversation_id',
        'communication_id',
        'channel',
        'role',
        'content',
        'status',
        'ai_model_used',
        'ai_generated_at',
        'approved_by',
        'approved_at',
        'sent_at',
    ];

    protected $casts = [
        'ai_generated_at' => 'datetime',
        'approved_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * Konuşma ilişkisi
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AIConversation::class);
    }

    /**
     * İletişim ilişkisi
     */
    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }

    /**
     * Onaylayan kullanıcı ilişkisi
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: Draft mesajlar
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope: Onay bekleyen mesajlar
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    /**
     * Scope: Onaylanmış mesajlar
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Gönderilmiş mesajlar
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope: Kanal bazlı
     */
    public function scopeByChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Mesajı onayla
     */
    public function approve(int $userId): bool
    {
        return $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    /**
     * Mesajı gönder
     */
    public function send(): bool
    {
        return $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Mesajın onaylanmış olup olmadığını kontrol et
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved' || $this->status === 'sent';
    }
}
