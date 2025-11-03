<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteApartman;
use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Site/Apartman API Controller
 * Live search ve site yönetimi için API endpoint'leri
 * Context7 Standard: C7-SITE-API-2025-10-17
 */
class SiteController extends Controller
{
    /**
     * Site/Apartman Live Search
     * GET /admin/api/sites/search
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2|max:100',
            'il_id' => 'nullable|exists:iller,id',
            'ilce_id' => 'nullable|exists:ilceler,id',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Arama parametreleri geçersiz',
                'errors' => $validator->errors()
            ], 422);
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
                    'display_text' => $this->buildDisplayText($site)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedSites,
                'count' => $formattedSites->count(),
                'query' => $query
            ]);

        } catch (\Exception $e) {
            Log::error('Site arama hatası', [
                'error' => $e->getMessage(),
                'query' => $request->input('q'),
                'filters' => $request->only(['il_id', 'ilce_id'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Arama sırasında bir hata oluştu'
            ], 500);
        }
    }

    /**
     * Yeni Site/Apartman Oluşturma
     * POST /admin/api/sites
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'il_id' => 'required|exists:iller,id',
            'ilce_id' => 'required|exists:ilceler,id',
            'mahalle_id' => 'nullable|exists:mahalleler,id',
            'blok_adi' => 'nullable|string|max:100',
            'adres' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Site oluşturma bilgileri eksik veya hatalı',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Aynı isimde site kontrolü
            $existingSite = SiteApartman::where('name', $request->name)
                ->where('il_id', $request->il_id)
                ->where('ilce_id', $request->ilce_id)
                ->first();

            if ($existingSite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu isimde bir site/apartman zaten mevcut',
                    'existing_site' => [
                        'id' => $existingSite->id,
                        'name' => $existingSite->name,
                        'display_text' => $this->buildDisplayText($existingSite)
                    ]
                ], 409);
            }

            $site = SiteApartman::create([
                'name' => $request->name,
                'blok_adi' => $request->blok_adi,
                'adres' => $request->adres,
                'il_id' => $request->il_id,
                'ilce_id' => $request->ilce_id,
                'mahalle_id' => $request->mahalle_id,
                'status' => 'active',
                'created_by' => auth()->id()
            ]);

            // İlişkileri yükle
            $site->load(['il:id,name', 'ilce:id,name', 'mahalle:id,name']);

            Log::info('Yeni site oluşturuldu', [
                'site_id' => $site->id,
                'name' => $site->name,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Site/apartman başarıyla oluşturuldu',
                'data' => [
                    'id' => $site->id,
                    'name' => $site->name,
                    'blok_adi' => $site->blok_adi,
                    'adres' => $site->adres,
                    'full_address' => $this->buildFullAddress($site),
                    'display_text' => $this->buildDisplayText($site)
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Site oluşturma hatası', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Site oluşturma sırasında bir hata oluştu'
            ], 500);
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

            return response()->json([
                'success' => true,
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
                    'display_text' => $this->buildDisplayText($site)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Site bulunamadı'
            ], 404);
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
                    'created_at' => $site->created_at?->format('d.m.Y H:i')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedSites,
                'pagination' => [
                    'current_page' => $sites->currentPage(),
                    'last_page' => $sites->lastPage(),
                    'per_page' => $sites->perPage(),
                    'total' => $sites->total()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Site listeleme hatası', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Site listesi alınırken hata oluştu'
            ], 500);
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
