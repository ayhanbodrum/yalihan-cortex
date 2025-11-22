<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ilan;
use App\Models\IlanTakvimSync;
use App\Models\YazlikDolulukDurumu;
use App\Services\CalendarSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CalendarSyncController extends AdminController
{
    protected $calendarSyncService;

    public function __construct(CalendarSyncService $calendarSyncService)
    {
        $this->calendarSyncService = $calendarSyncService;
    }

    public function getSyncs($ilanId)
    {
        try {
            $ilan = Ilan::findOrFail($ilanId);

            $syncs = IlanTakvimSync::where('ilan_id', $ilanId)
                                   ->orderBy('platform')
                                   ->get();

            return response()->json([
                'success' => true,
                'data' => $syncs
            ]);

        } catch (\Exception $e) {
            Log::error('Get syncs error', [
                'ilan_id' => $ilanId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Senkronizasyonlar alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createSync(Request $request, $ilanId)
    {
        try {
            $request->validate([
                'platform' => 'required|in:airbnb,booking_com,google_calendar',
                'external_listing_id' => 'required|string',
                'sync_enabled' => 'boolean',
            ]);

            $ilan = Ilan::findOrFail($ilanId);

            $sync = IlanTakvimSync::create([
                'ilan_id' => $ilanId,
                'platform' => $request->platform,
                'external_listing_id' => $request->external_listing_id,
                'sync_enabled' => $request->boolean('sync_enabled', true),
                'sync_token' => null,
                'last_sync_at' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Senkronizasyon oluşturuldu',
                'data' => $sync
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Create sync error', [
                'ilan_id' => $ilanId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Senkronizasyon oluşturulamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    public function manualSync(Request $request, $ilanId)
    {
        try {
            $request->validate([
                'platform' => 'required|in:airbnb,booking_com,google_calendar',
            ]);

            $result = $this->calendarSyncService->syncCalendar(
                $ilanId,
                $request->platform
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Senkronizasyon başarılı',
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Manual sync error', [
                'ilan_id' => $ilanId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Senkronizasyon hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCalendar($ilanId)
    {
        try {
            $ilan = Ilan::findOrFail($ilanId);

            $availability = YazlikDolulukDurumu::where('ilan_id', $ilanId)
                                                ->where('tarih', '>=', now())
                                                ->where('tarih', '<=', now()->addDays(90))
                                                ->orderBy('tarih')
                                                ->get()
                                                ->map(function ($item) {
                                                    return [
                                                        'date' => $item->tarih->format('Y-m-d'),
                                                        'status' => $item->status,
                                                        'reason' => $item->aciklama,
                                                    ];
                                                });

            return response()->json([
                'success' => true,
                'data' => [
                    'ilan_id' => $ilanId,
                    'availability' => $availability
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get calendar error', [
                'ilan_id' => $ilanId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Takvim alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    public function blockDates(Request $request, $ilanId)
    {
        try {
            $request->validate([
                'dates' => 'required|array',
                'dates.*' => 'required|date',
                'reason' => 'nullable|string',
            ]);

            $ilan = Ilan::findOrFail($ilanId);

            $blocked = [];
            foreach ($request->dates as $date) {
                $block = YazlikDolulukDurumu::updateOrCreate(
                    [
                        'ilan_id' => $ilanId,
                        'tarih' => $date,
                    ],
                    [
                        'status' => 'bloke',
                        'aciklama' => $request->reason ?? 'Manuel engelleme',
                    ]
                );

                $blocked[] = $block;
            }

            return response()->json([
                'success' => true,
                'message' => 'Tarihler engellendi',
                'data' => $blocked
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Block dates error', [
                'ilan_id' => $ilanId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Tarihler engellenemedi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSync(Request $request, $ilanId, $syncId)
    {
        try {
            $request->validate([
                'external_listing_id' => 'string',
                'sync_enabled' => 'boolean',
            ]);

            $sync = IlanTakvimSync::where('ilan_id', $ilanId)
                                  ->findOrFail($syncId);

            $sync->update($request->only(['external_listing_id', 'sync_enabled']));

            return response()->json([
                'success' => true,
                'message' => 'Senkronizasyon güncellendi',
                'data' => $sync
            ]);

        } catch (\Exception $e) {
            Log::error('Update sync error', [
                'ilan_id' => $ilanId,
                'sync_id' => $syncId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Senkronizasyon güncellenemedi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteSync($ilanId, $syncId)
    {
        try {
            $sync = IlanTakvimSync::where('ilan_id', $ilanId)
                                  ->findOrFail($syncId);

            $sync->delete();

            return response()->json([
                'success' => true,
                'message' => 'Senkronizasyon silindi'
            ]);

        } catch (\Exception $e) {
            Log::error('Delete sync error', [
                'ilan_id' => $ilanId,
                'sync_id' => $syncId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Senkronizasyon silinemedi: ' . $e->getMessage()
            ], 500);
        }
    }
}
