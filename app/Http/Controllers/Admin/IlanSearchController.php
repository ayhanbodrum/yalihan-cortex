<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ilan;
use App\Services\IlanReferansService;
use Illuminate\Http\Request;

/**
 * Gelişmiş İlan Arama Controller
 *
 * Context7 Standardı: C7-ILAN-SEARCH-2025-10-11
 *
 * Arama Tipleri:
 * - Referans numarası (YE-SAT-YALKVK-001234)
 * - Kişi telefonu
 * - Portal ID (sahibinden, emlakjet, vb.)
 * - Site/Apartman adı
 * - İlan başlığı
 */
class IlanSearchController extends AdminController
{
    protected $referansService;

    public function __construct(IlanReferansService $referansService)
    {
        $this->referansService = $referansService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $ilanlar = Ilan::with(['kategori', 'il', 'ilce', 'site', 'ilanSahibi'])
            ->latest()
            ->paginate(15);

        if ($request->expectsJson()) {
            return response()->json($ilanlar);
        }

        return view('admin.ilan-search.index', compact('ilanlar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.ilan-search.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'baslik' => 'required|string|max:255',
            'kategori_id' => 'required|exists:ilan_kategorileri,id',
            'il_id' => 'required|exists:iller,id',
            'ilce_id' => 'required|exists:ilceler,id',
            'fiyat' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft'
        ]);

        $data = $request->all();
        $data['referans_no'] = $this->referansService->generateReferansNo();

        $ilan = Ilan::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'İlan created successfully',
                'data' => $ilan
            ], 201);
        }

        return redirect()
            ->route('admin.ilan-search.index')
            ->with('success', 'İlan created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ilan $ilan)
    {
        $ilan->load(['kategori', 'il', 'ilce', 'mahalle', 'site', 'ilanSahibi', 'danisman']);

        if (request()->expectsJson()) {
            return response()->json($ilan);
        }

        return view('admin.ilan-search.show', compact('ilan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ilan $ilan)
    {
        return view('admin.ilan-search.edit', compact('ilan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ilan $ilan)
    {
        $request->validate([
            'baslik' => 'required|string|max:255',
            'kategori_id' => 'required|exists:ilan_kategorileri,id',
            'il_id' => 'required|exists:iller,id',
            'ilce_id' => 'required|exists:ilceler,id',
            'fiyat' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft'
        ]);

        $ilan->update($request->all());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'İlan updated successfully',
                'data' => $ilan->fresh()
            ]);
        }

        return redirect()
            ->route('admin.ilan-search.index')
            ->with('success', 'İlan updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ilan $ilan)
    {
        $ilan->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'İlan deleted successfully'
            ]);
        }

        return redirect()
            ->route('admin.ilan-search.index')
            ->with('success', 'İlan deleted successfully');
    }

    /**
     * Unified search endpoint (tüm arama tipleri)
     *
     * GET /admin/ilanlar/search?q=YE-SAT-001234
     * GET /admin/ilanlar/search?q=05551234567
     * GET /admin/ilanlar/search?q=123456789 (portal ID)
     * GET /admin/ilanlar/search?q=Ülkerler Sitesi
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
            'type' => 'nullable|in:all,referans,telefon,portal,site'
        ]);

        $searchTerm = trim($request->input('q'));
        $limit = $request->input('limit', 20);
        $type = $request->input('type', 'all');

        // Arama tipi otomatik tespit
        $detectedType = $this->detectSearchType($searchTerm);

        // Arama query oluştur
        $query = $this->referansService->searchQuery($searchTerm);

        // İlanları getir (eager loading)
        $ilanlar = $query->with([
            'kategori',
            'il',
            'ilce',
            'mahalle',
            'site',
            'ilanSahibi',
            'danisman'
        ])
        ->latest()
        ->limit($limit)
        ->get();

        // Response formatla
        return response()->json([
            'success' => true,
            'search_term' => $searchTerm,
            'detected_type' => $detectedType,
            'count' => $ilanlar->count(),
            'results' => $ilanlar->map(function($ilan) {
                return [
                    'id' => $ilan->id,
                    'referans_no' => $ilan->referans_no,
                    'dosya_adi' => $ilan->dosya_adi,
                    'baslik' => $ilan->baslik,
                    'fiyat' => $ilan->fiyat,
                    'kategori' => $ilan->kategori?->name,
                    'lokasyon' => $this->getLokasyonText($ilan),
                    'site' => $ilan->site?->name,
                    'mal_sahibi' => $ilan->ilanSahibi?->full_name ?? null,
                    'telefon' => $ilan->ilanSahibi?->telefon ?? null,
                    'status' => $ilan->status,
                    'created_at' => $ilan->created_at?->format('d.m.Y'),
                    'url' => route('admin.ilanlar.show', $ilan->id),
                    'edit_url' => route('admin.ilanlar.edit', $ilan->id),
                ];
            })
        ]);
    }

    /**
     * Referans numarası ile direkt bulma
     *
     * GET /admin/ilanlar/by-referans/{referansNo}
     *
     * @param string $referansNo
     * @return \Illuminate\Http\JsonResponse
     */
    public function findByReferans(string $referansNo)
    {
        $ilan = $this->referansService->findByReferansNo($referansNo);

        if (!$ilan) {
            return response()->json([
                'success' => false,
                'message' => 'İlan bulunamadı'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'ilan' => $ilan->load(['kategori', 'il', 'ilce', 'site', 'ilanSahibi'])
        ]);
    }

    /**
     * Kişi telefonu ile ilanları bulma
     *
     * GET /admin/ilanlar/by-telefon?phone=05551234567
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function findByTelefon(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10'
        ]);

        $phone = preg_replace('/[^0-9]/', '', $request->phone);

        $ilanlar = Ilan::whereHas('ilanSahibi', function($q) use ($phone) {
            $q->where('telefon', 'LIKE', "%{$phone}%")
              ->orWhere('cep_telefonu', 'LIKE', "%{$phone}%");
        })
        ->with(['kategori', 'il', 'ilce', 'site', 'ilanSahibi'])
        ->latest()
        ->limit(50)
        ->get();

        return response()->json([
            'success' => true,
            'phone' => $phone,
            'count' => $ilanlar->count(),
            'results' => $ilanlar
        ]);
    }

    /**
     * Portal ID ile ilan bulma
     *
     * GET /admin/ilanlar/by-portal?portal=sahibinden&id=123456789
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function findByPortalId(Request $request)
    {
        $request->validate([
            'portal' => 'required|in:sahibinden,emlakjet,hepsiemlak,zingat,hurriyetemlak',
            'id' => 'required|string'
        ]);

        $portal = $request->portal;
        $portalId = $request->id;

        $fieldMap = [
            'sahibinden' => 'sahibinden_id',
            'emlakjet' => 'emlakjet_id',
            'hepsiemlak' => 'hepsiemlak_id',
            'zingat' => 'zingat_id',
            'hurriyetemlak' => 'hurriyetemlak_id'
        ];

        $ilan = Ilan::where($fieldMap[$portal], $portalId)
                    ->with(['kategori', 'il', 'ilce', 'site', 'ilanSahibi'])
                    ->first();

        if (!$ilan) {
            return response()->json([
                'success' => false,
                'message' => ucfirst($portal) . ' ID bulunamadı'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'ilan' => $ilan
        ]);
    }

    /**
     * Site/Apartman adı ile ilanları bulma
     *
     * GET /admin/ilanlar/by-site?name=Ülkerler
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function findBySite(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2'
        ]);

        $siteName = $request->name;

        $ilanlar = Ilan::whereHas('site', function($q) use ($siteName) {
            $q->where('name', 'LIKE', "%{$siteName}%");
        })
        ->with(['kategori', 'il', 'ilce', 'site', 'ilanSahibi'])
        ->latest()
        ->limit(50)
        ->get();

        return response()->json([
            'success' => true,
            'site_name' => $siteName,
            'count' => $ilanlar->count(),
            'results' => $ilanlar
        ]);
    }

    /**
     * Arama tipi otomatik tespit
     *
     * @param string $searchTerm
     * @return string
     */
    protected function detectSearchType(string $searchTerm): string
    {
        // Referans numarası (YE- ile başlıyor)
        if (str_starts_with(strtoupper($searchTerm), 'YE-')) {
            return 'referans';
        }

        // Telefon (sadece rakam ve bazı karakterler)
        if (preg_match('/^[0-9+\s()-]+$/', $searchTerm)) {
            return 'telefon';
        }

        // Portal ID (8+ haneli sayı)
        if (preg_match('/^\d{8,}$/', $searchTerm)) {
            return 'portal_id';
        }

        // Site/Apartman adı veya başlık
        return 'text';
    }

    /**
     * Lokasyon text oluştur
     *
     * @param Ilan $ilan
     * @return string
     */
    protected function getLokasyonText(Ilan $ilan): string
    {
        $parts = array_filter([
            $ilan->mahalle?->mahalle_adi,
            $ilan->ilce?->ilce_adi,
            $ilan->il?->il_adi
        ]);

        return implode(', ', $parts) ?: 'Belirtilmemiş';
    }
}

