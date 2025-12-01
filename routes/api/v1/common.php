<?php

use App\Http\Controllers\Admin\IlanSearchController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Api\BookingRequestController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\Context7Controller;
use App\Http\Controllers\Api\CurrencyRateController;
use App\Http\Controllers\Api\DanismanController;
use App\Http\Controllers\Api\DemirbasController;
use App\Http\Controllers\Api\ExchangeRateController;
use App\Http\Controllers\Api\FeaturesController;
use App\Http\Controllers\Api\GeocodingController;
use App\Http\Controllers\Api\GeoProxyController;
use App\Http\Controllers\Api\KisiController;
use App\Http\Controllers\Api\ListingNavigationController;
use App\Http\Controllers\Api\N8nWebhookController;
use App\Http\Controllers\Api\QRCodeController;
use App\Http\Controllers\Api\ReferenceController;
use App\Http\Controllers\Api\SiteApartmanController;
use App\Http\Controllers\Api\TKGMController;
use App\Http\Controllers\Api\UnifiedSearchController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Common API Routes (v1)
|--------------------------------------------------------------------------
|
| Common/public API endpoints
| Includes categories, features, geocoding, currency, etc.
|
*/

// Health Check
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is healthy',
        'timestamp' => now()->toISOString(),
    ]);
})->name('api.health');

// Geo Proxy (Context7) - Nominatim/Overpass via server with cache
Route::prefix('geo')->name('api.geo.')->group(function () {
    Route::post('/geocode', [GeoProxyController::class, 'geocode'])->name('geocode');
    Route::post('/reverse-geocode', [GeoProxyController::class, 'reverseGeocode'])->name('reverse-geocode');
    Route::get('/nearby', [GeoProxyController::class, 'nearby'])->name('nearby');
});

// QR Code API Routes
Route::prefix('qrcode')->name('api.qrcode.')->middleware(['web'])->group(function () {
    Route::get('/listing/{ilanId}', [QRCodeController::class, 'generateForListing'])->name('listing');
    Route::get('/whatsapp/{ilanId}', [QRCodeController::class, 'generateForWhatsApp'])->name('whatsapp');
    Route::get('/ai-suggestions/{ilanId}', [QRCodeController::class, 'getAISuggestions'])->name('ai-suggestions');
    Route::get('/statistics', [QRCodeController::class, 'getStatistics'])->name('statistics')->middleware('auth');
});

// Listing Navigation API Routes
Route::prefix('navigation')->name('api.navigation.')->middleware(['web'])->group(function () {
    Route::get('/listing/{ilanId}', [ListingNavigationController::class, 'getNavigation'])->name('listing');
    Route::get('/similar/{ilanId}', [ListingNavigationController::class, 'getSimilar'])->name('similar');
    Route::get('/ai-suggestions/{ilanId}', [ListingNavigationController::class, 'getAISuggestions'])->name('ai-suggestions');
});

// Categories API Routes (Standardized)
Route::prefix('categories')->name('api.categories.')->group(function () {
    Route::get('/sub/{parentId}', [CategoriesController::class, 'getSubcategories'])->name('subcategories');
    Route::get('/publication-types/{categoryId}', [CategoriesController::class, 'getPublicationTypes'])->name('publication-types');
});

// Features API Routes
Route::prefix('features')->name('api.features.')->group(function () {
    Route::get('/category/{categoryId}', [FeaturesController::class, 'getFeaturesByCategory'])->name('category');
});

// Demirbaşlar API Routes (Hiyerarşik Yapı)
Route::prefix('demirbas')->name('api.demirbas.')->group(function () {
    Route::get('/categories', [DemirbasController::class, 'getCategories'])->name('categories');
});

// Geocoding API Routes (CORS proxy)
Route::prefix('geocoding')->name('api.geocoding.')->group(function () {
    Route::get('/search', [GeocodingController::class, 'search'])->name('search');
    Route::get('/reverse', [GeocodingController::class, 'reverse'])->name('reverse');
});

// Site/Apartman API Routes
Route::prefix('site-apartman')->name('api.site-apartman.')->group(function () {
    Route::get('/search', [SiteApartmanController::class, 'search'])->name('search');
    Route::get('/{id}', [SiteApartmanController::class, 'show'])->name('show');
});

