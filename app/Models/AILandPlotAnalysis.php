<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AI Arsa Analizi Model
 *
 * Context7 Standardı: C7-AI-LAND-PLOT-ANALYSIS-MODEL-2025-11-20
 */
class AILandPlotAnalysis extends Model
{
    use HasFactory;

    protected $table = 'ai_land_plot_analyses';

    protected $fillable = [
        'ilan_id',
        'analysis_type',
        'analysis_data',
        'recommendations',
        'market_data',
        'confidence_score',
        'price_score',
        'risk_score',
        'market_score',
        'suggested_price_min',
        'suggested_price_max',
        'current_price',
        'ai_model_used',
        'ai_prompt_version',
        'ai_generated_at',
        'created_by',
    ];

    protected $casts = [
        'analysis_data' => 'array',
        'recommendations' => 'array',
        'market_data' => 'array',
        'confidence_score' => 'decimal:2',
        'price_score' => 'decimal:2',
        'risk_score' => 'decimal:2',
        'market_score' => 'decimal:2',
        'suggested_price_min' => 'decimal:2',
        'suggested_price_max' => 'decimal:2',
        'current_price' => 'decimal:2',
        'ai_generated_at' => 'datetime',
    ];

    /**
     * İlan ilişkisi
     */
    public function ilan(): BelongsTo
    {
        return $this->belongsTo(Ilan::class);
    }

    /**
     * Oluşturan kullanıcı
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
