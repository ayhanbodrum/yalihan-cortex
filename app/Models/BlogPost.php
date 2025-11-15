<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'featured_image_alt',
        'gallery',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'structured_data',
        'status',
        'published_at',
        'scheduled_at',
        'user_id',
        'blog_category_id',
        'view_count',
        'like_count',
        'comment_count',
        'reading_time',
        'is_featured',
        'is_sticky',
        'allow_comments',
        'is_breaking_news',
        'social_shares',
    ];

    protected $casts = [
        'gallery' => 'array',
        'meta_keywords' => 'array',
        'structured_data' => 'array',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'comment_count' => 'integer',
        'reading_time' => 'decimal:1',
        'is_featured' => 'boolean',
        'is_sticky' => 'boolean',
        'allow_comments' => 'boolean',
        'is_breaking_news' => 'boolean',
        'social_shares' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function author(): BelongsTo
    {
        return $this->user();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tags');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(BlogComment::class)->where('status', 'approved');
    }

    public function views(): HasMany
    {
        return $this->hasMany(BlogView::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled')->where('scheduled_at', '>', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeSticky($query)
    {
        return $query->where('is_sticky', true);
    }

    public function scopeBreakingNews($query)
    {
        return $query->where('is_breaking_news', true);
    }

    public function scopeRecent($query, $limit = 5)
    {
        return $query->published()->orderBy('published_at', 'desc')->limit($limit);
    }

    public function scopePopular($query, $limit = 5)
    {
        return $query->published()->orderBy('view_count', 'desc')->limit($limit);
    }

    public function scopeByCategory($query, $categorySlug)
    {
        return $query->whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }

    public function scopeByTag($query, $tagSlug)
    {
        return $query->whereHas('tags', function ($q) use ($tagSlug) {
            $q->where('slug', $tagSlug);
        });
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%")
                ->orWhere('content', 'like', "%{$term}%");
        });
    }

    // Accessors
    public function getUrlAttribute()
    {
        return route('blog.show', $this->slug);
    }

    public function getIsPublishedAttribute()
    {
        return $this->status === 'published' && $this->published_at <= now();
    }

    public function getExcerptOrContentAttribute()
    {
        return $this->excerpt ?: Str::limit(strip_tags($this->content), 160);
    }

    public function getReadingTimeFormattedAttribute()
    {
        if (! $this->reading_time) {
            return '1 dakika';
        }

        $minutes = (int) $this->reading_time;

        return $minutes.' dakika';
    }

    public function getPublishedDateFormattedAttribute()
    {
        return $this->published_at ? $this->published_at->format('d.m.Y') : null;
    }

    public function getPublishedTimeAttribute()
    {
        return $this->published_at ? $this->published_at->diffForHumans() : null;
    }

    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image ? asset('storage/'.$this->featured_image) : asset('images/blog-placeholder.jpg');
    }

    // Status Management Methods
    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_at' => $this->published_at ?: now(),
        ]);
    }

    public function unpublish()
    {
        $this->update([
            'status' => 'draft',
        ]);
    }

    public function archive()
    {
        $this->update([
            'status' => 'archived',
        ]);
    }

    public function schedule($dateTime)
    {
        $this->update([
            'status' => 'scheduled',
            'scheduled_at' => Carbon::parse($dateTime),
        ]);
    }

    // Interaction Methods
    public function recordView($user = null, $ipAddress = null)
    {
        $viewData = [
            'blog_post_id' => $this->id,
            'ip_address' => $ipAddress ?: request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->header('referer'),
            'created_at' => now(),
        ];

        if ($user) {
            $viewData['user_id'] = $user->id;
        }

        // Prevent duplicate views from same IP in last hour
        $recentView = BlogView::where('blog_post_id', $this->id)
            ->where('ip_address', $viewData['ip_address'])
            ->where('created_at', '>', now()->subHour())
            ->first();

        if (! $recentView) {
            BlogView::create($viewData);
            $this->increment('view_count');
        }
    }

    public function incrementViews()
    {
        $this->increment('view_count');
    }

    public function incrementLikes()
    {
        $this->increment('like_count');
    }

    public function incrementComments()
    {
        $this->increment('comment_count');
    }

    public function decrementComments()
    {
        $this->decrement('comment_count');
    }

    // Content Processing
    public function generateExcerpt($length = 160)
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }

        return Str::limit(strip_tags($this->content), $length);
    }

    public function calculateReadingTime()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = max(1, ceil($wordCount / 200)); // Average reading speed: 200 words per minute

        $this->update(['reading_time' => $minutes]);

        return $minutes;
    }

    public function updateCommentCount()
    {
        $this->comment_count = $this->approvedComments()->count();
        $this->save();
    }

    public function generateStructuredData()
    {
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $this->title,
            'description' => $this->generateExcerpt(),
            'author' => [
                '@type' => 'Person',
                'name' => $this->author->name,
            ],
            'datePublished' => $this->published_at ? $this->published_at->toISOString() : null,
            'dateModified' => $this->updated_at->toISOString(),
            'url' => $this->url,
        ];

        if ($this->featured_image) {
            $structuredData['image'] = [
                '@type' => 'ImageObject',
                'url' => asset('storage/'.$this->featured_image),
            ];
        }

        if ($this->category) {
            $structuredData['articleSection'] = $this->category->name;
        }

        $this->update(['structured_data' => $structuredData]);

        return $structuredData;
    }

    // SEO Methods
    public function getSeoTitle()
    {
        return $this->meta_title ?: $this->title;
    }

    public function getSeoDescription()
    {
        return $this->meta_description ?: $this->generateExcerpt();
    }

    public function getSeoKeywords()
    {
        if ($this->meta_keywords && is_array($this->meta_keywords)) {
            return implode(', ', $this->meta_keywords);
        }

        // Auto-generate from tags
        return $this->tags->pluck('name')->implode(', ');
    }

    // Related Content
    public function getRelatedPosts($limit = 4)
    {
        $query = static::published()
            ->where('id', '!=', $this->id);

        // Try to find posts in same category first
        if ($this->category) {
            $categoryPosts = $query->clone()
                ->where('blog_category_id', $this->category->id)
                ->limit($limit)
                ->get();

            if ($categoryPosts->count() >= $limit) {
                return $categoryPosts;
            }
        }

        // If not enough posts in same category, find by shared tags
        if ($this->tags->isNotEmpty()) {
            $tagPosts = $query->clone()
                ->whereHas('tags', function ($q) {
                    $q->whereIn('blog_tags.id', $this->tags->pluck('id'));
                })
                ->limit($limit)
                ->get();

            if ($tagPosts->count() >= $limit) {
                return $tagPosts;
            }
        }

        // Fallback to latest posts
        return $query->latest('published_at')
            ->limit($limit)
            ->get();
    }

    // Mutators
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value ?: $this->title);
    }

    // Auto-generate slug when title is set
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (! $post->reading_time && $post->content) {
                $post->calculateReadingTime();
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('content')) {
                $post->calculateReadingTime();
            }
        });

        static::created(function ($post) {
            // ✅ CACHE INVALIDATION: Yeni post eklendiğinde cache'i temizle
            Cache::forget('blog_posts_stats');
        });

        static::updated(function ($post) {
            // ✅ CACHE INVALIDATION: Post güncellendiğinde cache'i temizle
            Cache::forget('blog_posts_stats');
        });

        static::deleted(function ($post) {
            // ✅ CACHE INVALIDATION: Post silindiğinde cache'i temizle
            Cache::forget('blog_posts_stats');
        });
    }
}
