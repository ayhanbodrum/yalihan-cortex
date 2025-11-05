<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\FeatureCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FeatureController extends Controller
{
    /**
     * Get features by category slug
     *
     * @param string $categorySlug
     * @return JsonResponse
     */
    public function getByCategory(string $categorySlug): JsonResponse
    {
        try {
            $category = FeatureCategory::where('slug', $categorySlug)
                ->where('enabled', true)
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori bulunamadı'
                ], 404);
            }

            $features = Feature::where('feature_category_id', $category->id)
                ->where(function($query) {
                    if (\Schema::hasColumn('features', 'status')) {
                        $query->where('status', true);
                    } elseif (\Schema::hasColumn('features', 'enabled')) {
                        $query->where('enabled', true);
                    }
                })
                ->orderBy('order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug']);

            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ],
                'features' => $features
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Özellikler yüklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all feature categories
     *
     * @return JsonResponse
     */
    public function getCategories(): JsonResponse
    {
        try {
            $categories = FeatureCategory::where(function($query) {
                    if (\Schema::hasColumn('feature_categories', 'status')) {
                        $query->where('status', true);
                    } elseif (\Schema::hasColumn('feature_categories', 'enabled')) {
                        $query->where('enabled', true);
                    }
                })
                ->orderBy('order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'icon', 'type']);

            return response()->json([
                'success' => true,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategoriler yüklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
