<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Response\ResponseService;
use App\Traits\ValidatesApiRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    use ValidatesApiRequests;

    public function index(Request $request)
    {
        // ✅ REFACTORED: Using ResponseService
        return ResponseService::success(null, 'Category endpoint - to be implemented');
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
                ->orderBy('display_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'icon']);

            Log::info('Subcategories query result', [
                'parent_id' => $parentId,
                'count' => $subCategories->count(),
                'categories' => $subCategories->pluck('name')->toArray()
            ]);

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success([
                'subcategories' => $subCategories->map(function ($cat) {
                    return [
                        'id' => $cat->id,
                        'name' => $cat->name,
                        'slug' => $cat->slug,
                        'icon' => $cat->icon
                    ];
                }),
                'count' => $subCategories->count()
            ], 'Alt kategoriler başarıyla yüklendi');
        } catch (\Exception $e) {
            Log::error('Subcategories loading error', [
                'parent_id' => $parentId,
                'error' => $e->getMessage()
            ]);

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('Alt kategoriler yüklenemedi', $e);
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
                // ✅ REFACTORED: Using ResponseService
                return ResponseService::notFound('Kategori bulunamadı');
            }

            $parentId = $category->parent_id ?? $categoryId;

            $yayinTipleri = \App\Models\IlanKategori::where('parent_id', $parentId)
                ->where('seviye', 2)
                ->where('status', 1)
                ->orderBy('display_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug']);

            Log::info('Publication types result', [
                'parent_id' => $parentId,
                'count' => $yayinTipleri->count(),
                'types' => $yayinTipleri->pluck('name')->toArray()
            ]);

            if ($yayinTipleri->isEmpty()) {
                // ✅ REFACTORED: Using ResponseService
                return ResponseService::success([
                    'types' => [],
                    'count' => 0,
                    'message' => 'Bu kategori için yayın tipi bulunamadı'
                ], 'Bu kategori için yayın tipi bulunamadı');
            }

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::success([
                'types' => $yayinTipleri->map(function ($type) {
                    return [
                        'id' => $type->id,
                        'name' => $type->name,
                        'slug' => $type->slug
                    ];
                }),
                'count' => $yayinTipleri->count(),
                'message' => 'Yayın tipleri yüklendi'
            ], 'Yayın tipleri yüklendi');
        } catch (\Exception $e) {
            Log::error('Publication types loading error', [
                'category_id' => $categoryId,
                'error' => $e->getMessage()
            ]);

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('Yayın tipleri yüklenirken hata oluştu', $e);
        }
    }
}
