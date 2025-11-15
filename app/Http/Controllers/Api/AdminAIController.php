<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIService as CoreAIService;
use App\Services\AI\PriceService;
use App\Services\Response\ResponseService;
use App\Models\AiLog;
use Illuminate\Http\Request;

class AdminAIController extends Controller
{
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'nullable|string|max:64',
            'user_msg' => 'required|string|max:2000',
            'context' => 'nullable|array',
        ]);

        try {
            $service = app(CoreAIService::class);
            $result = $service->generate($validated['user_msg'], $validated['context'] ?? []);
            $meta = $result['metadata'] ?? [];
            return ResponseService::success([
                'message' => $result['data'] ?? '',
            ], 'Chat yanıtı', 200, [
                'provider' => $meta['provider'] ?? null,
                'response_time' => $meta['duration'] ?? 0,
                'timestamp' => $meta['timestamp'] ?? now()->toISOString(),
            ]);
        } catch (\Throwable $e) {
            return ResponseService::serverError('Chat isteği başarısız', $e);
        }
    }

    public function pricePredict(Request $request)
    {
        $validated = $request->validate([
            'property_data' => 'required|array',
            'currency' => 'nullable|string',
        ]);
        try {
            $service = app(PriceService::class);
            $out = $service->predict($validated['property_data']);
            if (!empty($validated['currency'])) {
                $conv = app(\App\Services\CurrencyConversionService::class)->convert((float)($out['predicted_price'] ?? 0), 'TRY', $validated['currency']);
                if ($conv) {
                    $out['predicted_price'] = (int)round($conv['amount']);
                    $out['currency'] = $conv['currency'];
                }
            }
            return ResponseService::success($out, 'Fiyat tahmini');
        } catch (\Throwable $e) {
            return ResponseService::serverError('Fiyat tahmini başarısız', $e);
        }
    }

    public function analytics(Request $request)
    {
        $days = (int)($request->get('days', 30));
        $total = AiLog::recent($days)->count();
        $success = AiLog::recent($days)->successful()->count();
        $failed = AiLog::recent($days)->failed()->count();
        $avg = AiLog::averageResponseTime();
        $providers = AiLog::providerUsage($days);
        $errorRate = $total > 0 ? round(($failed / $total) * 100, 2) : 0;
        $successRate = $total > 0 ? round(($success / $total) * 100, 2) : 0;
        return ResponseService::success([
            'average_response_time' => $avg,
            'success_rate' => $successRate,
            'error_rate' => $errorRate,
            'provider_usage' => $providers,
            'total_requests' => $total,
        ], 'AI analytics');
    }
}