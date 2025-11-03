<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class BlogTag extends Model
{
    use HasFactory;

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
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

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
}
