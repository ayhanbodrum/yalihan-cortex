<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PropertyValuationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertyValuationController extends Controller
{
    private $valuationService;
    
    public function __construct(PropertyValuationService $valuationService)
    {
        $this->valuationService = $valuationService;
    }
    
    /**
     * Arsa değerleme
     */
    public function calculateLandValue(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parcel_data' => 'required|array',
            'parcel_data.ada' => 'required|integer',
            'parcel_data.parsel' => 'required|integer',
            'parcel_data.il' => 'required|string',
            'parcel_data.ilce' => 'required|string',
            'parcel_data.alan' => 'required|string',
            'parcel_data.nitelik' => 'required|string',
            'market_data' => 'nullable|array'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $result = $this->valuationService->calculateLandValue(
            $request->parcel_data,
            $request->market_data ?? []
        );
        
        return response()->json($result);
    }
    
    /**
     * Karşılaştırmalı analiz
     */
    public function getComparativeAnalysis(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parcel_data' => 'required|array',
            'parcel_data.il' => 'required|string',
            'parcel_data.ilce' => 'required|string',
            'parcel_data.nitelik' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $result = $this->valuationService->getComparativeAnalysis($request->parcel_data);
        
        return response()->json($result);
    }
    
    /**
     * ROI hesaplama
     */
    public function calculateROI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_price' => 'required|numeric|min:0',
            'current_value' => 'required|numeric|min:0',
            'holding_period_years' => 'nullable|numeric|min:0.1',
            'additional_costs' => 'nullable|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $result = $this->valuationService->calculateROI($request->all());
        
        return response()->json($result);
    }
    
    /**
     * Vergi hesaplama
     */
    public function calculateTaxes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required|numeric|min:0',
            'is_residential' => 'nullable|boolean',
            'is_first_sale' => 'nullable|boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $result = $this->valuationService->calculateTaxes($request->all());
        
        return response()->json($result);
    }
    
    /**
     * Market trend analizi
     */
    public function getMarketTrends(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'il' => 'required|string',
            'ilce' => 'required|string',
            'period' => 'nullable|integer|min:1|max:24'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $result = $this->valuationService->getMarketTrends([
            'il' => $request->il,
            'ilce' => $request->ilce
        ], $request->period ?? 12);
        
        return response()->json($result);
    }
    
    /**
     * Finansal rapor oluştur
     */
    public function generateFinancialReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parcel' => 'required|array',
            'parcel.ada' => 'required|integer',
            'parcel.parsel' => 'required|integer',
            'parcel.il' => 'required|string',
            'parcel.ilce' => 'required|string',
            'parcel.alan' => 'required|string',
            'parcel.nitelik' => 'required|string',
            'market' => 'nullable|array',
            'is_residential' => 'nullable|boolean',
            'is_first_sale' => 'nullable|boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $result = $this->valuationService->generateFinancialReport($request->all());
        
        return response()->json($result);
    }
    
    /**
     * Fiyat önerisi
     */
    public function getPriceSuggestion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parcel_data' => 'required|array',
            'parcel_data.il' => 'required|string',
            'parcel_data.ilce' => 'required|string',
            'parcel_data.alan' => 'required|string',
            'parcel_data.nitelik' => 'required|string',
            'suggestion_type' => 'nullable|in:conservative,moderate,aggressive'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $suggestionType = $request->suggestion_type ?? 'moderate';
        
        // Temel değerleme
        $valuation = $this->valuationService->calculateLandValue(
            $request->parcel_data,
            $request->market_data ?? []
        );
        
        if (!$valuation['success']) {
            return response()->json($valuation);
        }
        
        $baseValue = $valuation['calculated_value'];
        
        // Öneri tipine göre çarpanlar
        $multipliers = [
            'conservative' => 0.85, // %15 düşük
            'moderate' => 1.0,      // Normal
            'aggressive' => 1.15    // %15 yüksek
        ];
        
        $suggestedPrice = $baseValue * $multipliers[$suggestionType];
        
        // Karşılaştırmalı analiz
        $comparative = $this->valuationService->getComparativeAnalysis($request->parcel_data);
        
        return response()->json([
            'success' => true,
            'suggestion_type' => $suggestionType,
            'base_value' => $baseValue,
            'suggested_price' => round($suggestedPrice, 2),
            'multiplier' => $multipliers[$suggestionType],
            'comparative_analysis' => $comparative,
            'confidence_score' => $valuation['confidence_score'],
            'reasoning' => $this->getSuggestionReasoning($suggestionType, $valuation, $comparative)
        ]);
    }
    
    /**
     * Öneri gerekçesi
     */
    private function getSuggestionReasoning($type, $valuation, $comparative)
    {
        $reasoning = [];
        
        switch ($type) {
            case 'conservative':
                $reasoning[] = 'Güvenli fiyatlandırma stratejisi';
                $reasoning[] = 'Hızlı satış için %15 indirim';
                $reasoning[] = 'Piyasa risklerini minimize eder';
                break;
            case 'moderate':
                $reasoning[] = 'Piyasa ortalamasına uygun fiyat';
                $reasoning[] = 'Dengeli satış süresi beklenir';
                $reasoning[] = 'Maksimum değer ve satış hızı dengesi';
                break;
            case 'aggressive':
                $reasoning[] = 'Maksimum değer için %15 prim';
                $reasoning[] = 'Uzun satış süresi beklenir';
                $reasoning[] = 'Yüksek kaliteli alıcı hedeflenir';
                break;
        }
        
        if ($valuation['confidence_score'] > 80) {
            $reasoning[] = 'Yüksek güven skoru (' . $valuation['confidence_score'] . '%)';
        } elseif ($valuation['confidence_score'] < 60) {
            $reasoning[] = 'Düşük güven skoru (' . $valuation['confidence_score'] . '%) - Dikkatli olun';
        }
        
        if ($comparative['success'] && $comparative['similar_count'] > 5) {
            $reasoning[] = 'Yeterli karşılaştırmalı veri mevcut (' . $comparative['similar_count'] . ' benzer özellik)';
        }
        
        return $reasoning;
    }
    
    /**
     * Toplu değerleme
     */
    public function bulkValuation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parcels' => 'required|array|min:1|max:20',
            'parcels.*.ada' => 'required|integer',
            'parcels.*.parsel' => 'required|integer',
            'parcels.*.il' => 'required|string',
            'parcels.*.ilce' => 'required|string',
            'parcels.*.alan' => 'required|string',
            'parcels.*.nitelik' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $results = [];
        $totalValue = 0;
        
        foreach ($request->parcels as $parcel) {
            $result = $this->valuationService->calculateLandValue($parcel);
            $results[] = [
                'ada' => $parcel['ada'],
                'parsel' => $parcel['parsel'],
                'success' => $result['success'],
                'value' => $result['calculated_value'] ?? 0,
                'confidence_score' => $result['confidence_score'] ?? 0,
                'data' => $result
            ];
            
            if ($result['success']) {
                $totalValue += $result['calculated_value'];
            }
        }
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'total_parcels' => count($results),
            'successful_valuations' => count(array_filter($results, function($r) { return $r['success']; })),
            'total_value' => round($totalValue, 2),
            'average_value' => count($results) > 0 ? round($totalValue / count($results), 2) : 0
        ]);
    }
}
