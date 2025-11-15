<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\FeatureCategory;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Services\FeaturesService;

class FeatureController extends Controller
{
    /**
     * Get features by category slug or ilan category
     * Context7: Supports applies_to filtering for category-specific features
     * - Handles both string and JSON array storage for applies_to
     * - Includes safe defaults to avoid irrelevant groups (e.g., konut-only groups on arsa)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // ✅ Context7: Get filters
            $categoryId = $request->get('category_id');
            $appliesTo = $request->get('applies_to');
            $yayinTipi = $request->get('yayin_tipi');
            $categorySlugFilter = $request->get('category');

            Log::info('🔍 FeatureController@index', compact('categoryId', 'appliesTo', 'yayinTipi'));

            // Helper closure: apply applies_to filter supporting JSON or string
            $applyAppliesToFilter = function ($query, string $column, string $needle) {
                return $query->where(function ($q) use ($column, $needle) {
                    // String storage: exact match or 'all'
                    $q->where($column, $needle)
                        ->orWhere($column, 'all');

                    // JSON storage: ["konut"], ["arsa"], etc. (works if column is JSON or TEXT containing JSON)
                    // MySQL/MariaDB JSON_CONTAINS
                    $q->orWhereRaw("JSON_VALID($column) AND JSON_CONTAINS($column, JSON_QUOTE(?))", [$needle]);
                });
            };

            // ✅ Load categories with filtering
            $service = new FeaturesService();
            $result = $service->list($appliesTo, $categorySlugFilter, $yayinTipi);

            return ResponseService::success([
                'data' => $result,
                'metadata' => [
                    'category_id' => $categoryId,
                    'applies_to' => $appliesTo,
                    'yayin_tipi' => $yayinTipi,
                    'total_categories' => count($result),
                    'total_features' => collect($result)->sum(fn($cat) => count($cat['features'])),
                ],
            ], 'Özellikler başarıyla getirildi');
        } catch (\Exception $e) {
            Log::error('FeatureController::index hatası', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ResponseService::serverError('Özellikler yüklenirken hata oluştu.', $e);
        }
    }

    /**
     * Get features by category slug (LEGACY - kept for backwards compatibility)
     *
     * @param string $categorySlug
     * @return JsonResponse
     */
    public function getByCategory(string $categorySlug): JsonResponse
    {
        try {
            Log::info('FeatureController::getByCategory başladı', [
                'categorySlug' => $categorySlug,
            ]);

            // ✅ Context7: status field kontrolü (migration'da status var)
            // Önce status kontrolü olmadan kategoriyi bul
            $category = FeatureCategory::where('slug', $categorySlug)->first();

            if (!$category) {
                Log::warning('FeatureController::getByCategory - Kategori bulunamadı', [
                    'categorySlug' => $categorySlug,
                ]);
                return ResponseService::notFound('Kategori bulunamadı');
            }

            // ✅ Context7: Status kontrolü - boolean true veya 1 kabul edilir
            // Status field'ı varsa ve false/0 ise skip et
            if (isset($category->status) && !$category->status) {
                Log::warning('FeatureController::getByCategory - Kategori pasif', [
                    'categorySlug' => $categorySlug,
                    'status' => $category->status,
                ]);
                return ResponseService::notFound('Kategori bulunamadı');
            }

            Log::info('FeatureController::getByCategory - Kategori bulundu', [
                'categoryId' => $category->id,
                'categoryName' => $category->name,
            ]);

            // ✅ Context7: Features sorgusu
            $features = Feature::where('feature_category_id', $category->id)
                ->where('status', true) // ✅ Context7: status field kullanılıyor (migration'da var)
                ->orderBy('display_order') // ✅ Context7: display_order field kullanılıyor
                ->orderBy('name')
                ->get(['id', 'name', 'slug']);

            Log::info('FeatureController::getByCategory - Features yüklendi', [
                'categorySlug' => $categorySlug,
                'featuresCount' => $features->count(),
            ]);

            return ResponseService::success([
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ],
                'features' => $features
            ], 'Özellikler başarıyla getirildi');
        } catch (\Exception $e) {
            Log::error('FeatureController::getByCategory hatası', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'categorySlug' => $categorySlug,
                'trace' => $e->getTraceAsString()
            ]);
            return ResponseService::serverError('Özellikler yüklenirken hata oluştu.', $e);
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
            // ✅ Context7: Sadece veritabanından veri çek
            $query = FeatureCategory::query();

            // ✅ Context7: status field kullanımı (migration'da status kolonu var)
            $query->where('status', true);

            // ✅ Context7: Sadece mevcut kolonları çek (type kolonu yok)
            $categories = $query->orderBy('display_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'icon']);

            return ResponseService::success([
                'categories' => $categories
            ], 'Özellik kategorileri başarıyla getirildi');
        } catch (\Exception $e) {
            // ✅ Context7: Hata detaylarını logla
            Log::error('FeatureController::getCategories hatası', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return ResponseService::serverError('Kategoriler yüklenirken hata oluştu.', $e);
        }
    }
}
