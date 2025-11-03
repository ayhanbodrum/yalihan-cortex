<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\IlanResim;
use App\Models\Ilan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

/**
 * İlan Resim API Controller
 * 
 * Context7 standartlarına uygun ilan resim yönetimi
 * CRUD işlemleri ve resim yükleme
 */
class IlanResimController extends Controller
{
    /**
     * İlan resimlerini listele
     */
    public function index(Request $request): JsonResponse
    {
        $query = IlanResim::where('status', 'Aktif');

        // İlan ID'ye göre filtrele
        if ($request->has('ilan_id')) {
            $query->where('ilan_id', $request->ilan_id);
        }

        $resimler = $query->orderBy('sira_no', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $resimler,
            'message' => 'İlan resimleri başarıyla getirildi'
        ]);
    }

    /**
     * İlan resmi oluştur
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ilan_id' => 'required|exists:ilanlar,id',
            'resim' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
            'sira_no' => 'nullable|integer|min:1',
            'ana_resim' => 'nullable|boolean',
            'alt_text' => 'nullable|string|max:255',
            'aciklama' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasyon hatası',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ilanId = $request->ilan_id;
            $resim = $request->file('resim');
            
            // Resim dosyasını kaydet
            $dosyaAdi = time() . '_' . uniqid() . '.' . $resim->getClientOriginalExtension();
            $dosyaYolu = $resim->storeAs('ilan-resimleri/' . $ilanId, $dosyaAdi, 'public');

            // Sıra numarası belirle
            $siraNo = $request->sira_no ?? $this->getNextSiraNo($ilanId);

            // Ana resim kontrolü
            $anaResim = $request->ana_resim ?? false;
            if ($anaResim) {
                // Diğer ana resimleri kaldır
                IlanResim::where('ilan_id', $ilanId)
                    ->where('ana_resim', true)
                    ->update(['ana_resim' => false]);
            }

            $ilanResim = IlanResim::create([
                'ilan_id' => $ilanId,
                'dosya_adi' => $dosyaAdi,
                'dosya_yolu' => $dosyaYolu,
                'dosya_boyutu' => $resim->getSize(),
                'mime_type' => $resim->getMimeType(),
                'sira_no' => $siraNo,
                'ana_resim' => $anaResim,
                'alt_text' => $request->alt_text,
                'aciklama' => $request->aciklama,
                'status' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'data' => $ilanResim,
                'message' => 'İlan resmi başarıyla yüklendi'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İlan resmi yüklenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * İlan resmi göster
     */
    public function show(string $id): JsonResponse
    {
        $resim = IlanResim::where('id', $id)
            ->where('status', 'Aktif')
            ->first();

        if (!$resim) {
            return response()->json([
                'success' => false,
                'message' => 'İlan resmi bulunamadı'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $resim,
            'message' => 'İlan resmi başarıyla getirildi'
        ]);
    }

    /**
     * İlan resmi güncelle
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $resim = IlanResim::where('id', $id)
            ->where('status', 'Aktif')
            ->first();

        if (!$resim) {
            return response()->json([
                'success' => false,
                'message' => 'İlan resmi bulunamadı'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'sira_no' => 'nullable|integer|min:1',
            'ana_resim' => 'nullable|boolean',
            'alt_text' => 'nullable|string|max:255',
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
            // Ana resim kontrolü
            if ($request->has('ana_resim') && $request->ana_resim) {
                // Diğer ana resimleri kaldır
                IlanResim::where('ilan_id', $resim->ilan_id)
                    ->where('id', '!=', $id)
                    ->where('ana_resim', true)
                    ->update(['ana_resim' => false]);
            }

            $resim->update($request->only(['sira_no', 'ana_resim', 'alt_text', 'aciklama', 'status']));

            return response()->json([
                'success' => true,
                'data' => $resim,
                'message' => 'İlan resmi başarıyla güncellendi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İlan resmi güncellenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * İlan resmi sil
     */
    public function destroy(string $id): JsonResponse
    {
        $resim = IlanResim::where('id', $id)
            ->where('status', 'Aktif')
            ->first();

        if (!$resim) {
            return response()->json([
                'success' => false,
                'message' => 'İlan resmi bulunamadı'
            ], 404);
        }

        try {
            // Dosyayı fiziksel olarak sil
            if (Storage::disk('public')->exists($resim->dosya_yolu)) {
                Storage::disk('public')->delete($resim->dosya_yolu);
            }

            // Veritabanından sil
            $resim->update(['status' => 'inactive']);

            return response()->json([
                'success' => true,
                'message' => 'İlan resmi başarıyla silindi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İlan resmi silinirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resim sıralamasını güncelle
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ilan_id' => 'required|exists:ilanlar,id',
            'resimler' => 'required|array',
            'resimler.*.id' => 'required|exists:ilan_resimleri,id',
            'resimler.*.sira_no' => 'required|integer|min:1',
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

            foreach ($request->resimler as $resimData) {
                IlanResim::where('id', $resimData['id'])
                    ->where('ilan_id', $request->ilan_id)
                    ->update(['sira_no' => $resimData['sira_no']]);
            }

            DB::commit();

            $resimler = IlanResim::where('ilan_id', $request->ilan_id)
                ->where('status', 'Aktif')
                ->orderBy('sira_no', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $resimler,
                'message' => 'Resim sıralaması başarıyla güncellendi'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Resim sıralaması güncellenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sonraki sıra numarasını al
     */
    private function getNextSiraNo(int $ilanId): int
    {
        $maxSira = IlanResim::where('ilan_id', $ilanId)
            ->where('status', 'Aktif')
            ->max('sira_no');

        return ($maxSira ?? 0) + 1;
    }
}