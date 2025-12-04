<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * IlanDraftController
 * ✅ Context7 Compliant - Auth middleware + throttle required
 *
 * Manages draft saving for listing creation form
 */
class IlanDraftController extends Controller
{
    /**
     * Save listing draft to cache
     * ✅ Context7: Authenticated users only, throttled, proper validation
     */
    public function save(Request $request)
    {
        try {
            // ✅ Context7: Authorize user
            if (!$request->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // ✅ Context7: Validate request data
            $validated = $request->validate([
                'ana_kategori_id' => 'nullable|integer|exists:ilan_kategoriler,id',
                'alt_kategori_id' => 'nullable|integer|exists:ilan_kategoriler,id',
                'yayin_tipi_id' => 'nullable|integer',
                'baslik' => 'nullable|string|max:255',
                'fiyat' => 'nullable|numeric',
                'para_birimi' => 'nullable|string|in:TRY,USD,EUR',
                'il_id' => 'nullable|integer',
                'ilce_id' => 'nullable|integer',
                'mahalle_id' => 'nullable|integer',
                'ilan_sahibi_id' => 'nullable|integer|exists:ilan_sahipleri,id',
                'status' => 'nullable|string|in:Aktif,Deaktif,İncelemede,Bekleniyor',
                'oncelik' => 'nullable|string|in:normal,yuksek,acil',
                'crm_only' => 'nullable|boolean',
                // Add other fields as needed
            ], [
                'ana_kategori_id.exists' => 'Seçili kategori bulunamadı',
                'ilan_sahibi_id.exists' => 'Seçili ilan sahibi bulunamadı',
            ]);

            // Get cache key for this user
            $cacheKey = "ilan_draft.{$request->user()->id}";

            // Get existing draft (if any)
            $draft = Cache::get($cacheKey, []);

            // Merge new data with existing draft
            $draft = array_merge($draft, $validated);

            // Cache for 7 days
            Cache::put($cacheKey, $draft, now()->addDays(7));

            Log::info('Listing draft saved', [
                'user_id' => $request->user()->id,
                'category_id' => $validated['ana_kategori_id'] ?? null,
                'timestamp' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Draft kaydedildi',
                'data' => [
                    'id' => "draft_{$request->user()->id}",
                    'status' => 'draft',
                    'created_at' => now()->toISOString(),
                    'fields_saved' => count($validated),
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Draft validation failed', [
                'user_id' => $request->user()->id ?? 'anonymous',
                'errors' => $e->errors(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Draft save error', [
                'user_id' => $request->user()->id ?? 'anonymous',
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Taslak kaydedilemedi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Load user's saved draft
     * ✅ Context7: Authenticated, get user's own draft
     */
    public function load(Request $request)
    {
        try {
            $cacheKey = "ilan_draft.{$request->user()->id}";
            $draft = Cache::get($cacheKey);

            return response()->json([
                'success' => (bool) $draft,
                'data' => $draft ?? [],
                'message' => $draft ? 'Taslak yüklendi' : 'Taslak bulunamadı',
            ]);
        } catch (\Exception $e) {
            Log::error('Draft load error', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Taslak yüklenemedi',
            ], 500);
        }
    }

    /**
     * Clear user's draft
     * ✅ Context7: Authenticated, user-specific
     */
    public function clear(Request $request)
    {
        try {
            $cacheKey = "ilan_draft.{$request->user()->id}";
            Cache::forget($cacheKey);

            Log::info('Draft cleared', ['user_id' => $request->user()->id]);

            return response()->json([
                'success' => true,
                'message' => 'Taslak temizlendi',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Taslak temizlenemedi',
            ], 500);
        }
    }
}
