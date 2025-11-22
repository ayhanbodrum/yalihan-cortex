<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Communication Model
 *
 * Context7 Standardı: C7-COMMUNICATION-MODEL-2025-11-20
 *
 * Çok kanallı iletişim kayıtlarını yönetir (polymorphic).
 */
class Communication extends Model
{
    use HasFactory;

    protected $fillable = [
        'communicable_type',
        'communicable_id',
        'channel',
        'message',
        'sender_name',
        'sender_phone',
        'sender_email',
        'sender_id',
        'status',
        'priority',
        'ai_analysis',
        'ai_analyzed_at',
        'assigned_to',
        'created_by',
    ];

    protected $casts = [
        'ai_analysis' => 'array',
        'ai_analyzed_at' => 'datetime',
    ];

    /**
     * Polymorphic ilişki: Hangi kayıtla ilişkili
     */
    public function communicable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Atanan kullanıcı
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Oluşturan kullanıcı
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * AI mesajları
     */
    public function aiMessages(): HasMany
    {
        return $this->hasMany(AIMessage::class, 'communication_id');
    }

    /**
     * Scope: Yeni mesajlar
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope: Okunmuş mesajlar
     */
    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    /**
     * Scope: Kanal bazında filtreleme
     */
    public function scopeChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Mesajı okundu olarak işaretle
     */
    public function markAsRead(): void
    {
        $this->update(['status' => 'read']);
    }

    /**
     * Mesajı cevaplandı olarak işaretle
     */
    public function markAsReplied(): void
    {
        $this->update(['status' => 'replied']);
    }
}
