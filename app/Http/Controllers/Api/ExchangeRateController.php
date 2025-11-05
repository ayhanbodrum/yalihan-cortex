<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TCMBCurrencyService;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Exchange Rate API Controller
 * 
 * Context7: Real-time currency rates for international listings
 */
class ExchangeRateController extends Controller
{
    protected TCMBCurrencyService $currencyService;
    
    public function __construct(TCMBCurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }
    
    /**
     * Get today's exchange rates
     * 
     * GET /api/exchange-rates
     */
    public function index(): JsonResponse
    {
        try {
            $rates = $this->currencyService->getTodayRates();
            
            return response()->json([
                'success' => true,
                'data' => $rates,
                'count' => count($rates),
                'source' => 'TCMB',
                'updated_at' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch exchange rates',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get specific currency rate
     * 
     * GET /api/exchange-rates/{code}
     */
    public function show(string $code): JsonResponse
    {
        try {
            $rate = $this->currencyService->getRate($code);
            
            if (!$rate) {
                return response()->json([
                    'success' => false,
                    'message' => "Currency {$code} not found"
                ], 404);
            }
            
            $rates = $this->currencyService->getTodayRates();
            
            return response()->json([
                'success' => true,
                'data' => $rates[$code] ?? null,
                'rate' => $rate,
                'symbol' => $this->currencyService->getCurrencySymbol($code)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch currency rate',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Convert amount between currencies
     * 
     * POST /api/exchange-rates/convert
     */
    public function convert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3'
        ]);
        
        try {
            $amount = $validated['amount'];
            $from = strtoupper($validated['from']);
            $to = strtoupper($validated['to']);
            
            // Convert to TRY first
            $tryAmount = $from === 'TRY' 
                ? $amount 
                : $this->currencyService->convertToTRY($amount, $from);
            
            // Then convert to target currency
            $result = $to === 'TRY' 
                ? $tryAmount 
                : $this->currencyService->convertFromTRY($tryAmount, $to);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'amount' => $amount,
                    'from' => $from,
                    'to' => $to,
                    'result' => round($result, 2),
                    'rate' => $from === 'TRY' ? null : $this->currencyService->getRate($from),
                    'formatted' => $this->currencyService->getCurrencySymbol($to) . ' ' . number_format($result, 2)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to convert currency',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get currency history
     * 
     * GET /api/exchange-rates/{code}/history
     */
    public function history(string $code, Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        
        try {
            $history = $this->currencyService->getRateHistory($code, $days);
            
            return response()->json([
                'success' => true,
                'data' => $history->map(function ($rate) {
                    return [
                        'date' => $rate->rate_date->format('Y-m-d'),
                        'buying' => $rate->buying_rate,
                        'selling' => $rate->selling_rate,
                        'average' => ($rate->buying_rate + $rate->selling_rate) / 2
                    ];
                }),
                'currency' => $code,
                'days' => $days
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch rate history',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get supported currencies
     * 
     * GET /api/exchange-rates/supported
     */
    public function supported(): JsonResponse
    {
        $currencies = $this->currencyService->getSupportedCurrencies();
        
        return response()->json([
            'success' => true,
            'data' => collect($currencies)->map(function ($code) {
                return [
                    'code' => $code,
                    'symbol' => $this->currencyService->getCurrencySymbol($code),
                    'name' => $this->getCurrencyName($code)
                ];
            })
        ]);
    }
    
    /**
     * Force update rates (admin only)
     * 
     * POST /api/exchange-rates/update
     */
    public function update(): JsonResponse
    {
        try {
            $updated = $this->currencyService->updateRates();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updated} exchange rates",
                'updated_count' => $updated
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update exchange rates',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get currency name
     * 
     * @param string $code
     * @return string
     */
    private function getCurrencyName($code)
    {
        return match($code) {
            'TRY' => 'Türk Lirası',
            'USD' => 'Amerikan Doları',
            'EUR' => 'Euro',
            'GBP' => 'İngiliz Sterlini',
            'CHF' => 'İsviçre Frangı',
            'CAD' => 'Kanada Doları',
            'AUD' => 'Avustralya Doları',
            'JPY' => 'Japon Yeni',
            default => $code
        };
    }
}



