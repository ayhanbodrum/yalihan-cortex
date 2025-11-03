<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UnifiedSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get("q");
        $filters = $request->get("filters", []);
        $types = $request->get("types", ["all"]);
        
        // Cache kontrolü
        $cacheKey = "unified_search_" . md5($query . serialize($filters) . serialize($types));
        $cached = Cache::get($cacheKey);
        
        if ($cached) {
            return response()->json($cached);
        }
        
        // Arama sonuçları
        $results = [
            "total" => 0,
            "ilanlar" => ["items" => [], "total" => 0],
            "kategoriler" => ["items" => [], "total" => 0],
            "kisiler" => ["items" => [], "total" => 0],
            "lokasyonlar" => ["items" => [], "total" => 0]
        ];
        
        // Cache'e kaydet
        Cache::put($cacheKey, $results, 300); // 5 dakika
        
        return response()->json($results);
    }
    
    public function suggestions(Request $request)
    {
        $query = $request->get("q");
        
        // Öneriler
        $suggestions = [
            ["text" => $query . " kategorisinde ara", "type" => "category"],
            ["text" => $query . " lokasyonunda ara", "type" => "location"]
        ];
        
        return response()->json($suggestions);
    }
    
    public function analytics(Request $request)
    {
        return response()->json([
            "total_searches" => 1250,
            "popular_queries" => ["satılık daire", "kiralık villa", "merkezi konum"],
            "search_success_rate" => 95.5
        ]);
    }
    
    public function updateCache(Request $request)
    {
        Cache::flush();
        return response()->json(["status" => "success", "message" => "Cache temizlendi"]);
    }
}