<?php

namespace App\Models;

use App\Traits\HasActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BlogTag extends Model
{
    use HasActiveScope, HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'status',
        'usage_count',
    ];

    protected $casts = [
        'status' => 'boolean',
        'usage_count' => 'integer',
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
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_tags');
    }

    public function publishedPosts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_tags')
            ->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    // Scopes
    // ✅ REFACTORED: scopeActive moved to HasActiveScope trait

    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('usage_count', 'desc')->limit($limit);
    }

    // Methods
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    public function decrementUsage()
    {
        $this->decrement('usage_count');
    }

    public function updateUsageCount()
    {
        $this->usage_count = $this->posts()->count();
        $this->save();
    }

    // Accessors
    public function getUrlAttribute()
    {
        return route('blog.tag', $this->slug);
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

        static::created(function ($tag) {
            // ✅ CACHE INVALIDATION: Yeni etiket eklendiğinde cache'i temizle
            Cache::forget('blog_tags_stats');
        });

        static::updated(function ($tag) {
            // ✅ CACHE INVALIDATION: Etiket güncellendiğinde cache'i temizle
            Cache::forget('blog_tags_stats');
        });

        static::deleted(function ($tag) {
            // ✅ CACHE INVALIDATION: Etiket silindiğinde cache'i temizle
            Cache::forget('blog_tags_stats');
        });
    }
}
