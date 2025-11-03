<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\TKGMService;

/**
 * TKGM API Controller
 *
 * Context7 Standardı: C7-TKGM-API-2025-10-11
 * Context7 Kural #70: TKGM Entegrasyonu
 */
class TKGMController extends Controller
{
    protected $tkgmService;

    public function __construct(TKGMService $tkgmService)
    {
        $this->tkgmService = $tkgmService;
    }

    /**
     * Parsel sorgulama
     *
     * POST /api/tkgm/parsel-sorgu
     */
    public function parselSorgula(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ada' => 'required|string|max:20',
            'parsel' => 'required|string|max:20',
            'il' => 'required|string|max:100',
            'ilce' => 'required|string|max:100'
        ]);

        $result = $this->tkgmService->parselSorgula(
            $validated['ada'],
            $validated['parsel'],
            $validated['il'],
            $validated['ilce']
        );

        return response()->json($result);
    }

    /**
     * Yatırım analizi
     *
     * POST /api/tkgm/yatirim-analizi
     */
    public function yatirimAnalizi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ada' => 'required|string',
            'parsel' => 'required|string',
            'il' => 'required|string',
            'ilce' => 'required|string'
        ]);

        // Önce parsel bilgilerini al
        $parselSonuc = $this->tkgmService->parselSorgula(
            $validated['ada'],
            $validated['parsel'],
            $validated['il'],
            $validated['ilce']
        );

        if (!$parselSonuc['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Parsel bilgileri alınamadı'
            ], 400);
        }

        // Yatırım analizi yap
        $analiz = $this->tkgmService->yatirimAnalizi($parselSonuc['parsel_bilgileri']);

        return response()->json([
            'success' => true,
            'parsel_bilgileri' => $parselSonuc['parsel_bilgileri'],
            'yatirim_analizi' => $analiz
        ]);
    }

    /**
     * TKGM health check
     *
     * GET /api/tkgm/health
     */
    public function healthCheck(): JsonResponse
    {
        $health = $this->tkgmService->healthCheck();
        return response()->json($health);
    }

    /**
     * Cache temizle
     *
     * POST /api/tkgm/clear-cache
     */
    public function clearCache(Request $request): JsonResponse
    {
        $this->tkgmService->clearCache(
            $request->get('ada'),
            $request->get('parsel'),
            $request->get('il'),
            $request->get('ilce')
        );

        return response()->json([
            'success' => true,
            'message' => 'TKGM cache temizlendi'
        ]);
    }
}
