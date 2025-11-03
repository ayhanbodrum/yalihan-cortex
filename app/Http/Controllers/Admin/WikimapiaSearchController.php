<?php

namespace App\Http\Controllers\Admin;

use App\Services\WikimapiaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WikimapiaSearchController extends AdminController
{
    protected WikimapiaService $wikimapiaService;

    public function __construct(WikimapiaService $wikimapiaService)
    {
        $this->wikimapiaService = $wikimapiaService;
    }

    /**
     * Site/Apartman sorgulama paneli ana sayfa
     */
    public function index()
    {
        return view('admin.wikimapia-search.index');
    }

    /**
     * Wikimapia'dan site/apartman araması yap
     */
    public function search(Request $request)
    {
        try {
            $request->validate([
                'query' => 'required|string|min:2',
                'lat' => 'required|numeric',
                'lon' => 'required|numeric',
                'radius' => 'sometimes|numeric|min:0.01|max:1'
            ]);

            $query = $request->input('query');
            $lat = $request->input('lat');
            $lon = $request->input('lon');
            $radius = $request->input('radius', 0.05);

            $results = $this->wikimapiaService->searchResidentialComplexes($query, $lat, $lon, $radius);

            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Arama tamamlandı'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $errorMessages = [];
            foreach ($errors as $field => $messages) {
                $errorMessages[] = implode(', ', $messages);
            }
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $errorMessages)
            ], 422);
        } catch (\Exception $e) {
            Log::error('Wikimapia search error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Arama sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Genel mekan araması
     */
    public function searchPlaces(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric'
        ]);

        try {
            $query = $request->input('query');
            $lat = $request->input('lat');
            $lon = $request->input('lon');

            $results = $this->wikimapiaService->searchPlaces($query, $lat, $lon);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Yakındaki mekanları listele
     */
    public function nearby(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'radius' => 'sometimes|numeric|min:0.01|max:0.5'
        ]);

        try {
            $lat = $request->input('lat');
            $lon = $request->input('lon');
            $radius = $request->input('radius', 0.05);

            $lonMin = $lon - $radius;
            $latMin = $lat - $radius;
            $lonMax = $lon + $radius;
            $latMax = $lat + $radius;

            $results = $this->wikimapiaService->getPlacesByArea($lonMin, $latMin, $lonMax, $latMax);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Place detaylarını getir
     */
    public function getPlaceDetails($id)
    {
        try {
            $place = $this->wikimapiaService->getPlaceById($id, ['main', 'location', 'photos', 'comments']);

            return response()->json([
                'success' => true,
                'data' => $place
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
