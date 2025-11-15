<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\CurrencyConversionService;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    public function setLocale(Request $request)
    {
        $locale = (string) $request->string('locale')->lower();

        $supported = array_keys(config('localization.supported_locales', []));

        if (! in_array($locale, $supported, true)) {
            return $this->errorResponse('Unsupported locale', 422);
        }

        session(['locale' => $locale]);

        return $this->successResponse();
    }

    public function setCurrency(Request $request, CurrencyConversionService $currencyConversionService)
    {
        $currency = strtoupper((string) $request->string('currency')->trim());

        $supported = array_keys($currencyConversionService->getSupported());

        if (! in_array($currency, $supported, true)) {
            return $this->errorResponse('Unsupported currency', 422);
        }

        session(['currency' => $currency]);

        return $this->successResponse();
    }

    private function successResponse()
    {
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    private function errorResponse(string $message, int $status)
    {
        if (request()->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }

        return back()->withErrors(['preferences' => $message]);
    }
}


