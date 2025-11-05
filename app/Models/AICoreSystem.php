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
        'is_active',
        'enabled',
    ];

    protected $casts = [
        'success_rate' => 'decimal:4',
        'usage_count' => 'integer',
        'is_active' => 'boolean',
        'enabled' => 'boolean',
    ];

    public $timestamps = true;

    /**
     * Aktif kayıtlar
     */
    public function scopeActive($query)
    {
        return $query->where('enabled', true)->where('is_active', true);
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
