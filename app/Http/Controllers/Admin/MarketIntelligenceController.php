<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Il;
use App\Models\MarketIntelligenceSetting;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Market Intelligence Controller
 *
 * Context7: Market Intelligence - Pazar İstihbaratı
 * Bölge yönetimi ve veri çekme ayarları
 */
class MarketIntelligenceController extends Controller
{
    /**
     * Dashboard - Genel bakış
     */
    public function dashboard()
    {
        return view('admin.market-intelligence.dashboard');
    }

    /**
     * Settings - Bölge ayarları sayfası
     */
    public function settings()
    {
        $iller = Il::orderBy('il_adi')->get();
        $userSettings = MarketIntelligenceSetting::forUser(Auth::id())
            ->with(['il', 'ilce', 'mahalle'])
            ->orderBy('priority')
            ->get();

        return view('admin.market-intelligence.settings', [
            'iller' => $iller,
            'settings' => $userSettings,
        ]);
    }

    /**
     * Compare - Fiyat karşılaştırma sayfası
     */
    public function compare($ilanId = null)
    {
        return view('admin.market-intelligence.compare', [
            'ilan_id' => $ilanId,
        ]);
    }

    /**
     * Trends - Piyasa trendleri sayfası
     */
    public function trends()
    {
        return view('admin.market-intelligence.trends');
    }

    /**
     * API: Aktif bölgeleri getir (n8n bot için)
     *
     * GET /api/admin/market-intelligence/active-regions
     * n8n bot'unun hangi bölgeleri tarayacağını döndürür
     */
    public function getActiveRegions()
    {
        try {
            // Global ayarlar + Tüm kullanıcıların aktif ayarları
            $activeRegions = MarketIntelligenceSetting::active()
                ->with(['il', 'ilce', 'mahalle'])
                ->orderBy('priority')
                ->get()
                ->map(function ($setting) {
                    return [
                        'id' => $setting->id,
                        'il_id' => $setting->il_id,
                        'il_adi' => $setting->il->il_adi ?? null,
                        'ilce_id' => $setting->ilce_id,
                        'ilce_adi' => $setting->ilce->ilce_adi ?? null,
                        'mahalle_id' => $setting->mahalle_id,
                        'mahalle_adi' => $setting->mahalle->mahalle_adi ?? null,
                        'is_active' => $setting->status,
                        'priority' => $setting->priority,
                        'is_global' => $setting->isGlobal(),
                        'location_text' => $setting->location_text,
                    ];
                });

            return ResponseService::success($activeRegions, 'Aktif bölgeler listelendi');
        } catch (\Exception $e) {
            return ResponseService::serverError('Aktif bölgeler getirilemedi', $e);
        }
    }

