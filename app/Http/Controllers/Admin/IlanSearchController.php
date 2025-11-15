<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\IlanInternalResource;
use App\Http\Resources\IlanPublicResource;
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
        // ✅ N+1 FIX: Eager loading with select optimization
        $ilanlar = Ilan::select([
            'id', 'baslik', 'referans_no', 'kategori_id', 'il_id', 'ilce_id',
            'site_id', 'ilan_sahibi_id', 'fiyat', 'status', 'created_at', 'updated_at'
        ])
        ->with([
            'kategori:id,name,slug',
            'il:id,il_adi',
            'ilce:id,ilce_adi',
            'site:id,name',
            'ilanSahibi:id,ad,soyad,telefon,email'
        ])
        ->latest()
        ->paginate(15);

        if ($request->expectsJson()) {
            // Admin endpoint - Internal resource kullan
            return response()->json([
                'success' => true,
                'data' => IlanInternalResource::collection($ilanlar)
            ]);
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

        // İlanları getir (eager loading - optimize edilmiş)
        $ilanlar = $query->with([
            'kategori:id,name',
            'il:id,il_adi',
            'ilce:id,ilce_adi',
            'mahalle:id,mahalle_adi',
            'site:id,name',
            'ilanSahibi:id,ad,soyad,telefon,cep_telefonu,email',
            'danisman:id,name,email',
            'fotograflar' => function($q) {
                $q->select('id', 'ilan_id', 'dosya_yolu', 'sira', 'kapak_fotografi')
                  ->orderBy('sira')
                  ->limit(5); // İlk 5 fotoğraf
            }
        ])
        ->latest()
        ->limit($limit)
        ->get();

        // Response formatla - Admin endpoint olduğu için Internal Resource kullan
        return response()->json([
            'success' => true,
            'search_term' => $searchTerm,
            'detected_type' => $detectedType,
            'count' => $ilanlar->count(),
            'results' => IlanInternalResource::collection($ilanlar)
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

        $ilan->load(['kategori', 'il', 'ilce', 'site', 'ilanSahibi', 'fotograflar']);

        return response()->json([
            'success' => true,
            'data' => new IlanInternalResource($ilan)
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
            'results' => IlanInternalResource::collection($ilanlar)
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

        $ilan->load(['fotograflar']);

        return response()->json([
            'success' => true,
            'data' => new IlanInternalResource($ilan)
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
            'results' => IlanInternalResource::collection($ilanlar)
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
