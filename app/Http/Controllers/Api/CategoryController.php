<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IlanKategoriYayinTipi;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Category endpoint - to be implemented']);
    }

    public function getSubcategories($parentId)
    {
        try {
            Log::info('Getting subcategories', [
                'parent_id' => $parentId
            ]);

            $subCategories = \App\Models\IlanKategori::where('parent_id', $parentId)
                ->where('seviye', 1)
                ->where('status', 1)
                ->orderBy('order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'icon']);

            Log::info('Subcategories query result', [
                'parent_id' => $parentId,
                'count' => $subCategories->count(),
                'categories' => $subCategories->pluck('name')->toArray()
            ]);

            return response()->json([
                'success' => true,
                'subcategories' => $subCategories->map(function ($cat) {
                    return [
                        'id' => $cat->id,
                        'name' => $cat->name,
                        'slug' => $cat->slug,
                        'icon' => $cat->icon
                    ];
                }),
                'count' => $subCategories->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Subcategories loading error', [
                'parent_id' => $parentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Alt kategoriler yüklenemedi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get publication types for a specific category
     */
    public function getPublicationTypes($categoryId)
    {
        try {
            Log::info('Getting publication types', [
                'category_id' => $categoryId
            ]);

            $category = \App\Models\IlanKategori::find($categoryId);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori bulunamadı'
                ], 404);
            }

            $parentId = $category->parent_id ?? $categoryId;

            $yayinTipleri = \App\Models\IlanKategori::where('parent_id', $parentId)
                ->where('seviye', 2)
                ->where('status', 1)
                ->orderBy('order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug']);

            Log::info('Publication types result', [
                'parent_id' => $parentId,
                'count' => $yayinTipleri->count(),
                'types' => $yayinTipleri->pluck('name')->toArray()
            ]);

            if ($yayinTipleri->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'types' => [],
                    'count' => 0,
                    'message' => 'Bu kategori için yayın tipi bulunamadı'
                ]);
            }

            return response()->json([
                'success' => true,
                'types' => $yayinTipleri->map(function ($type) {
                    return [
                        'id' => $type->id,
                        'name' => $type->name,
                        'slug' => $type->slug
                    ];
                }),
                'count' => $yayinTipleri->count(),
                'message' => 'Yayın tipleri yüklendi'
            ]);

        } catch (\Exception $e) {
            Log::error('Publication types loading error', [
                'category_id' => $categoryId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Yayın tipleri yüklenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
