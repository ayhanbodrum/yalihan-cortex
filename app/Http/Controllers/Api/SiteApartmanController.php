<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteApartman;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SiteApartmanController extends Controller
{
    /**
     * Site/Apartman arama
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2',
            'type' => 'nullable|string|in:site,apartman'
        ]);

        try {
            $query = SiteApartman::query();
            
            // Tip filtresi
            if ($request->type) {
                $query->where('tip', $request->type);
            }
            
            // Arama
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%')
                  ->orWhere('adres', 'LIKE', '%' . $request->q . '%');
            });
            
            $results = $query->limit(10)->get([
                'id', 'name', 'adres', 'toplam_daire_sayisi', 'tip'
            ]);
            
            // Context7 Live Search compatibility: add 'text' field
            $results->each(function ($item) {
                $item->text = $item->name;
                $item->daire_sayisi = $item->toplam_daire_sayisi;
            });

            return response()->json([
                'success' => true,
                'data' => $results, // Changed from 'results' to 'data'
                'count' => $results->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Arama sırasında hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Site/Apartman detayları
     */
    public function show($id): JsonResponse
    {
        try {
            $site = SiteApartman::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'site' => $site
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Site bulunamadı',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
