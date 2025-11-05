<?php

namespace App\Services;

use App\Models\Ilan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\Logging\LogService;

class PropertyValuationService
{
    /**
     * Arsa değerleme algoritması
     */
    public function calculateLandValue($parcelData, $marketData = [])
    {
        try {
            $baseValue = $this->getBaseLandValue($parcelData);
            $locationMultiplier = $this->getLocationMultiplier($parcelData);
            $sizeMultiplier = $this->getSizeMultiplier($parcelData['alan']);
            $marketMultiplier = $this->getMarketMultiplier($marketData);
            
            $calculatedValue = $baseValue * $locationMultiplier * $sizeMultiplier * $marketMultiplier;
            
            return [
                'success' => true,
                'base_value' => $baseValue,
                'location_multiplier' => $locationMultiplier,
                'size_multiplier' => $sizeMultiplier,
                'market_multiplier' => $marketMultiplier,
                'calculated_value' => round($calculatedValue, 2),
                'currency' => 'TRY',
                'calculation_date' => now()->toISOString(),
                'confidence_score' => $this->calculateConfidenceScore($parcelData, $marketData)
            ];
            
        } catch (\Exception $e) {
            // ✅ STANDARDIZED: Using LogService
            LogService::error('Arsa değerleme hatası', [
                'parcel_data' => $parcelData,
                'market_data' => $marketData
            ], $e);
            
            return [
                'success' => false,
                'message' => 'Arsa değerleme hesaplanamadı: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Temel arsa değeri
     */
    private function getBaseLandValue($parcelData)
    {
        $il = $parcelData['il'] ?? '';
        $ilce = $parcelData['ilce'] ?? '';
        $nitelik = $parcelData['nitelik'] ?? '';
        
        // İl bazlı temel fiyatlar (TL/m²)
        $basePrices = [
            'İstanbul' => [
                'Kadıköy' => 15000,
                'Beşiktaş' => 20000,
                'Şişli' => 18000,
                'Beyoğlu' => 12000,
                'default' => 10000
            ],
            'Ankara' => [
                'Çankaya' => 8000,
                'Keçiören' => 5000,
                'Mamak' => 4000,
                'default' => 6000
            ],
            'İzmir' => [
                'Konak' => 7000,
                'Karşıyaka' => 6000,
                'Bornova' => 5000,
                'default' => 5500
            ],
            'Muğla' => [
                'Bodrum' => 12000,
                'Marmaris' => 10000,
                'Fethiye' => 8000,
                'default' => 7000
            ],
            'Antalya' => [
                'Muratpaşa' => 8000,
                'Kepez' => 5000,
                'Konyaaltı' => 7000,
                'default' => 6000
            ],
            'default' => 3000
        ];
        
        $ilPrices = $basePrices[$il] ?? $basePrices['default'];
        $basePrice = $ilPrices[$ilce] ?? $ilPrices['default'];
        
        // Nitelik bazlı çarpanlar
        $qualityMultipliers = [
            'Arsa' => 1.0,
            'Konut' => 1.2,
            'Ticari' => 1.5,
            'Sanayi' => 0.8,
            'Tarım' => 0.3
        ];
        
        $qualityMultiplier = $qualityMultipliers[$nitelik] ?? 1.0;
        
        return $basePrice * $qualityMultiplier;
    }
    
    /**
     * Lokasyon çarpanı
     */
    private function getLocationMultiplier($parcelData)
    {
        $mahalle = $parcelData['mahalle'] ?? '';
        $mevkii = $parcelData['mevkii'] ?? '';
        
        // Mahalle bazlı çarpanlar
        $mahalleMultipliers = [
            'Merkez' => 1.5,
            'Sahil' => 2.0,
            'Dağ' => 0.8,
            'Orman' => 0.6,
            'Tarım' => 0.4
        ];
        
        $mahalleMultiplier = 1.0;
        foreach ($mahalleMultipliers as $keyword => $multiplier) {
            if (strpos($mahalle, $keyword) !== false || strpos($mevkii, $keyword) !== false) {
                $mahalleMultiplier = $multiplier;
                break;
            }
        }
        
        return $mahalleMultiplier;
    }
    
    /**
     * Alan çarpanı
     */
    private function getSizeMultiplier($alan)
    {
        $alan = (float) str_replace(',', '.', $alan);
        
        if ($alan < 100) {
            return 1.5; // Küçük arsalar daha pahalı
        } elseif ($alan < 500) {
            return 1.2;
        } elseif ($alan < 1000) {
            return 1.0;
        } elseif ($alan < 5000) {
            return 0.9;
        } else {
            return 0.8; // Büyük arsalar daha ucuz
        }
    }
    
    /**
     * Piyasa çarpanı
     */
    private function getMarketMultiplier($marketData)
    {
        if (empty($marketData)) {
            return 1.0;
        }
        
        $marketIndex = $marketData['index'] ?? 100;
        return $marketIndex / 100;
    }
    
    /**
     * Güven skoru hesapla
     */
    private function calculateConfidenceScore($parcelData, $marketData)
    {
        $score = 0;
        
        // Veri tamamlık skoru
        if (!empty($parcelData['il'])) $score += 20;
        if (!empty($parcelData['ilce'])) $score += 20;
        if (!empty($parcelData['mahalle'])) $score += 15;
        if (!empty($parcelData['alan'])) $score += 15;
        if (!empty($parcelData['nitelik'])) $score += 10;
        if (!empty($parcelData['mevkii'])) $score += 10;
        if (!empty($marketData)) $score += 10;
        
        return min($score, 100);
    }
    
    /**
     * Karşılaştırmalı analiz
     */
    public function getComparativeAnalysis($parcelData)
    {
        try {
            $similarProperties = $this->findSimilarProperties($parcelData);
            
            if (empty($similarProperties)) {
                return [
                    'success' => false,
                    'message' => 'Benzer özellikler bulunamadı'
                ];
            }
            
            $avgPrice = array_sum(array_column($similarProperties, 'price')) / count($similarProperties);
            $minPrice = min(array_column($similarProperties, 'price'));
            $maxPrice = max(array_column($similarProperties, 'price'));
            
            return [
                'success' => true,
                'similar_count' => count($similarProperties),
                'average_price' => round($avgPrice, 2),
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'price_range' => $maxPrice - $minPrice,
                'similar_properties' => $similarProperties
            ];
            
        } catch (\Exception $e) {
            // ✅ STANDARDIZED: Using LogService
            LogService::error('Karşılaştırmalı analiz hatası', [
                'parcel_data' => $parcelData
            ], $e);
            
            return [
                'success' => false,
                'message' => 'Karşılaştırmalı analiz yapılamadı: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Benzer özellikleri bul
     */
    private function findSimilarProperties($parcelData)
    {
        $query = Ilan::where('il', $parcelData['il'])
            ->where('ilce', $parcelData['ilce'])
            ->where('nitelik', $parcelData['nitelik'])
            ->whereNotNull('fiyat')
            ->where('fiyat', '>', 0);
        
        if (!empty($parcelData['mahalle'])) {
            $query->where('mahalle', 'like', '%' . $parcelData['mahalle'] . '%');
        }
        
        $properties = $query->limit(10)->get();
        
        return $properties->map(function($property) {
            return [
                'id' => $property->id,
                'title' => $property->baslik,
                'price' => $property->fiyat,
                'area' => $property->alan,
                'price_per_sqm' => $property->alan > 0 ? $property->fiyat / $property->alan : 0,
                'location' => $property->il . ', ' . $property->ilce
            ];
        })->toArray();
    }
    
    /**
     * ROI hesaplama
     */
    public function calculateROI($investmentData)
    {
        try {
            $purchasePrice = $investmentData['purchase_price'];
            $currentValue = $investmentData['current_value'];
            $holdingPeriod = $investmentData['holding_period_years'] ?? 1;
            $additionalCosts = $investmentData['additional_costs'] ?? 0;
            
            $totalInvestment = $purchasePrice + $additionalCosts;
            $profit = $currentValue - $totalInvestment;
            $roi = ($profit / $totalInvestment) * 100;
            $annualROI = $roi / $holdingPeriod;
            
            return [
                'success' => true,
                'total_investment' => $totalInvestment,
                'current_value' => $currentValue,
                'profit' => $profit,
                'roi_percentage' => round($roi, 2),
                'annual_roi_percentage' => round($annualROI, 2),
                'holding_period_years' => $holdingPeriod
            ];
            
        } catch (\Exception $e) {
            // ✅ STANDARDIZED: Using LogService
            LogService::error('ROI hesaplama hatası', [
                'investment_data' => $investmentData
            ], $e);
            
            return [
                'success' => false,
                'message' => 'ROI hesaplanamadı: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Vergi hesaplama
     */
    public function calculateTaxes($propertyData)
    {
        try {
            $propertyValue = $propertyData['value'];
            $isResidential = $propertyData['is_residential'] ?? true;
            $isFirstSale = $propertyData['is_first_sale'] ?? false;
            
            $taxes = [];
            
            // KDV hesaplama
            if (!$isResidential) {
                $taxes['kdv'] = $propertyValue * 0.18; // %18 KDV
            } else {
                $taxes['kdv'] = $isFirstSale ? 0 : $propertyValue * 0.18;
            }
            
            // Damga vergisi
            $taxes['damga_vergisi'] = $propertyValue * 0.00948; // %0.948
            
            // Tapu harcı
            $taxes['tapu_harcı'] = $propertyValue * 0.04; // %4
            
            // Noter harcı
            $taxes['noter_harcı'] = $propertyValue * 0.001; // %0.1
            
            $totalTaxes = array_sum($taxes);
            
            return [
                'success' => true,
                'property_value' => $propertyValue,
                'taxes' => $taxes,
                'total_taxes' => $totalTaxes,
                'net_amount' => $propertyValue - $totalTaxes,
                'tax_percentage' => round(($totalTaxes / $propertyValue) * 100, 2)
            ];
            
        } catch (\Exception $e) {
            // ✅ STANDARDIZED: Using LogService
            LogService::error('Vergi hesaplama hatası', [
                'property_data' => $propertyData
            ], $e);
            
            return [
                'success' => false,
                'message' => 'Vergi hesaplanamadı: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Market trend analizi
     */
    public function getMarketTrends($location, $period = 12)
    {
        try {
            $trends = Ilan::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('AVG(fiyat) as avg_price'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('il', $location['il'])
                ->where('ilce', $location['ilce'])
                ->where('created_at', '>=', now()->subMonths($period))
                ->whereNotNull('fiyat')
                ->where('fiyat', '>', 0)
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();
            
            if ($trends->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Yeterli veri bulunamadı'
                ];
            }
            
            $trendData = $trends->map(function($trend) {
                return [
                    'period' => $trend->year . '-' . str_pad($trend->month, 2, '0', STR_PAD_LEFT),
                    'avg_price' => round($trend->avg_price, 2),
                    'count' => $trend->count
                ];
            });
            
            // Trend analizi
            $firstPrice = $trendData->first()['avg_price'];
            $lastPrice = $trendData->last()['avg_price'];
            $trendPercentage = (($lastPrice - $firstPrice) / $firstPrice) * 100;
            
            return [
                'success' => true,
                'trend_data' => $trendData,
                'trend_percentage' => round($trendPercentage, 2),
                'trend_direction' => $trendPercentage > 0 ? 'up' : 'down',
                'period_months' => $period
            ];
            
        } catch (\Exception $e) {
            // ✅ STANDARDIZED: Using LogService
            LogService::error('Market trend analizi hatası', [
                'location' => $location,
                'period' => $period
            ], $e);
            
            return [
                'success' => false,
                'message' => 'Market trend analizi yapılamadı: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Finansal rapor oluştur
     */
    public function generateFinancialReport($propertyData)
    {
        try {
            $valuation = $this->calculateLandValue($propertyData['parcel'], $propertyData['market'] ?? []);
            $comparative = $this->getComparativeAnalysis($propertyData['parcel']);
            $taxes = $this->calculateTaxes([
                'value' => $valuation['calculated_value'] ?? 0,
                'is_residential' => $propertyData['is_residential'] ?? true,
                'is_first_sale' => $propertyData['is_first_sale'] ?? false
            ]);
            
            $report = [
                'report_date' => now()->toISOString(),
                'property_info' => $propertyData['parcel'],
                'valuation' => $valuation,
                'comparative_analysis' => $comparative,
                'tax_calculation' => $taxes,
                'summary' => [
                    'estimated_value' => $valuation['calculated_value'] ?? 0,
                    'confidence_score' => $valuation['confidence_score'] ?? 0,
                    'total_taxes' => $taxes['total_taxes'] ?? 0,
                    'net_value' => $taxes['net_amount'] ?? 0
                ]
            ];
            
            return [
                'success' => true,
                'report' => $report
            ];
            
        } catch (\Exception $e) {
            // ✅ STANDARDIZED: Using LogService
            LogService::error('Finansal rapor oluşturma hatası', [
                'property_data' => $propertyData
            ], $e);
            
            return [
                'success' => false,
                'message' => 'Finansal rapor oluşturulamadı: ' . $e->getMessage()
            ];
        }
    }
}
