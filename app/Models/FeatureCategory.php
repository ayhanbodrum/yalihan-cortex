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
        'display_order', // Context7: Veritabanında display_order kolonu var
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'display_order' => 'integer', // Context7: Veritabanında display_order kolonu var
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
     * Context7: ONLY status field (enabled FORBIDDEN)
     */
    public function enabledFeatures()
    {
        $query = $this->features();

        // ✅ Context7: ONLY check status field
        // ❌ REMOVED: enabled field check (Context7 violation)
        if (Schema::hasColumn('features', 'status')) {
            return $query->where('status', true)->orderBy('display_order');
        }

        return $query->orderBy('display_order');
    }

    /**
     * Scope: Only enabled categories
     * Context7: ONLY status field (enabled FORBIDDEN)
     */
    public function scopeEnabled($query)
    {
        // ✅ Context7: ONLY check status field
        // ❌ REMOVED: enabled field check (Context7 violation)
        if (Schema::hasColumn('feature_categories', 'status')) {
            return $query->where('status', true);
        }

        // If status column doesn't exist, return all (graceful degradation)
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
        return $query->orderBy('display_order')->orderBy('name'); // Context7: display_order kullan
    }
}
