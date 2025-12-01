<?php

namespace App\Modules\Finans\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finans\Models\Komisyon;
use App\Modules\Finans\Services\KomisyonService;
use App\Services\Logging\LogService;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Commission Controller
 *
 * Context7 Standardı: C7-KOMISYON-CONTROLLER-2025-11-25
 *
 * CRUD operations + AI-powered commission calculation and optimization
 */
class KomisyonController extends Controller
{
    protected KomisyonService $komisyonService;

    public function __construct(KomisyonService $komisyonService)
    {
        $this->komisyonService = $komisyonService;
    }

    /**
     * List all commissions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Komisyon::with(['ilan', 'kisi', 'danisman', 'saticiDanisman', 'aliciDanisman']);

            // Filters
            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->has('komisyon_tipi')) {
                $query->where('komisyon_tipi', $request->input('komisyon_tipi'));
            }

            if ($request->has('danisman_id')) {
                $query->where('danisman_id', $request->input('danisman_id'));
            }

            if ($request->has('kisi_id')) {
                $query->where('kisi_id', $request->input('kisi_id'));
            }

            if ($request->has('ilan_id')) {
                $query->where('ilan_id', $request->input('ilan_id'));
            }

            $komisyonlar = $query->orderBy('hesaplama_tarihi', 'desc')
                ->paginate($request->input('per_page', 20));

            return ResponseService::success($komisyonlar, 'Komisyonlar başarıyla getirildi');
        } catch (\Exception $e) {
            return ResponseService::serverError('Komisyonlar getirilemedi', $e);
        }
    }

    /**
     * Show single commission
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            $komisyon = Komisyon::with(['ilan', 'kisi', 'danisman', 'saticiDanisman', 'aliciDanisman'])->findOrFail($id);

            return ResponseService::success($komisyon, 'Komisyon başarıyla getirildi');
        } catch (\Exception $e) {
            return ResponseService::notFound('Komisyon bulunamadı');
        }
    }

    /**
     * Create new commission
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ilan_id' => 'required|exists:ilanlar,id',
            'kisi_id' => 'required|exists:kisiler,id',
            'danisman_id' => 'required|exists:users,id',
            'komisyon_tipi' => 'required|string|in:satis,kiralama,danismanlik',
            'komisyon_orani' => 'nullable|numeric|min:0|max:100',
            'ilan_fiyati' => 'required|numeric|min:0',
            'para_birimi' => 'required|string|max:3',
            'notlar' => 'nullable|string|max:2000',
            'satici_danisman_id' => 'nullable|exists:users,id',
            'alici_danisman_id' => 'nullable|exists:users,id',
            'split_ratio' => 'nullable|regex:/^\d{1,3}-\d{1,3}$/',
        ]);

        if ($validator->fails()) {
            return ResponseService::validationError($validator->errors()->toArray());
        }

        try {
            if ($request->filled('satici_danisman_id') || $request->filled('alici_danisman_id')) {
                $komisyon = $this->komisyonService->calculateSplitCommission(
                    (int) $request->input('ilan_id'),
                    (int) $request->input('kisi_id'),
                    $request->input('satici_danisman_id'),
                    $request->input('alici_danisman_id'),
                    (string) $request->input('komisyon_tipi'),
                    $request->input('split_ratio')
                );
            } elseif (! $request->has('komisyon_orani')) {
                $komisyon = $this->komisyonService->calculateCommission(
                    $request->input('ilan_id'),
                    $request->input('kisi_id'),
                    $request->input('danisman_id'),
                    $request->input('komisyon_tipi')
                );
            } else {
                // Manuel oran ile oluştur
                $komisyonOrani = $request->input('komisyon_orani');
                $ilanFiyati = $request->input('ilan_fiyati');
                $komisyonTutari = $ilanFiyati * ($komisyonOrani / 100);

                $komisyon = Komisyon::create([
                    'ilan_id' => $request->input('ilan_id'),
                    'kisi_id' => $request->input('kisi_id'),
                    'danisman_id' => $request->input('danisman_id'),
                    'komisyon_tipi' => $request->input('komisyon_tipi'),
                    'komisyon_orani' => $komisyonOrani,
                    'komisyon_tutari' => $komisyonTutari,
                    'para_birimi' => $request->input('para_birimi'),
                    'ilan_fiyati' => $ilanFiyati,
                    'hesaplama_tarihi' => now(),
                    'status' => 'hesaplandi',
                    'notlar' => $request->input('notlar'),
                ]);
            }

            LogService::action('komisyon_created', 'komisyon', $komisyon->id);

            return ResponseService::success($komisyon, 'Komisyon başarıyla oluşturuldu');
        } catch (\Exception $e) {
            return ResponseService::serverError('Komisyon oluşturulamadı', $e);
        }
    }

    /**
     * Update commission
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'komisyon_orani' => 'sometimes|numeric|min:0|max:100',
            'ilan_fiyati' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|string|in:hesaplandi,onaylandi,odendi',
            'notlar' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return ResponseService::validationError($validator->errors()->toArray());
        }

        try {
            $komisyon = Komisyon::findOrFail($id);

            // Oran veya fiyat değiştiyse yeniden hesapla
            if ($request->has('komisyon_orani') || $request->has('ilan_fiyati')) {
                $komisyonOrani = $request->input('komisyon_orani', $komisyon->komisyon_orani);
                $ilanFiyati = $request->input('ilan_fiyati', $komisyon->ilan_fiyati);
                $komisyonTutari = $ilanFiyati * ($komisyonOrani / 100);

                $komisyon->update([
                    'komisyon_orani' => $komisyonOrani,
                    'ilan_fiyati' => $ilanFiyati,
                    'komisyon_tutari' => $komisyonTutari,
                    'hesaplama_tarihi' => now(),
                    'status' => 'hesaplandi',
                    'notlar' => $request->input('notlar', $komisyon->notlar),
                ]);
            } else {
                $komisyon->update($request->only(['status', 'notlar']));
            }

            LogService::action('komisyon_updated', 'komisyon', $komisyon->id);

            return ResponseService::success($komisyon, 'Komisyon başarıyla güncellendi');
        } catch (\Exception $e) {
            return ResponseService::serverError('Komisyon güncellenemedi', $e);
        }
    }

    /**
     * Delete commission
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $komisyon = Komisyon::findOrFail($id);
            $komisyon->delete();

            LogService::action('komisyon_deleted', 'komisyon', $id);

            return ResponseService::success(null, 'Komisyon başarıyla silindi');
        } catch (\Exception $e) {
            return ResponseService::serverError('Komisyon silinemedi', $e);
        }
    }

    /**
     * Approve commission
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve(int $id)
    {
        try {
            $komisyon = Komisyon::findOrFail($id);
            $komisyon->onayla();

            LogService::action('komisyon_approved', 'komisyon', $id);

            return ResponseService::success($komisyon, 'Komisyon başarıyla onaylandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('Komisyon onaylanamadı', $e);
        }
    }

    /**
     * Pay commission
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function pay(int $id)
    {
        try {
            $komisyon = Komisyon::findOrFail($id);
            $komisyon->ode();

            LogService::action('komisyon_paid', 'komisyon', $id);

            return ResponseService::success($komisyon, 'Komisyon başarıyla ödendi olarak işaretlendi');
        } catch (\Exception $e) {
            return ResponseService::serverError('Komisyon ödenemedi', $e);
        }
    }

    /**
     * Recalculate commission
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function recalculate(int $id)
    {
        try {
            $komisyon = Komisyon::findOrFail($id);
            $komisyon->hesaplaKomisyon();

            LogService::action('komisyon_recalculated', 'komisyon', $id);

            return ResponseService::success($komisyon, 'Komisyon başarıyla yeniden hesaplandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('Komisyon yeniden hesaplanamadı', $e);
        }
    }

    // ═══════════════════════════════════════════════════════════
    // 🤖 AI-POWERED ENDPOINTS
    // ═══════════════════════════════════════════════════════════

    /**
     * AI ile optimal komisyon oranı önerisi
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function aiSuggestRate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ilan_id' => 'required|exists:ilanlar,id',
            'komisyon_tipi' => 'required|string|in:satis,kiralama,danismanlik',
            'ilan_fiyati' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ResponseService::validationError($validator->errors()->toArray());
        }

        try {
            $result = $this->komisyonService->suggestOptimalRate(
                $request->input('ilan_id'),
                $request->input('komisyon_tipi'),
                $request->input('ilan_fiyati')
            );

            return ResponseService::success($result, 'AI komisyon oranı önerisi tamamlandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('AI komisyon oranı önerisi başarısız', $e);
        }
    }

    /**
     * AI ile komisyon optimizasyonu
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function aiOptimize(int $id)
    {
        try {
            $komisyon = Komisyon::findOrFail($id);
            $result = $this->komisyonService->optimizeCommission($komisyon);

            return ResponseService::success($result, 'AI komisyon optimizasyonu tamamlandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('AI komisyon optimizasyonu başarısız', $e);
        }
    }

    /**
     * AI ile komisyon analizi
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function aiAnalyze(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'danisman_id' => 'nullable|exists:users,id',
            'komisyon_tipi' => 'nullable|string|in:satis,kiralama,danismanlik',
        ]);

        if ($validator->fails()) {
            return ResponseService::validationError($validator->errors()->toArray());
        }

        try {
            $result = $this->komisyonService->analyzeCommissions(
                $request->input('danisman_id'),
                $request->input('komisyon_tipi')
            );

            return ResponseService::success($result, 'AI komisyon analizi tamamlandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('AI komisyon analizi başarısız', $e);
        }
    }
}
