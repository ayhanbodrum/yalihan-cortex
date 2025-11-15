<?php

namespace App\Http\Controllers\Api;

use App\Models\IlanKategoriYayinTipi;
use App\Http\Controllers\Controller;
use App\Services\Response\ResponseService;
use App\Traits\ValidatesApiRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoriesController extends Controller
{
    use ValidatesApiRequests;

    /**
     * Alt kategorileri getir (Ana kategori ID'sine göre)
     * ✅ Context7: seviye=1 ve status=true olan kategorileri getirir
     */
    public function getSubcategories($parentId)
    {
        try {
            Log::info('Getting subcategories (Context7)', [
                'parent_id' => $parentId
            ]);

            $subCategories = \App\Models\IlanKategori::where('parent_id', $parentId)
                ->where('seviye', 1) // ✅ Context7: Alt kategoriler seviye=1
                ->where('status', true) // ✅ Context7: boolean status
                ->orderBy('display_order') // ✅ Context7: display_order kullan
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'icon']);

            Log::info('Subcategories result (Context7)', [
                'parent_id' => $parentId,
                'count' => $subCategories->count(),
                'categories' => $subCategories->pluck('name')->toArray()
            ]);

            // ✅ REFACTORED: Using ResponseService - ResponseService format'ına uygun
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
     * Kategori yayın tipleri - ilan_kategori_yayin_tipleri tablosundan al
     * Context7 uyumlu: seed edilmiş data kullanır
     *
     * ✅ Context7: Alt kategori ID'si geldiğinde, ana kategori ID'sini bulup yayın tiplerini getirir
     * ✅ Context7: ilan_kategori_yayin_tipleri tablosu kullanılır (seviye=2 değil!)
     */
    public function getPublicationTypes($categoryId)
    {
        try {
            Log::info('Getting publication types (Context7)', [
                'category_id' => $categoryId
            ]);

            $category = \App\Models\IlanKategori::find($categoryId);

            if (!$category) {
                // ✅ REFACTORED: Using ResponseService
                return ResponseService::notFound('Kategori bulunamadı');
            }

            // ✅ Context7: Ana kategori ID'sini bul (alt kategori ise parent_id, değilse kendisi)
            $anaKategoriId = $category->parent_id ?: $categoryId;

            // ✅ Context7: Alt kategori için pivot tablo kontrolü (alt_kategori_yayin_tipi)
            $yayinTipleriIds = null;
            if ($category->parent_id) {
                // Alt kategori - pivot tablodan filtrelenmiş yayın tiplerini al
                try {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('alt_kategori_yayin_tipi', 'alt_kategori_id')) {
                        $yayinTipleriIds = \Illuminate\Support\Facades\DB::table('alt_kategori_yayin_tipi')
                            ->where('alt_kategori_id', $categoryId)
                            ->where(function ($query) {
                                if (\Illuminate\Support\Facades\Schema::hasColumn('alt_kategori_yayin_tipi', 'status')) {
                                    $query->where('status', true);
                                }
                            })
                            ->pluck('yayin_tipi_id')
                            ->toArray();
                    }
                } catch (\Exception $e) {
                    Log::warning('Pivot tablo sorgusu başarısız, ana kategori yayın tipleri kullanılıyor', [
                        'error' => $e->getMessage(),
                        'category_id' => $categoryId
                    ]);
                }
            }

            // ✅ Context7: ilan_kategori_yayin_tipleri tablosundan getir
            $query = IlanKategoriYayinTipi::where('kategori_id', $anaKategoriId)
                ->where('status', true); // Context7: boolean status

            // Eğer pivot tabloda filtrelenmiş ID'ler varsa, onları kullan
            if ($yayinTipleriIds && !empty($yayinTipleriIds)) {
                $query->whereIn('id', $yayinTipleriIds);
            }

            $yayinTipleri = $query
                ->orderBy('display_order')
                ->orderBy('yayin_tipi')
                ->get(['id', 'yayin_tipi', 'kategori_id', 'display_order']);

            Log::info('Publication types result (Context7)', [
                'ana_kategori_id' => $anaKategoriId,
                'alt_kategori_id' => $category->parent_id ? $categoryId : null,
                'pivot_filtered' => !empty($yayinTipleriIds),
                'count' => $yayinTipleri->count(),
                'types' => $yayinTipleri->pluck('yayin_tipi')->toArray()
            ]);

            if ($yayinTipleri->isEmpty()) {
                // ✅ REFACTORED: Using ResponseService
                return ResponseService::success([
                    'types' => [],
                    'count' => 0,
                    'message' => 'Bu kategori için yayın tipi bulunamadı'
                ], 'Bu kategori için yayın tipi bulunamadı');
            }

            // ✅ REFACTORED: Using ResponseService - ResponseService format'ına uygun
            return ResponseService::success([
                'types' => $yayinTipleri->map(function ($type) {
                    return [
                        'id' => $type->id,
                        'name' => $type->yayin_tipi, // ✅ Context7: 'yayin_tipi' kolonundan (name değil)
                        'slug' => \Illuminate\Support\Str::slug($type->yayin_tipi)
                    ];
                }),
                'count' => $yayinTipleri->count(),
                'message' => 'Yayın tipleri yüklendi'
            ], 'Yayın tipleri yüklendi');
        } catch (\Exception $e) {
            Log::error('Publication types loading error', [
                'category_id' => $categoryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // ✅ REFACTORED: Using ResponseService
            return ResponseService::serverError('Yayın tipleri yüklenirken hata oluştu', $e);
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
