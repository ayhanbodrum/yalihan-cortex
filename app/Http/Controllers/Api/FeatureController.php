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
     * Get features by category slug (applies_to filtresi ile)
     * ✅ FIX: FeaturesService kullanarak applies_to filtresi ile tüm kategorileri döndür
     *
     * @param string $categorySlug - İlan kategori slug'ı (konut, arsa, vb.)
     * @param Request $request - Query params: yayin_tipi
     * @return JsonResponse
     */
    public function getByCategory(string $categorySlug, Request $request): JsonResponse
    {
        try {
            Log::info('FeatureController::getByCategory başladı', [
                'categorySlug' => $categorySlug,
                'yayin_tipi' => $request->get('yayin_tipi'),
            ]);

            // ✅ FIX: FeaturesService kullanarak applies_to filtresi ile tüm kategorileri getir
            $featuresService = new FeaturesService();
            $yayinTipi = $request->get('yayin_tipi');

            // ✅ Context7: applies_to = ilan kategori slug'ı (konut, arsa, vb.)
            $categories = $featuresService->list($categorySlug, null, $yayinTipi);

            Log::info('FeatureController::getByCategory - Features yüklendi', [
                'categorySlug' => $categorySlug,
                'categoriesCount' => count($categories),
                'totalFeatures' => array_sum(array_map(fn($c) => count($c['features'] ?? []), $categories)),
            ]);

            return ResponseService::success([
                'data' => $categories, // ✅ FIX: FeaturesService format'ı (kategoriler + feature'lar)
                'features' => $categories, // ✅ Backward compatibility
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
