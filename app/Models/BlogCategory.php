<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'sort_order',
        'status',
        'meta',
    ];

    protected $casts = [
        'status' => 'boolean',
        'meta' => 'array',
        'sort_order' => 'integer',
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
        return $query->orderBy('sort_order')->orderBy('name');
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
}
