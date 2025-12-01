<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FeatureAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'feature_id',
        'assignable_type',
        'assignable_id',
        'value',
        'is_required',
        'is_visible',
        'display_order', // Context7: order → display_order
        'conditional_logic',
        'group_name',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_visible' => 'boolean',
        'display_order' => 'integer', // Context7: order → display_order
        'conditional_logic' => 'array',
    ];

    /**
     * Get the feature
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    /**
     * Get the assignable model (polymorphic)
     */
    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope: Visible only
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope: Required only
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope: Ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('id'); // Context7: order → display_order
    }

    /**
     * Scope: Filter by assignable type
     */
    public function scopeForType($query, string $type)
    {
        return $query->where('assignable_type', $type);
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
     * Get grouped assignments
     */
    public static function getGrouped($assignableType, $assignableId)
    {
        return static::where('assignable_type', $assignableType)
            ->where('assignable_id', $assignableId)
            ->visible()
            ->withFeature()
            ->ordered()
            ->get()
            ->groupBy('group_name');
    }

    /**
     * Check if conditional logic is met
     */
    public function checkConditionalLogic(array $values): bool
    {
        if (empty($this->conditional_logic)) {
            return true;
        }

        foreach ($this->conditional_logic as $condition) {
            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? '=';
            $value = $condition['value'] ?? null;

            if (! isset($values[$field])) {
                return false;
            }

            switch ($operator) {
                case '=':
                    if ($values[$field] != $value) {
                        return false;
                    }
                    break;
                case '!=':
                    if ($values[$field] == $value) {
                        return false;
                    }
                    break;
                case '>':
                    if ($values[$field] <= $value) {
                        return false;
                    }
                    break;
                case '<':
                    if ($values[$field] >= $value) {
                        return false;
                    }
                    break;
                case 'in':
                    if (! in_array($values[$field], (array) $value)) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }
}
