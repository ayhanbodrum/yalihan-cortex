<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Price\CurrencyRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Currency Rate API Controller
 *
 * Context7 Standardı: C7-CURRENCY-API-2025-10-11
 */
class CurrencyRateController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyRateService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Get current exchange rates
     *
     * @return JsonResponse
     */
    public function getRates(): JsonResponse
    {
        try {
            $rateData = $this->currencyService->getRates();

            return response()->json([
                'success' => true,
                'rates' => $rateData['rates'],
                'last_updated' => $rateData['last_updated'],
                'source' => $rateData['source'],
                'base_currency' => $rateData['base_currency']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Döviz kurları yüklenemedi',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert between currencies
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function convert(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'from' => 'required|string|in:TRY,USD,EUR,GBP',
            'to' => 'required|string|in:TRY,USD,EUR,GBP',
        ]);

        try {
            $converted = $this->currencyService->convert(
                $request->amount,
                $request->from,
                $request->to
            );

            return response()->json([
                'success' => true,
                'original' => [
                    'amount' => $request->amount,
                    'currency' => $request->from,
                    'formatted' => $this->currencyService->format($request->amount, $request->from)
                ],
                'converted' => [
                    'amount' => $converted,
                    'currency' => $request->to,
                    'formatted' => $this->currencyService->format($converted, $request->to)
                ],
                'rate' => $this->currencyService->getRate($request->from, $request->to)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Döviz çevrimi başarısız',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supported currencies
     *
     * @return JsonResponse
     */
    public function getSupportedCurrencies(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'currencies' => $this->currencyService->getSupportedCurrencies()
        ]);
    }

    /**
     * Refresh rates cache
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        try {
            $rates = $this->currencyService->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Döviz kurları güncellendi',
                'rates' => $rates
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Kurlar yenilenemedi',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
