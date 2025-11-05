<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FeatureCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'icon',
        'order',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get all features in this category
     * Context7: Veritabanında feature_category_id kolonu var
     */
    public function features()
    {
        return $this->hasMany(Feature::class, 'feature_category_id');
    }

    /**
     * Get only enabled features
     * Context7: Schema kontrolü ile status/enabled
     */
    public function enabledFeatures()
    {
        $query = $this->features();
        if (Schema::hasColumn('features', 'status')) {
            return $query->where('status', true)->orderBy('order');
        } elseif (Schema::hasColumn('features', 'enabled')) {
            return $query->where('enabled', true)->orderBy('order');
        }
        return $query->orderBy('order');
    }

    /**
     * Scope: Only enabled categories
     * Context7: Schema kontrolü ile status/enabled
     */
    public function scopeEnabled($query)
    {
        if (Schema::hasColumn('feature_categories', 'status')) {
            return $query->where('status', true);
        } elseif (Schema::hasColumn('feature_categories', 'enabled')) {
            return $query->where('enabled', true);
        }
        return $query;
    }

    /**
     * Scope: Filter by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }
}