    /**
     * API: Bölge ayarlarını kaydet
     *
     * POST /api/admin/market-intelligence/settings
     */
    public function saveSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'regions' => 'required|array',
                'regions.*.il_id' => 'required|exists:iller,id',
                'regions.*.ilce_id' => 'nullable|exists:ilceler,id',
                'regions.*.mahalle_id' => 'nullable|exists:mahalleler,id',
                'regions.*.status' => 'required|boolean',
                'regions.*.priority' => 'nullable|integer|min:0|max:100',
            ]);

            $userId = Auth::id();
            $savedCount = 0;

            DB::beginTransaction();

            foreach ($validated['regions'] as $region) {
                MarketIntelligenceSetting::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'il_id' => $region['il_id'],
                        'ilce_id' => $region['ilce_id'] ?? null,
                        'mahalle_id' => $region['mahalle_id'] ?? null,
                    ],
                    [
                        'status' => $region['status'] ? 1 : 0,
                        'priority' => $region['priority'] ?? 0,
                    ]
                );
                $savedCount++;
            }

            DB::commit();

            return ResponseService::success([
                'saved_count' => $savedCount,
            ], 'Bölge ayarları başarıyla kaydedildi');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseService::serverError('Bölge ayarları kaydedilemedi', $e);
        }
    }

    /**
     * API: Bölge ayarını sil
     *
     * DELETE /api/admin/market-intelligence/settings/{id}
     */
    public function deleteSetting($id)
    {
        try {
            $setting = MarketIntelligenceSetting::findOrFail($id);

            // Sadece kendi ayarlarını silebilir (veya admin)
            $user = Auth::user();
            if ($setting->user_id !== $user->id && !in_array($user->role_id, [1, 2])) {
                return ResponseService::forbidden('Bu ayarı silme yetkiniz yok');
            }

            $setting->delete();

            return ResponseService::success(null, 'Bölge ayarı başarıyla silindi');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseService::notFound('Bölge ayarı bulunamadı');
        } catch (\Exception $e) {
            return ResponseService::serverError('Bölge ayarı silinemedi', $e);
        }
    }

    /**
     * API: Bölge ayarını aktif/pasif yap
     *
     * PATCH /api/admin/market-intelligence/settings/{id}/toggle
     */
    public function toggleSetting($id)
    {
        try {
            $setting = MarketIntelligenceSetting::findOrFail($id);

            // Sadece kendi ayarlarını değiştirebilir (veya admin)
            $user = Auth::user();
            if ($setting->user_id !== $user->id && !in_array($user->role_id, [1, 2])) {
                return ResponseService::forbidden('Bu ayarı değiştirme yetkiniz yok');
            }

            $setting->status = !$setting->status;
            $setting->save();

            return ResponseService::success([
                'id' => $setting->id,
                'status' => $setting->status,
                'is_active' => $setting->isActive(),
            ], $setting->status ? 'Bölge ayarı aktif edildi' : 'Bölge ayarı pasif edildi');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseService::notFound('Bölge ayarı bulunamadı');
        } catch (\Exception $e) {
            return ResponseService::serverError('Bölge ayarı güncellenemedi', $e);
        }
    }

    /**
     * API: n8n bot'tan gelen verileri senkronize et
     *
     * POST /api/admin/market-intelligence/sync
     * n8n bot'unun çektiği ilanları Laravel'e gönderir
     */
    public function sync(Request $request)
    {
        try {
            $validated = $request->validate([
                'source' => 'required|string|in:sahibinden,hepsiemlak,emlakjet',
                'region' => 'nullable|array',
                'region.il_id' => 'nullable|integer',
                'region.ilce_id' => 'nullable|integer',
                'listings' => 'required|array',
                'listings.*.external_id' => 'required|string',
                'listings.*.url' => 'nullable|string|max:500',
                'listings.*.title' => 'required|string|max:500',
                'listings.*.price' => 'nullable|numeric',
                'listings.*.currency' => 'nullable|string|max:10',
                'listings.*.location_il' => 'nullable|string|max:100',
                'listings.*.location_ilce' => 'nullable|string|max:100',
                'listings.*.location_mahalle' => 'nullable|string|max:100',
                'listings.*.m2_brut' => 'nullable|integer',
                'listings.*.m2_net' => 'nullable|integer',
                'listings.*.room_count' => 'nullable|string|max:20',
                'listings.*.listing_date' => 'nullable|date',
                'listings.*.snapshot_data' => 'nullable|array',
            ]);

            $source = $validated['source'];
            $listings = $validated['listings'];
            $syncedCount = 0;
            $updatedCount = 0;
            $newCount = 0;

            DB::connection('market_intelligence')->beginTransaction();

            foreach ($listings as $listingData) {
                $existing = \App\Models\MarketListing::where('source', $source)
                    ->where('external_id', $listingData['external_id'])
                    ->first();

                $priceChanged = false;
                $newPrice = $listingData['price'] ?? null;

                if ($existing) {
                    // Mevcut ilan - fiyat değişikliğini kontrol et
                    if ($newPrice && $existing->price != $newPrice) {
                        $priceChanged = true;
                        $existing->addPriceHistory($newPrice);
                    }

                    // Güncelle
                    $existing->update([
                        'url' => $listingData['url'] ?? $existing->url,
                        'title' => $listingData['title'] ?? $existing->title,
                        'price' => $newPrice ?? $existing->price,
                        'currency' => $listingData['currency'] ?? $existing->currency,
                        'location_il' => $listingData['location_il'] ?? $existing->location_il,
                        'location_ilce' => $listingData['location_ilce'] ?? $existing->location_ilce,
                        'location_mahalle' => $listingData['location_mahalle'] ?? $existing->location_mahalle,
                        'm2_brut' => $listingData['m2_brut'] ?? $existing->m2_brut,
                        'm2_net' => $listingData['m2_net'] ?? $existing->m2_net,
                        'room_count' => $listingData['room_count'] ?? $existing->room_count,
                        'listing_date' => $listingData['listing_date'] ?? $existing->listing_date,
                        'last_seen_at' => now(),
                        'status' => 1, // Aktif (hala yayında)
                        'snapshot_data' => $listingData['snapshot_data'] ?? $existing->snapshot_data,
                    ]);

                    $updatedCount++;
                } else {
                    // Yeni ilan
                    \App\Models\MarketListing::create([
                        'source' => $source,
                        'external_id' => $listingData['external_id'],
                        'url' => $listingData['url'] ?? null,
                        'title' => $listingData['title'],
                        'price' => $newPrice,
                        'currency' => $listingData['currency'] ?? 'TRY',
                        'location_il' => $listingData['location_il'] ?? null,
                        'location_ilce' => $listingData['location_ilce'] ?? null,
                        'location_mahalle' => $listingData['location_mahalle'] ?? null,
                        'm2_brut' => $listingData['m2_brut'] ?? null,
                        'm2_net' => $listingData['m2_net'] ?? null,
                        'room_count' => $listingData['room_count'] ?? null,
                        'listing_date' => $listingData['listing_date'] ?? now()->toDateString(),
                        'last_seen_at' => now(),
                        'status' => 1,
                        'snapshot_data' => $listingData['snapshot_data'] ?? null,
                        'price_history' => $newPrice ? [[
                            'date' => now()->toDateString(),
                            'price' => $newPrice,
                        ]] : null,
                    ]);

                    $newCount++;
                }

                $syncedCount++;
            }

            DB::connection('market_intelligence')->commit();

            return ResponseService::success([
                'synced_count' => $syncedCount,
                'new_count' => $newCount,
                'updated_count' => $updatedCount,
                'source' => $source,
            ], sprintf(
                '%d ilan senkronize edildi (%d yeni, %d güncellendi)',
                $syncedCount,
                $newCount,
                $updatedCount
            ));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError($e->errors());
        } catch (\Exception $e) {
            DB::connection('market_intelligence')->rollBack();

            \App\Services\Logging\LogService::error(
                'Market Intelligence sync failed',
                [
                    'source' => $validated['source'] ?? 'unknown',
                    'listings_count' => count($validated['listings'] ?? []),
                    'error' => $e->getMessage(),
                ],
                $e,
                \App\Services\Logging\LogService::CHANNEL_AI
            );

            return ResponseService::serverError('Veri senkronizasyonu başarısız oldu: ' . $e->getMessage(), $e);
        }
    }
}
