<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AI Sözleşme Taslağı Model
 *
 * Context7 Standardı: C7-AI-CONTRACT-DRAFT-MODEL-2025-11-20
 *
 * AI tarafından üretilen sözleşme taslaklarını yönetir.
 * Tüm taslaklar onaylanmadan kullanılmaz.
 */
class AIContractDraft extends Model
{
    use HasFactory;

    protected $table = 'ai_contract_drafts';

    protected $fillable = [
        'contract_type',
        'property_id',
        'kisi_id',
        'status',
        'content',
        'ai_model_used',
        'ai_generated_at',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'ai_generated_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * İlan ilişkisi
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Ilan::class, 'property_id');
    }

    /**
     * Kişi ilişkisi
     */
    public function kisi(): BelongsTo
    {
        return $this->belongsTo(Kisi::class, 'kisi_id');
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
     * Scope: Reddedilmiş taslaklar
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope: Sözleşme tipi bazlı
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('contract_type', $type);
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
     * Taslağı reddet
     */
    public function reject(int $userId): bool
    {
        return $this->update([
            'status' => 'rejected',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    /**
     * Taslağın onaylanmış olup olmadığını kontrol et
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