// Site/Apartman Live Search API (Public - no auth required)
Route::get('/sites/search', [SiteController::class, 'search'])->name('api.sites.search');
Route::get('/site-apartman/search', function (\Illuminate\Http\Request $request) {
    $controller = app(SiteController::class);

    return $controller->search($request);
})->name('api.site-apartman.search');

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/sites/detail/{id}', [SiteController::class, 'show'])->name('api.sites.detail');
});

// Currency Rate API (Real-time döviz kurları)
Route::prefix('currency')->name('api.currency.')->group(function () {
    Route::get('/rates', [CurrencyRateController::class, 'getRates'])->name('rates');
    Route::post('/convert', [CurrencyRateController::class, 'convert'])->name('convert');
    Route::get('/supported', [CurrencyRateController::class, 'getSupportedCurrencies'])->name('supported');
    Route::post('/refresh', [CurrencyRateController::class, 'refresh'])->name('refresh');
});

// Exchange Rates API (TCMB Integration - Context7 Compliant)
Route::prefix('exchange-rates')->name('api.exchange-rates.')->group(function () {
    Route::get('/', [ExchangeRateController::class, 'index'])->name('index');
    Route::get('/supported', [ExchangeRateController::class, 'supported'])->name('supported');
    Route::get('/{code}', [ExchangeRateController::class, 'show'])->name('show');
    Route::get('/{code}/history', [ExchangeRateController::class, 'history'])->name('history');
    Route::post('/convert', [ExchangeRateController::class, 'convert'])->name('convert');
    Route::post('/update', [ExchangeRateController::class, 'update'])
        ->middleware(['web', 'auth'])
        ->name('update');
});

// Reference & File Management API (Context7 Compliant)
Route::prefix('reference')->name('api.reference.')->middleware(['web', 'auth'])->group(function () {
    Route::post('/generate', [ReferenceController::class, 'generateRef'])->name('generate');
    Route::get('/validate/{referansNo}', [ReferenceController::class, 'validateRef'])->name('validate');
    Route::post('/basename', [ReferenceController::class, 'generateBasename'])->name('basename');
    Route::post('/portal', [ReferenceController::class, 'updatePortalNumber'])->name('portal');
    Route::get('/{ilanId}', [ReferenceController::class, 'getReferenceInfo'])->name('info');
    Route::post('/batch-generate', [ReferenceController::class, 'batchGenerateRef'])->name('batch-generate');
});

// n8n Webhook API Routes (Context7 Standard: C7-N8N-WEBHOOK-2025-11-20)
Route::prefix('webhook/n8n')->name('api.webhook.n8n.')
    ->middleware(['throttle:60,1', 'n8n.secret']) // Rate limiting + Security
    ->group(function () {
        // Test endpoint
        Route::post('/test', [N8nWebhookController::class, 'test'])->name('test');

        // AI endpoints
        Route::post('/ai/ilan-taslagi', [N8nWebhookController::class, 'ilanTaslagi'])->name('ai.ilan-taslagi');
        Route::post('/ai/mesaj-taslagi', [N8nWebhookController::class, 'mesajTaslagi'])->name('ai.mesaj-taslagi');
        Route::post('/ai/sozlesme-taslagi', [N8nWebhookController::class, 'sozlesmeTaslagi'])->name('ai.sozlesme-taslagi');

        // Market Analysis & Listing Management
        Route::post('/analyze-market', [N8nWebhookController::class, 'analyzeMarket'])->name('analyze-market');
        Route::post('/create-draft-listing', [N8nWebhookController::class, 'createDraftListing'])->name('create-draft-listing');
        Route::post('/trigger-reverse-match', [N8nWebhookController::class, 'triggerReverseMatch'])->name('trigger-reverse-match');
    });

// Context7 Status API
Route::get('/context7/status', [Context7Controller::class, 'status'])->name('api.context7.status');

Route::prefix('context7')->name('api.context7.')->group(function () {
    Route::get('/memory/performance', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'day_changes' => \App\Models\IlanPrivateAudit::whereDate('created_at', now()->toDateString())->count(),
                'month_changes' => \App\Models\IlanPrivateAudit::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            ],
        ]);
    })->name('memory.performance');
});

// Danışman API
Route::prefix('danisman')->name('api.danisman.')->group(function () {
    Route::get('/{id}', [DanismanController::class, 'show'])->name('show');
});

