<?php

namespace App\Http\Controllers\Admin;

use App\Models\SiteApartman;
use Illuminate\Http\Request;

class SiteController extends AdminController
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Site endpoint - to be implemented']);
    }

    /**
     * Site/Apartman Live Search API
     * Context7: C7-SITE-LIVE-SEARCH-2025-10-17
     */
    public function search(Request $request)
    {
        // Context7: İzolasyon Sistemi - Dual parameter support (search_term OR q)
        $searchTerm = $request->get('q', $request->get('search_term', ''));
        $type = $request->get('type', ''); // site or apartman

        if (empty($searchTerm) || strlen($searchTerm) < 2) {
            return response()->json([
                'success' => true,
                'data' => [],
                'results' => [], // Context7: Dual format for compatibility
                'message' => 'En az 2 karakter giriniz'
            ]);
        }

        try {
            $query = SiteApartman::query();

            // Type filter if provided
            if (!empty($type)) {
                $query->where('tip', $type);
            }

            $sites = $query->where('site_adi', 'LIKE', "%{$searchTerm}%")
                ->orWhere('adres', 'LIKE', "%{$searchTerm}%")
                ->orWhere('sehir', 'LIKE', "%{$searchTerm}%")
                ->orWhere('ilce', 'LIKE', "%{$searchTerm}%")
                ->limit($request->get('limit', 10))
                ->get()
                ->map(function ($site) {
                    return [
                        'id' => $site->id,
                        'text' => $site->site_adi . ' - ' . $site->adres . ', ' . $site->sehir,
                        'name' => $site->site_adi,
                        'adres' => $site->adres,
                        'sehir' => $site->sehir,
                        'ilce' => $site->ilce,
                        'mahalle' => $site->mahalle,
                        'posta_kodu' => $site->posta_kodu,
                        'toplam_daire_sayisi' => $site->daire_sayisi ?? 0, // Context7: Frontend expects this key
                        'daire_sayisi' => $site->daire_sayisi ?? 0 // Keep old key for compatibility
                    ];
                });

            $resultsArray = $sites->values()->toArray();

            return response()->json([
                'success' => true,
                'data' => $resultsArray, // Context7: Standard key
                'results' => $resultsArray, // Context7: Dual format for site-apartman-selection.blade.php
                'count' => $sites->count(),
                'message' => $sites->count() . ' sonuç bulundu'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Arama sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
