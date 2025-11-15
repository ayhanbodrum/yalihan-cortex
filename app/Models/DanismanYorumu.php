<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * DanismanYorumu Model
 * Context7 Compliance: status field, kisi_id (not musteri_id)
 */
class DanismanYorumu extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'danisman_yorumlari';

    protected $fillable = [
        'danisman_id',
        'kisi_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'rating',
        'yorum',
        'status',
        'ip_address',
        'user_agent',
        'moderated_by',
        'moderated_at',
        'moderation_reason',
        'like_count',
        'dislike_count',
    ];

    protected $casts = [
        'rating' => 'integer',
        'like_count' => 'integer',
        'dislike_count' => 'integer',
        'moderated_at' => 'datetime',
    ];

    /**
     * Danışman ilişkisi
     */
    public function danisman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'danisman_id');
    }

    /**
     * Kişi ilişkisi (yorum yapan)
     * Context7: kisi_id, not musteri_id
     */
    public function kisi(): BelongsTo
    {
        return $this->belongsTo(Kisi::class, 'kisi_id');
    }

    /**
     * Moderator ilişkisi
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Scopes
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Accessors
     */
    public function getAuthorNameAttribute(): string
    {
        return $this->kisi ? $this->kisi->tam_ad : ($this->guest_name ?? 'Misafir');
    }

    public function getAuthorEmailAttribute(): ?string
    {
        return $this->kisi ? $this->kisi->email : $this->guest_email;
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Yorumu onayla
     */
    public function approve(?int $moderatorId = null): void
    {
        $this->status = 'approved';
        $this->moderated_by = $moderatorId ?? auth()->id();
        $this->moderated_at = now();
        $this->save();
    }

    /**
     * Yorumu reddet
     */
    public function reject(?int $moderatorId = null, ?string $reason = null): void
    {
        $this->status = 'rejected';
        $this->moderated_by = $moderatorId ?? auth()->id();
        $this->moderated_at = now();
        $this->moderation_reason = $reason;
        $this->save();
    }
}
