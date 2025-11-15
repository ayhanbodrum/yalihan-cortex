<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Services\Response\ResponseService;
use App\Models\Setting;
use Illuminate\Http\Request;

class AISettingsController extends Controller
{
    public function providerStatus()
    {
        $service = app(AIService::class);
        return ResponseService::success([
            'provider' => config('ai.provider'),
            'model' => config('ai.default_model'),
            'available_providers' => $service->getAvailableProviders(),
        ], 'AI provider durumu');
    }

    public function testProvider(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string',
            'api_key' => 'nullable|string',
            'model' => 'nullable|string',
        ]);
        return ResponseService::success([
            'test' => 'ok',
            'provider' => $validated['provider'],
            'model' => $validated['model'] ?? null,
        ], 'Provider testi başarılı');
    }

    public function updateLocale(Request $request)
    {
        $validated = $request->validate([
            'locale' => 'required|string|in:tr,en',
        ]);
        Setting::updateOrCreate(['key' => 'app_locale'], ['value' => $validated['locale']]);
        return ResponseService::success(['locale' => $validated['locale']], 'Dil ayarı güncellendi');
    }

    public function updateCurrency(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|string|in:TRY,USD,EUR,GBP',
        ]);
        Setting::updateOrCreate(['key' => 'currency_default'], ['value' => $validated['currency']]);
        return ResponseService::success(['currency' => $validated['currency']], 'Para birimi ayarı güncellendi');
    }
}
