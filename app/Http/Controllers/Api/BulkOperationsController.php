<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\FeatureCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Bulk Operations API Controller
 * 
 * PHASE 2.3: Toplu işlemler için API endpoints
 * Context7 Compliant
 * 
 * @author Yalıhan Emlak - Context7 Team
 * @date 2025-11-04
 */
class BulkOperationsController extends Controller
{
    /**
     * Toplu kategori atama
     * POST /api/admin/bulk/assign-category
     */
    public function assignCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*' => 'required|exists:features,id',
            'category_id' => 'required|exists:feature_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $updated = Feature::whereIn('id', $request->items)
                ->update(['category_id' => $request->category_id]);

            DB::commit();

            Log::info('Bulk category assignment', [
                'items_count' => count($request->items),
                'category_id' => $request->category_id,
                'updated' => $updated,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "{$updated} özellik kategoriye atandı",
                'updated_count' => $updated
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Bulk category assignment failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Toplu kategori atama başarısız: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toplu aktif/pasif yapma
     * POST /api/admin/bulk/toggle-status
     */
    public function toggleStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*' => 'required|exists:features,id',
            'enabled' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $updated = Feature::whereIn('id', $request->items)
                ->update(['enabled' => $request->enabled]);

            DB::commit();

            $action = $request->enabled ? 'aktif' : 'pasif';

            Log::info('Bulk status toggle', [
                'items_count' => count($request->items),
                'enabled' => $request->enabled,
                'updated' => $updated,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "{$updated} özellik {$action} yapıldı",
                'updated_count' => $updated
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Bulk status toggle failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Toplu durum değiştirme başarısız: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toplu silme
     * POST /api/admin/bulk/delete
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*' => 'required|exists:features,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $deleted = Feature::whereIn('id', $request->items)->delete();

            DB::commit();

            Log::warning('Bulk delete performed', [
                'items_count' => count($request->items),
                'deleted' => $deleted,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "{$deleted} özellik silindi",
                'deleted_count' => $deleted
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Bulk delete failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Toplu silme başarısız: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toplu sıralama güncelleme
     * POST /api/admin/bulk/reorder
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:features,id',
            'items.*.order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            foreach ($request->items as $item) {
                Feature::where('id', $item['id'])
                    ->update(['order' => $item['order']]);
            }

            DB::commit();

            Log::info('Bulk reorder performed', [
                'items_count' => count($request->items),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sıralama güncellendi'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Bulk reorder failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sıralama güncellenemedi: ' . $e->getMessage()
            ], 500);
        }
    }
}

