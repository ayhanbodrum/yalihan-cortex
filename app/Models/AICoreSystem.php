<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * AI Core System Modeli
 *
 * AI sistem yapılandırmaları ve öğrenilen prompt'ları saklar
 * Context7 Compliant
 */
class AICoreSystem extends Model
{
    use HasFactory;

    protected $table = 'ai_core_system';

    protected $fillable = [
        'context',
        'task_type',
        'prompt_template',
        'success_rate',
        'usage_count',
        'status', // Context7: is_active → status (migration: 2025_11_11_103355)
    ];

    protected $casts = [
        'success_rate' => 'decimal:4',
        'usage_count' => 'integer',
        'status' => 'boolean', // Context7: is_active → status
    ];

    public $timestamps = true;

    /**
     * Aktif kayıtlar
     * Context7: is_active → status
     */
    public function scopeActive($query)
    {
        return $query->where('status', true); // Context7: is_active → status
    }

    /**
     * Belirli context için kayıtlar
     */
    public function scopeByContext($query, $context)
    {
        return $query->where('context', $context);
    }

    /**
     * En başarılı prompt'ları getir
     */
    public function scopeBestPerforming($query)
    {
        return $query->orderBy('success_rate', 'desc');
    }
}
