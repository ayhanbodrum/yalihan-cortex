<?php

use Illuminate\Support\Facades\Route;

// Preferences Routes
Route::prefix('preferences')->name('preferences.')->group(function () {
    Route::post('/locale', function () {
        return redirect()->back();
    })->name('locale');
    Route::post('/currency', function () {
        return redirect()->back();
    })->name('currency');
});

// Ana Sayfa - Gerçek Admin Paneli (Context7 Standartları)
Route::get('/', function () {
    return redirect()->route('admin.dashboard.index');
})->name('home');

// Secure file routes
Route::middleware(['web', 'throttle:secure_file'])->group(function () {
    Route::get('/secure-file/{encodedPath}', [\App\Http\Controllers\SecureFileController::class, 'serveSecureFile'])
        ->name('secure.file');
    Route::delete('/secure-file/{encodedPath}', [\App\Http\Controllers\SecureFileController::class, 'deleteSecureFile'])
        ->name('secure.file.delete')
        ->middleware('auth');
});

// Yalihan Design System - Clean Version
Route::get('/yalihan', function () {
    return view('yaliihan-home-clean');
})->name('yalihan.home');

// Context7 Gerçek Admin Paneli - Demo yerine gerçek proje
Route::get('/context7-demo', function () {
    return redirect()->route('admin.dashboard.index');
})->name('context7.demo');

// Yalihan Design System - Component Version (Kaldırıldı)

// Yalihan Property Detail Page
Route::get('/yalihan/property/{id}', function ($id) {
    return view('yaliihan-property-detail', ['id' => $id]);
})->name('yalihan.property.detail');

// Yalihan Property Listing Page
Route::get('/yalihan/properties', function () {
    return view('yaliihan-property-listing');
})->name('yalihan.properties');


// Yalihan Contact Page
Route::get('/yalihan/contact', function () {
    return view('yaliihan-contact');
})->name('yalihan.contact');

use App\Http\Controllers\AI\AISearchController;
use App\Http\Controllers\VillaController;

// AI Explore (public)
Route::get('/ai/explore', [AISearchController::class, 'explore'])->name('ai.explore');

// ============================================
// 🏖️ PUBLIC VILLA LISTING (TatildeKirala Tarzı)
// ============================================
Route::prefix('yazliklar')->name('villas.')->group(function () {
    // Villa listing page
    Route::get('/', [VillaController::class, 'index'])->name('index');

    // Villa detail page
    Route::get('/{id}', [VillaController::class, 'show'])->name('show');

    // Availability check (AJAX)
    Route::post('/check-availability', [VillaController::class, 'checkAvailability'])->name('check-availability');
});

// Simple ilan create route (for testing)
Route::get('/simple-create', function () {
    $anaKategoriler = \App\Models\IlanKategori::whereNull('parent_id')->orderBy('name')->get(['id', 'name']);
    $kisiler = \App\Models\Kisi::where('status', 'Aktif')->get();
    $danismanlar = \App\Models\User::whereHas('roles', function ($q) {
        $q->where('name', 'danisman');
    })->where('status', 1)->get();
    $iller = \App\Models\Il::orderBy('il_adi')->get();

    return view('admin.ilanlar.simple-create', compact('anaKategoriler', 'kisiler', 'danismanlar', 'iller'));
});

// İlan store route for live search form
Route::post('/ilanlar', [\App\Http\Controllers\Admin\IlanController::class, 'store'])->name('ilanlar.store');

// Hibrit Arama Demo
Route::get('/hybrid-search-demo', function () {
    return view('admin.test.hybrid-search-demo');
})->name('hybrid-search-demo');

// İlan başarı sayfası
Route::get('/ilan-success/{ilan}', function ($ilan) {
    $ilan = \App\Models\Ilan::with(['il', 'ilce', 'mahalle', 'ilanSahibi', 'danisman'])->find($ilan);
    return view('admin.ilanlar.success', compact('ilan'));
})->name('ilan.success')->middleware(['web', 'auth']);

// ===== STABLE-CREATE ROUTES REMOVED - USE /admin/ilanlar/create INSTEAD =====

