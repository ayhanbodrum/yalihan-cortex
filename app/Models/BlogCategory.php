<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'display_order', // Context7: order → display_order (migration: 2025_11_11_103353)
        'status',
        'meta',
    ];

    protected $casts = [
        'status' => 'boolean',
        'meta' => 'array',
        'display_order' => 'integer', // Context7: order → display_order
    ];

    // Auto-generate slug when name is set
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    // Relationships
    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    public function publishedPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class)->where('status', 'published')->where('published_at', '<=', now());
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name'); // Context7: order → display_order
    }

    // Accessors
    public function getPostCountAttribute()
    {
        return $this->posts()->count();
    }

    public function getPublishedPostCountAttribute()
    {
        return $this->publishedPosts()->count();
    }

    public function getUrlAttribute()
    {
        return route('blog.category', $this->slug);
    }

    // Mutators
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value ?: $this->name);
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::created(function ($category) {
            // ✅ CACHE INVALIDATION: Yeni kategori eklendiğinde cache'i temizle
            Cache::forget('blog_categories_stats');
        });

        static::updated(function ($category) {
            // ✅ CACHE INVALIDATION: Kategori güncellendiğinde cache'i temizle
            Cache::forget('blog_categories_stats');
        });

        static::deleted(function ($category) {
            // ✅ CACHE INVALIDATION: Kategori silindiğinde cache'i temizle
            Cache::forget('blog_categories_stats');
        });
    }
}
