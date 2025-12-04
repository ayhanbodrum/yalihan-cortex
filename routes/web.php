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

// Context7 demo route removed - use admin.dashboard

// Yalihan Design System - Component Version (Kaldırıldı)

// Yalihan Property Detail Page
Route::get('/yalihan/property/{id}', function ($id) {
    return view('yaliihan-property-detail', ['id' => $id]);
})->name('yalihan.property.detail');

// Yalihan Property Listing Page
Route::get('/yalihan/properties', function () {
    return view('yaliihan-property-listing');
})->name('yalihan.properties');

// TKGM Parsel Sorgulama - MOVED TO routes/api/v1/admin.php
// Route: POST /api/v1/admin/properties/tkgm-lookup
// This endpoint is now centralized in the API system

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

// DEPRECATED: Old Photo upload endpoint - REMOVED (use /api/v1/admin/photos instead)
// This endpoint was marked as deprecated and has been removed
// Archived in routes/api/v1/admin.php if needed for backward compatibility

use App\Http\Controllers\Admin\CustomerProfileController;
use App\Http\Controllers\Admin\EtiketController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\IlanKategoriController;
use App\Http\Controllers\Admin\KisiController;
use App\Http\Controllers\Admin\TalepPortfolyoController;
use App\Http\Controllers\Admin\TKGMParselController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogSitemapController;
use App\Http\Controllers\Ilan\PropertyFeatureController;
use App\Http\Controllers\IlanPublicController;
use App\Http\Controllers\ProfileController;
use App\Modules\TalepAnaliz\Controllers\TalepAnalizController;

// Auth rotalarını dahil et
require __DIR__ . '/auth.php';

// Include validation routes - REMOVED (duplicate with admin.php validate routes)

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Ana sayfa - Yalihan Design System
Route::get('/', function () {
    return view('yaliihan-home-clean');
})->name('home');

// Test and demo routes removed - use production endpoints instead

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

// Frontend Danışmanlar Routes
Route::get('/danismanlar', function () {
    return redirect()->route('frontend.danismanlar.index');
})->name('advisors');

Route::prefix('danismanlar')->name('frontend.danismanlar.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Frontend\DanismanController::class, 'index'])->name('index');
    Route::get('/{id}', [\App\Http\Controllers\Frontend\DanismanController::class, 'show'])->name('show');
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
            'locations' => \App\Models\Il::count(),
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

// Neo Location Selector Test - REMOVED
// Use /api/v1/location/* endpoints instead

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

// TKGM Test Routes - REMOVED: Eski sistem temizlendi, yeni sistem routes/api/v1/admin.php'de

Route::get('/test-ollama-models', function () {
    $aiService = app(\App\Services\AIService::class);
    $modelsData = $aiService->getOllamaModels();
    $recommendations = $aiService->getModelRecommendations();

    return response()->json([
        'ollama_models' => $modelsData,
        'recommendations' => $recommendations,
        'timestamp' => now()->toISOString(),
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

    // Talep Portfolyo (Admin) - Context7 uyumlu sayfa
    Route::middleware(['web', 'auth', 'admin', 'throttle:60,1'])
        ->get('/admin/talep-portfolyo', [TalepPortfolyoController::class, 'index'])
        ->name('admin.talep-portfolyo.index');
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

    // Etiket Yönetimi - REMOVED (controller deleted, feature unused)

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

        // Feature Categories - REMOVED (Controller deleted)
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

    // Tip Yönetimi - REMOVED (controller deleted, feature unused)

    // Feature API Routes (Modal Selector) - MOVED TO api-admin.php (API routes için)

    // AI Monitoring Dashboard - Web UI (JSON APIs moved to routes/api/v1/admin.php)
    Route::middleware(['web', 'auth', 'throttle:60,1'])->group(function () {
        Route::get('/admin/ai-monitor', [\App\Http\Controllers\Admin\SystemMonitorController::class, 'index'])->name('admin.ai-monitor.index');
        // Note: JSON live endpoints are now at /api/v1/admin/ai-monitor/*
    });
});

/*
|--------------------------------------------------------------------------
| Admin Blog Routes
|--------------------------------------------------------------------------
*/

// AI Chat Routes - MOVED TO routes/api/v1/ai.php
// Centralized API: POST /api/v1/chat/* endpoints
// Note: Backward compatibility routes - deprecated, use v1 endpoints
Route::prefix('api/ai')->middleware('throttle:30,1')->group(function () {
    // DEPRECATED: Use /api/v1/chat/* instead
    Route::post('/chat', [App\Http\Controllers\Api\AIChatController::class, 'chat']);
    Route::post('/generate-description', [App\Http\Controllers\Api\AIChatController::class, 'generateDescription']);
    Route::post('/suggest-tags', [App\Http\Controllers\Api\AIChatController::class, 'suggestTags']);
    Route::post('/analyze-demand', [App\Http\Controllers\Api\AIChatController::class, 'analyzeDemand']);
    Route::post('/find-matching-properties', [App\Http\Controllers\Api\AIChatController::class, 'findMatchingProperties']);
});

// Advanced AI Routes moved to api.php

// Test routes removed - use production APIs

// FRONTEND DYNAMIC FORM ROUTES - Use /api/v1/* endpoints for form handling

// ===== AI STATUS API - MOVED TO routes/api/v1/ai.php =====
// Route: GET /api/v1/environment/status or similar
// This endpoint has been centralized in the API system
