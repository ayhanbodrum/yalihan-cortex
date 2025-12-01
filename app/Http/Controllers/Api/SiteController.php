<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteApartman;
use App\Services\Response\ResponseService;
use App\Traits\ValidatesApiRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Site/Apartman API Controller
 * Live search ve site yönetimi için API endpoint'leri
 * Context7 Standard: C7-SITE-API-2025-10-17
 */
class SiteController extends Controller
{
    use ValidatesApiRequests;

    /**
     * Site/Apartman Live Search
     * GET /admin/api/sites/search
     */
    public function search(Request $request)
    {
        $validated = $this->validateRequestWithResponse($request, [
            'q' => 'required|string|min:2|max:100',
            'il_id' => 'nullable|exists:iller,id',
            'ilce_id' => 'nullable|exists:ilceler,id',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            $query = $request->input('q');
            $ilId = $request->input('il_id');
            $ilceId = $request->input('ilce_id');
            $limit = $request->input('limit', 20);

            $sitesQuery = SiteApartman::where('status', 'active')
                ->where('name', 'like', "%{$query}%");

            // İl filtresi
            if ($ilId) {
                $sitesQuery->where('il_id', $ilId);
            }

            // İlçe filtresi
            if ($ilceId) {
                $sitesQuery->where('ilce_id', $ilceId);
            }

            $sites = $sitesQuery->with(['il:id,name', 'ilce:id,name', 'mahalle:id,name'])
                ->select(['id', 'name', 'blok_adi', 'adres', 'il_id', 'ilce_id', 'mahalle_id'])
                ->limit($limit)
                ->orderBy('name')
                ->get();

            $formattedSites = $sites->map(function ($site) {
                return [
                    'id' => $site->id,
                    'name' => $site->name,
                    'blok_adi' => $site->blok_adi,
                    'adres' => $site->adres,
                    'full_address' => $this->buildFullAddress($site),
                    'il_id' => $site->il_id,
                    'ilce_id' => $site->ilce_id,
                    'mahalle_id' => $site->mahalle_id,
                    'display_text' => $this->buildDisplayText($site),
                ];
            });

            return ResponseService::success([
                'data' => $formattedSites,
                'count' => $formattedSites->count(),
                'query' => $query,
            ], 'Site araması başarıyla tamamlandı');
        } catch (\Exception $e) {
            Log::error('Site arama hatası', [
                'error' => $e->getMessage(),
                'query' => $request->input('q'),
                'filters' => $request->only(['il_id', 'ilce_id']),
            ]);

            return ResponseService::serverError('Arama sırasında hata oluştu.', $e);
        }
    }

    /**
     * Yeni Site/Apartman Oluşturma
     * POST /admin/api/sites
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequestWithResponse($request, [
            'name' => 'required|string|max:255',
            'il_id' => 'required|exists:iller,id',
            'ilce_id' => 'required|exists:ilceler,id',
            'mahalle_id' => 'nullable|exists:mahalleler,id',
            'blok_adi' => 'nullable|string|max:100',
            'adres' => 'nullable|string|max:500',
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        try {
            // Aynı isimde site kontrolü
            $existingSite = SiteApartman::where('name', $request->name)
                ->where('il_id', $request->il_id)
                ->where('ilce_id', $request->ilce_id)
                ->first();

            if ($existingSite) {
                return ResponseService::error('Bu isimde bir site/apartman zaten mevcut', 409, [
                    'existing_site' => [
                        'id' => $existingSite->id,
                        'name' => $existingSite->name,
                        'display_text' => $this->buildDisplayText($existingSite),
                    ],
                ], 'DUPLICATE_SITE');
            }

            $site = SiteApartman::create([
                'name' => $request->name,
                'blok_adi' => $request->blok_adi,
                'adres' => $request->adres,
                'il_id' => $request->il_id,
                'ilce_id' => $request->ilce_id,
                'mahalle_id' => $request->mahalle_id,
                'status' => 'active',
                'created_by' => auth()->id(),
            ]);

            // İlişkileri yükle
            $site->load(['il:id,name', 'ilce:id,name', 'mahalle:id,name']);

            Log::info('Yeni site oluşturuldu', [
                'site_id' => $site->id,
                'name' => $site->name,
                'user_id' => auth()->id(),
            ]);

            return ResponseService::success([
                'id' => $site->id,
                'name' => $site->name,
                'blok_adi' => $site->blok_adi,
                'adres' => $site->adres,
                'full_address' => $this->buildFullAddress($site),
                'display_text' => $this->buildDisplayText($site),
            ], 'Site/apartman başarıyla oluşturuldu', 201);
        } catch (\Exception $e) {
            Log::error('Site oluşturma hatası', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return ResponseService::serverError('Site oluşturma sırasında hata oluştu.', $e);
        }
    }

    /**
     * Site detayları
     * GET /admin/api/sites/{id}
     */
    public function show($id)
    {
        try {
            $site = SiteApartman::with(['il:id,name', 'ilce:id,name', 'mahalle:id,name'])
                ->findOrFail($id);

            return ResponseService::success([
                'data' => [
                    'id' => $site->id,
                    'name' => $site->name,
                    'blok_adi' => $site->blok_adi,
                    'adres' => $site->adres,
                    'full_address' => $this->buildFullAddress($site),
                    'il_id' => $site->il_id,
                    'ilce_id' => $site->ilce_id,
                    'mahalle_id' => $site->mahalle_id,
                    'status' => $site->status,
                    'display_text' => $this->buildDisplayText($site),
                ],
            ], 'Site detayları başarıyla getirildi');
        } catch (\Exception $e) {
            return ResponseService::notFound('Site bulunamadı');
        }
    }

    /**
     * Site listesi (Admin panel için)
     * GET /admin/api/sites
     */
    public function index(Request $request)
    {
        try {
            $query = SiteApartman::with(['il:id,name', 'ilce:id,name', 'mahalle:id,name']);

            // Filtreler
            if ($request->filled('il_id')) {
                $query->where('il_id', $request->il_id);
            }

            if ($request->filled('ilce_id')) {
                $query->where('ilce_id', $request->ilce_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Sayfalama
            $perPage = $request->input('per_page', 15);
            $sites = $query->orderBy('name')->paginate($perPage);

            // Format data
            $formattedSites = $sites->getCollection()->map(function ($site) {
                return [
                    'id' => $site->id,
                    'name' => $site->name,
                    'blok_adi' => $site->blok_adi,
                    'adres' => $site->adres,
                    'full_address' => $this->buildFullAddress($site),
                    'status' => $site->status,
                    'display_text' => $this->buildDisplayText($site),
                    'created_at' => $site->created_at?->format('d.m.Y H:i'),
                ];
            });

            return ResponseService::success([
                'data' => $formattedSites,
                'pagination' => [
                    'current_page' => $sites->currentPage(),
                    'last_page' => $sites->lastPage(),
                    'per_page' => $sites->perPage(),
                    'total' => $sites->total(),
                ],
            ], 'Site listesi başarıyla getirildi');
        } catch (\Exception $e) {
            Log::error('Site listeleme hatası', [
                'error' => $e->getMessage(),
                'filters' => $request->all(),
            ]);

            return ResponseService::serverError('Site listesi alınırken hata oluştu.', $e);
        }
    }

    /**
     * Tam adres oluşturma
     */
    private function buildFullAddress($site)
    {
        $parts = [];

        if ($site->adres) {
            $parts[] = $site->adres;
        }

        if ($site->mahalle && $site->mahalle->name) {
            $parts[] = $site->mahalle->name;
        }

        if ($site->ilce && $site->ilce->name) {
            $parts[] = $site->ilce->name;
        }

        if ($site->il && $site->il->name) {
            $parts[] = $site->il->name;
        }

        return implode(', ', $parts);
    }

    /**
     * Gösterim metni oluşturma (Live search için)
     */
    private function buildDisplayText($site)
    {
        $text = $site->name;

        if ($site->blok_adi) {
            $text .= " ({$site->blok_adi})";
        }

        if ($site->ilce && $site->ilce->name) {
            $text .= " - {$site->ilce->name}";
        }

        if ($site->il && $site->il->name) {
            $text .= ", {$site->il->name}";
        }

        return $text;
    }
}
