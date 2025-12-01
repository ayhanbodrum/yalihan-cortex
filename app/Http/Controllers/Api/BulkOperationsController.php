<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Services\Response\ResponseService;
use App\Traits\ValidatesApiRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Bulk Operations API Controller
 *
 * PHASE 2.3: Toplu işlemler için API endpoints
 * Context7 Compliant
 *
 * @author Yalıhan Emlak - Context7 Team
 *
 * @date 2025-11-04
 */
class BulkOperationsController extends Controller
{
    use ValidatesApiRequests;

    /**
     * Toplu kategori atama
     * POST /api/admin/bulk/assign-category
     */
    public function assignCategory(Request $request)
    {
        $validated = $this->validateRequestWithResponse($request, [
            'items' => 'required|array|min:1',
            'items.*' => 'required|exists:features,id',
            'category_id' => 'required|exists:feature_categories,id',
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
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
                'user_id' => auth()->id(),
            ]);

            return ResponseService::success([
                'updated_count' => $updated,
            ], "{$updated} özellik kategoriye atandı");
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Bulk category assignment failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return ResponseService::serverError('Toplu kategori atama başarısız.', $e);
        }
    }

    /**
     * Toplu aktif/pasif yapma
     * POST /api/admin/bulk/toggle-status
     */
    public function toggleStatus(Request $request)
    {
        $validated = $this->validateRequestWithResponse($request, [
            'items' => 'required|array|min:1',
            'items.*' => 'required|exists:features,id',
            'status' => 'required|boolean', // Context7: enabled → status
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            DB::beginTransaction();

            $updated = Feature::whereIn('id', $request->items)
                ->update(['status' => $request->status]); // Context7: enabled → status

            DB::commit();

            $action = $request->status ? 'active' : 'inactive'; // Context7: Türkçe yerine İngilizce status değerleri

            Log::info('Bulk status toggle', [
                'items_count' => count($request->items),
                'status' => $request->status, // Context7: enabled → status
                'updated' => $updated,
                'user_id' => auth()->id(),
            ]);

            return ResponseService::success([
                'updated_count' => $updated,
            ], "{$updated} özellik {$action} yapıldı");
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Bulk status toggle failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return ResponseService::serverError('Toplu durum değiştirme başarısız.', $e);
        }
    }

    /**
     * Toplu silme
     * POST /api/admin/bulk/delete
     */
    public function bulkDelete(Request $request)
    {
        $validated = $this->validateRequestWithResponse($request, [
            'items' => 'required|array|min:1',
            'items.*' => 'required|exists:features,id',
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            DB::beginTransaction();

            $deleted = Feature::whereIn('id', $request->items)->delete();

            DB::commit();

            Log::warning('Bulk delete performed', [
                'items_count' => count($request->items),
                'deleted' => $deleted,
                'user_id' => auth()->id(),
            ]);

            return ResponseService::success([
                'deleted_count' => $deleted,
            ], "{$deleted} özellik silindi");
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Bulk delete failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return ResponseService::serverError('Toplu silme başarısız.', $e);
        }
    }

    /**
     * Toplu sıralama güncelleme
     * POST /api/admin/bulk/reorder
     */
    public function reorder(Request $request)
    {
        $validated = $this->validateRequestWithResponse($request, [
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:features,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            DB::beginTransaction();

            foreach ($request->items as $item) {
                $displayOrder = $item['display_order'] ?? $item['order'] ?? 0;
                Feature::where('id', $item['id'])
                    ->update(['display_order' => $displayOrder]);
            }

            DB::commit();

            Log::info('Bulk reorder performed', [
                'items_count' => count($request->items),
                'user_id' => auth()->id(),
            ]);

            return ResponseService::success([], 'Sıralama güncellendi');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Bulk reorder failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return ResponseService::serverError('Sıralama güncellenemedi.', $e);
        }
    }
}
