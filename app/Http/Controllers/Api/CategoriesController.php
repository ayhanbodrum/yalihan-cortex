<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoriesController extends Controller
{
    public function getSubcategories($parentId)
    {
        try {
            Log::info('Getting subcategories (YENİ SİSTEM)', [
                'parent_id' => $parentId
            ]);

            $subCategories = \App\Models\IlanKategori::where('parent_id', $parentId)
                ->where('seviye', 1)
                ->where('status', 1)
                ->orderBy('order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'icon']);

            Log::info('Subcategories result (YENİ SİSTEM)', [
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
     * Kategori yayın tipleri - ilan_kategori_yayin_tipleri tablosundan al
     * Context7 uyumlu: seed edilmiş data kullanır
     */
    public function getPublicationTypes($categoryId)
    {
        try {
            Log::info('Getting publication types (YENİ SİSTEM)', [
                'category_id' => $categoryId
            ]);

            $category = \App\Models\IlanKategori::find($categoryId);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori bulunamadı'
                ], 404);
            }

            // Ana kategori ID'sini bul
            $parentId = $category->parent_id ?: $categoryId;

            // ilan_kategori_yayin_tipleri tablosundan getir
            $yayinTipleri = DB::table('ilan_kategori_yayin_tipleri')
                ->where('kategori_id', $parentId)
                ->where('status', 'Aktif')
                ->orderBy('order')
                ->orderBy('yayin_tipi')
                ->get(['id', 'yayin_tipi', 'kategori_id', 'order']);

            Log::info('Publication types result (YENİ SİSTEM)', [
                'parent_id' => $parentId,
                'count' => $yayinTipleri->count(),
                'types' => $yayinTipleri->pluck('yayin_tipi')->toArray()
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
                        'name' => $type->yayin_tipi, // Context7: 'yayin_tipi' kolonundan
                        'slug' => \Illuminate\Support\Str::slug($type->yayin_tipi)
                    ];
                }),
                'count' => $yayinTipleri->count(),
                'message' => 'Yayın tipleri yüklendi'
            ]);

        } catch (\Exception $e) {
            Log::error('Publication types loading error', [
                'category_id' => $categoryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Yayın tipleri yüklenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kategori yayın tiplerini al
     */
    private function getCategoryPublicationTypes($categoryId)
    {
        $types = [];

        switch ($categoryId) {
            case 1: // Villa/Daire
                $types = [
                    ['id' => 1, 'name' => 'Satılık', 'description' => 'Satılık villa/daire'],
                    ['id' => 2, 'name' => 'Kiralık', 'description' => 'Kiralık villa/daire']
                ];
                break;

            case 2: // Arsa
                $types = [
                    ['id' => 3, 'name' => 'Satılık', 'description' => 'Satılık arsa'],
                    ['id' => 4, 'name' => 'Kiralık', 'description' => 'Kiralık arsa']
                ];
                break;

            case 3: // Yazlık
                $types = [
                    ['id' => 5, 'name' => 'Kiralık', 'description' => 'Kiralık yazlık'],
                    ['id' => 6, 'name' => 'Günlük Kiralık', 'description' => 'Günlük kiralık yazlık']
                ];
                break;

            case 4: // İşyeri
                $types = [
                    ['id' => 7, 'name' => 'Satılık', 'description' => 'Satılık işyeri'],
                    ['id' => 8, 'name' => 'Kiralık', 'description' => 'Kiralık işyeri'],
                    ['id' => 9, 'name' => 'Devren', 'description' => 'Devren işyeri']
                ];
                break;

            default:
                $types = [
                    ['id' => 1, 'name' => 'Satılık', 'description' => 'Satılık'],
                    ['id' => 2, 'name' => 'Kiralık', 'description' => 'Kiralık']
                ];
                break;
        }

        return $types;
    }
}