// Kişi CRM API (Context7 uyumlu)
Route::prefix('kisiler')->name('api.kisiler.')->group(function () {
    Route::get('/search', [KisiController::class, 'search'])->name('search');
    Route::get('/{id}/ilan-gecmisi', [KisiController::class, 'getIlanGecmisi'])->name('ilan-gecmisi');
    Route::get('/{id}/profil', [KisiController::class, 'getProfil'])->name('profil');
    Route::get('/{id}/ai-gecmis-analiz', [KisiController::class, 'getAIGecmisAnaliz'])->name('ai-gecmis-analiz');
    Route::post('/', [KisiController::class, 'store'])->name('store');
});

// Users API (Sistem Danışmanları - users tablosu)
Route::prefix('users')->name('api.users.')->group(function () {
    Route::get('/search', [UserController::class, 'search'])->name('search');
    Route::get('/danismanlar', [UserController::class, 'danismanlar'])->name('danismanlar');
});

// İlan Arama API (Context7 uyumlu)
Route::prefix('ilanlar')->name('api.ilanlar.')->group(function () {
    Route::get('/search', [IlanSearchController::class, 'search'])->name('search');
    Route::get('/by-referans/{referansNo}', [IlanSearchController::class, 'findByReferans'])->name('by-referans');
    Route::get('/by-telefon', [IlanSearchController::class, 'findByTelefon'])->name('by-telefon');
    Route::get('/by-portal', [IlanSearchController::class, 'findByPortalId'])->name('by-portal');
    Route::get('/by-site', [IlanSearchController::class, 'findBySite'])->name('by-site');
});

// Unified Search Routes
Route::prefix('api/search')->name('api.search.')->group(function () {
    Route::get('/unified', [UnifiedSearchController::class, 'search'])->name('unified');
    Route::get('/suggestions', [UnifiedSearchController::class, 'suggestions'])->name('suggestions');
    Route::get('/analytics', [UnifiedSearchController::class, 'analytics'])->name('analytics');
    Route::post('/cache', [UnifiedSearchController::class, 'updateCache'])->name('cache');
});

// TKGM Parsel Sorgulama API (Context7 Kural #70)
Route::prefix('tkgm')->name('api.tkgm.')->middleware(['throttle:20,1'])->group(function () {
    Route::post('/parsel-sorgu', [TKGMController::class, 'parselSorgula'])->name('parsel-sorgu');
    Route::post('/yatirim-analizi', [TKGMController::class, 'yatirimAnalizi'])->name('yatirim-analizi');
    Route::get('/health', [TKGMController::class, 'healthCheck'])->name('health');
    Route::post('/clear-cache', [TKGMController::class, 'clearCache'])
        ->middleware('can:admin')
        ->name('clear-cache');
});

// Public Booking Request API (Context7 Compliant)
Route::post('/booking-request', [BookingRequestController::class, 'store'])->name('api.booking-request');
Route::post('/check-availability', [BookingRequestController::class, 'checkAvailability'])->name('api.check-availability');
Route::post('/get-booking-price', [BookingRequestController::class, 'getPrice'])->name('api.get-booking-price');
Route::post('/suggest-alternatives', [BookingRequestController::class, 'suggestAlternatives'])->name('api.suggest-alternatives');

// Wikimapia API Test
Route::prefix('wikimapia')->name('api.wikimapia.')->group(function () {
    Route::get('/test', function () {
        $service = new \App\Services\WikimapiaService;
        $lat = 41.0082;
        $lon = 28.9744;
        $results = $service->searchPlaces('Kadıköy', $lat, $lon, ['count' => 5]);

        return response()->json([
            'success' => true,
            'data' => $results,
            'message' => 'Wikimapia API test successful',
        ]);
    })->name('test');

    Route::get('/search', function (\Illuminate\Http\Request $request) {
        $service = new \App\Services\WikimapiaService;
        $query = $request->input('q', '');
        $lat = $request->input('lat', 41.0082);
        $lon = $request->input('lon', 28.9744);
        $results = $service->searchPlaces($query, $lat, $lon);

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    })->name('search');

    Route::post('/places/nearby', function (\Illuminate\Http\Request $request) {
        $service = new \App\Services\WikimapiaService;
        $lat = $request->input('lat');
        $lon = $request->input('lon');
        $radius = $request->input('radius', 0.01);
        $lonMin = $lon - $radius;
        $latMin = $lat - $radius;
        $lonMax = $lon + $radius;
        $latMax = $lat + $radius;
        $places = $service->getPlacesByArea($lonMin, $latMin, $lonMax, $latMax);

        return response()->json([
            'success' => true,
            'data' => $places,
        ]);
    })->name('places.nearby');
});