// Old Photo upload endpoint - Deprecated
Route::post('/api/ilanlar/upload-photos-deprecated', function (\Illuminate\Http\Request $request) {
    try {
        $request->validate([
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120' // 5MB max per photo
        ]);

        $uploadedPhotos = [];

        // Create temporary directory for photos before ilan creation
        $tempDir = 'temp-photos/' . uniqid();

        foreach ($request->file('photos') as $photo) {
            $fileName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs($tempDir, $fileName, 'public');

            $uploadedPhotos[] = [
                'temp_path' => $path,
                'original_name' => $photo->getClientOriginalName(),
                'size' => $photo->getSize(),
                'preview_url' => \Illuminate\Support\Facades\Storage::url($path)
            ];
        }

        // Store in session for later processing
        session(['temp_photos' => $uploadedPhotos]);

        return response()->json([
            'success' => true,
            'message' => count($uploadedPhotos) . ' fotoğraf geçici olarak yüklendi.',
            'photos' => array_map(function ($photo) {
                return [
                    'id' => uniqid(),
                    'url' => $photo['preview_url'],
                    'name' => $photo['original_name']
                ];
            }, $uploadedPhotos)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Fotoğraf yükleme hatası: ' . $e->getMessage()
        ], 500);
    }
})->middleware('web');
use App\Http\Controllers\Admin\AyarlarController;
use App\Http\Controllers\Admin\EtiketController;
use App\Http\Controllers\Admin\FeatureCategoryController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\IlanKategoriController;
use App\Http\Controllers\Admin\KisiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AISettingsController;
use App\Http\Controllers\Admin\TKGMParselController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogSitemapController;
use App\Http\Controllers\IlanPublicController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CustomerProfileController;
use App\Http\Controllers\Ilan\PropertyFeatureController;
use App\Modules\TalepAnaliz\Controllers\TalepAnalizController;
use Illuminate\Http\Request;

// Auth rotalarını dahil et
require __DIR__ . '/auth.php';

// Include validation routes
require __DIR__ . '/web/admin/validation.php';

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Ana sayfa - Yalihan Design System
Route::get('/', function () {
    return view('yaliihan-home-clean');
})->name('home');

// Demo sayfası
Route::get('/demo-interactive', function () {
    return view('demo-interactive');
})->name('demo.interactive');

// Test ayarlar sayfası
Route::get('/test-ayarlar', function () {
    $ayarlar = App\Models\Setting::all()->pluck('value', 'key')->toArray();

    return view('admin.ayarlar.index', compact('ayarlar'))
        ->with('featureCategories', collect())
        ->with('ilanFeatureAyarlari', [])
        ->with('nearbyPlaceCategories', collect());
})->name('test.ayarlar');

// Modern Frontend Routes - Redirected to main
Route::prefix('modern')->name('modern.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('home');
    })->name('home');

    Route::get('/listings', function () {
        return redirect()->route('ilanlar.index');
    })->name('listings');

    Route::get('/listing/{id}', function ($id) {
        return redirect()->route('ilanlar.show', $id);
    })->name('listing.detail');
});

// Public Pages
Route::get('/hakkimizda', function () {
    return view('pages.about');
})->name('about');

Route::get('/iletisim', function () {
    return view('pages.contact');
})->name('contact');

Route::get('/danismanlar', function () {
    return view('pages.advisors');
})->name('advisors');

// Frontend Danışmanlar Routes
Route::prefix('danismanlar')->name('frontend.danismanlar.')->group(function () {
    Route::get('/', function () {
        return view('frontend.danismanlar.index');
    })->name('index');
    Route::get('/{id}', function ($id) {
        return view('frontend.danismanlar.show', compact('id'));
    })->name('show');
});

// Public Ilan routes (only published)
Route::prefix('ilanlar')->name('ilanlar.')->group(function () {
    Route::get('/', [IlanPublicController::class, 'index'])->name('index');
    Route::get('/international', [IlanPublicController::class, 'international'])->name('international');
    Route::get('/kategori/{kategoriId}', [IlanPublicController::class, 'kategoriIlanlari'])->name('kategori');
    Route::get('/{id}', [IlanPublicController::class, 'show'])->name('show');
    Route::get('/{id}/calendar', [IlanPublicController::class, 'calendar'])->name('calendar');
    Route::get('/{id}/nearby', [IlanPublicController::class, 'nearby'])->name('nearby');
});

