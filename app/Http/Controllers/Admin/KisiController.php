<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kisi;
use App\Modules\Crm\Services\KisiService;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KisiController extends AdminController
{
    protected $kisiService;

    /**
     * Constructor
     * Context7: KisiService dependency injection
     */
    public function __construct(KisiService $kisiService)
    {
        $this->kisiService = $kisiService;
    }

    /**
     * Display a listing of the resource.
     * Context7: Kişi listesi ve filtreleme
     *
     * @return Response|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // ✅ EAGER LOADING: Select optimization ile birlikte
        $query = Kisi::with([
            'danisman:id,name,email',
            'il:id,il_adi',
            'ilce:id,ilce_adi',
            'mahalle:id,mahalle_adi',
        ])->select([
            'id', 'ad', 'soyad', 'telefon', 'email', 'status',
            'danisman_id', 'il_id', 'ilce_id', 'mahalle_id',
            'kisi_tipi', 'created_at', 'updated_at',
        ]);

        // Context7 uyumlu arama
        $search = trim((string) $request->get('q'));
        if ($search !== '') {
            $query->search($search); // Model scope kullanımı
        }

        // Context7 uyumlu status filtreleme
        $status = $request->get('status');
        if ($status === 'Aktif') {
            $query->where('status', true); // Context7 uyumlu
        } elseif ($status === 'Pasif') {
            $query->pasif(); // Model scope kullanımı
        }

        // Danışman filtresi (Context7 uyumlu)
        $danismanId = $request->get('danisman_id');
        if ($danismanId) {
            $query->byDanisman($danismanId); // Model scope kullanımı
        }

        // Kişi tipi filtresi (Context7: kisi_tipi, not musteri_tipi)
        $kisiTipi = $request->get('kisi_tipi');
        if ($kisiTipi) {
            $query->where('kisi_tipi', $kisiTipi);
        }

        // Context7 uyumlu sıralama
        $sort = $request->get('sort');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('ad')->orderBy('soyad');
                break;
            case 'name_desc':
                $query->orderByDesc('ad')->orderByDesc('soyad');
                break;
            case 'created_asc':
                $query->orderBy('created_at');
                break;
            case 'crm_score_desc':
                // CRM skoruna göre sıralama (Context7 özelliği)
                $query->orderByRaw('(
                    CASE WHEN ad IS NOT NULL AND soyad IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN telefon IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN email IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN tc_kimlik IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN il_id IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN ilce_id IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN mahalle_id IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN kisi_tipi IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN meslek IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN gelir_duzeyi IS NOT NULL THEN 10 ELSE 0 END
                ) DESC');
                break;
            default:
                $query->orderByDesc('created_at');
        }

        $kisiler = $query->paginate(20)->withQueryString();

        // Context7 uyumlu istatistikler (view'da $stats olarak kullanılıyor)
        $stats = [
            'total' => Kisi::count(),
            'active' => Kisi::where('status', true)->count(), // Context7 uyumlu
            'pasif' => Kisi::pasif()->count(),
            'taslak' => Kisi::where('status', false)->count(), // Pasif kişi sayısı
            'ev_sahibi' => Kisi::where('kisi_tipi', 'Ev Sahibi')->count(),
            'satici' => Kisi::where('kisi_tipi', 'Satıcı')->count(),
            'alici' => Kisi::where('kisi_tipi', 'Alıcı')->count(),
            'kiraci' => Kisi::where('kisi_tipi', 'Kiracı')->count(),
        ];

        // Backward compatibility
        $istatistikler = $stats;
        $taslak = $stats['taslak'];

        // Context7 uyumlu mükerrer kontrolü
        $olasiKopyalar = [];
        if ($search === '') {
            $olasiKopyalar = Kisi::select('email')
                ->whereNotNull('email')
                ->groupBy('email')
                ->havingRaw('COUNT(*) > 1')
                ->limit(5)
                ->pluck('email')
                ->toArray();
        }

        $filters = ['q' => $search, 'status' => $status, 'sort' => $sort, 'danisman_id' => $danismanId, 'kisi_tipi' => $kisiTipi]; // Context7: kisi_tipi

        // ✅ Context7 & Yalıhan Bekçi: Danışman sorgusu standartı
        // 1. Spatie Permission: whereHas('roles') kullan (roles plural, role singular YASAK)
        // 2. Status kontrolü: where('status', 1) - Sadece aktif danışmanlar
        // 3. Select optimization: select(['id', 'name', 'email'])
        // 4. Eager loading: with('roles:id,name') - N+1 query önleme
        // 5. Sıralama: orderBy('name')
        $danismanlar = \App\Models\User::with('roles:id,name')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'danisman');
            })
            ->where('status', 1) // ✅ Context7: Sadece aktif danışmanlar
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get();

        // ✅ FALLBACK: Eğer role ile danışman bulunamazsa, aktif kullanıcıları göster
        // Bu durum geçici bir çözümdür - kullanıcılara role atanmalı
        if ($danismanlar->isEmpty()) {
            $danismanlar = \App\Models\User::where('status', 1)
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->get();
        }

        if (view()->exists('admin.kisiler.index')) {
            return response()->view('admin.kisiler.index', compact('kisiler', 'filters', 'stats', 'istatistikler', 'olasiKopyalar', 'taslak', 'danismanlar'));
        }

        return $this->renderAny(['Crm::musteriler.index'], compact('kisiler', 'filters', 'istatistikler'));
    }

    /**
     * Show the form for creating a new resource.
     * Context7: Yeni kişi oluşturma formu
     *
     * @return Response|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        // ✅ Context7 & Yalıhan Bekçi: Danışman sorgusu standartı
        // 1. Spatie Permission: whereHas('roles') kullan (roles plural, role singular YASAK)
        // 2. Status kontrolü: where('status', 1) - Sadece aktif danışmanlar
        // 3. Select optimization: select(['id', 'name', 'email'])
        // 4. Eager loading: with('roles:id,name') - N+1 query önleme
        // 5. Sıralama: orderBy('name')
        $danismanlar = \App\Models\User::with('roles:id,name')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'danisman');
            })
            ->where('status', 1) // ✅ Context7: Sadece aktif danışmanlar
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get();

        // ✅ FALLBACK: Eğer role ile danışman bulunamazsa, aktif kullanıcıları göster
        // Bu durum geçici bir çözümdür - kullanıcılara role atanmalı
        if ($danismanlar->isEmpty()) {
            $danismanlar = \App\Models\User::where('status', 1)
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->get();
        }

        // ✅ N+1 FIX: Select optimization
        $iller = \App\Models\Il::select(['id', 'il_adi'])
            ->orderBy('il_adi')
            ->get();

        $musteriTipleri = ['ev_sahibi', 'satici', 'alici', 'kiraci'];
        $kaynaklar = ['Web', 'Telefon', 'Referans', 'Sosyal Medya', 'Diğer'];

        return $this->renderAny(['admin.kisiler.create', 'Crm::kisiler.create'], compact('danismanlar', 'iller', 'musteriTipleri', 'kaynaklar'));
    }

    /**
     * Store a newly created resource in storage.
     * Context7: Yeni kişi kaydetme
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function store(Request $request)
    {
        // Context7 uyumlu validasyon (mahalle_id added - 2025-10-31, adres fixed - 2025-11-13, kisi_tipi required - 2025-11-11)
        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'soyad' => 'required|string|max:255',
            'telefon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'tc_kimlik' => 'nullable|string|max:11|min:11',
            'status' => 'required|string|in:Aktif,Pasif,Beklemede',
            'kisi_tipi' => 'required|string|in:Müşteri,Potansiyel,Tedarikçi,Yatırımcı,Ev Sahibi,Alıcı,Kiracı,Satıcı', // ✅ Context7: kisi_tipi required (NOT NULL constraint)
            'danisman_id' => 'nullable|exists:users,id',
            'il_id' => 'nullable|exists:iller,id',
            'ilce_id' => 'nullable|exists:ilceler,id',
            'mahalle_id' => 'nullable|exists:mahalleler,id',
            'adres_detay' => 'nullable|string|max:500', // Form field name
            'notlar' => 'nullable|string',
        ]);

        // ✅ Context7: adres_detay → adres mapping (database column name)
        if (isset($validated['adres_detay'])) {
            $validated['adres'] = $validated['adres_detay'];
            unset($validated['adres_detay']);
        }

        try {
            // ✅ REFACTORED: Use KisiService
            $kisi = $this->kisiService->createKisi($validated);

            return redirect()
                ->route('admin.kisiler.index')
                ->with('success', $kisi->ad.' '.$kisi->soyad.' başarıyla eklendi! ✅');
        } catch (\Exception $e) {
            // ✅ STANDARDIZED: Using ResponseService (automatic logging)
            if ($request->expectsJson()) {
                return ResponseService::serverError('Kişi eklenirken hata oluştu', $e);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Kişi eklenirken hata oluştu: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Context7: Kişi detay sayfası
     *
     * @param  int|string|Kisi  $kisi
     * @return Response|\Illuminate\Contracts\View\View
     */
    public function show($kisi)
    {
        $kisi = $this->resolve($kisi);

        // ✅ N+1 FIX: Eager loading ekle
        $kisi->load([
            'danisman:id,name,email',
            'il:id,il_adi',
            'ilce:id,ilce_adi',
            'mahalle:id,mahalle_adi',
            'etiketler:id,name,color',
        ]);

        return $this->renderAny(['admin.kisiler.show', 'Crm::musteriler.show'], ['kisi' => $kisi]);
    }

    /**
     * Show the form for editing the specified resource.
     * Context7: Kişi düzenleme formu
     *
     * @param  int|string|Kisi  $kisi
     * @return Response|\Illuminate\Contracts\View\View
     */
    public function edit($kisi)
    {
        $kisi = $this->resolve($kisi);

        // ✅ N+1 FIX: Eager loading ekle
        $kisi->load([
            'danisman:id,name,email',
            'il:id,il_adi',
            'ilce:id,ilce_adi',
            'mahalle:id,mahalle_adi',
            'etiketler:id,name,color',
        ]);

        // ✅ Context7 & Yalıhan Bekçi: Danışman sorgusu standartı
        // 1. Spatie Permission: whereHas('roles') kullan (roles plural, role singular YASAK)
        // 2. Status kontrolü: where('status', 1) - Sadece aktif danışmanlar
        // 3. Select optimization: select(['id', 'name', 'email'])
        // 4. Eager loading: with('roles:id,name') - N+1 query önleme
        // 5. Sıralama: orderBy('name')
        $danismanlar = \App\Models\User::with('roles:id,name')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'danisman');
            })
            ->where('status', 1) // ✅ Context7: Sadece aktif danışmanlar
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get();

        // ✅ FALLBACK: Eğer role ile danışman bulunamazsa, aktif kullanıcıları göster
        // Bu durum geçici bir çözümdür - kullanıcılara role atanmalı
        if ($danismanlar->isEmpty()) {
            $danismanlar = \App\Models\User::where('status', 1)
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->get();
        }

        // ✅ N+1 FIX: Select optimization
        $iller = \App\Models\Il::select(['id', 'il_adi'])
            ->orderBy('il_adi')
            ->get();

        // ✅ N+1 FIX: Select optimization
        $ilceler = $kisi->il_id ? \App\Models\Ilce::where('il_id', $kisi->il_id)
            ->select(['id', 'ilce_adi'])
            ->orderBy('ilce_adi')
            ->get() : [];

        // ✅ N+1 FIX: Select optimization
        $mahalleler = $kisi->ilce_id ? \App\Models\Mahalle::where('ilce_id', $kisi->ilce_id)
            ->select(['id', 'mahalle_adi'])
            ->orderBy('mahalle_adi')
            ->get() : [];

        $musteriTipleri = ['ev_sahibi', 'satici', 'alici', 'kiraci'];

        // ✅ N+1 FIX: Select optimization
        $etiketler = \App\Modules\Crm\Models\Etiket::select(['id', 'name', 'color'])
            ->orderBy('name')
            ->get();

        $kaynaklar = ['Web', 'Telefon', 'Referans', 'Sosyal Medya', 'Diğer'];

        // Kişinin mevcut etiket ID'lerini al
        $kisiEtiketIds = $kisi->etiketler ? $kisi->etiketler->pluck('id')->toArray() : [];

        return $this->renderAny(['admin.kisiler.edit', 'Crm::musteriler.edit'], [
            'kisi' => $kisi,
            'danismanlar' => $danismanlar,
            'iller' => $iller,
            'ilceler' => $ilceler,
            'mahalleler' => $mahalleler,
            'musteriTipleri' => $musteriTipleri,
            'etiketler' => $etiketler,
            'kaynaklar' => $kaynaklar,
            'kisiEtiketIds' => $kisiEtiketIds,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * Context7: Kişi güncelleme
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function update(Request $request, Kisi $kisi)
    {
        // Context7: Validation (mahalle_id added - 2025-10-31, adres fixed - 2025-11-13)
        $validated = $request->validate([
            'ad' => 'required|string|max:100',
            'soyad' => 'required|string|max:100',
            'telefon' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'kisi_tipi' => 'required|in:Müşteri,Potansiyel,Tedarikçi,Yatırımcı,Ev Sahibi,Alıcı,Kiracı,Satıcı',
            'danisman_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Aktif,Pasif,Beklemede',
            'il_id' => 'nullable|exists:iller,id',
            'ilce_id' => 'nullable|exists:ilceler,id',
            'mahalle_id' => 'nullable|exists:mahalleler,id',
            'adres_detay' => 'nullable|string|max:500', // Form field name
            'notlar' => 'nullable|string',
            'etiketler_ids' => 'nullable|array',
            'etiketler_ids.*' => 'exists:etiketler,id',
        ]);

        // ✅ Context7: adres_detay → adres mapping (database column name)
        if (isset($validated['adres_detay'])) {
            $validated['adres'] = $validated['adres_detay'];
            unset($validated['adres_detay']);
        }

        try {
            // Remove non-existent fields
            $updateData = collect($validated)->except(['etiketler_ids'])->toArray();

            // ✅ REFACTORED: Use KisiService
            $this->kisiService->updateKisi($kisi, $updateData);

            // Sync etiketler (tags) if provided
            if ($request->has('etiketler_ids')) {
                $kisi->etiketler()->sync($request->etiketler_ids);
            }

            return redirect()
                ->route('admin.kisiler.edit', $kisi->id)
                ->with('success', $kisi->ad.' '.$kisi->soyad.' başarıyla güncellendi! ✅');
        } catch (\Exception $e) {
            // ✅ STANDARDIZED: Using ResponseService (automatic logging)
            if ($request->expectsJson()) {
                return ResponseService::serverError('Kişi güncellenirken hata oluştu', $e);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Kişi güncellenirken hata oluştu: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Context7: Kişi silme
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function destroy(Kisi $kisi)
    {
        try {
            // ✅ REFACTORED: Use KisiService
            $kisiAdi = $kisi->ad.' '.$kisi->soyad;
            $this->kisiService->deleteKisi($kisi);

            // JSON response for AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $kisiAdi.' başarıyla silindi.',
                ]);
            }

            // Redirect for form submissions
            return redirect()
                ->route('admin.kisiler.index')
                ->with('success', $kisiAdi.' başarıyla silindi.');
        } catch (\Exception $e) {
            // JSON response for AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kişi silinirken bir hata oluştu: '.$e->getMessage(),
                ], 500);
            }

            // Redirect for form submissions
            return redirect()
                ->route('admin.kisiler.index')
                ->with('error', 'Kişi silinirken bir hata oluştu: '.$e->getMessage());
        }
    }

    /**
     * Search persons
     * Context7: Kişi arama endpoint
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        // Context7 uyumlu kişi arama
        $search = $request->get('q', '');
        $limit = $request->get('limit', 10);

        if (empty($search)) {
            return response()->json(['items' => []]);
        }

        $kisiler = Kisi::search($search)
            ->where('status', true) // Context7 uyumlu
            ->with(['il', 'ilce'])
            ->limit($limit)
            ->get()
            ->map(function ($kisi) {
                return [
                    'id' => $kisi->id,
                    'text' => $kisi->tam_ad.' - '.($kisi->telefon ?? 'Tel yok').' - '.($kisi->il->il_adi ?? ''),
                    'tam_ad' => $kisi->tam_ad,
                    'telefon' => $kisi->telefon,
                    'email' => $kisi->email,
                    'il' => $kisi->il->il_adi ?? '',
                    'crm_score' => $kisi->crm_score,
                    'is_owner_eligible' => $kisi->isOwnerEligible(),
                ];
            });

        return response()->json(['items' => $kisiler]);
    }

    /**
     * Check for duplicate persons
     * Context7: Mükerrer kişi kontrolü
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkDuplicate(Request $request)
    {
        // Context7 uyumlu mükerrer kontrolü
        $email = $request->get('email');
        $telefon = $request->get('telefon');
        $tc_kimlik = $request->get('tc_kimlik');

        $duplicates = [];

        if ($email) {
            $emailDuplicate = Kisi::where('email', $email)->first();
            if ($emailDuplicate) {
                $duplicates[] = [
                    'type' => 'email',
                    'kisi' => $emailDuplicate->tam_ad,
                    'value' => $email,
                ];
            }
        }

        if ($telefon) {
            $telefonDuplicate = Kisi::where('telefon', $telefon)->first();
            if ($telefonDuplicate) {
                $duplicates[] = [
                    'type' => 'telefon',
                    'kisi' => $telefonDuplicate->tam_ad,
                    'value' => $telefon,
                ];
            }
        }

        if ($tc_kimlik) {
            $tcDuplicate = Kisi::where('tc_kimlik', $tc_kimlik)->first();
            if ($tcDuplicate) {
                $duplicates[] = [
                    'type' => 'tc_kimlik',
                    'kisi' => $tcDuplicate->tam_ad,
                    'value' => $tc_kimlik,
                ];
            }
        }

        return response()->json([
            'duplicate' => ! empty($duplicates),
            'duplicates' => $duplicates,
        ]);
    }

    /**
     * Bulk action for persons
     * Context7: Toplu işlem endpoint
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAction(Request $request)
    {
        // Context7 uyumlu toplu işlemler
        $action = $request->get('action');
        $ids = $request->get('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Hiç kişi seçilmedi']);
        }

        $query = Kisi::whereIn('id', $ids);

        switch ($action) {
            case 'activate':
                $query->update(['status' => true]); // Context7 uyumlu
                $message = count($ids).' kişi etkinleştirildi';
                break;

            case 'pasif_yap':
                $query->update(['status' => false]); // Context7 uyumlu
                $message = count($ids).' kişi pasif yapıldı';
                break;

            case 'sil':
            case 'delete':
                $query->delete();
                $message = count($ids).' kişi silindi';
                break;

            default:
                return response()->json(['success' => false, 'message' => 'Geçersiz işlem']);
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    /**
     * AI analysis for person
     * Context7: AI destekli kişi analizi
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function aiAnalyze(Request $request)
    {
        // Context7 uyumlu AI analiz önerileri
        $kisiId = $request->get('kisi_id');
        $kisi = Kisi::find($kisiId);

        if (! $kisi) {
            return response()->json(['success' => false, 'message' => 'Kişi bulunamadı']);
        }

        $suggestions = [];

        // CRM skoruna göre öneriler
        if ($kisi->crm_score < 50) {
            $suggestions[] = [
                'type' => 'crm_score',
                'priority' => 'high',
                'message' => 'CRM skoru düşük ('.$kisi->crm_score.'/100). Eksik bilgileri tamamlayın.',
                'actions' => [
                    'tc_kimlik' => ! $kisi->tc_kimlik ? 'TC Kimlik No ekleyin' : null,
                    'telefon' => ! $kisi->telefon ? 'Telefon numarası ekleyin' : null,
                    'email' => ! $kisi->email ? 'E-posta adresi ekleyin' : null,
                    'adres' => ! $kisi->il_id ? 'Adres bilgilerini tamamlayın' : null,
                ],
            ];
        }

        // İlan sahibi uygunluğu
        if (! $kisi->isOwnerEligible()) {
            $suggestions[] = [
                'type' => 'owner_eligibility',
                'priority' => 'medium',
                'message' => 'Bu kişi ilan sahibi olarak uygun değil.',
                'actions' => [
                    'tc_kimlik' => ! $kisi->tc_kimlik ? 'TC Kimlik No gerekli' : null,
                    'telefon' => ! $kisi->telefon ? 'Telefon numarası gerekli' : null,
                    'adres' => ! $kisi->il_id ? 'Adres bilgileri gerekli' : null,
                ],
            ];
        }

        // Kişi tipi önerileri (Context7: kisi_tipi)
        if (! $kisi->kisi_tipi) {
            $suggestions[] = [
                'type' => 'kisi_tipi',
                'priority' => 'medium',
                'message' => 'Kişi tipi belirtilmemiş.',
                'actions' => [
                    'kisi_tipi' => 'Kişi tipini seçin',
                ],
            ];
        }

        return response()->json([
            'success' => true,
            'suggestions' => array_filter($suggestions),
            'crm_score' => $kisi->crm_score,
            'is_owner_eligible' => $kisi->isOwnerEligible(),
        ]);
    }

    /**
     * Person tracking page
     * Context7: Kişi takip sayfası
     *
     * @return Response|\Illuminate\Contracts\View\View
     */
    public function takip(Request $request)
    {
        return $this->renderAny(['admin.kisiler.takip', 'Crm::musteriler.index']);
    }

    /**
     * Resolve person from various types
     * Context7: Kişi resolver helper
     *
     * @param  int|string|Kisi  $kisi
     */
    /**
     * Context7: Kişi resolver helper
     *
     * @param  int|string|Kisi  $kisi
     */
    private function resolve($kisi): Kisi
    {
        if ($kisi instanceof Kisi) {
            return $kisi;
        }

        // ✅ N+1 FIX: Eager loading ekle (default relationships)
        return Kisi::with([
            'danisman:id,name,email',
            'il:id,il_adi',
            'ilce:id,ilce_adi',
            'mahalle:id,mahalle_adi',
        ])->findOrFail($kisi);
    }

    /**
     * Render any available view
     * Context7: View render helper
     */
    private function renderAny(array $views, array $data = []): Response|\Illuminate\Contracts\View\View
    {
        foreach ($views as $view) {
            if (view()->exists($view)) {
                return response()->view($view, $data);
            }
        }

        return response('Kişiler sayfaları hazır değil', 200);
    }
}
