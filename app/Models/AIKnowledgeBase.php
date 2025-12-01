<?php

namespace App\Models;

use App\Traits\HasActiveScope;
use App\Traits\HasAIUsageTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AIKnowledgeBase extends Model
{
    use HasActiveScope, HasAIUsageTracking, HasFactory, SoftDeletes;

    protected $table = 'ai_knowledge_base';

    protected $fillable = [
        'title',
        'content',
        'category',
        'tags',
        'source',
        'language',
        'priority',
        'status',
        'metadata',
        'last_used_at',
        'usage_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tags' => 'array',
        'metadata' => 'array',
        'status' => 'boolean',
        'priority' => 'integer',
        'usage_count' => 'integer',
        'last_used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'language' => 'tr',
        'priority' => 1,
        'status' => true,
        'usage_count' => 0,
    ];

    /**
     * Default language for scopeByLanguage()
     * Used by HasAIUsageTracking trait
     *
     * @var string
     */
    protected $defaultLanguage = 'tr';

    // Relationships
    public function embeddings(): HasMany
    {
        return $this->hasMany(AIEmbedding::class, 'knowledge_base_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    // ✅ REFACTORED: scopeActive moved to HasActiveScope trait
    // ✅ REFACTORED: scopeByLanguage, scopeRecentlyUsed, scopePopular, incrementUsage moved to HasAIUsageTracking trait

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, $minPriority = 1)
    {
        return $query->where('priority', '>=', $minPriority);
    }

    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('title', 'LIKE', "%{$searchTerm}%")
                ->orWhere('content', 'LIKE', "%{$searchTerm}%")
                ->orWhereJsonContains('tags', $searchTerm);
        });
    }

    // Mutators
    public function setTagsAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['tags'] = json_encode(array_map('trim', explode(',', $value)));
        } else {
            $this->attributes['tags'] = json_encode($value);
        }
    }

    // Accessors
    public function getTagsListAttribute()
    {
        return is_array($this->tags) ? implode(', ', $this->tags) : '';
    }

    public function getContentPreviewAttribute()
    {
        return \Illuminate\Support\Str::limit(strip_tags($this->content), 150);
    }

    public function getPriorityLabelAttribute()
    {
        $labels = [
            1 => 'Düşük',
            2 => 'Düşük',
            3 => 'Normal',
            4 => 'Normal',
            5 => 'Orta',
            6 => 'Orta',
            7 => 'Yüksek',
            8 => 'Yüksek',
            9 => 'Kritik',
            10 => 'Kritik',
        ];

        return $labels[$this->priority] ?? 'Normal';
    }

    public function getSourceLabelAttribute()
    {
        $labels = [
            'manual' => 'Manuel Giriş',
            'import' => 'İçe Aktarım',
            'auto' => 'Otomatik',
        ];

        return $labels[$this->source] ?? 'Bilinmiyor';
    }

    // Methods
    // ✅ REFACTORED: incrementUsage moved to HasAIUsageTracking trait

    public function hasTag($tag)
    {
        return in_array($tag, $this->tags ?? []);
    }

    public function addTag($tag)
    {
        $tags = $this->tags ?? [];
        if (! in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->tags = $tags;
            $this->save();
        }
    }

    public function removeTag($tag)
    {
        $tags = $this->tags ?? [];
        $tags = array_filter($tags, fn ($t) => $t !== $tag);
        $this->tags = array_values($tags);
        $this->save();
    }

    public function createEmbedding($content = null, $model = 'text-embedding-ada-002')
    {
        $contentToEmbed = $content ?? $this->content;

        // Split content into chunks if too long
        $chunks = $this->splitIntoChunks($contentToEmbed);

        foreach ($chunks as $index => $chunk) {
            AIEmbedding::create([
                'knowledge_base_id' => $this->id,
                'content_hash' => hash('sha256', $chunk),
                'content_chunk' => $chunk,
                'embedding_vector' => [], // Will be filled by AI service
                'embedding_model' => $model,
                'chunk_index' => $index,
                'chunk_size' => strlen($chunk),
            ]);
        }
    }

    private function splitIntoChunks($content, $maxChunkSize = 8000)
    {
        if (strlen($content) <= $maxChunkSize) {
            return [$content];
        }

        $chunks = [];
        $sentences = preg_split('/(?<=[.!?])\s+/', $content, -1, PREG_SPLIT_NO_EMPTY);
        $currentChunk = '';

        foreach ($sentences as $sentence) {
            if (strlen($currentChunk.' '.$sentence) > $maxChunkSize) {
                if (! empty($currentChunk)) {
                    $chunks[] = trim($currentChunk);
                    $currentChunk = $sentence;
                } else {
                    // Sentence is too long, split by words
                    $words = explode(' ', $sentence);
                    $wordChunk = '';
                    foreach ($words as $word) {
                        if (strlen($wordChunk.' '.$word) > $maxChunkSize) {
                            if (! empty($wordChunk)) {
                                $chunks[] = trim($wordChunk);
                                $wordChunk = $word;
                            } else {
                                $chunks[] = $word; // Single word is too long
                            }
                        } else {
                            $wordChunk .= ($wordChunk ? ' ' : '').$word;
                        }
                    }
                    if (! empty($wordChunk)) {
                        $currentChunk = $wordChunk;
                    }
                }
            } else {
                $currentChunk .= ($currentChunk ? ' ' : '').$sentence;
            }
        }

        if (! empty($currentChunk)) {
            $chunks[] = trim($currentChunk);
        }

        return $chunks;
    }

    public function getRelatedKnowledge($limit = 5)
    {
        return static::active()
            ->where('id', '!=', $this->id)
            ->where('category', $this->category)
            ->orderBy('priority', 'desc')
            ->orderBy('usage_count', 'desc')
            ->limit($limit)
            ->get();
    }

    // Static methods
    public static function getCategories()
    {
        return static::active()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();
    }

    public static function getAllTags()
    {
        $allTags = static::active()
            ->whereNotNull('tags')
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return $allTags;
    }

    public static function getPopularContent($limit = 10)
    {
        return static::active()
            ->orderBy('usage_count', 'desc')
            ->orderBy('priority', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function searchContent($query, $category = null, $language = 'tr')
    {
        $builder = static::active()
            ->byLanguage($language)
            ->search($query);

        if ($category) {
            $builder->byCategory($category);
        }

        return $builder->orderBy('priority', 'desc')
            ->orderBy('usage_count', 'desc')
            ->get();
    }
}