// Frontend Portfolio Routes
Route::prefix('portfolio')->name('frontend.portfolio.')->group(function () {
    Route::get('/', function () {
        $properties = \App\Models\Ilan::where('status', 'Aktif')
            ->with(['il', 'ilce', 'etiketler'])
            ->paginate(12);

        $stats = [
            'total_properties' => \App\Models\Ilan::count(),
            'active_properties' => \App\Models\Ilan::where('status', 'Aktif')->count(),
            'total_value' => \App\Models\Ilan::where('status', 'Aktif')->sum('fiyat') / 1000000,
            'locations' => \App\Models\Il::count()
        ];

        return view('frontend.portfolio.index', compact('properties', 'stats'));
    })->name('index');

    Route::get('/{id}', [IlanPublicController::class, 'show'])->name('detail');
});

// Danışman ilanları
Route::get('/danisman/{id}/ilanlar', [IlanPublicController::class, 'danismanIlanlari'])->name('danisman.ilanlar');

// Özellikler API (Public)
Route::get('/ozellikler/by-emlak-turu', [FeatureController::class, 'getByEmlakTuru'])
    ->name('ozellikler.by-emlak-turu');

// Test endpoint
Route::get('/test-features', function () {
    return response()->json([
        'success' => true,
        'message' => 'Test endpoint çalışıyor',
        'features' => [],
    ]);
});

// Neo Location Selector Test
Route::get('/test/neo-location', function () {
    return view('test.neo-location-test');
})->name('test.neo-location');

