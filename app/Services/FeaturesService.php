<?php

namespace App\Services;

use App\Models\Feature;
use App\Models\FeatureCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class FeaturesService
{
    public function list(?string $appliesTo = null, ?string $categorySlug = null, $yayinTipi = null): array
    {
        $key = 'features:list:'.md5(($appliesTo ?? '').'|'.($categorySlug ?? '').'|'.($yayinTipi ?? ''));

        return Cache::remember($key, 300, function () use ($appliesTo, $categorySlug, $yayinTipi) {
            $categoriesQuery = FeatureCategory::query();
            if ($appliesTo && Schema::hasColumn('feature_categories', 'applies_to')) {
                $categoriesQuery->where(function ($q) use ($appliesTo) {
                    $q->where('applies_to', $appliesTo)->orWhere('applies_to', 'all');
                    $q->orWhereRaw('JSON_VALID(applies_to) AND JSON_CONTAINS(applies_to, JSON_QUOTE(?))', [$appliesTo]);
                });
            }
            if ($categorySlug) {
                $categoriesQuery->where('slug', $categorySlug);
            }
            if ($yayinTipi) {
                if (Schema::hasColumn('feature_categories', 'publication_type_id')) {
                    $categoriesQuery->where(function ($q) use ($yayinTipi) {
                        $q->where('publication_type_id', $yayinTipi);
                    });
                } elseif (Schema::hasColumn('feature_categories', 'publication_types')) {
                    $categoriesQuery->whereRaw('JSON_VALID(publication_types) AND JSON_CONTAINS(publication_types, JSON_QUOTE(?))', [$yayinTipi]);
                }
            }
            $categoriesQuery->where('status', true);
            $categories = $categoriesQuery->orderBy('display_order')->orderBy('name')->get();
            if ($appliesTo === 'arsa') {
                $excludeSlugs = ['ic-ozellikleri', 'dis-ozellikleri', 'muhit', 'ulasim', 'cephe', 'manzara'];
                $categories = $categories->reject(fn ($c) => in_array($c->slug, $excludeSlugs));
            }
            $result = [];
            foreach ($categories as $category) {
                $featuresQuery = Feature::where('feature_category_id', $category->id);
                if ($appliesTo && Schema::hasColumn('features', 'applies_to')) {
                    $featuresQuery->where(function ($q) use ($appliesTo) {
                        $q->where('applies_to', $appliesTo)->orWhereNull('applies_to');
                        $q->orWhereRaw('JSON_VALID(applies_to) AND JSON_CONTAINS(applies_to, JSON_QUOTE(?))', [$appliesTo]);
                    });
                }
                if ($yayinTipi) {
                    if (Schema::hasColumn('features', 'publication_type_id')) {
                        $featuresQuery->where(function ($q) use ($yayinTipi) {
                            $q->where('publication_type_id', $yayinTipi)->orWhereNull('publication_type_id');
                        });
                    } elseif (Schema::hasColumn('features', 'publication_types')) {
                        $featuresQuery->where(function ($q) use ($yayinTipi) {
                            $q->orWhereRaw('JSON_VALID(publication_types) AND JSON_CONTAINS(publication_types, JSON_QUOTE(?))', [$yayinTipi]);
                        });
                    }
                }
                $featuresQuery->where('status', true);
                $features = $featuresQuery->orderBy('display_order')->orderBy('name')->get([
                    'id', 'name', 'slug', 'type', 'options', 'unit', 'is_required', 'is_filterable', 'display_order', 'description',
                ]);
                if ($features->isNotEmpty()) {
                    $result[] = [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'icon' => $category->icon ?? 'fas fa-star',
                        'features' => $features->map(function ($feature) {
                            return [
                                'id' => $feature->id,
                                'name' => $feature->name,
                                'slug' => $feature->slug,
                                'type' => $feature->type,
                                'options' => $feature->options ? json_decode($feature->options) : null,
                                'unit' => $feature->unit,
                                'is_required' => (bool) $feature->is_required,
                                'is_filterable' => (bool) $feature->is_filterable,
                                'description' => $feature->description,
                            ];
                        }),
                    ];
                }
            }

            return $result;
        });
    }
}
