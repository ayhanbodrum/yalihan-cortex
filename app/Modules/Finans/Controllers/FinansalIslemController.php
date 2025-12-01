<?php

namespace App\Modules\Finans\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finans\Models\FinansalIslem;
use App\Modules\Finans\Services\FinansService;
use App\Services\Logging\LogService;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Financial Transaction Controller
 *
 * Context7 Standardı: C7-FINANS-CONTROLLER-2025-11-25
 *
 * CRUD operations + AI-powered analysis and suggestions
 */
class FinansalIslemController extends Controller
{
    protected FinansService $finansService;

    public function __construct(FinansService $finansService)
    {
        $this->finansService = $finansService;
    }

    /**
     * List all financial transactions
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = FinansalIslem::with(['ilan', 'kisi', 'gorev', 'onaylayan']);

            // Filters
            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->has('islem_tipi')) {
                $query->where('islem_tipi', $request->input('islem_tipi'));
            }

            if ($request->has('kisi_id')) {
                $query->where('kisi_id', $request->input('kisi_id'));
            }

            if ($request->has('ilan_id')) {
                $query->where('ilan_id', $request->input('ilan_id'));
            }

            if ($request->has('start_date')) {
                $query->where('tarih', '>=', $request->input('start_date'));
            }

            if ($request->has('end_date')) {
                $query->where('tarih', '<=', $request->input('end_date'));
            }

            $islemler = $query->orderBy('tarih', 'desc')
                ->paginate($request->input('per_page', 20));

            // If AJAX request, return JSON
            if ($request->wantsJson() || $request->ajax()) {
                return ResponseService::success($islemler, 'Finansal işlemler başarıyla getirildi');
            }

            // Return view for web requests
            return view('admin.finans.islemler.index', [
                'islemler' => $islemler
            ]);
        } catch (\Exception $e) {
            // If AJAX request, return JSON error
            if ($request->wantsJson() || $request->ajax()) {
                return ResponseService::serverError('Finansal işlemler getirilemedi', $e);
            }

            // Return view with error for web requests
            return view('admin.finans.islemler.index', [
                'islemler' => collect([])
            ])->with('error', 'Finansal işlemler yüklenirken bir hata oluştu.');
        }
    }

    /**
     * Show single financial transaction
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            $islem = FinansalIslem::with(['ilan', 'kisi', 'gorev', 'onaylayan'])->findOrFail($id);

            return ResponseService::success($islem, 'Finansal işlem başarıyla getirildi');
        } catch (\Exception $e) {
            return ResponseService::notFound('Finansal işlem bulunamadı');
        }
    }

    /**
     * Create new financial transaction
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ilan_id' => 'nullable|exists:ilanlar,id',
            'kisi_id' => 'nullable|exists:kisiler,id',
            'gorev_id' => 'nullable|exists:gorevler,id',
            'islem_tipi' => 'required|string|in:komisyon,odeme,masraf,gelir,gider',
            'miktar' => 'required|numeric|min:0',
            'para_birimi' => 'required|string|max:3',
            'aciklama' => 'nullable|string|max:1000',
            'tarih' => 'required|date',
            'referans_no' => 'nullable|string|max:100',
            'fatura_no' => 'nullable|string|max:100',
            'notlar' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            // If AJAX request, return JSON
            if ($request->wantsJson() || $request->ajax()) {
                return ResponseService::validationError($validator->errors()->toArray());
            }

            // Return back with errors for web requests
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $islem = FinansalIslem::create([
                'ilan_id' => $request->input('ilan_id') ?: null,
                'kisi_id' => $request->input('kisi_id') ?: null,
                'gorev_id' => $request->input('gorev_id') ?: null,
                'islem_tipi' => $request->input('islem_tipi'),
                'miktar' => $request->input('miktar'),
                'para_birimi' => $request->input('para_birimi'),
                'aciklama' => $request->input('aciklama'),
                'tarih' => $request->input('tarih'),
                'status' => 'bekliyor',
                'referans_no' => $request->input('referans_no'),
                'fatura_no' => $request->input('fatura_no'),
                'notlar' => $request->input('notlar'),
            ]);

            LogService::action('finansal_islem_created', 'finansal_islem', $islem->id);

            // If AJAX request, return JSON
            if ($request->wantsJson() || $request->ajax()) {
                return ResponseService::success($islem, 'Finansal işlem başarıyla oluşturuldu');
            }

            // Redirect for web requests
            return redirect()->route('admin.finans.islemler.index')
                ->with('success', 'Finansal işlem başarıyla oluşturuldu');
        } catch (\Exception $e) {
            // If AJAX request, return JSON error
            if ($request->wantsJson() || $request->ajax()) {
                return ResponseService::serverError('Finansal işlem oluşturulamadı', $e);
            }

            // Return back with error for web requests
            return redirect()->back()
                ->with('error', 'Finansal işlem oluşturulurken bir hata oluştu.')
                ->withInput();
        }
    }

    /**
     * Update financial transaction
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'islem_tipi' => 'sometimes|string|in:komisyon,odeme,masraf,gelir,gider',
            'miktar' => 'sometimes|numeric|min:0',
            'para_birimi' => 'sometimes|string|max:3',
            'aciklama' => 'nullable|string|max:1000',
            'tarih' => 'sometimes|date',
            'status' => 'sometimes|string|in:bekliyor,onaylandi,reddedildi,tamamlandi',
            'referans_no' => 'nullable|string|max:100',
            'fatura_no' => 'nullable|string|max:100',
            'notlar' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return ResponseService::validationError($validator->errors()->toArray());
        }

        try {
            $islem = FinansalIslem::findOrFail($id);
            $islem->update($request->only([
                'islem_tipi',
                'miktar',
                'para_birimi',
                'aciklama',
                'tarih',
                'status',
                'referans_no',
                'fatura_no',
                'notlar',
            ]));

            LogService::action('finansal_islem_updated', 'finansal_islem', $islem->id);

            return ResponseService::success($islem, 'Finansal işlem başarıyla güncellendi');
        } catch (\Exception $e) {
            return ResponseService::serverError('Finansal işlem güncellenemedi', $e);
        }
    }

    /**
     * Delete financial transaction
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $islem = FinansalIslem::findOrFail($id);
            $islem->delete();

            LogService::action('finansal_islem_deleted', 'finansal_islem', $id);

            return ResponseService::success(null, 'Finansal işlem başarıyla silindi');
        } catch (\Exception $e) {
            return ResponseService::serverError('Finansal işlem silinemedi', $e);
        }
    }

    /**
     * Approve financial transaction
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve(Request $request, int $id)
    {
        try {
            $islem = FinansalIslem::findOrFail($id);
            $onaylayanId = $request->user()->id;

            $islem->onayla($onaylayanId);

            LogService::action('finansal_islem_approved', 'finansal_islem', $id, [
                'onaylayan_id' => $onaylayanId,
            ]);

            return ResponseService::success($islem, 'Finansal işlem başarıyla onaylandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('Finansal işlem onaylanamadı', $e);
        }
    }

    /**
     * Reject financial transaction
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'not' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return ResponseService::validationError($validator->errors()->toArray());
        }

        try {
            $islem = FinansalIslem::findOrFail($id);
            $onaylayanId = $request->user()->id;

            $islem->reddet($onaylayanId, $request->input('not'));

            LogService::action('finansal_islem_rejected', 'finansal_islem', $id, [
                'onaylayan_id' => $onaylayanId,
                'not' => $request->input('not'),
            ]);

            return ResponseService::success($islem, 'Finansal işlem başarıyla reddedildi');
        } catch (\Exception $e) {
            return ResponseService::serverError('Finansal işlem reddedilemedi', $e);
        }
    }

    /**
     * Complete financial transaction
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete(int $id)
    {
        try {
            $islem = FinansalIslem::findOrFail($id);
            $islem->tamamla();

            LogService::action('finansal_islem_completed', 'finansal_islem', $id);

            return ResponseService::success($islem, 'Finansal işlem başarıyla tamamlandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('Finansal işlem tamamlanamadı', $e);
        }
    }

    // ═══════════════════════════════════════════════════════════
    // 🤖 AI-POWERED ENDPOINTS
    // ═══════════════════════════════════════════════════════════

    /**
     * AI ile finansal analiz
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function aiAnalyze(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kisi_id' => 'nullable|exists:kisiler,id',
            'ilan_id' => 'nullable|exists:ilanlar,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return ResponseService::validationError($validator->errors()->toArray());
        }

        try {
            $query = FinansalIslem::query();

            if ($request->has('kisi_id')) {
                $query->where('kisi_id', $request->input('kisi_id'));
            }

            if ($request->has('ilan_id')) {
                $query->where('ilan_id', $request->input('ilan_id'));
            }

            if ($request->has('start_date')) {
                $query->where('tarih', '>=', $request->input('start_date'));
            }

            if ($request->has('end_date')) {
                $query->where('tarih', '<=', $request->input('end_date'));
            }

            $data = $query->get()->map(function ($islem) {
                return [
                    'tarih' => $islem->tarih->format('Y-m-d'),
                    'islem_tipi' => $islem->islem_tipi,
                    'miktar' => $islem->miktar,
                    'para_birimi' => $islem->para_birimi,
                    'status' => $islem->status,
                ];
            })->toArray();

            $result = $this->finansService->analyzeFinancials($data, [
                'kisi_id' => $request->input('kisi_id'),
                'ilan_id' => $request->input('ilan_id'),
            ]);

            return ResponseService::success($result, 'AI finansal analiz tamamlandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('AI finansal analiz başarısız', $e);
        }
    }

    /**
     * AI ile finansal tahmin
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function aiPredict(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kisi_id' => 'nullable|exists:kisiler,id',
            'ilan_id' => 'nullable|exists:ilanlar,id',
            'period' => 'required|string|in:month,quarter,year',
        ]);

        if ($validator->fails()) {
            return ResponseService::validationError($validator->errors()->toArray());
        }

        try {
            $result = $this->finansService->predictFinancials(
                $request->input('kisi_id'),
                $request->input('ilan_id'),
                $request->input('period')
            );

            return ResponseService::success($result, 'AI finansal tahmin tamamlandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('AI finansal tahmin başarısız', $e);
        }
    }

    /**
     * AI ile fatura önerisi
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function aiSuggestInvoice(int $id)
    {
        try {
            $islem = FinansalIslem::findOrFail($id);
            $result = $this->finansService->suggestInvoice($islem);

            return ResponseService::success($result, 'AI fatura önerisi tamamlandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('AI fatura önerisi başarısız', $e);
        }
    }

    /**
     * AI ile risk analizi
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function aiAnalyzeRisk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kisi_id' => 'nullable|exists:kisiler,id',
            'ilan_id' => 'nullable|exists:ilanlar,id',
        ]);

        if ($validator->fails()) {
            return ResponseService::validationError($validator->errors()->toArray());
        }

        try {
            $result = $this->finansService->analyzeRisk(
                $request->input('kisi_id'),
                $request->input('ilan_id')
            );

            return ResponseService::success($result, 'AI risk analizi tamamlandı');
        } catch (\Exception $e) {
            return ResponseService::serverError('AI risk analizi başarısız', $e);
        }
    }

    /**
     * AI ile özet rapor
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function aiGenerateSummary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'islem_tipi' => 'nullable|string|in:komisyon,odeme,masraf,gelir,gider',
            'status' => 'nullable|string|in:bekliyor,onaylandi,reddedildi,tamamlandi',
        ]);

        if ($validator->fails()) {
            return ResponseService::validationError($validator->errors()->toArray());
        }

        try {
            $filters = $request->only(['start_date', 'end_date', 'islem_tipi', 'status']);
            $result = $this->finansService->generateSummaryReport($filters);

            return ResponseService::success($result, 'AI özet rapor oluşturuldu');
        } catch (\Exception $e) {
            return ResponseService::serverError('AI özet rapor oluşturulamadı', $e);
        }
    }
}
