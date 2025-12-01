<?php

namespace App\Models;

use App\Traits\HasAIUsageTracking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class AIEmbedding extends Model
{
    use HasAIUsageTracking, HasFactory;

    protected $table = 'ai_embeddings';

    protected $fillable = [
        'embeddable_type',
        'embeddable_id',
        'field_name',
        'content',
        'embedding',
        'model_name',
        'dimensions',
        'language',
        'similarity_threshold',
        'last_used_at',
        'usage_count',
    ];

    protected $casts = [
        'embedding' => 'array',
        'dimensions' => 'integer',
        'similarity_threshold' => 'decimal:4',
        'usage_count' => 'integer',
        'last_used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'usage_count' => 0,
        'similarity_threshold' => 0.7,
        'language' => 'tr',
    ];

    // Relationships
    public function embeddable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    // ✅ REFACTORED: scopeByLanguage, scopeRecentlyUsed, scopePopular, incrementUsage moved to HasAIUsageTracking trait

    public function scopeByModel($query, $modelName)
    {
        return $query->where('model_name', $modelName);
    }

    public function scopeByField($query, $fieldName)
    {
        return $query->where('field_name', $fieldName);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('embeddable_type', $type);
    }

    public function scopeHighSimilarity($query, $threshold = 0.8)
    {
        return $query->where('similarity_threshold', '>=', $threshold);
    }

    // Accessors
    public function getContentPreviewAttribute()
    {
        return Str::limit($this->content, 100);
    }

    public function getEmbeddingDimensionsAttribute()
    {
        return is_array($this->embedding) ? count($this->embedding) : 0;
    }

    public function getModelDisplayNameAttribute()
    {
        $models = [
            'text-embedding-ada-002' => 'OpenAI Ada v2',
            'text-embedding-3-small' => 'OpenAI v3 Small',
            'text-embedding-3-large' => 'OpenAI v3 Large',
            'sentence-transformers' => 'Sentence Transformers',
            'multilingual-e5' => 'Multilingual E5',
        ];

        return $models[$this->model_name] ?? $this->model_name;
    }

    public function getLanguageDisplayNameAttribute()
    {
        $languages = [
            'tr' => 'Türkçe',
            'en' => 'English',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'es' => 'Español',
        ];

        return $languages[$this->language] ?? $this->language;
    }

    public function getUsageFrequencyAttribute()
    {
        if (! $this->last_used_at) {
            return 'Hiç kullanılmamış';
        }

        $daysSinceLastUse = $this->last_used_at->diffInDays(Carbon::now());

        if ($daysSinceLastUse === 0) {
            return 'Bugün kullanıldı';
        } elseif ($daysSinceLastUse <= 7) {
            return 'Bu hafta kullanıldı';
        } elseif ($daysSinceLastUse <= 30) {
            return 'Bu ay kullanıldı';
        } else {
            return $daysSinceLastUse.' gün önce kullanıldı';
        }
    }

    // Methods
    // ✅ REFACTORED: incrementUsage moved to HasAIUsageTracking trait

    public function updateEmbedding(array $embedding, $modelName = null)
    {
        $data = [
            'embedding' => $embedding,
            'dimensions' => count($embedding),
        ];

        if ($modelName) {
            $data['model_name'] = $modelName;
        }

        $this->update($data);
    }

    public function calculateSimilarity(array $queryEmbedding)
    {
        if (! is_array($this->embedding) || empty($this->embedding)) {
            return 0;
        }

        return $this->cosineSimilarity($this->embedding, $queryEmbedding);
    }

    public function isValidEmbedding()
    {
        return is_array($this->embedding) &&
            count($this->embedding) > 0 &&
            $this->dimensions > 0;
    }

    public function needsUpdate($maxAge = 30)
    {
        return $this->updated_at->diffInDays(Carbon::now()) > $maxAge;
    }

    public function getEmbeddingVector()
    {
        return $this->embedding;
    }

    public function setEmbeddingVector(array $vector)
    {
        $this->embedding = $vector;
        $this->dimensions = count($vector);
    }

    // Static methods
    public static function createForModel($model, $fieldName, $content, array $embedding, $modelName = 'text-embedding-ada-002', $language = 'tr')
    {
        return static::create([
            'embeddable_type' => get_class($model),
            'embeddable_id' => $model->id,
            'field_name' => $fieldName,
            'content' => $content,
            'embedding' => $embedding,
            'model_name' => $modelName,
            'dimensions' => count($embedding),
            'language' => $language,
        ]);
    }

    public static function findSimilar(array $queryEmbedding, $threshold = 0.7, $limit = 10, $type = null, $language = null)
    {
        $query = static::query();

        if ($type) {
            $query->byType($type);
        }

        if ($language) {
            $query->byLanguage($language);
        }

        $embeddings = $query->get();
        $similarities = [];

        foreach ($embeddings as $embedding) {
            if (! $embedding->isValidEmbedding()) {
                continue;
            }

            $similarity = $embedding->calculateSimilarity($queryEmbedding);

            if ($similarity >= $threshold) {
                $similarities[] = [
                    'embedding' => $embedding,
                    'similarity' => $similarity,
                ];
            }
        }

        // Sort by similarity (highest first)
        usort($similarities, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        // Return top results
        return collect(array_slice($similarities, 0, $limit));
    }

    public static function searchByContent($content, $threshold = 0.7, $limit = 10, $type = null, $language = 'tr')
    {
        // This would typically involve generating an embedding for the content first
        // For now, we'll do a simple text search as fallback
        $query = static::query()
            ->where('content', 'LIKE', '%'.$content.'%');

        if ($type) {
            $query->byType($type);
        }

        if ($language) {
            $query->byLanguage($language);
        }

        return $query->limit($limit)->get();
    }

    public static function getModelStats($modelName = null)
    {
        $query = static::query();

        if ($modelName) {
            $query->byModel($modelName);
        }

        return [
            'total_embeddings' => $query->count(),
            'total_usage' => $query->sum('usage_count'),
            'avg_dimensions' => $query->avg('dimensions'),
            'languages' => $query->groupBy('language')
                ->selectRaw('language, count(*) as count')
                ->pluck('count', 'language'),
            'types' => $query->groupBy('embeddable_type')
                ->selectRaw('embeddable_type, count(*) as count')
                ->pluck('count', 'embeddable_type'),
            'recently_used' => $query->recentlyUsed(7)->count(),
            'popular' => $query->popular(5)->count(),
        ];
    }

    public static function cleanupUnused($days = 90)
    {
        $cutoffDate = Carbon::now()->subDays($days);

        return static::where('last_used_at', '<', $cutoffDate)
            ->where('usage_count', 0)
            ->delete();
    }

    public static function cleanupOrphaned()
    {
        $orphaned = 0;
        $types = static::distinct('embeddable_type')->pluck('embeddable_type');

        foreach ($types as $type) {
            if (! class_exists($type)) {
                $orphaned += static::where('embeddable_type', $type)->delete();

                continue;
            }

            $embeddings = static::where('embeddable_type', $type)->get();

            if ($embeddings->isEmpty()) {
                continue;
            }

            // ✅ PERFORMANCE FIX: N+1 query önlendi - Tüm embeddable_id'leri tek query'de kontrol et
            $embeddableIds = $embeddings->pluck('embeddable_id')->toArray();
            $existingIds = $type::whereIn('id', $embeddableIds)->pluck('id')->toArray();

            // ✅ PERFORMANCE FIX: Bulk delete kullan (N query → 1 query)
            $orphanedIds = array_diff($embeddableIds, $existingIds);
            if (! empty($orphanedIds)) {
                $deletedCount = static::where('embeddable_type', $type)
                    ->whereIn('embeddable_id', $orphanedIds)
                    ->delete();
                $orphaned += $deletedCount;
            }
        }

        return $orphaned;
    }

    // Helper methods
    private function cosineSimilarity(array $a, array $b)
    {
        if (count($a) !== count($b)) {
            return 0;
        }

        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        for ($i = 0; $i < count($a); $i++) {
            $dotProduct += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        $normA = sqrt($normA);
        $normB = sqrt($normB);

        if ($normA == 0 || $normB == 0) {
            return 0;
        }

        return $dotProduct / ($normA * $normB);
    }

    private function euclideanDistance(array $a, array $b)
    {
        if (count($a) !== count($b)) {
            return PHP_FLOAT_MAX;
        }

        $sum = 0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }

        return sqrt($sum);
    }

    private function manhattanDistance(array $a, array $b)
    {
        if (count($a) !== count($b)) {
            return PHP_FLOAT_MAX;
        }

        $sum = 0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += abs($a[$i] - $b[$i]);
        }

        return $sum;
    }
}
