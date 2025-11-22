<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AI İlan Taslağı Model
 *
 * Context7 Standardı: C7-AI-ILAN-TASLAGI-MODEL-2025-11-20
 *
 * AI tarafından üretilen ilan taslaklarını yönetir.
 * Tüm taslaklar onaylanmadan önce 'draft' durumunda kalır.
 */
class AIIlanTaslagi extends Model
{
    use HasFactory;

    protected $table = 'ai_ilan_taslaklari';

    protected $fillable = [
        'ilan_id',
        'danisman_id',
        'status',
        'ai_response',
        'ai_model_used',
        'ai_prompt_version',
        'ai_generated_at',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'ai_response' => 'array',
        'ai_generated_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * İlan ilişkisi
     */
    public function ilan(): BelongsTo
    {
        return $this->belongsTo(Ilan::class);
    }

    /**
     * Danışman ilişkisi
     */
    public function danisman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'danisman_id');
    }

    /**
     * Onaylayan kullanıcı ilişkisi
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: Draft taslaklar
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope: Onay bekleyen taslaklar
     */
    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending_review');
    }

    /**
     * Scope: Onaylanmış taslaklar
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Yayınlanmış taslaklar
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Taslağı onayla
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
     * Taslağı yayınla
     */
    public function publish(): bool
    {
        return $this->update([
            'status' => 'published',
        ]);
    }

    /**
     * Taslağın onaylanmış olup olmadığını kontrol et
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved' || $this->status === 'published';
    }
}
