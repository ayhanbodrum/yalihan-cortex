<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * 🧠 TKGM LEARNING PATTERN MODEL
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 *
 * Learning Engine'in öğrendiği pattern'ler burada saklanır.
 *
 * Pattern Types:
 * - price_kaks: KAKS-Fiyat korelasyonu
 * - location_premium: Lokasyon premium değeri
 * - imar_effect: İmar durumu etkisi
 * - velocity: Satış hızı analizi
 * - roi: Yatırım getirisi
 *
 * @property int $id
 * @property string $pattern_type
 * @property int|null $il_id
 * @property int|null $ilce_id
 * @property int|null $mahalle_id
 * @property array $pattern_data
 * @property int $sample_count
 * @property float $confidence_level
 * @property string $last_calculated_at
 * @property string $last_updated_at
 * @property int $prediction_count
 * @property float|null $prediction_accuracy
 * @property int $successful_predictions
 * @property int $status
 *
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 */
class TkgmLearningPattern extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tkgm_learning_patterns';

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // 🎯 PATTERN TYPES (Constants)
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public const TYPE_PRICE_KAKS = 'price_kaks';
    public const TYPE_LOCATION_PREMIUM = 'location_premium';
    public const TYPE_IMAR_EFFECT = 'imar_effect';
    public const TYPE_VELOCITY = 'velocity';
    public const TYPE_ROI = 'roi';

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // ✅ CONTEXT7: FILLABLE FIELDS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    protected $fillable = [
        'pattern_type',
        'il_id',
        'ilce_id',
        'mahalle_id',
        'pattern_data',
        'sample_count',
        'confidence_level',
        'last_calculated_at',
        'last_updated_at',
        'prediction_count',
        'prediction_accuracy',
        'successful_predictions',
        'status',
    ];

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // ✅ CONTEXT7: CASTS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    protected $casts = [
        'il_id' => 'integer',
        'ilce_id' => 'integer',
        'mahalle_id' => 'integer',
        'pattern_data' => 'array',
        'sample_count' => 'integer',
        'confidence_level' => 'decimal:2',
        'last_calculated_at' => 'datetime',
        'last_updated_at' => 'datetime',
        'prediction_count' => 'integer',
        'prediction_accuracy' => 'decimal:2',
        'successful_predictions' => 'integer',
        'status' => 'boolean',
    ];

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // 🔗 RELATIONSHIPS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    /**
     * İl ilişkisi
     */
    public function il()
    {
        return $this->belongsTo(\App\Models\Il::class, 'il_id');
    }

    /**
     * İlçe ilişkisi
     */
    public function ilce()
    {
        return $this->belongsTo(\App\Models\Ilce::class, 'ilce_id');
    }

    /**
     * Mahalle ilişkisi
     */
    public function mahalle()
    {
        return $this->belongsTo(\App\Models\Mahalle::class, 'mahalle_id');
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // 📊 SCOPES
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    /**
     * Belirli pattern tipi
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('pattern_type', $type);
    }

    /**
     * Belirli lokasyon
     */
    public function scopeLocation($query, $ilId, $ilceId = null, $mahalleId = null)
    {
        $query->where('il_id', $ilId);

        if ($ilceId) {
            $query->where('ilce_id', $ilceId);
        }

        if ($mahalleId) {
            $query->where('mahalle_id', $mahalleId);
        }

        return $query;
    }

    /**
     * Yüksek güven seviyesi (>= %70)
     */
    public function scopeHighConfidence($query)
    {
        return $query->where('confidence_level', '>=', 70);
    }

    /**
     * Aktif pattern'ler
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // 🧮 COMPUTED PROPERTIES
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    /**
     * Pattern güvenilir mi?
     */
    public function getIsReliableAttribute(): bool
    {
        return $this->confidence_level >= 70 && $this->sample_count >= 5;
    }

    /**
     * Pattern'in yaşı (gün olarak)
     */
    public function getAgeInDaysAttribute(): int
    {
        return now()->diffInDays($this->last_updated_at);
    }

    /**
     * Pattern güncel mi? (30 gün içinde güncellenmiş)
     */
    public function getIsFreshAttribute(): bool
    {
        return $this->age_in_days <= 30;
    }
}
