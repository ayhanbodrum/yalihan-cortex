<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FeatureValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'feature_id',
        'valuable_type',
        'valuable_id',
        'value',
        'value_type',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * Get the feature
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    /**
     * Get the valuable model (polymorphic)
     */
    public function valuable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get typed value
     */
    public function getTypedValueAttribute()
    {
        return match ($this->value_type) {
            'integer' => (int) $this->value,
            'float' => (float) $this->value,
            'boolean' => (bool) $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Set typed value
     */
    public function setTypedValue($value)
    {
        $this->value_type = match (true) {
            is_int($value) => 'integer',
            is_float($value) => 'float',
            is_bool($value) => 'boolean',
            is_array($value) => 'json',
            default => 'string',
        };

        $this->value = is_array($value) ? json_encode($value) : $value;
        return $this;
    }

    /**
     * Scope: Filter by valuable type
     */
    public function scopeForType($query, string $type)
    {
        return $query->where('valuable_type', $type);
    }

    /**
     * Scope: With feature details
     */
    public function scopeWithFeature($query)
    {
        return $query->with(['feature' => function ($q) {
            $q->with('category');
        }]);
    }

    /**
     * Get all values for a model as key-value array
     */
    public static function getForModel($model): array
    {
        return static::where('valuable_type', get_class($model))
            ->where('valuable_id', $model->id)
            ->withFeature()
            ->get()
            ->mapWithKeys(function ($featureValue) {
                return [$featureValue->feature->slug => $featureValue->typed_value];
            })
            ->toArray();
    }

    /**
     * Set value for a model and feature
     */
    public static function setForModel($model, Feature $feature, $value)
    {
        $featureValue = static::updateOrCreate(
            [
                'feature_id' => $feature->id,
                'valuable_type' => get_class($model),
                'valuable_id' => $model->id,
            ],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'value_type' => match (true) {
                    is_int($value) => 'integer',
                    is_float($value) => 'float',
                    is_bool($value) => 'boolean',
                    is_array($value) => 'json',
                    default => 'string',
                },
            ]
        );

        return $featureValue;
    }

    /**
     * Bulk set values for a model
     */
    public static function bulkSetForModel($model, array $values)
    {
        // ✅ PERFORMANCE FIX: N+1 query önlendi - Tüm feature'ları tek query'de al
        $featureSlugs = array_keys($values);
        $features = Feature::whereIn('slug', $featureSlugs)->get()->keyBy('slug');

        $valuableType = get_class($model);
        $valuableId = $model->id;

        // ✅ PERFORMANCE FIX: Bulk insert/update için hazırlık
        $toInsert = [];
        $toUpdate = [];

        // Mevcut kayıtları tek query'de al
        $existingValues = static::where('valuable_type', $valuableType)
            ->where('valuable_id', $valuableId)
            ->whereIn('feature_id', $features->pluck('id')->toArray())
            ->get()
            ->keyBy('feature_id');

        foreach ($values as $featureSlug => $value) {
            $feature = $features->get($featureSlug);
            if (!$feature) {
                continue;
            }

            $valueString = is_array($value) ? json_encode($value) : $value;
            $valueType = match (true) {
                is_int($value) => 'integer',
                is_float($value) => 'float',
                is_bool($value) => 'boolean',
                is_array($value) => 'json',
                default => 'string',
            };

            $existing = $existingValues->get($feature->id);
            if ($existing) {
                // Update için hazırla
                $toUpdate[] = [
                    'id' => $existing->id,
                    'value' => $valueString,
                    'value_type' => $valueType,
                ];
            } else {
                // Insert için hazırla
                $toInsert[] = [
                    'feature_id' => $feature->id,
                    'valuable_type' => $valuableType,
                    'valuable_id' => $valuableId,
                    'value' => $valueString,
                    'value_type' => $valueType,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // ✅ PERFORMANCE FIX: Bulk insert
        if (!empty($toInsert)) {
            static::insert($toInsert);
        }

        // ✅ PERFORMANCE FIX: Bulk update (CASE WHEN ile)
        if (!empty($toUpdate)) {
            $valueCases = [];
            $valueTypeCases = [];
            $bindings = [];
            $ids = [];

            foreach ($toUpdate as $update) {
                $valueCases[] = "WHEN ? THEN ?";
                $valueTypeCases[] = "WHEN ? THEN ?";
                $bindings[] = $update['id'];
                $bindings[] = $update['value'];
                $bindings[] = $update['id'];
                $bindings[] = $update['value_type'];
                $ids[] = $update['id'];
            }

            $idsPlaceholder = implode(',', array_fill(0, count($ids), '?'));
            $valueCasesSql = implode(' ', $valueCases);
            $valueTypeCasesSql = implode(' ', $valueTypeCases);

            \Illuminate\Support\Facades\DB::statement(
                "UPDATE feature_values
                 SET value = CASE id {$valueCasesSql} END,
                     value_type = CASE id {$valueTypeCasesSql} END,
                     updated_at = NOW()
                 WHERE id IN ({$idsPlaceholder})",
                array_merge($bindings, $ids)
            );
        }
    }
}
