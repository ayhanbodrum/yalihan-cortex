<?php

namespace App\Services\Ilan;

use App\Models\IlanKategori;
use App\Models\FeatureCategory;

class IlanFeatureService
{
    public function getFeaturesByCategory($categoryId, $yayinTipi = null): array
    {
        $category = IlanKategori::findOrFail($categoryId);

        $featureCategories = FeatureCategory::with(['features' => function($query) use ($category, $yayinTipi) {
            $query->where('status', true)
                  ->where(function($q) use ($category) {
                      $q->where('applies_to', 'all')
                        ->orWhere('applies_to', $category->slug)
                        ->orWhere('applies_to', 'like', "%,{$category->slug},%")
                        ->orWhere('applies_to', 'like', "{$category->slug},%")
                        ->orWhere('applies_to', 'like', "%,{$category->slug}");
                  });

            if ($yayinTipi) {
                $yayinTipiLower = strtolower($yayinTipi);
                $query->where(function($q) use ($category, $yayinTipiLower) {
                    $q->where('applies_to', 'all')
                      ->orWhere('applies_to', 'like', "%{$category->slug}|{$yayinTipiLower}")
                      ->orWhere('applies_to', 'like', "{$category->slug}|{$yayinTipiLower}%")
                      ->orWhere('applies_to', 'like', "%|{$yayinTipiLower}")
                      ->orWhere('applies_to', 'like', "{$yayinTipiLower}|%")
                      ->orWhere('applies_to', $yayinTipiLower);
                });
            }

            $query->orderBy('display_order');
        }])
        ->whereHas('features', function($query) use ($category, $yayinTipi) {
            $query->where('status', true)
                  ->where(function($q) use ($category) {
                      $q->where('applies_to', 'all')
                        ->orWhere('applies_to', $category->slug)
                        ->orWhere('applies_to', 'like', "%,{$category->slug},%")
                        ->orWhere('applies_to', 'like', "{$category->slug},%")
                        ->orWhere('applies_to', 'like', "%,{$category->slug}");
                  });

            if ($yayinTipi) {
                $yayinTipiLower = strtolower($yayinTipi);
                $query->where(function($q) use ($category, $yayinTipiLower) {
                    $q->where('applies_to', 'all')
                      ->orWhere('applies_to', 'like', "%{$category->slug}|{$yayinTipiLower}")
                      ->orWhere('applies_to', 'like', "{$category->slug}|{$yayinTipiLower}%")
                      ->orWhere('applies_to', 'like', "%|{$yayinTipiLower}")
                      ->orWhere('applies_to', 'like', "{$yayinTipiLower}|%")
                      ->orWhere('applies_to', $yayinTipiLower);
                });
            }
        })
        ->where('status', true)
        ->orderBy('display_order')
        ->get();

        $transformed = $featureCategories->map(function($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'icon' => $cat->icon ?? 'fas fa-star',
                'features' => $cat->features->map(function($feature) {
                    return [
                        'id' => $feature->id,
                        'name' => $feature->name,
                        'slug' => $feature->slug,
                        'type' => $feature->type ?? 'boolean',
                        'options' => $feature->options ?? null,
                        'unit' => $feature->unit ?? null,
                        'required' => $feature->required ?? false,
                        'description' => $feature->description ?? null,
                    ];
                })
            ];
        });

        return [
            'feature_categories' => $transformed,
            'metadata' => [
                'category_slug' => $category->slug,
                'yayin_tipi' => $yayinTipi,
                'total_features' => $transformed->sum(fn($cat) => count($cat['features']))
            ]
        ];
    }
}
