<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Feature extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'field_type',
        'field_options',
        'field_unit',
        'field_icon',
        'is_required',
        'is_filterable',
        'is_searchable',
        'validation_rules',
        'ai_auto_fill',
        'ai_suggestion',
        'ai_calculation',
        'ai_prompt',
        'order',
        'enabled',
        'show_in_listing',
        'show_in_detail',
        'show_in_filter',
    ];

    protected $casts = [
        'field_options' => 'array',
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'is_searchable' => 'boolean',
        'ai_auto_fill' => 'boolean',
        'ai_suggestion' => 'boolean',
        'ai_calculation' => 'boolean',
        'enabled' => 'boolean',
        'show_in_listing' => 'boolean',
        'show_in_detail' => 'boolean',
        'show_in_filter' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($feature) {
            if (empty($feature->slug)) {
                $feature->slug = Str::slug($feature->name);
            }
        });
    }

    /**
     * Get the category that owns the feature
     */
    public function category()
    {
        return $this->belongsTo(FeatureCategory::class, 'category_id');
    }

    /**
     * Get all assignments for this feature
     */
    public function assignments()
    {
        return $this->hasMany(FeatureAssignment::class);
    }

    /**
     * Get all values for this feature
     */
    public function values()
    {
        return $this->hasMany(FeatureValue::class);
    }

    /**
     * Get assignments for a specific type
     */
    public function assignmentsFor(string $type)
    {
        return $this->assignments()->where('assignable_type', $type);
    }

    /**
     * Check if feature is assigned to a model
     */
    public function isAssignedTo($model): bool
    {
        return $this->assignments()
            ->where('assignable_type', get_class($model))
            ->where('assignable_id', $model->id)
            ->exists();
    }

    /**
     * Assign this feature to a model
     */
    public function assignTo($model, array $config = [])
    {
        return FeatureAssignment::updateOrCreate(
            [
                'feature_id' => $this->id,
                'assignable_type' => get_class($model),
                'assignable_id' => $model->id,
            ],
            array_merge([
                'is_required' => $this->is_required,
                'is_visible' => true,
                'order' => $this->order,
            ], $config)
        );
    }

    /**
     * Remove assignment from a model
     */
    public function unassignFrom($model)
    {
        return $this->assignments()
            ->where('assignable_type', get_class($model))
            ->where('assignable_id', $model->id)
            ->delete();
    }

    /**
     * Scope: Only enabled features
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope: Filter by field type
     */
    public function scopeOfFieldType($query, string $type)
    {
        return $query->where('field_type', $type);
    }

    /**
     * Scope: Filterable features
     */
    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }

    /**
     * Scope: Searchable features
     */
    public function scopeSearchable($query)
    {
        return $query->where('is_searchable', true);
    }

    /**
     * Scope: Required features
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
        return $query->orderBy('order')->orderBy('name');
    }

    /**
     * Scope: With category
     */
    public function scopeWithCategory($query)
    {
        return $query->with('category');
    }

    /**
     * Get field options as array
     */
    public function getFieldOptionsArrayAttribute()
    {
        return is_array($this->field_options) ? $this->field_options : [];
    }

    /**
     * Check if feature has AI capabilities
     */
    public function hasAiCapabilities(): bool
    {
        return $this->ai_auto_fill || $this->ai_suggestion || $this->ai_calculation;
    }
}