// Lokasyon API Endpoint'leri
Route::prefix('api/location')->group(function () {
    Route::get('/countries', function () {
        $countries = \Illuminate\Support\Facades\DB::table('ulkeler')
            ->select('id', 'ulke_adi as name', 'ulke_kodu as code')
            ->orderBy('ulke_adi')
            ->get();

        return response()->json(['success' => true, 'countries' => $countries]);
    });

    Route::get('/provinces', [\App\Http\Controllers\Api\LocationController::class, 'getProvinces']);

    Route::get('/districts', [\App\Http\Controllers\Api\LocationController::class, 'getDistricts']);

    Route::get('/neighborhoods', [\App\Http\Controllers\Api\LocationController::class, 'getNeighborhoods']);

    // Alt Kategoriler API
    Route::get('/alt-kategoriler/{anaKategoriId}', function ($anaKategoriId) {
        try {
            $altKategoriler = \Illuminate\Support\Facades\DB::table('ilan_kategorileri')
                ->where('parent_id', $anaKategoriId)
                ->where('status', 1)
                ->select('id', 'name', 'parent_id')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $altKategoriler
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Alt kategoriler yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    });

    // EmlakLoc v4.1.0 - Form Wizard için ek endpointler
    Route::get('/districts-by-province', [\App\Http\Controllers\Admin\AddressController::class, 'getDistrictsByProvince']);
    Route::get('/neighborhoods-by-district', [\App\Http\Controllers\Admin\AddressController::class, 'getNeighborhoodsByDistrict']);

    Route::get('/neighborhood/{id}', function ($id) {
        $neighborhood = \Illuminate\Support\Facades\DB::table('mahalleler')
            ->select('id', 'mahalle_adi as name', 'ilce_id as district_id', 'enlem', 'boylam')
            ->where('id', $id)
            ->first();

        if (! $neighborhood) {
            return response()->json(['success' => false, 'message' => 'Neighborhood not found'], 404);
        }

        return response()->json(['success' => true, 'neighborhood' => $neighborhood]);
    });

    Route::get('/search', function (\Illuminate\Http\Request $request) {
        $query = $request->get('q', '');
        $limit = $request->get('limit', 10);

        if (strlen($query) < 2) {
            return response()->json(['success' => true, 'results' => []]);
        }

        $results = [];

        // İl arama
        $provinces = \Illuminate\Support\Facades\DB::table('iller')
            ->select('id', 'il_adi as name', 'plaka_kodu as code')
            ->where('il_adi', 'LIKE', "%{$query}%")
            ->limit($limit)
            ->get();

        foreach ($provinces as $province) {
            $results[] = [
                'type' => 'province',
                'id' => $province->id,
                'name' => $province->name,
                'display' => $province->name . " ({$province->code})",
            ];
        }

        // İlçe arama
        $districts = \Illuminate\Support\Facades\DB::table('ilceler as ic')
            ->join('iller as il', 'ic.il_id', '=', 'il.id')
            ->select('ic.id', 'ic.ilce_adi as name', 'il.il_adi as province_name')
            ->where('ic.ilce_adi', 'LIKE', "%{$query}%")
            ->limit($limit)
            ->get();

        foreach ($districts as $district) {
            $results[] = [
                'type' => 'district',
                'id' => $district->id,
                'name' => $district->name,
                'display' => $district->name . ' / ' . $district->province_name,
            ];
        }

        return response()->json(['success' => true, 'results' => array_slice($results, 0, $limit)]);
    });
});

// Test sayfaları
Route::get('/test-form-handler', function () {
    return response()->file(public_path('test-form-handler.html'));
})->name('test.form.handler');

// Not: Test API endpoints temizlendi. Ana location API kullanılıyor: /api/locations/*

/*
|--------------------------------------------------------------------------
| Blog Routes (Public)
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'blog'], function () {
    Route::get('/', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/search', [BlogController::class, 'search'])->name('blog.search');
    Route::get('/rss', [BlogController::class, 'rss'])->name('blog.rss');

    // Sitemap routes
    Route::get('/sitemap.xml', [BlogSitemapController::class, 'index'])->name('blog.sitemap.index');
    Route::get('/sitemap-posts.xml', [BlogSitemapController::class, 'posts'])->name('blog.sitemap.posts');
    Route::get('/sitemap-categories.xml', [BlogSitemapController::class, 'categories'])->name('blog.sitemap.categories');
    Route::get('/sitemap-tags.xml', [BlogSitemapController::class, 'tags'])->name('blog.sitemap.tags');

    // Archive routes
    Route::get('/archive/{year}', [BlogController::class, 'archive'])->name('blog.archive.year');
    Route::get('/archive/{year}/{month}', [BlogController::class, 'archive'])->name('blog.archive.month');
    Route::get('/load-more', [BlogController::class, 'loadMore'])->name('blog.load-more');

    // Category and Tag routes
    Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
    Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');

    // Post routes
    Route::get('/{slug}', [BlogController::class, 'show'])->name('blog.show');
    Route::post('/{post}/comment', [BlogController::class, 'storeComment'])->name('blog.comment.store');

    // AJAX routes
    Route::post('/{post}/like', [BlogController::class, 'likePost'])->name('blog.post.like');
    Route::post('/comment/{comment}/like', [BlogController::class, 'likeComment'])->name('blog.comment.like');
    Route::post('/comment/{comment}/dislike', [BlogController::class, 'dislikeComment'])->name('blog.comment.dislike');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

// Test routes (development only)
if (config('app.env') === 'local') {
    Route::get('/test/emlak-loc-integration', function () {
        return view('test.emlak-loc-integration');
    });

    Route::get('/test/form-wizard-debug', function () {
        // İller verisini hazırla
        $iller = \App\Models\Il::where('active', 1)->orderBy('il_adi')->get();
        return view('test.form-wizard-debug', compact('iller'));
    });

    // Site/Apartman Live Search Test
    Route::get('/test/site-live-search', function () {
        return view('admin.test.site-live-search');
    })->name('test.site-live-search');
}

// TKGM Public Test Routes (Auth gerektirmez)
Route::get('/test-tkgm', function () {
    return view('test-tkgm');
})->name('test-tkgm');

Route::get('/tkgm-test-center', function () {
    return view('tkgm-test-center');
})->name('tkgm-test-center');

Route::post('/test-tkgm-direct', function (\Illuminate\Http\Request $request) {
    $service = app(\App\Services\TKGMService::class);
    return $service->parselSorgula(
        $request->ada,
        $request->parsel,
        $request->il,
        $request->ilce,
        $request->mahalle
    );
})->name('test-tkgm-direct');

Route::post('/test-tkgm-investment', function (\Illuminate\Http\Request $request) {
    $service = app(\App\Services\TKGMService::class);

    // Önce parsel bilgilerini al
    $parselResult = $service->parselSorgula(
        $request->ada,
        $request->parsel,
        $request->il,
        $request->ilce,
        $request->mahalle
    );

    if (!$parselResult['success']) {
        return response()->json([
            'success' => false,
            'message' => 'Önce parsel bilgileri bulunmalı'
        ]);
    }

    // Yatırım analizini yap
    $investmentAnalysis = $service->yatirimAnalizi($parselResult['parsel_bilgileri']);

    return response()->json([
        'success' => true,
        'parsel_bilgileri' => $parselResult['parsel_bilgileri'],
        'yatirim_analizi' => $investmentAnalysis
    ]);
})->name('test-tkgm-investment');

Route::post('/test-tkgm-ai-plan', function (\Illuminate\Http\Request $request) {
    $service = app(\App\Services\TKGMService::class);

    // Önce parsel sorgula
    $parselResult = $service->parselSorgula(
        $request->input('ada'),
        $request->input('parsel'),
        $request->input('il'),
        $request->input('ilce'),
        $request->input('mahalle')
    );

    if (!$parselResult['success']) {
        return response()->json($parselResult);
    }

    // AI plan notları analizi
    $teknikBilgiler = $request->input('teknik_bilgiler', []);
    $aiPlanNotlari = $service->aiPlanNotlariAnalizi($parselResult, $teknikBilgiler);

    return response()->json([
        'success' => true,
        'parsel_bilgileri' => $parselResult['parsel_bilgileri'],
        'ai_plan_notlari' => $aiPlanNotlari,
        'timestamp' => now()->toISOString()
    ]);
})->name('test-tkgm-ai-plan');

Route::get('/test-ollama-models', function () {
    $aiService = app(\App\Services\AIService::class);
    $modelsData = $aiService->getOllamaModels();
    $recommendations = $aiService->getModelRecommendations();

    return response()->json([
        'ollama_models' => $modelsData,
        'recommendations' => $recommendations,
        'timestamp' => now()->toISOString()
    ]);
})->name('test-ollama-models');

Route::middleware('auth')->group(function () {
    // TKGM Parsel Sorgulama Sistemi
    Route::prefix('admin/tkgm-parsel')->name('admin.tkgm-parsel.')->group(function () {
        Route::get('/', [TKGMParselController::class, 'index'])->name('index');
        Route::post('/query', [TKGMParselController::class, 'query'])->name('query');
        Route::post('/bulk-query', [TKGMParselController::class, 'bulkQuery'])->name('bulk-query');
        Route::get('/history', [TKGMParselController::class, 'history'])->name('history');
        Route::get('/stats', [TKGMParselController::class, 'stats'])->name('stats');
    });
    // AI Settings (Admin) - REMOVED: Çakışan route, admin.php'de mevcut
    // Context7 & MCP: İlan Özellikleri
    Route::get('/admin/property/{propertyId}/features', [PropertyFeatureController::class, 'show'])->name('admin.property.features');
    // MCP & AI destekli müşteri profili
    Route::get('/admin/customer-profile/{customerId}', [CustomerProfileController::class, 'show'])->name('admin.customer-profile');
    // Test Paneli (Context7 Template)
    // Profile Management
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // Admin redirect
    Route::get('/admin', function () {
        return redirect()->route('admin.dashboard.index');
    });


    // Context7 Test Route
    Route::get('/admin/context7-test', function () {
        return view('admin.context7-test');
    })->name('admin.context7-test');

    // Danışman Management (Outside admin prefix)
    Route::prefix('/danisman')->name('danisman.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DanismanController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\DanismanController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\DanismanController::class, 'store'])->name('store');
        Route::get('/{danisman}', [\App\Http\Controllers\Admin\DanismanController::class, 'show'])->name('show');
        Route::get('/{danisman}/edit', [\App\Http\Controllers\Admin\DanismanController::class, 'edit'])->name('edit');
        Route::put('/{danisman}', [\App\Http\Controllers\Admin\DanismanController::class, 'update'])->name('update');
        Route::delete('/{danisman}', [\App\Http\Controllers\Admin\DanismanController::class, 'destroy'])->name('destroy');
        Route::post('/{danisman}/toggle-status', [\App\Http\Controllers\Admin\DanismanController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\DanismanController::class, 'bulkAction'])->name('bulk-action');
    });

    // CRM - Kişi Yönetimi (Public access)
    Route::resource('/kisiler', KisiController::class)->parameters(['kisiler' => 'kisi']);

    // İlan Kategori Yönetimi
    Route::prefix('/ilan-kategorileri')->name('ilan-kategorileri.')->group(function () {
        Route::get('/yayin-tipleri', [IlanKategoriController::class, 'getYayinTipleri'])->name('getYayinTipleri');
        Route::get('/filter/{level}', [IlanKategoriController::class, 'filterByLevel'])->name('filterByLevel');
    });

    // Etiket Yönetimi
    Route::resource('/etiketler', EtiketController::class)->except(['show']);

    // Talep Analiz Modülü
    Route::prefix('/talep-analiz')->name('talep-analiz.')->group(function () {
        Route::get('/', [TalepAnalizController::class, 'index'])->name('index');
        Route::get('/test', [TalepAnalizController::class, 'testSayfasi'])->name('test');
        Route::get('/{id}', [TalepAnalizController::class, 'analizEt'])->name('show');
        Route::post('/toplu-analiz', [TalepAnalizController::class, 'topluAnalizEt'])->name('toplu');
        Route::get('/{id}/rapor', [TalepAnalizController::class, 'raporOlustur'])->name('rapor');
    });

    // Özellikler Management
    Route::prefix('ozellikler')->name('ozellikler.')->group(function () {
        Route::get('/', [FeatureController::class, 'index'])->name('index');
        Route::get('/create', [FeatureController::class, 'create'])->name('create');
        Route::post('/', [FeatureController::class, 'store'])->name('store');
        Route::get('/{id}', [FeatureController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [FeatureController::class, 'edit'])->name('edit');
        Route::put('/{id}', [FeatureController::class, 'update'])->name('update');
        Route::delete('/{id}', [FeatureController::class, 'destroy'])->name('destroy');

        // Feature Categories
        Route::prefix('kategoriler')->name('kategoriler.')->group(function () {
            Route::get('/', [FeatureCategoryController::class, 'index'])->name('index');
            Route::get('/create', [FeatureCategoryController::class, 'create'])->name('create');
            Route::post('/', [FeatureCategoryController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [FeatureCategoryController::class, 'edit'])->name('edit');
            Route::put('/{id}', [FeatureCategoryController::class, 'update'])->name('update');
            Route::delete('/{id}', [FeatureCategoryController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/features', [FeatureCategoryController::class, 'features'])->name('features');
        });
    });

    // Kullanıcı Yönetimi - MOVED TO admin.php with proper AuthController
    // Route kullanıcılar yönetimi admin.php'ye taşındı

    // Settings Management (Legacy - moved to admin.php)
    Route::get('/settings', function () {
        return redirect()->route('admin.ayarlar.index');
    });



    // Valuation Dashboard Routes (under /admin)
    Route::prefix('admin/valuation')->name('valuation.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.valuation.dashboard');
        })->name('dashboard');

        Route::get('/parcel-search', function () {
            return view('admin.valuation.parcel-search');
        })->name('parcel-search');

        Route::get('/calculate', function () {
            return view('admin.valuation.calculate');
        })->name('calculate');

        Route::get('/reports', function () {
            return view('admin.valuation.reports');
        })->name('reports');

        Route::get('/analytics', function () {
            return view('admin.valuation.analytics');
        })->name('analytics');

        Route::get('/history', function () {
            return view('admin.valuation.history');
        })->name('history');

        Route::get('/market-trends', function () {
            return view('admin.valuation.market-trends');
        })->name('market-trends');
    });



    // Tip Yönetimi
    Route::prefix('tip-yonetimi')->name('tip-yonetimi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TipYonetimiController::class, 'index'])->name('index');
        Route::get('/{kategori}/create', [\App\Http\Controllers\Admin\TipYonetimiController::class, 'create'])->name('create');
        Route::post('/{kategori}', [\App\Http\Controllers\Admin\TipYonetimiController::class, 'store'])->name('store');
        Route::get('/{kategori}/{id}/edit', [\App\Http\Controllers\Admin\TipYonetimiController::class, 'edit'])->name('edit');
        Route::put('/{kategori}/{id}', [\App\Http\Controllers\Admin\TipYonetimiController::class, 'update'])->name('update');
        Route::delete('/{kategori}/{id}', [\App\Http\Controllers\Admin\TipYonetimiController::class, 'destroy'])->name('destroy');
    });

    // Feature API Routes (Modal Selector) - MOVED TO api-admin.php (API routes için)

    // AI Monitoring Dashboard
    Route::middleware(['web', 'auth', 'throttle:60,1'])->group(function () {
        Route::get('/admin/ai-monitor', [\App\Http\Controllers\Admin\SystemMonitorController::class, 'index'])->name('admin.ai-monitor.index');
        // JSON live endpoints
        Route::get('/admin/ai-monitor/mcp', [\App\Http\Controllers\Admin\SystemMonitorController::class, 'apiMcpStatus'])->name('admin.ai-monitor.mcp');
        Route::get('/admin/ai-monitor/apis', [\App\Http\Controllers\Admin\SystemMonitorController::class, 'apiApiStatus'])->name('admin.ai-monitor.apis');
        Route::get('/admin/ai-monitor/self-healing', [\App\Http\Controllers\Admin\SystemMonitorController::class, 'apiSelfHealing'])->name('admin.ai-monitor.self');
        Route::get('/admin/ai-monitor/errors', [\App\Http\Controllers\Admin\SystemMonitorController::class, 'apiRecentErrors'])->name('admin.ai-monitor.errors');
        // Yeni ekosistem analiz endpoint'leri
        Route::get('/admin/ai-monitor/code-health', [\App\Http\Controllers\Admin\SystemMonitorController::class, 'apiCodeHealth'])->name('admin.ai-monitor.code-health');
        Route::get('/admin/ai-monitor/duplicates', [\App\Http\Controllers\Admin\SystemMonitorController::class, 'apiDuplicateFiles'])->name('admin.ai-monitor.duplicates');
        Route::get('/admin/ai-monitor/conflicts', [\App\Http\Controllers\Admin\SystemMonitorController::class, 'apiConflictingRoutes'])->name('admin.ai-monitor.conflicts');
        // Sayfa Sağlığı
        Route::get('/admin/ai-monitor/pages-health', [\App\Http\Controllers\Admin\SystemMonitorController::class, 'apiPagesHealth'])->name('admin.ai-monitor.pages-health');
        // Context7 Öğretim ve Öneri Endpoint'leri
        Route::post('/admin/ai-monitor/run-context7-fix', [\App\Http\Controllers\Admin\SystemMonitorController::class, 'runContext7Fix'])->name('admin.ai-monitor.run-context7-fix');
        Route::post('/admin/ai-monitor/apply-suggestion', [\App\Http\Controllers\Admin\SystemMonitorController::class, 'applySuggestion'])->name('admin.ai-monitor.apply-suggestion');
        Route::get('/admin/ai-monitor/context7-rules', [\App\Http\Controllers\Admin\SystemMonitorController::class, 'getContext7Rules'])->name('admin.ai-monitor.context7-rules');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Blog Routes
|--------------------------------------------------------------------------
*/


// AI API Routes (Web middleware for CSRF support)
Route::prefix('api/ai')->group(function () {
    Route::post('/chat', [App\Http\Controllers\Api\AIChatController::class, 'chat']);
    Route::post('/generate-description', [App\Http\Controllers\Api\AIChatController::class, 'generateDescription']);
    Route::post('/suggest-tags', [App\Http\Controllers\Api\AIChatController::class, 'suggestTags']);
    Route::post('/analyze-demand', [App\Http\Controllers\Api\AIChatController::class, 'analyzeDemand']);
    Route::post('/find-matching-properties', [App\Http\Controllers\Api\AIChatController::class, 'findMatchingProperties']);
});

// Advanced AI Routes moved to api.php

// Test routes removed - AI Monitor working correctly

// ===== FRONTEND DYNAMIC FORM ROUTES =====
Route::prefix('dynamic-form')->name('dynamic-form.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Frontend\DynamicFormController::class, 'index'])->name('index');
    Route::post('/render', [\App\Http\Controllers\Frontend\DynamicFormController::class, 'renderForm'])->name('render');
    Route::post('/submit', [\App\Http\Controllers\Frontend\DynamicFormController::class, 'submitForm'])->name('submit');
    Route::post('/ai-suggestion', [\App\Http\Controllers\Frontend\DynamicFormController::class, 'getAISuggestion'])->name('ai-suggestion');
});

// ===== AI STATUS API =====
Route::get('/api/ai/status', function () {
    try {
        $aiService = app(\App\Services\AIService::class);
        $settings = \App\Models\Setting::whereIn('key', ['ollama_url', 'ollama_model', 'ai_provider'])->pluck('value', 'key');

        return response()->json([
            'success' => true,
            'model' => $settings['ollama_model'] ?? 'Ollama Qwen2.5',
            'connected' => true, // Basit kontrol
            'features' => 68, // Matrix'teki toplam field sayısı
            'provider' => $settings['ai_provider'] ?? 'ollama'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'model' => 'Bilinmiyor',
            'connected' => false,
            'features' => 0,
            'error' => $e->getMessage()
        ]);
    }
})->name('api.ai.status');
