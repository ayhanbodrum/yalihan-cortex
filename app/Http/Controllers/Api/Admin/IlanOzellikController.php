<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\IlanOzellik;
use App\Models\Ilan;
use App\Models\Ozellik;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * İlan Özellik API Controller
 * 
 * Context7 standartlarına uygun ilan özellik yönetimi
 * CRUD işlemleri ve özellik değer yönetimi
 */
class IlanOzellikController extends Controller
{
    /**
     * İlan özelliklerini listele
     */
    public function index(Request $request): JsonResponse
    {
        $query = IlanOzellik::with(['ilan', 'ozellik'])
            ->where('status', 'Aktif');

        // İlan ID'ye göre filtrele
        if ($request->has('ilan_id')) {
            $query->where('ilan_id', $request->ilan_id);
        }

        // Özellik ID'ye göre filtrele
        if ($request->has('ozellik_id')) {
            $query->where('ozellik_id', $request->ozellik_id);
        }

        $ozellikler = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $ozellikler,
            'message' => 'İlan özellikleri başarıyla getirildi'
        ]);
    }

    /**
     * İlan özelliği oluştur
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ilan_id' => 'required|exists:ilanlar,id',
            'ozellik_id' => 'required|exists:ozellikler,id',
            'deger' => 'nullable|string|max:255',
            'aciklama' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasyon hatası',
                'errors' => $validator->errors()
            ], 422);
        }

        // Aynı ilan için aynı özellik zaten var mı kontrol et
        $existing = IlanOzellik::where('ilan_id', $request->ilan_id)
            ->where('ozellik_id', $request->ozellik_id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Bu ilan için bu özellik zaten mevcut'
            ], 409);
        }

        try {
            $ozellik = IlanOzellik::create([
                'ilan_id' => $request->ilan_id,
                'ozellik_id' => $request->ozellik_id,
                'deger' => $request->deger,
                'aciklama' => $request->aciklama,
                'status' => 'active'
            ]);

            $ozellik->load(['ilan', 'ozellik']);

            return response()->json([
                'success' => true,
                'data' => $ozellik,
                'message' => 'İlan özelliği başarıyla oluşturuldu'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İlan özelliği oluşturulurken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * İlan özelliği göster
     */
    public function show(string $id): JsonResponse
    {
        $ozellik = IlanOzellik::with(['ilan', 'ozellik'])
            ->where('id', $id)
            ->where('status', 'Aktif')
            ->first();

        if (!$ozellik) {
            return response()->json([
                'success' => false,
                'message' => 'İlan özelliği bulunamadı'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ozellik,
            'message' => 'İlan özelliği başarıyla getirildi'
        ]);
    }

    /**
     * İlan özelliği güncelle
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $ozellik = IlanOzellik::where('id', $id)
            ->where('status', 'Aktif')
            ->first();

        if (!$ozellik) {
            return response()->json([
                'success' => false,
                'message' => 'İlan özelliği bulunamadı'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'deger' => 'nullable|string|max:255',
            'aciklama' => 'nullable|string|max:1000',
            'status' => 'nullable|in:Aktif,Pasif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasyon hatası',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ozellik->update($request->only(['deger', 'aciklama', 'status']));
            $ozellik->load(['ilan', 'ozellik']);

            return response()->json([
                'success' => true,
                'data' => $ozellik,
                'message' => 'İlan özelliği başarıyla güncellendi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İlan özelliği güncellenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * İlan özelliği sil
     */
    public function destroy(string $id): JsonResponse
    {
        $ozellik = IlanOzellik::where('id', $id)
            ->where('status', 'Aktif')
            ->first();

        if (!$ozellik) {
            return response()->json([
                'success' => false,
                'message' => 'İlan özelliği bulunamadı'
            ], 404);
        }

        try {
            $ozellik->update(['status' => 'inactive']);

            return response()->json([
                'success' => true,
                'message' => 'İlan özelliği başarıyla silindi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İlan özelliği silinirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * İlan için özellikleri toplu güncelle
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ilan_id' => 'required|exists:ilanlar,id',
            'ozellikler' => 'required|array',
            'ozellikler.*.ozellik_id' => 'required|exists:ozellikler,id',
            'ozellikler.*.deger' => 'nullable|string|max:255',
            'ozellikler.*.aciklama' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasyon hatası',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $ilanId = $request->ilan_id;
            
            // Mevcut özellikleri pasif yap
            IlanOzellik::where('ilan_id', $ilanId)->update(['status' => 'inactive']);

            // Yeni özellikleri ekle
            foreach ($request->ozellikler as $ozellikData) {
                IlanOzellik::updateOrCreate(
                    [
                        'ilan_id' => $ilanId,
                        'ozellik_id' => $ozellikData['ozellik_id']
                    ],
                    [
                        'deger' => $ozellikData['deger'] ?? null,
                        'aciklama' => $ozellikData['aciklama'] ?? null,
                        'status' => 'active'
                    ]
                );
            }

            DB::commit();

            $ozellikler = IlanOzellik::with(['ozellik'])
                ->where('ilan_id', $ilanId)
                ->where('status', 'Aktif')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $ozellikler,
                'message' => 'İlan özellikleri başarıyla güncellendi'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'İlan özellikleri güncellenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}