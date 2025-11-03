<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeocodingController extends Controller
{
    /**
     * Nominatim geocoding proxy (CORS sorunu çözümü)
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:3|max:255'
        ]);

        try {
            $query = $request->input('q');
            
            // Nominatim API'ye proxy isteği
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'YalihanEmlak/1.0 (Real Estate Management System)'
                ])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $query,
                    'format' => 'json',
                    'limit' => 5,
                    'countrycodes' => 'tr', // Türkiye sınırlı
                    'addressdetails' => 1
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return response()->json([
                    'success' => true,
                    'results' => $data,
                    'query' => $query,
                    'count' => count($data)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Geocoding servisi yanıt vermiyor',
                    'error' => 'HTTP ' . $response->status()
                ], 500);
            }
            
        } catch (\Exception $e) {
            \Log::error('Geocoding hatası: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Adres arama başarısız',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reverse geocoding (koordinat → adres)
     */
    public function reverse(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lon' => 'required|numeric|between:-180,180'
        ]);

        try {
            $lat = $request->input('lat');
            $lon = $request->input('lon');
            
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'YalihanEmlak/1.0 (Real Estate Management System)'
                ])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat' => $lat,
                    'lon' => $lon,
                    'format' => 'json',
                    'addressdetails' => 1
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return response()->json([
                    'success' => true,
                    'result' => $data,
                    'coordinates' => ['lat' => $lat, 'lon' => $lon]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Reverse geocoding başarısız',
                    'error' => 'HTTP ' . $response->status()
                ], 500);
            }
            
        } catch (\Exception $e) {
            \Log::error('Reverse geocoding hatası: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Koordinat arama başarısız',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
