<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Feature extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'feature_category_id', // Context7: Veritabanında feature_category_id var
        'name',
        'slug',
        'description',
        'type', // Context7: Veritabanında type var
        'options', // Context7: Veritabanında options var (JSON)
        'unit', // Context7: Veritabanında unit var
        'is_required',
        'is_filterable',
        'is_searchable',
        'order',
        'status', // Context7: Veritabanında status var
        // Legacy fields (backward compatibility)
        'category_id',
        'field_type',
        'field_options',
        'field_unit',
        'field_icon',
        'validation_rules',
        'ai_auto_fill',
        'ai_suggestion',
        'ai_calculation',
        'ai_prompt',
        'enabled',
        'show_in_listing',
        'show_in_detail',
        'show_in_filter',
    ];

    protected $casts = [
        'options' => 'array', // Context7: Veritabanında options var
        'field_options' => 'array', // Legacy
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'is_searchable' => 'boolean',
        'ai_auto_fill' => 'boolean',
        'ai_suggestion' => 'boolean',
        'ai_calculation' => 'boolean',
        'enabled' => 'boolean',
        'status' => 'boolean', // Context7: Veritabanında status var
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
     * Context7: Veritabanında feature_category_id kolonu var
     */
    public function category()
    {
        return $this->belongsTo(FeatureCategory::class, 'feature_category_id');
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
     * Context7: Schema kontrolü ile status/enabled
     */
    public function scopeEnabled($query)
    {
        if (Schema::hasColumn('features', 'status')) {
            return $query->where('status', true);
        } elseif (Schema::hasColumn('features', 'enabled')) {
            return $query->where('enabled', true);
        }
        return $query;
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
