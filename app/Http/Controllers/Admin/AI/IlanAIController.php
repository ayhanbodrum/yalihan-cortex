<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\OllamaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * İlan AI Controller
 *
 * Context7 Standardı: C7-ILAN-AI-CONTROLLER-2025-10-11
 *
 * Ollama Gemma2:2b ile AI içerik üretimi
 */
class IlanAIController extends Controller
{
    protected OllamaService $ollamaService;

    public function __construct(OllamaService $ollamaService)
    {
        $this->ollamaService = $ollamaService;
    }

    /**
     * AI Öneri Endpoint (Unified)
     *
     * POST /admin/ilanlar/ai-suggest
     */
    public function suggest(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:title,description,location,price'
        ]);

        try {
            $action = $request->input('action');

            switch ($action) {
                case 'title':
                    return $this->generateTitle($request);

                case 'description':
                    return $this->generateDescription($request);

                case 'location':
                    return $this->analyzeLocation($request);

                case 'price':
                    return $this->suggestPrice($request);

                default:
                    return response()->json([
                        'success' => false,
                        'error' => 'Invalid action'
                    ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'AI işlemi başarısız',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Başlık üret
     */
    protected function generateTitle(Request $request): JsonResponse
    {
        $data = [
            'kategori' => $request->input('kategori', 'Gayrimenkul'),
            'lokasyon' => $this->getLocation($request),
            'yayin_tipi' => $request->input('yayin_tipi', 'Satılık'),
            'fiyat' => $this->formatPrice($request->input('fiyat'), $request->input('para_birimi')),
            'tone' => $request->input('ai_tone', 'seo')
        ];

        $titles = $this->ollamaService->generateTitle($data);

        return response()->json([
            'success' => true,
            'variants' => $titles,
            'count' => count($titles),
            'model' => 'gemma2:2b'
        ]);
    }

    /**
     * Açıklama üret
     */
    protected function generateDescription(Request $request): JsonResponse
    {
        $data = [
            'kategori' => $request->input('kategori', 'Gayrimenkul'),
            'lokasyon' => $this->getLocation($request),
            'fiyat' => $this->formatPrice($request->input('fiyat'), $request->input('para_birimi')),
            'metrekare' => $request->input('metrekare', ''),
            'oda_sayisi' => $request->input('oda_sayisi', ''),
            'tone' => $request->input('ai_tone', 'seo')
        ];

        $description = $this->ollamaService->generateDescription($data);

        return response()->json([
            'success' => true,
            'description' => $description,
            'length' => strlen($description),
            'model' => 'gemma2:2b'
        ]);
    }

    /**
     * Lokasyon analizi
     */
    protected function analyzeLocation(Request $request): JsonResponse
    {
        $locationData = [
            'il' => $request->input('il'),
            'ilce' => $request->input('ilce'),
            'mahalle' => $request->input('mahalle', ''),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude')
        ];

        $analysis = $this->ollamaService->analyzeLocation($locationData);

        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'model' => 'gemma2:2b'
        ]);
    }

    /**
     * Fiyat önerisi
     */
    protected function suggestPrice(Request $request): JsonResponse
    {
        $propertyData = [
            'base_price' => (float) $request->input('fiyat', 0),
            'kategori' => $request->input('kategori', 'Gayrimenkul'),
            'metrekare' => $request->input('metrekare', 0),
            'lokasyon' => $this->getLocation($request)
        ];

        $suggestions = $this->ollamaService->suggestPrice($propertyData);

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions,
            'model' => 'gemma2:2b'
        ]);
    }

    /**
     * Health check
     */
    public function health(): JsonResponse
    {
        $isHealthy = $this->ollamaService->isHealthy();

        return response()->json([
            'success' => $isHealthy,
            'model' => config('ai.ollama_model'),
            'endpoint' => config('ai.ollama_api_url'),
            'status' => $isHealthy ? 'online' : 'offline'
        ]);
    }

    /**
     * Lokasyon string'i oluştur
     */
    protected function getLocation(Request $request): string
    {
        $parts = array_filter([
            $request->input('il'),
            $request->input('ilce'),
            $request->input('mahalle')
        ]);

        return implode(', ', $parts) ?: 'Bodrum';
    }

    /**
     * Fiyat formatla
     */
    protected function formatPrice(?string $amount, ?string $currency): string
    {
        if (!$amount) {
            return '';
        }

        $symbols = [
            'TRY' => '₺',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£'
        ];

        $formatted = number_format((float) $amount, 0, ',', '.');
        $symbol = $symbols[$currency ?? 'TRY'] ?? '₺';

        return $formatted . ' ' . $symbol;
    }
}
