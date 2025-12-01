<?php

use App\Http\Controllers\Admin\AnalyticsDashboardController;
use App\Http\Controllers\Admin\CRMController;
use App\Http\Controllers\Admin\KisiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// (Legacy) Wizard test routes kaldırıldı

Route::middleware(['web'])->prefix('admin')->name('admin.')->group(function () {
    // AI Command Center Dashboard (Yukarı taşındı - öncelikli)
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AI\AdvancedAIController::class, 'performanceDashboard'])
            ->name('dashboard')
            ->middleware('auth'); // Auth middleware eklendi
        Route::get('/health', [\App\Http\Controllers\AI\AdvancedAIController::class, 'systemHealth'])
            ->name('health');
        Route::get('/statistics', [\App\Http\Controllers\AI\AdvancedAIController::class, 'usageStatistics'])
            ->name('statistics');
    });

    // Main dashboard route - Context7: Controller kullanımı (kod tekrarı önlendi)
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    // Context7: admin.dashboard.index alias (birçok view'da kullanılıyor)
    Route::get('/dashboard/index', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard.index');

    // Dashboard aliases for backward compatibility
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('index');

    // Component Library Demo
    Route::get('/components-demo', function () {
        return view('admin.components-demo');
    })->name('components.demo');

    // Architecture route
    // Analytics routes
    Route::prefix('/analytics')->name('analytics.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('index');
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AnalyticsController::class, 'dashboard'])->name('dashboard');

        // Context7 Analytics Dashboard Routes
        Route::get('/context7', [AnalyticsDashboardController::class, 'index'])->name('context7');
        Route::get('/context7/data', [AnalyticsDashboardController::class, 'getData'])->name('context7.data');
        Route::get('/context7/compliance-trends', [AnalyticsDashboardController::class, 'complianceTrends'])->name('context7.compliance');
        Route::get('/context7/velocity-chart', [AnalyticsDashboardController::class, 'velocityChart'])->name('context7.velocity');
        Route::post('/context7/recalculate-health', [AnalyticsDashboardController::class, 'recalculateHealth'])->name('context7.recalculate');

        // Impact Metrics Routes for Real-Time Dashboard Widget
        Route::get('/impact-metrics', [\App\Http\Controllers\Admin\SimpleImpactController::class, 'getImpactMetrics'])->name('impact.metrics');
        Route::post('/ai/generate-ideas', [\App\Http\Controllers\Admin\SimpleImpactController::class, 'generateIdeas'])->name('ai.generate-ideas');
        Route::post('/ai/code-review', [\App\Http\Controllers\Admin\SimpleImpactController::class, 'runCodeReview'])->name('ai.code-review');

        Route::get('/data', [\App\Http\Controllers\Admin\AnalyticsController::class, 'data'])->name('data');
        Route::get('/export', [\App\Http\Controllers\Admin\AnalyticsController::class, 'export'])->name('export');
        Route::get('/create', [\App\Http\Controllers\Admin\AnalyticsController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AnalyticsController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\AnalyticsController::class, 'show'])->whereNumber('id')->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\AnalyticsController::class, 'edit'])->whereNumber('id')->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\AnalyticsController::class, 'update'])->whereNumber('id')->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\AnalyticsController::class, 'destroy'])->whereNumber('id')->name('destroy');
    });

    // Form Validation routes
    Route::prefix('/validate')->name('validate.')->group(function () {
        Route::post('/field', [\App\Http\Controllers\Admin\FormValidationController::class, 'validateField'])->name('field');
        Route::post('/auto-save', [\App\Http\Controllers\Admin\FormValidationController::class, 'autoSave'])->name('auto-save');
        Route::get('/auto-saved-data', [\App\Http\Controllers\Admin\FormValidationController::class, 'getAutoSavedData'])->name('auto-saved-data');
        Route::delete('/auto-saved-data', [\App\Http\Controllers\Admin\FormValidationController::class, 'clearAutoSavedData'])->name('clear-auto-saved-data');
    });

    // Category routes
    Route::prefix('/ilan-kategorileri')->name('ilan-kategorileri.')->group(function () {
        // Inline kategori güncelleme (AJAX, Context7)
        Route::post('/{id}/inline-update', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'inlineUpdate'])->name('inline-update');
        Route::get('/{id}/alt-kategoriler', [\App\Http\Controllers\Admin\FeatureController::class, 'getAltKategoriler'])->name('alt-kategoriler-features');
    });

    // Publication type routes
    Route::prefix('/yayin-tipleri')->name('yayin-tipleri.')->group(function () {
        Route::get('/{id}/tipler', [\App\Http\Controllers\Admin\FeatureController::class, 'getYayinTipleri'])->name('tipler');
    });

    // ✅ Yayın Tipi Yöneticisi artık PropertyTypeManagerController kullanıyor
    // Eski route kaldırıldı: /yayin-tipi-yoneticisi → /property-type-manager
    // Context7 Compliance: Tek route kullanımı (2025-11-11)

    // Yalihan Bekçi Monitoring Dashboard
    Route::prefix('/yalihan-bekci')->name('yalihan-bekci.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\YalihanBekciController::class, 'index'])->name('index');
        Route::get('/live-data', [\App\Http\Controllers\Admin\YalihanBekciController::class, 'liveData'])->name('live-data');
        Route::post('/run-check', [\App\Http\Controllers\Admin\YalihanBekciController::class, 'runCheck'])->name('run-check');
    });

    // Performance routes

    // Alias routes for backwards compatibility
    Route::get('/', function () {
        return redirect()->route('admin.dashboard.index');
    })->name('dashboard.root');

    // Site/Apartman Management Routes
    Route::resource('/site-apartman', \App\Http\Controllers\Admin\SiteApartmanController::class);
    Route::get('/site-apartman/{id}/ilceler', [\App\Http\Controllers\Admin\SiteApartmanController::class, 'getIlceler'])->name('site-apartman.ilceler');
    Route::get('/site-apartman/{id}/mahalleler', [\App\Http\Controllers\Admin\SiteApartmanController::class, 'getMahalleler'])->name('site-apartman.mahalleler');
    Route::get('/site-apartman/search', [\App\Http\Controllers\Admin\SiteApartmanController::class, 'search'])->name('site-apartman.search');

    // Anahtar Yönetimi Routes
    Route::resource('/anahtar-yonetimi', \App\Http\Controllers\Admin\AnahtarYonetimiController::class);
    Route::patch('/anahtar-yonetimi/{id}/status', [\App\Http\Controllers\Admin\AnahtarYonetimiController::class, 'updateStatus'])->name('anahtar-yonetimi.status');
    Route::post('/anahtar-yonetimi/{id}/deliver', [\App\Http\Controllers\Admin\AnahtarYonetimiController::class, 'deliver'])->name('anahtar-yonetimi.deliver');

    // Blog routes
    Route::prefix('/blog')->name('blog.')->group(function () {
        // Blog Dashboard
        Route::get('/', [\App\Http\Controllers\Admin\BlogController::class, 'index'])->name('index');

        // Blog Posts
        Route::resource('/posts', \App\Http\Controllers\Admin\BlogController::class)->parameters(['posts' => 'post']);

        // Blog Post Actions
        Route::post('/posts/{post}/publish', [\App\Http\Controllers\Admin\BlogController::class, 'publish'])->name('posts.publish');
        Route::post('/posts/{post}/unpublish', [\App\Http\Controllers\Admin\BlogController::class, 'unpublish'])->name('posts.unpublish');
        Route::post('/posts/{post}/feature', [\App\Http\Controllers\Admin\BlogController::class, 'feature'])->name('posts.feature');
        Route::post('/posts/{post}/stick', [\App\Http\Controllers\Admin\BlogController::class, 'stick'])->name('posts.stick');

        // Blog Categories
        Route::prefix('/categories')->name('categories.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\BlogController::class, 'categories'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\BlogController::class, 'createCategory'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\BlogController::class, 'storeCategory'])->name('store');
            Route::get('/{category}/edit', [\App\Http\Controllers\Admin\BlogController::class, 'editCategory'])->name('edit');
            Route::put('/{category}', [\App\Http\Controllers\Admin\BlogController::class, 'updateCategory'])->name('update');
            Route::delete('/{category}', [\App\Http\Controllers\Admin\BlogController::class, 'destroyCategory'])->name('destroy');
            Route::post('/{category}/toggle', [\App\Http\Controllers\Admin\BlogController::class, 'toggleCategory'])->name('toggle');
        });

        // Blog Tags
        Route::prefix('/tags')->name('tags.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\BlogController::class, 'tags'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\BlogController::class, 'createTag'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\BlogController::class, 'storeTag'])->name('store');
            Route::get('/{tag}/edit', [\App\Http\Controllers\Admin\BlogController::class, 'editTag'])->name('edit');
            Route::put('/{tag}', [\App\Http\Controllers\Admin\BlogController::class, 'updateTag'])->name('update');
            Route::delete('/{tag}', [\App\Http\Controllers\Admin\BlogController::class, 'destroyTag'])->name('destroy');
            Route::patch('/{tag}/toggle', [\App\Http\Controllers\Admin\BlogController::class, 'toggleTag'])->name('toggle');
        });

        // Blog Comments
        Route::prefix('/comments')->name('comments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\BlogController::class, 'comments'])->name('index');
            Route::post('/{comment}/approve', [\App\Http\Controllers\Admin\BlogController::class, 'approveComment'])->name('approve');
            Route::post('/{comment}/reject', [\App\Http\Controllers\Admin\BlogController::class, 'rejectComment'])->name('reject');
            Route::post('/{comment}/spam', [\App\Http\Controllers\Admin\BlogController::class, 'markCommentAsSpam'])->name('spam');
        });

        // Blog Analytics
        Route::get('/analytics', [\App\Http\Controllers\Admin\BlogController::class, 'analytics'])->name('analytics');
        Route::post('/clear-cache', [\App\Http\Controllers\Admin\BlogController::class, 'clearSidebarCache'])->name('clear-cache');
    });

    // DEBUG: Test auth status
    Route::get('/auth-test', function () {
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::check() ? Auth::user()->email : null,
            'session_id' => session()->getId(),
            'session_keys' => array_keys(session()->all()),
        ]);
    })->name('auth.test');

    // Test route - menü test sayfası
    Route::get('/test-menu', function () {
        return view('admin.test-menu');
    })->name('test-menu');

    // Legacy/alias routes for Context7 menu compatibility
    // AI Redirect Routes - REMOVED (Controller deleted, functionality moved to AI Settings)

    Route::get('/analytics-redirect', function () {
        return redirect()->route('admin.analytics.index');
    })->name('analytics.redirect');

    // Turkish alias for reports
    Route::get('/raporlar', function () {
        return redirect()->route('admin.reports.index');
    })->name('raporlar.redirect');

    // CRM legacy aliases
    Route::get('/customers', function () {
        return redirect()->route('admin.crm.customers.index');
    })->name('customers.redirect');

    // "Müşteriler" Turkish aliases → Kişiler CRUD
    // Context7: GET istekleri için redirect kullanılabilir, POST/PUT/DELETE için route forwarding yapılmalı
    Route::get('/musteriler', function () {
        return redirect()->route('admin.kisiler.index');
    })->name('musteriler.index');
    Route::get('/musteriler/create', function () {
        return redirect()->route('admin.kisiler.create');
    })->name('musteriler.create');
    Route::get('/musteriler/{id}', function ($id) {
        return redirect()->route('admin.kisiler.show', ['kisi' => $id]);
    })->whereNumber('id')->name('musteriler.show');
    Route::get('/musteriler/{id}/edit', function ($id) {
        return redirect()->route('admin.kisiler.edit', ['kisi' => $id]);
    })->whereNumber('id')->name('musteriler.edit');

    // Context7: POST/PUT/DELETE için route forwarding (redirect yerine)
    Route::post('/musteriler', [KisiController::class, 'store'])->name('musteriler.store');
    Route::put('/musteriler/{id}', [KisiController::class, 'update'])->whereNumber('id')->name('musteriler.update');
    Route::delete('/musteriler/{id}', [KisiController::class, 'destroy'])->whereNumber('id')->name('musteriler.destroy');

    // Sales (Satışlar) redirects
    Route::get('/satislar/create', function () {
        return redirect()->route('admin.analitik.raporlar.satis-raporu');
    })->name('satislar.create');

    // My Listings
    Route::prefix('/my-listings')->name('my-listings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MyListingsController::class, 'index'])->name('index');
        Route::post('/search', [\App\Http\Controllers\Admin\MyListingsController::class, 'search'])->name('search');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\MyListingsController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/stats', [\App\Http\Controllers\Admin\MyListingsController::class, 'getStats'])->name('stats');
        Route::get('/export', [\App\Http\Controllers\Admin\MyListingsController::class, 'export'])->name('export');
    });

    // İlan Yönetimi
    // Context7 Smart İlan System Routes
    Route::resource('/ilanlar', \App\Http\Controllers\Admin\IlanController::class)->parameters(['ilanlar' => 'ilan']);

    // Bulk Actions (Context7: Toplu işlemler)
    Route::post('/ilanlar/bulk-action', [\App\Http\Controllers\Admin\IlanController::class, 'bulkAction'])
        ->name('ilanlar.bulk-action');

    // Test route for category cascading
    // DEPRECATED: Test route removed (kategori sistemi production'da test edildi)
    // Route::get('/ilanlar-test', [\App\Http\Controllers\Admin\IlanController::class, 'testCategories'])->name('ilanlar.test-categories');

    // Draft Save Route (Context7 uyumlu)
    Route::post('/ilanlar/draft', function () {
        return response()->json([
            'success' => true,
            'message' => 'Draft saved successfully (Context7 uyumlu)',
            'data' => [
                'id' => rand(1000, 9999),
                'status' => 'draft',
                'created_at' => now()->toISOString(),
            ],
        ]);
    })->name('ilanlar.draft');

    Route::prefix('/changelog')->name('changelog.')->group(function () {
        Route::post('/', [\App\Http\Controllers\Admin\ChangelogController::class, 'store'])->name('store');
    });

    // Test route
    Route::get('/test-simple', function () {
        return 'Simple route works!';
    });
    // Smart İlan routes removed - Use standard resource routes instead

    // İlan ek routes
    Route::prefix('/ilanlar')->name('ilanlar.')->group(function () {
        // AJAX & API Routes
        Route::get('/search', [\App\Http\Controllers\Admin\IlanController::class, 'search'])->name('search');
        Route::get('/filter', [\App\Http\Controllers\Admin\IlanController::class, 'filter'])->name('filter');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\IlanController::class, 'bulkAction'])->name('bulk-action');
        Route::post('/{ilan}/toggle-status', [\App\Http\Controllers\Admin\IlanController::class, 'toggleStatus'])->name('toggle-status');

        // Segment-based workflow routes
        Route::prefix('/segments')->name('segments.')->group(function () {
            // Yeni ilan oluşturma
            Route::get('/create', [\App\Http\Controllers\Admin\IlanSegmentController::class, 'create'])->name('create');
            Route::get('/create/{segment}', [\App\Http\Controllers\Admin\IlanSegmentController::class, 'showCreate'])->name('create.segment');
            Route::post('/create/{segment}', [\App\Http\Controllers\Admin\IlanSegmentController::class, 'storeCreate'])->name('store.create');

            // Mevcut ilan düzenleme
            Route::get('/{ilan}', [\App\Http\Controllers\Admin\IlanSegmentController::class, 'show'])->name('show');
            Route::get('/{ilan}/{segment}', [\App\Http\Controllers\Admin\IlanSegmentController::class, 'showEdit'])->name('show.segment');
            Route::post('/{ilan}/{segment}', [\App\Http\Controllers\Admin\IlanSegmentController::class, 'store'])->name('store');
        });

        // İlan Create API Routes (Context7 Standard)
        Route::prefix('/api')->name('api.')->group(function () {
            Route::get('/features/category/{categoryId}', [\App\Http\Controllers\Api\FeaturesController::class, 'getFeaturesByCategory'])->name('features.category');
            Route::get('/categories/publication-types/{categoryId}', [\App\Http\Controllers\Api\CategoryController::class, 'getPublicationTypes'])->name('categories.publication-types');
            Route::get('/categories/{parentId}/subcategories', [\App\Http\Controllers\Api\CategoryController::class, 'getSubcategories'])->name('categories.subcategories');
        });

        // AI Routes
        Route::prefix('/ai')->name('ai.')->group(function () {
            Route::post('/bulk-analyze', [\App\Http\Controllers\Admin\AI\IlanAIController::class, 'bulkAnalyze'])->name('bulk-analyze');
            Route::post('/suggest', [\App\Http\Controllers\Admin\AI\IlanAIController::class, 'suggest'])->name('suggest');
            Route::get('/health', [\App\Http\Controllers\Admin\AI\IlanAIController::class, 'health'])->name('health');
        });

        // Export Routes
        Route::get('/export/excel', [\App\Http\Controllers\Admin\IlanController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [\App\Http\Controllers\Admin\IlanController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export', [\App\Http\Controllers\Admin\IlanController::class, 'exportExcel'])->name('export');

        // Legacy Routes
        Route::post('/save-draft', [\App\Http\Controllers\Admin\IlanController::class, 'saveDraft'])->name('save-draft');
        Route::post('/auto-save', [\App\Http\Controllers\Admin\IlanController::class, 'autoSave'])->name('auto-save');
        Route::post('/{ilan}/update-status', [\App\Http\Controllers\Admin\IlanController::class, 'updateStatus'])->name('update-status');
        Route::post('/bulk-update', [\App\Http\Controllers\Admin\IlanController::class, 'bulkUpdate'])->name('bulk-update');
        Route::post('/bulk-delete', [\App\Http\Controllers\Admin\IlanController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/live-search', [\App\Http\Controllers\Admin\IlanController::class, 'liveSearch'])->name('live-search');
        Route::post('/generate-ai-title', [\App\Http\Controllers\Admin\IlanController::class, 'generateAiTitle'])->name('generate-ai-title');
        Route::post('/generate-ai-description', [\App\Http\Controllers\Admin\IlanController::class, 'generateAiDescription'])->name('generate-ai-description');
        Route::get('/{ilan}/price-history', [\App\Http\Controllers\Admin\IlanController::class, 'priceHistoryApi'])->name('price-history');
        Route::post('/{ilan}/refresh-rate', [\App\Http\Controllers\Admin\IlanController::class, 'refreshRate'])->name('refresh-rate');
        Route::post('/{ilan}/upload-photos', [\App\Http\Controllers\Admin\IlanController::class, 'uploadPhotos'])->name('upload-photos');
        Route::delete('/{ilan}/photos/{photo}', [\App\Http\Controllers\Admin\IlanController::class, 'deletePhoto'])->name('delete-photo');
        // Context7: forbidden pattern fixed - using sequence instead
        Route::post('/{ilan}/update-photo-sequence', [\App\Http\Controllers\Admin\IlanController::class, 'updatePhotoSequence'])->name('update-photo-sequence');
        Route::post('/{ilan}/duplicate', [\App\Http\Controllers\Admin\IlanController::class, 'duplicate'])->name('duplicate');
        Route::get('/ilanlarim', [\App\Http\Controllers\Admin\IlanController::class, 'ilanlarim'])->name('ilanlarim');
    });

    // Kullanıcılar - Full Resource Controller
    Route::resource('/kullanicilar', \App\Http\Controllers\Admin\UserController::class, [
        'except' => ['show'],
    ]);

    // CRM Yönetimi (Redirect to proper CRM dashboard)
    Route::get('/crm-legacy', function () {
        return redirect()->route('admin.crm.dashboard');
    })->name('crm.legacy');

    // Bildirimler
    Route::get('/notifications', function () {
        return view('admin.notifications.index');
    })->name('notifications.index');

    // AI Sistemi - REMOVED (consolidated into ai-settings)

    // Takım Yönetimi
    Route::get('/takim-yonetimi', function () {
        return redirect()->route('admin.takim-yonetimi.takim.index');
    })->name('takim-yonetimi.index');

    // Takım Yönetimi - Kısa yol (Legacy support)
    Route::get('/takim-yonetimi/takim', function () {
        return redirect()->route('admin.takim-yonetimi.takim.index');
    })->name('takim-yonetimi.takim.redirect');

    // Analytics
    Route::get('/analytics', function () {
        $totalIlanlar = \App\Models\Ilan::count();
        $totalKategoriler = \App\Models\IlanKategori::count();
        $totalKullanicilar = \App\Models\User::count();
        $newIlanlarThisMonth = \App\Models\Ilan::whereMonth('created_at', now()->month)->count();

        // Son 7 günün ilan trendi
        $ilanTrendi = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = \App\Models\Ilan::whereDate('created_at', $date->toDateString())->count();
            $ilanTrendi[] = [
                'date' => $date->format('d.m'),
                'count' => $count,
            ];
        }

        // Kategori dağılımı (Top 5)
        $kategoriDagilimi = \App\Models\IlanKategori::withCount('ilanlar')
            ->orderBy('ilanlar_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($kategori) {
                return [
                    'name' => $kategori->name,
                    'count' => $kategori->ilanlar_count,
                ];
            });

        // En popüler kategori
        $topKategori = \App\Models\IlanKategori::withCount('ilanlar')
            ->orderBy('ilanlar_count', 'desc')
            ->first();

        $metrics = [
            'total_ilanlar' => $totalIlanlar,
            'total_kategoriler' => $totalKategoriler,
            'total_kullanicilar' => $totalKullanicilar,
            'new_ilanlar_this_month' => $newIlanlarThisMonth,
            'ilan_trendi' => $ilanTrendi,
            'kategori_dagilimi' => $kategoriDagilimi,
            'top_kategori' => $topKategori ? $topKategori->name : 'Veri yok',
            'top_kategori_count' => $topKategori ? $topKategori->ilanlar_count : 0,
            'last_updated' => now()->format('d.m.Y H:i'),
        ];

        return view('admin.analytics.index', compact('metrics'));
    })->name('analytics.index');

    // Telegram Bot
    Route::get('/telegram', function () {
        return view('admin.telegram.index');
    })->name('telegram.index');

    // Ayarlar

    // İlan Kategorileri
    Route::prefix('/ilan-kategorileri')->name('ilan-kategorileri.')->group(function () {
        // AJAX endpoints - MUST BE BEFORE {kategori} wildcard routes!
        Route::get('/alt-kategoriler', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'getAltKategoriler'])->name('alt-kategoriler');
        Route::get('/yayin-tipleri', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'getYayinTipleri'])->name('yayin-tipleri');
        Route::get('/{id}/ozellikler', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'getOzellikler'])->name('ozellikler'); // ✅ NEW: Kategoriye özel özellikler
        Route::get('/stats', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'stats'])->name('stats');
        Route::get('/export', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'export'])->name('export');
        Route::get('/create', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'create'])->name('create');

        // Resource routes
        Route::get('/', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'store'])->name('store');
        Route::get('/{kategori}', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'show'])->whereNumber('kategori')->name('show');
        Route::get('/slug/{slug}', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'show'])->name('show.slug');
        Route::get('/{kategori}/edit', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'edit'])->name('edit');
        Route::put('/{kategori}', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'update'])->name('update');
        Route::delete('/{kategori}', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'bulkAction'])->name('bulk-action');
        Route::post('/reorder', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'updateOrder'])->name('reorder');

        // Context7 Dynamic Form Fields API
        Route::get('/dynamic-fields/{propertyType}', [\App\Http\Controllers\Admin\IlanController::class, 'getDynamicFields'])->name('dynamic-fields');
        Route::post('/ai-property-suggestions', [\App\Http\Controllers\Admin\IlanController::class, 'getAIPropertySuggestions'])->name('ai-property-suggestions');

        // Context7 Property Type AI Description API
        Route::post('/ai-property-type-description', [\App\Http\Controllers\Admin\IlanController::class, 'generatePropertyTypeAiDescription'])->name('ai-property-type-description');

        // Context7 API Testing Endpoints
        Route::get('/api/health', [\App\Http\Controllers\Admin\IlanController::class, 'apiHealthCheck'])->name('api-health');
        Route::get('/api/stats', [\App\Http\Controllers\Admin\IlanController::class, 'apiStats'])->name('api-stats');
        Route::get('/api/performance', [\App\Http\Controllers\Admin\IlanController::class, 'apiPerformance'])->name('api-performance');

        // Context7 Advanced AI Features API
        Route::post('/ai-multi-language-description', [\App\Http\Controllers\Admin\IlanController::class, 'generateMultiLanguageAiDescription'])->name('ai-multi-language-description');
        Route::post('/ai-image-based-description', [\App\Http\Controllers\Admin\IlanController::class, 'generateImageBasedAiDescription'])->name('ai-image-based-description');
        Route::post('/ai-location-based-suggestions', [\App\Http\Controllers\Admin\IlanController::class, 'getLocationBasedAiSuggestions'])->name('ai-location-based-suggestions');
        Route::post('/ai-price-optimization', [\App\Http\Controllers\Admin\IlanController::class, 'optimizePriceWithAi'])->name('ai-price-optimization');

        // Features API (Context7)
        Route::prefix('/api/features')->name('api.features.')->group(function () {
            Route::get('/category/{categoryId}', [\App\Http\Controllers\Admin\IlanController::class, 'getFeaturesByCategory'])->name('by-category');
        });
    });

    // AI Core System Test Routes
    Route::prefix('ai-core-test')->name('ai-core-test.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AICoreTestController::class, 'index'])->name('index');
        Route::post('/test-ai', [\App\Http\Controllers\Admin\AICoreTestController::class, 'testAI'])->name('test-ai');
        Route::post('/teach-ai', [\App\Http\Controllers\Admin\AICoreTestController::class, 'teachAI'])->name('teach-ai');
        Route::post('/test-storage', [\App\Http\Controllers\Admin\AICoreTestController::class, 'testStorage'])->name('test-storage');
        Route::get('/system-status', [\App\Http\Controllers\Admin\AICoreTestController::class, 'getSystemStatus'])->name('system-status');
    });

    // AI Destekli Kategori Yönetimi Routes
    Route::prefix('ai-category')->name('ai-category.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AICategoryController::class, 'index'])->name('index');
        Route::get('/test', [\App\Http\Controllers\Admin\AICategoryController::class, 'test'])->name('test');
        Route::post('/analyze', [\App\Http\Controllers\Admin\AICategoryController::class, 'analyzeCategory'])->name('analyze');
        Route::post('/suggestions', [\App\Http\Controllers\Admin\AICategoryController::class, 'getCategorySuggestions'])->name('suggestions');
        Route::post('/hibrit-siralama', [\App\Http\Controllers\Admin\AICategoryController::class, 'generateHibritSiralama'])->name('hibrit-siralama');
        Route::post('/smart-form', [\App\Http\Controllers\Admin\AICategoryController::class, 'generateSmartForm'])->name('smart-form');
        Route::post('/matrix', [\App\Http\Controllers\Admin\AICategoryController::class, 'manageMatrix'])->name('matrix');
        Route::post('/teach', [\App\Http\Controllers\Admin\AICategoryController::class, 'teachAICategory'])->name('teach');
        Route::get('/analyze-all', [\App\Http\Controllers\Admin\AICategoryController::class, 'analyzeAllCategories'])->name('analyze-all');
        Route::post('/update-ai-success', [\App\Http\Controllers\Admin\AICategoryController::class, 'updateAISuccess'])->name('update-ai-success');
    });

    // 🎯 Property Type Manager (Tek Sayfada Kategori, Yayın Tipi ve İlişki Yönetimi)
    Route::prefix('/property-type-manager')->name('property_types.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'index'])->name('index');
        Route::get('/{kategoriId}', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'show'])->name('show');

        // AJAX Toggle Endpoints
        Route::post('/{kategoriId}/toggle-yayin-tipi', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'toggleYayinTipi'])->name('toggle_yayin_tipi');
        Route::post('/{kategoriId}/bulk-save', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'bulkSave'])->name('bulk_save');
        // Context7 Fix: Duplicate route name removed (line 491 has same endpoint)
        Route::delete('/{kategoriId}/yayin-tipi/{yayinTipiId}', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'destroyYayinTipi'])->name('destroy_yayin_tipi');
        Route::delete('/{kategoriId}/alt-kategori/{altKategoriId}', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'destroyAltKategori'])->name('destroy_alt_kategori');

        // 🔗 Field Dependencies Management (Yeni - Alan İlişkileri Yönetimi)
        Route::get('/{kategoriId}/field-dependencies', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'fieldDependenciesIndex'])->name('field_dependencies');
        Route::post('/{kategoriId}/field-dependencies', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'storeFieldDependency'])->name('field_dependencies.store');
        Route::put('/{kategoriId}/field-dependencies/{fieldId}', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'updateFieldDependency'])->name('field_dependencies.update');
        Route::delete('/{kategoriId}/field-dependencies/{fieldId}', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'destroyFieldDependency'])->name('field_dependencies.destroy');

        // AJAX Endpoints
        Route::post('/toggle-field-dependency', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'toggleFieldDependency'])->name('toggle_field_dependency');
        // Context7: forbidden pattern fixed - using sequence instead
        Route::post('/update-field-sequence', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'updateFieldSequence'])->name('update_field_sequence');
        Route::post('/toggle-feature', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'toggleFeature'])->name('toggle_feature');
        // 🆕 Yayın tipi oluşturma
        Route::post('/{kategoriId}/yayin-tipi', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'createYayinTipi'])->name('create_yayin_tipi');

        // ✅ Context7: Tüm kategoriler için eksik yayın tiplerini otomatik ekle
        Route::post('/ensure-all-yayin-tipleri', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'ensureAllYayinTipleri'])->name('ensure_all_yayin_tipleri');

        // 🎯 POLYMORPHIC FEATURE ASSIGNMENT ENDPOINTS
        Route::post('/property-type/{propertyTypeId}/assign-feature', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'assignFeature'])->name('assign_feature');
        Route::delete('/property-type/{propertyTypeId}/unassign-feature', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'unassignFeature'])->name('unassign_feature');
        Route::post('/property-type/{propertyTypeId}/sync-features', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'syncFeatures'])->name('sync_features');
        Route::post('/toggle-feature-assignment', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'toggleFeatureAssignment'])->name('toggle_feature_assignment');
        Route::put('/feature-assignment/{assignmentId}', [\App\Http\Controllers\Admin\PropertyTypeManagerController::class, 'updateFeatureAssignment'])->name('update_feature_assignment');
    });

    // Kişi Yönetimi (Context7 Uyumlu)
    Route::prefix('/kisiler')->name('kisiler.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\KisiController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\KisiController::class, 'create'])->name('create');
        Route::get('/create-context7', function () {
            return view('admin.kisiler.create-context7');
        })->name('create-context7');
        Route::post('/', [\App\Http\Controllers\Admin\KisiController::class, 'store'])->name('store');
        Route::get('/search', [\App\Http\Controllers\Admin\KisiController::class, 'search'])->name('search');
        Route::post('/check-duplicate', [\App\Http\Controllers\Admin\KisiController::class, 'checkDuplicate'])->name('check-duplicate');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\KisiController::class, 'bulkAction'])->name('bulk-action');
        Route::post('/ai-analyze', [\App\Http\Controllers\Admin\KisiController::class, 'aiAnalyze'])->name('ai-analyze');
        Route::get('/takip', [\App\Http\Controllers\Admin\KisiController::class, 'takip'])->name('takip');
        Route::get('/{kisi}', [\App\Http\Controllers\Admin\KisiController::class, 'show'])->name('show');
        Route::get('/{kisi}/edit', [\App\Http\Controllers\Admin\KisiController::class, 'edit'])->name('edit');
        Route::put('/{kisi}', [\App\Http\Controllers\Admin\KisiController::class, 'update'])->name('update');
        Route::delete('/{kisi}', [\App\Http\Controllers\Admin\KisiController::class, 'destroy'])->name('destroy');
    });

    // 🗑️ Site Özellikleri - REMOVED (Now using Polymorphic Features System)
    // Old routes removed, now managed via: /admin/ozellikler/kategoriler
    // Site Özellikleri category_id = 5 in feature_categories table

    // Wikimapia Site/Apartman Sorgulama Paneli
    Route::prefix('/wikimapia-search')->name('wikimapia-search.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\WikimapiaSearchController::class, 'index'])->name('index');
        Route::post('/search', [\App\Http\Controllers\Admin\WikimapiaSearchController::class, 'search'])->name('search');
        Route::post('/search-places', [\App\Http\Controllers\Admin\WikimapiaSearchController::class, 'searchPlaces'])->name('search-places');
        Route::post('/nearby', [\App\Http\Controllers\Admin\WikimapiaSearchController::class, 'nearby'])->name('nearby');
        Route::get('/place/{id}', [\App\Http\Controllers\Admin\WikimapiaSearchController::class, 'getPlaceDetails'])->name('place-details');
        Route::post('/save-site', [\App\Http\Controllers\Admin\WikimapiaSearchController::class, 'saveSite'])->name('save-site');
        Route::get('/saved-sites', [\App\Http\Controllers\Admin\WikimapiaSearchController::class, 'getSavedSites'])->name('saved-sites');

        // ✅ Context7: TurkiyeAPI entegrasyonu route'ları (harita sistemi için)
        Route::get('/location-data', [\App\Http\Controllers\Admin\WikimapiaSearchController::class, 'getLocationData'])->name('location-data');
        Route::post('/location-from-coordinates', [\App\Http\Controllers\Admin\WikimapiaSearchController::class, 'getLocationFromCoordinates'])->name('location-from-coordinates');
    });

    // Danışman Yönetimi (Standardize edildi - users tablosu kullanılıyor)
    Route::prefix('/danisman')->name('danisman.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DanismanController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\DanismanController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\DanismanController::class, 'store'])->name('store');
        Route::get('/{danisman}', [\App\Http\Controllers\Admin\DanismanController::class, 'show'])->name('show');
        Route::get('/{danisman}/edit', [\App\Http\Controllers\Admin\DanismanController::class, 'edit'])->name('edit');
        Route::put('/{danisman}', [\App\Http\Controllers\Admin\DanismanController::class, 'update'])->name('update');
        Route::delete('/{danisman}', [\App\Http\Controllers\Admin\DanismanController::class, 'destroy'])->name('destroy');

        // AJAX işlemleri
        Route::get('/search', [\App\Http\Controllers\Admin\DanismanController::class, 'search'])->name('search');
        Route::post('/toggle-status/{danisman}', [\App\Http\Controllers\Admin\DanismanController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/update-online-status/{danisman}', [\App\Http\Controllers\Admin\DanismanController::class, 'updateOnlineStatus'])->name('update-online-status');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\DanismanController::class, 'bulkAction'])->name('bulk-action');

        // Raporlar
        Route::get('/performance-report', [\App\Http\Controllers\Admin\DanismanController::class, 'performanceReport'])->name('performance-report');
    });

    // Talepler Yönetimi
    Route::prefix('talepler')->name('talepler.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TalepController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\TalepController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\TalepController::class, 'store'])->name('store');
        Route::get('/{talep}', [\App\Http\Controllers\Admin\TalepController::class, 'show'])->name('show');
        Route::get('/{talep}/edit', [\App\Http\Controllers\Admin\TalepController::class, 'edit'])->name('edit');
        Route::put('/{talep}', [\App\Http\Controllers\Admin\TalepController::class, 'update'])->name('update');
        Route::delete('/{talep}', [\App\Http\Controllers\Admin\TalepController::class, 'destroy'])->name('destroy');
        Route::get('/{talep}/eslesen', [\App\Http\Controllers\Admin\TalepController::class, 'eslesen'])->name('eslesen');
        Route::get('/search', [\App\Http\Controllers\Admin\TalepController::class, 'search'])->name('search');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\TalepController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/test', [\App\Http\Controllers\Admin\TalepController::class, 'test'])->name('test');
        Route::get('/debug', [\App\Http\Controllers\Admin\TalepController::class, 'debug'])->name('debug');
    });

    // Eşleştirme Sistemi
    Route::prefix('/eslesmeler')->name('eslesmeler.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EslesmeController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\EslesmeController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\EslesmeController::class, 'store'])->name('store');
        Route::get('/{eslesme}', [\App\Http\Controllers\Admin\EslesmeController::class, 'show'])->name('show');
        Route::get('/{eslesme}/edit', [\App\Http\Controllers\Admin\EslesmeController::class, 'edit'])->name('edit');
        Route::put('/{eslesme}', [\App\Http\Controllers\Admin\EslesmeController::class, 'update'])->name('update');
        Route::delete('/{eslesme}', [\App\Http\Controllers\Admin\EslesmeController::class, 'destroy'])->name('destroy');

        // Özel eşleştirme işlemleri
        Route::get('/auto-match', [\App\Http\Controllers\Admin\EslesmeController::class, 'autoMatch'])->name('auto-match');
        Route::post('/bulk-create', [\App\Http\Controllers\Admin\EslesmeController::class, 'bulkCreate'])->name('bulk-create');

        // API endpoints for form data
        Route::prefix('/api')->name('api.')->group(function () {
            Route::get('/kisiler', [\App\Http\Controllers\Admin\EslesmeController::class, 'getKisiler'])->name('kisiler');
            Route::get('/danismanlar', [\App\Http\Controllers\Admin\EslesmeController::class, 'getDanismanlar'])->name('danismanlar');
            Route::get('/talepler', [\App\Http\Controllers\Admin\EslesmeController::class, 'getTalepler'])->name('talepler');
            Route::post('/ai/eslesme-onerileri', [\App\Http\Controllers\Admin\EslesmeController::class, 'getAIEslesmeOnerileri'])->name('ai.eslesme-onerileri');
        });
    });

    // Eşleştirmeler kısa yolu (yeni sisteme yönlendir)
    Route::get('/eslesme', function () {
        return redirect()->route('admin.eslesmeler.index');
    })->name('eslesme.index');

    // Özellikler (Admin UI) - /admin/ozellikler - Clean version
    Route::prefix('/ozellikler')->name('ozellikler.')->group(function () {
        // Ana özellik yönetimi - OzellikController kullan (ozellikler tablosu için)
        Route::get('/', [\App\Http\Controllers\Admin\OzellikController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\OzellikController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\OzellikController::class, 'store'])->name('store');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\OzellikController::class, 'bulkAction'])->name('bulk-action'); // ✅ Bulk Actions
        Route::get('/{ozellik}/edit', [\App\Http\Controllers\Admin\OzellikController::class, 'edit'])->name('edit');
        Route::put('/{ozellik}', [\App\Http\Controllers\Admin\OzellikController::class, 'update'])->name('update');
        Route::delete('/{ozellik}', [\App\Http\Controllers\Admin\OzellikController::class, 'destroy'])->name('destroy');

        // Özellik kategorileri
        Route::prefix('/kategoriler')->name('kategoriler.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'store'])->name('store');
            Route::get('/kategorisiz-ozellikler', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'kategorisizOzellikler'])->name('kategorisiz');
            Route::post('/reorder', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'reorder'])
                ->middleware('throttle:30,1')
                ->name('reorder');
            Route::get('/slug/check', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'checkSlug'])->name('slug.check');
            Route::post('/bulk-toggle-status', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'bulkToggleStatus'])->name('bulk-toggle-status');
            Route::post('/bulk-delete', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'bulkDelete'])->name('bulk-delete');
            Route::get('/stats', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'stats'])->name('stats');

            // ✅ FIX: Specific routes BEFORE wildcard routes
            Route::get('/{id}/ozellikler', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'ozellikler'])->whereNumber('id')->name('ozellikler');
            Route::get('/{id}/edit', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'edit'])->whereNumber('id')->name('edit');
            Route::patch('/{id}/quick-update', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'quickUpdate'])->whereNumber('id')->name('quick-update');
            Route::post('/{id}/duplicate', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'duplicate'])->whereNumber('id')->name('duplicate');
            Route::patch('/{kategori}/toggle-status', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'toggleStatus'])
                ->middleware('throttle:60,1')
                ->name('toggle');

            // ✅ FIX: Wildcard routes LAST
            Route::get('/{id}', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'show'])->whereNumber('id')->name('show');
            Route::put('/{id}', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'update'])->whereNumber('id')->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\OzellikKategoriController::class, 'destroy'])->whereNumber('id')->name('destroy');
        });

        // Özellikler (Features) - ozellikler tablosu için
        Route::prefix('/features')->name('features.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OzellikController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\OzellikController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\OzellikController::class, 'store'])->name('store');
            Route::get('/{ozellik}/edit', [\App\Http\Controllers\Admin\OzellikController::class, 'edit'])->name('edit');
            Route::put('/{ozellik}', [\App\Http\Controllers\Admin\OzellikController::class, 'update'])->name('update');
            Route::delete('/{ozellik}', [\App\Http\Controllers\Admin\OzellikController::class, 'destroy'])->name('destroy');
        });

        // Geriye dönük uyumluluk & kısa yol adları
        // Eski English 'categories.*' isimleri kullanan view'lar için redirect (URI /ozellikler/categories ...)
        Route::redirect('/categories', '/admin/ozellikler/kategoriler')->name('categories.index');
        Route::redirect('/categories/create', '/admin/ozellikler/kategoriler/create')->name('categories.create');
    });

    // Geriye dönük uyumluluk: Eski yol alias'ları (/admin/module/ozellikler/*)
    Route::get('/module/ozellikler-redirect', function () {
        return redirect()->route('admin.ozellikler.index');
    })->name('module.ozellikler.index');

    Route::get('/module/ozellikler/kategoriler', function () {
        return redirect()->route('admin.ozellikler.kategoriler.index');
    });
    Route::get('/module/ozellikler/kategoriler/kategorisiz-ozellikler', function () {
        return redirect()->route('admin.ozellikler.kategoriler.kategorisiz');
    });

    // Harita
    Route::get('/harita', [\App\Http\Controllers\Admin\MapController::class, 'index'])->name('map.index');

    // Smart Calculator
    Route::get('/smart-calculator', [\App\Http\Controllers\Admin\SmartCalculatorController::class, 'index'])->name('smart-calculator');

    // Raporlama Sistemi
    Route::prefix('/reports')->name('reports.')->group(function () {
        Route::get('/', function () {
            return view('admin.reports.index');
        })->name('index');
        // Context7: musteri → kisi (with backward compat alias)
        Route::get('/kisiler', [\App\Http\Controllers\Admin\ReportingController::class, 'musteriReports'])->name('kisiler');
        Route::get('/musteriler', function () {
            return redirect()->route('admin.reports.kisiler');
        })->name('musteriler'); // Backward compatibility alias
        Route::get('/performance', [\App\Http\Controllers\Admin\ReportingController::class, 'performanceReports'])->name('performance');
        Route::post('/export/excel', [\App\Http\Controllers\Admin\ReportingController::class, 'exportExcel'])->name('export.excel');
        Route::post('/export/pdf', [\App\Http\Controllers\Admin\ReportingController::class, 'exportPdf'])->name('export.pdf');
    });

    // Finans Yönetimi (Financial Management)
    // Context7 Standardı: C7-FINANS-ADMIN-2025-11-25
    Route::prefix('/finans')->name('finans.')->group(function () {
        // Health Check
        Route::get('/health', function () {
            return response()->json(['success' => true, 'module' => 'finans', 'ai_enabled' => true]);
        })->name('health');

        // Finansal İşlemler (Financial Transactions)
        Route::prefix('/islemler')->name('islemler.')->group(function () {
            Route::get('/', [\App\Modules\Finans\Controllers\FinansalIslemController::class, 'index'])->name('index');
            Route::get('/create', function () {
                return view('admin.finans.islemler.create');
            })->name('create');
            Route::post('/', [\App\Modules\Finans\Controllers\FinansalIslemController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Modules\Finans\Controllers\FinansalIslemController::class, 'show'])->name('show');
            Route::get('/{id}/edit', function ($id) {
                return view('admin.finans.islemler.edit', ['id' => $id]);
            })->name('edit');
            Route::put('/{id}', [\App\Modules\Finans\Controllers\FinansalIslemController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Modules\Finans\Controllers\FinansalIslemController::class, 'destroy'])->name('destroy');

            // Status Management
            Route::post('/{id}/approve', [\App\Modules\Finans\Controllers\FinansalIslemController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [\App\Modules\Finans\Controllers\FinansalIslemController::class, 'reject'])->name('reject');
            Route::post('/{id}/complete', [\App\Modules\Finans\Controllers\FinansalIslemController::class, 'complete'])->name('complete');

            // 🤖 AI-Powered Endpoints
            Route::post('/ai/analyze', [\App\Modules\Finans\Controllers\FinansalIslemController::class, 'aiAnalyze'])->name('ai.analyze');
            Route::post('/ai/predict', [\App\Modules\Finans\Controllers\FinansalIslemController::class, 'aiPredict'])->name('ai.predict');
            Route::get('/{id}/ai/invoice', [\App\Modules\Finans\Controllers\FinansalIslemController::class, 'aiSuggestInvoice'])->name('ai.invoice');
            Route::post('/ai/risk', [\App\Modules\Finans\Controllers\FinansalIslemController::class, 'aiAnalyzeRisk'])->name('ai.risk');
            Route::post('/ai/summary', [\App\Modules\Finans\Controllers\FinansalIslemController::class, 'aiGenerateSummary'])->name('ai.summary');
        });

        // Komisyonlar (Commissions)
        Route::prefix('/komisyonlar')->name('komisyonlar.')->group(function () {
            Route::get('/', [\App\Modules\Finans\Controllers\KomisyonController::class, 'index'])->name('index');
            Route::get('/create', function () {
                return view('admin.finans.komisyonlar.create');
            })->name('create');
            Route::post('/', [\App\Modules\Finans\Controllers\KomisyonController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Modules\Finans\Controllers\KomisyonController::class, 'show'])->name('show');
            Route::put('/{id}', [\App\Modules\Finans\Controllers\KomisyonController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Modules\Finans\Controllers\KomisyonController::class, 'destroy'])->name('destroy');

            // Status Management
            Route::post('/{id}/approve', [\App\Modules\Finans\Controllers\KomisyonController::class, 'approve'])->name('approve');
            Route::post('/{id}/pay', [\App\Modules\Finans\Controllers\KomisyonController::class, 'pay'])->name('pay');
            Route::post('/{id}/recalculate', [\App\Modules\Finans\Controllers\KomisyonController::class, 'recalculate'])->name('recalculate');

            // 🤖 AI-Powered Endpoints
            Route::post('/ai/suggest-rate', [\App\Modules\Finans\Controllers\KomisyonController::class, 'aiSuggestRate'])->name('ai.suggest-rate');
            Route::post('/{id}/ai/optimize', [\App\Modules\Finans\Controllers\KomisyonController::class, 'aiOptimize'])->name('ai.optimize');
            Route::post('/ai/analyze', [\App\Modules\Finans\Controllers\KomisyonController::class, 'aiAnalyze'])->name('ai.analyze');
        });
    });

    // Sistem Ayarları
    Route::prefix('/ayarlar')->name('ayarlar.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AyarlarController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AyarlarController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AyarlarController::class, 'store'])->name('store');
        Route::post('/bulk-store', [\App\Http\Controllers\Admin\AyarlarController::class, 'bulkStore'])->name('bulk-store');
        Route::post('/bulk-update', [\App\Http\Controllers\Admin\AyarlarController::class, 'bulkUpdate'])->name('bulk-update');
        Route::post('/clear-caches', [\App\Http\Controllers\Admin\AyarlarController::class, 'clearCaches'])->name('clear-caches');
        Route::get('/{ayar}', [\App\Http\Controllers\Admin\AyarlarController::class, 'show'])->name('show');
        Route::get('/{ayar}/edit', [\App\Http\Controllers\Admin\AyarlarController::class, 'edit'])->name('edit');
        Route::put('/{ayar}', [\App\Http\Controllers\Admin\AyarlarController::class, 'update'])->name('update');
        Route::delete('/{ayar}', [\App\Http\Controllers\Admin\AyarlarController::class, 'destroy'])->name('destroy');
    });

    // ✅ REMOVED: Duplicate route - Use admin.ayarlar.bulk-update instead
    // Route::post('/settings/update', [\App\Http\Controllers\Admin\AyarlarController::class, 'bulkUpdate'])->name('settings.update');

    // AI Ayarları
    Route::prefix('/ai-settings')->name('ai-settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AISettingsController::class, 'index'])->name('index');
        Route::put('/', [\App\Http\Controllers\Admin\AISettingsController::class, 'update'])->name('update');
        Route::post('/', [\App\Http\Controllers\Admin\AISettingsController::class, 'update'])->name('update.post'); // POST support for form compatibility
        Route::post('/test-provider', [\App\Http\Controllers\Admin\AISettingsController::class, 'testProvider'])->name('test-provider');
        Route::post('/test-query', [\App\Http\Controllers\Admin\AISettingsController::class, 'testQuery'])->name('test-query');
        Route::post('/test-ollama', [\App\Http\Controllers\Admin\AISettingsController::class, 'testOllamaConnection'])->name('test-ollama');
        Route::get('/analytics', [\App\Http\Controllers\Admin\AISettingsController::class, 'analytics'])->name('analytics'); // Context7: Real-time analytics
        Route::get('/provider-status', [\App\Http\Controllers\Admin\AISettingsController::class, 'getProviderStatus'])->name('provider-status');
        Route::get('/statistics', [\App\Http\Controllers\Admin\AISettingsController::class, 'statistics'])->name('statistics');
        Route::post('/proxy-ollama', [\App\Http\Controllers\Admin\AISettingsController::class, 'proxyOllama'])->name('proxy-ollama');

        // API Key ve Ayarlar Güncelleme Route'ları (2025-11-30 eklendi)
        Route::post('/update-api-key', [\App\Http\Controllers\Admin\AISettingsController::class, 'updateApiKey'])->name('update-api-key');
        Route::post('/update-ollama-url', [\App\Http\Controllers\Admin\AISettingsController::class, 'updateOllamaUrl'])->name('update-ollama-url');
        Route::post('/update-provider-model', [\App\Http\Controllers\Admin\AISettingsController::class, 'updateProviderModel'])->name('update-provider-model');
        Route::post('/update-openai-organization', [\App\Http\Controllers\Admin\AISettingsController::class, 'updateOpenAIOrganization'])->name('update-openai-organization');
        Route::post('/update-locale', [\App\Http\Controllers\Admin\AISettingsController::class, 'updateLocale'])->name('update-locale');
        Route::post('/update-currency', [\App\Http\Controllers\Admin\AISettingsController::class, 'updateCurrency'])->name('update-currency');
    });

    // Telegram Bot Yönetimi
    Route::prefix('/telegram-bot')->name('telegram-bot.')->group(function () {
        Route::get('/', [App\Modules\TakimYonetimi\Controllers\Admin\TelegramBotController::class, 'index'])->name('index');
        Route::get('/status', [App\Modules\TakimYonetimi\Controllers\Admin\TelegramBotController::class, 'status'])->name('status');
        Route::post('/set-webhook', [App\Modules\TakimYonetimi\Controllers\Admin\TelegramBotController::class, 'setWebhook'])->name('set-webhook');
        Route::post('/send-test-message', [App\Modules\TakimYonetimi\Controllers\Admin\TelegramBotController::class, 'sendTestMessage'])->name('send-test-message');
        Route::get('/webhook-info', [App\Modules\TakimYonetimi\Controllers\Admin\TelegramBotController::class, 'getWebhookInfo'])->name('webhook-info');
        Route::post('/update-settings', [App\Modules\TakimYonetimi\Controllers\Admin\TelegramBotController::class, 'updateSettings'])->name('update-settings');
        Route::post('/send-test', [App\Modules\TakimYonetimi\Controllers\Admin\TelegramBotController::class, 'sendTestMessage'])->name('send-test');
        Route::get('/test', [App\Modules\TakimYonetimi\Controllers\Admin\TelegramBotController::class, 'testBot'])->name('test');
        Route::post('/generate-pairing-code', [App\Modules\TakimYonetimi\Controllers\Admin\TelegramBotController::class, 'generatePairingCode'])->name('generate-pairing-code');
    });

    // Takım Yönetimi ve Görev Dağılımı
    Route::prefix('/takim-yonetimi')->name('takim-yonetimi.')->group(function () {
        // Görev Yönetimi
        Route::prefix('/gorevler')->name('gorevler.')->group(function () {
            Route::get('/', [\App\Modules\TakimYonetimi\Controllers\Admin\GorevController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\TakimYonetimi\Controllers\Admin\GorevController::class, 'create'])->name('create');
            Route::post('/', [\App\Modules\TakimYonetimi\Controllers\Admin\GorevController::class, 'store'])->name('store');
            Route::get('/{gorev}', [\App\Modules\TakimYonetimi\Controllers\Admin\GorevController::class, 'show'])->name('show');
            Route::get('/{gorev}/edit', [\App\Modules\TakimYonetimi\Controllers\Admin\GorevController::class, 'edit'])->name('edit');
            Route::put('/{gorev}', [\App\Modules\TakimYonetimi\Controllers\Admin\GorevController::class, 'update'])->name('update');
            Route::delete('/{gorev}', [\App\Modules\TakimYonetimi\Controllers\Admin\GorevController::class, 'destroy'])->name('destroy');

            // Görev Raporları
            Route::get('/raporlar', [\App\Modules\TakimYonetimi\Controllers\Admin\GorevController::class, 'raporlar'])->name('raporlar');

            // Toplu Görev Atama
            Route::post('/toplu-ata', [\App\Modules\TakimYonetimi\Controllers\Admin\GorevController::class, 'topluGorevAta'])->name('toplu-ata');
        });

        // Takım Yönetimi
        Route::prefix('/takim')->name('takim.')->group(function () {
            Route::get('/', [\App\Modules\TakimYonetimi\Controllers\Admin\TakimController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\TakimYonetimi\Controllers\Admin\TakimController::class, 'create'])->name('create');
            Route::post('/', [\App\Modules\TakimYonetimi\Controllers\Admin\TakimController::class, 'store'])->name('store');
            Route::get('/performans', [\App\Modules\TakimYonetimi\Controllers\Admin\TakimController::class, 'performans'])->name('performans');
            Route::get('/takim-performans', [\App\Modules\TakimYonetimi\Controllers\Admin\TakimController::class, 'takimPerformans'])->name('takim-performans');
            Route::get('/{takimId}', [\App\Modules\TakimYonetimi\Controllers\Admin\TakimController::class, 'show'])->name('show');
            Route::get('/{takimId}/edit', [\App\Modules\TakimYonetimi\Controllers\Admin\TakimController::class, 'edit'])->name('edit');
            Route::put('/{takimId}', [\App\Modules\TakimYonetimi\Controllers\Admin\TakimController::class, 'update'])->name('update');
            Route::delete('/{takimId}', [\App\Modules\TakimYonetimi\Controllers\Admin\TakimController::class, 'destroy'])->name('destroy');

            // Takım Üye Yönetimi
            Route::post('/uye-ekle', [\App\Modules\TakimYonetimi\Controllers\Admin\TakimController::class, 'uyeEkle'])->name('uye-ekle');
            Route::post('/uye-cikar', [\App\Modules\TakimYonetimi\Controllers\Admin\TakimController::class, 'uyeCikar'])->name('uye-cikar');
        });
    });

    // CRM Routes
    // Context7 Compliance: Moved to admin.crm.* namespace (was crm.* - deprecated)
    Route::prefix('crm')->name('crm.')->group(function () {
        // CRM Dashboard - Context7: admin.crm.dashboard route name
        Route::get('/', [CRMController::class, 'index'])->name('dashboard');

        // CRM Enhanced Dashboard (Added: 2025-11-25)
        Route::get('/dashboard-v2', [\App\Http\Controllers\Admin\CRMDashboardController::class, 'index'])->name('dashboard.v2');
        Route::get('/pipeline', [\App\Http\Controllers\Admin\CRMDashboardController::class, 'pipeline'])->name('pipeline');
        Route::get('/lead-sources', [\App\Http\Controllers\Admin\CRMDashboardController::class, 'leadSourceAnalytics'])->name('lead-sources');
        Route::post('/recalculate-scores', [\App\Http\Controllers\Admin\CRMDashboardController::class, 'recalculateScores'])->name('recalculate-scores');

        // CRM AJAX Actions
        Route::post('/kisi/{kisi}/update-pipeline', [\App\Http\Controllers\Admin\CRMDashboardController::class, 'updatePipelineStage'])->name('update-pipeline');
        Route::post('/kisi/{kisi}/update-segment', [\App\Http\Controllers\Admin\CRMDashboardController::class, 'updateSegment'])->name('update-segment');

        // Note: /dashboard path removed - use /admin/crm/ instead
        Route::get('/customers', [CRMController::class, 'customers'])->name('customers.index');
        Route::get('/customers/create', [CRMController::class, 'create'])->name('customers.create');
        Route::post('/customers', [CRMController::class, 'store'])->name('customers.store');
        Route::get('/customers/{customer}', [CRMController::class, 'show'])->name('customers.show');
        Route::get('/customers/{customer}/edit', [CRMController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customer}', [CRMController::class, 'update'])->name('customers.update');

        // CRM Actions
        Route::post('/customers/{customer}/notes', [CRMController::class, 'addNote'])->name('customers.notes.add');
        Route::post('/customers/{customer}/activities', [CRMController::class, 'addActivity'])->name('customers.activities.add');
        Route::put('/customers/{customer}/followup', [CRMController::class, 'updateFollowUp'])->name('customers.followup.update');

        // AI Analysis Routes
        Route::post('/customers/{customer}/ai-analysis', [CRMController::class, 'aiAnalysis'])->name('customers.ai-analysis');
        Route::post('/customers/{customer}/purchase-prediction', [CRMController::class, 'purchasePrediction'])->name('customers.purchase-prediction');

        // AI CRM Routes
        Route::get('/ai-analyze', [CRMController::class, 'aiAnalyze'])->name('ai-analyze');
        Route::post('/fix-duplicates', [CRMController::class, 'fixDuplicates'])->name('fix-duplicates');
        Route::get('/generate-report', [CRMController::class, 'generateReport'])->name('generate-report');
        Route::post('/customers/{customer}/risk-score', [CRMController::class, 'riskScore'])->name('customers.risk-score');
        Route::post('/customers/{customer}/followup-suggestions', [CRMController::class, 'followUpSuggestions'])->name('customers.followup-suggestions');
        Route::post('/customers/{customer}/suggest-tags', [CRMController::class, 'suggestTags'])->name('customers.suggest-tags');
    });

    // Talep-Portföy Eşleştirme Routes
    // ⚠️ CRITICAL: Özel route'lar generic route'ların ÖNÜNDE olmalı!
    Route::prefix('talep-portfolyo')->name('talep-portfolyo.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\TalepPortfolyoController::class, 'index'])->name('index');

        // 🎯 Özel Route'lar (ÖNCE)
        Route::get('/ai-status', [App\Http\Controllers\Admin\TalepPortfolyoController::class, 'aiStatus'])->name('ai-status');
        Route::post('/toplu-analiz', [App\Http\Controllers\Admin\TalepPortfolyoController::class, 'topluAnaliz'])->name('toplu-analiz');
        Route::post('/cache-temizle', [App\Http\Controllers\Admin\TalepPortfolyoController::class, 'cacheTemizle'])->name('cache-temizle');

        // 🔀 Generic Route'lar (SONRA) - {talep} her şeyi yakalar!
        Route::get('/{talep}', [App\Http\Controllers\Admin\TalepPortfolyoController::class, 'show'])->name('show');
        Route::post('/{talep}/analiz', [App\Http\Controllers\Admin\TalepPortfolyoController::class, 'analizEt'])->name('analiz');
        Route::post('/{talep}/portfolyo-oner', [App\Http\Controllers\Admin\TalepPortfolyoController::class, 'portfolyoOner'])->name('portfolyo-oner');
    });

    // Bildirim Sistemi Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('store');
        Route::get('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('show');
        Route::delete('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('destroy');

        // Bildirim İşlemleri
        Route::post('/{notification}/mark-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/{notification}/mark-unread', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsUnread'])->name('mark-unread');
        Route::post('/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');

        // API Endpoints
        Route::get('/api/statistics', [\App\Http\Controllers\Admin\NotificationController::class, 'statistics'])->name('api.statistics');
        Route::get('/api/unread-count', [\App\Http\Controllers\Admin\NotificationController::class, 'unreadCount'])->name('api.unread-count');
        Route::get('/api/recent', [\App\Http\Controllers\Admin\NotificationController::class, 'recent'])->name('api.recent');

        // Test Endpoints
        Route::get('/test', [\App\Http\Controllers\Admin\NotificationController::class, 'testRealTime'])->name('test');
        Route::get('/test-page', function () {
            return view('admin.notifications.index');
        })->name('test-page');

        Route::post('/test', [\App\Http\Controllers\Admin\NotificationController::class, 'testRealTime'])->name('test.post');
        Route::post('/test-sms', [\App\Http\Controllers\Admin\NotificationController::class, 'testSms'])->name('test-sms');
        Route::post('/test-email', [\App\Http\Controllers\Admin\NotificationController::class, 'testEmail'])->name('test-email');
    });

    // Danışman Özel Route'ları
    // Context7: musteri → kisi terminology
    Route::prefix('kisilerim')->name('kisilerim.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\KisiController::class, 'musterilerim'])->name('index');
    });

    // Backward compatibility alias
    Route::prefix('musterilerim')->name('musterilerim.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.kisilerim.index');
        })->name('index');
    });

    Route::prefix('ilanlarim')->name('ilanlarim.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\IlanController::class, 'ilanlarim'])->name('index');
    });

    Route::prefix('taleplerim')->name('taleplerim.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TalepController::class, 'taleplerim'])->name('index');
    });

    Route::prefix('raporlarim')->name('raporlarim.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReportingController::class, 'raporlarim'])->name('index');
    });

    // User Özel Route'ları
    Route::prefix('profilim')->name('profilim.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('index');
        Route::put('/', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('update');
    });

    // ✅ REMOVED: UserSettingsController placeholder - Use admin.ayarlar instead
    // Route::prefix('ayarlarim')->name('ayarlarim.')->group(function () {
    //     Route::get('/', [\App\Http\Controllers\Admin\UserSettingsController::class, 'index'])->name('index');
    //     Route::put('/', [\App\Http\Controllers\Admin\UserSettingsController::class, 'update'])->name('update');
    // });

    Route::prefix('adres-yonetimi')->name('adres-yonetimi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'index'])->name('index');

        // Context7: Specific routes FIRST (before generic /{type}/{id})
        Route::get('/ulkeler', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'getUlkeler'])->name('ulkeler');
        Route::get('/bolgeler', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'getBolgeler'])->name('bolgeler');
        Route::get('/iller', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'getIller'])->name('iller');
        Route::get('/iller/{ulkeId}', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'getIllerByUlke'])->name('iller.by-ulke');
        Route::get('/ilceler', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'getIlceler'])->name('ilceler');
        Route::get('/ilceler/{ilId}', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'getIlcelerByIl'])->name('ilceler.by-il');
        Route::get('/mahalleler', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'getMahalleler'])->name('mahalleler');
        Route::get('/mahalleler/{ilceId}', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'getMahallelerByIlce'])->name('mahalleler.by-ilce');

        // ✅ Context7: TurkiyeAPI entegrasyonu route'ları
        Route::post('/sync-from-turkiyeapi', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'syncFromTurkiyeAPI'])->name('sync-from-turkiyeapi');
        Route::post('/fetch-from-turkiyeapi', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'fetchFromTurkiyeAPI'])->name('fetch-from-turkiyeapi');
        Route::get('/ilceler/{ilId}/turkiyeapi', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'getIlcelerByIlFromTurkiyeAPI'])->name('ilceler.by-il.turkiyeapi');
        Route::get('/all-location-types/{ilceId}/turkiyeapi', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'getAllLocationTypesFromTurkiyeAPI'])->name('all-location-types.turkiyeapi');

        // Generic routes LAST (catch-all)
        Route::get('/create/{type}', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'create'])->name('create');
        Route::get('/{type}/{id}/edit', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'edit'])->name('edit');
        Route::get('/{type}/{id}', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'show'])->name('show');
        Route::post('/{type}', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'store'])->name('store');
        Route::put('/{type}/{id}', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'update'])->name('update');
        Route::delete('/{type}/{id}', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'destroy'])->name('destroy');

        // Bulk actions
        Route::post('/bulk-delete', [\App\Http\Controllers\Admin\AdresYonetimiController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // ✅ REMOVED: SettingsController placeholder - Use admin.ayarlar instead
    // Route::get('/ayarlar/konum', [\App\Http\Controllers\Admin\SettingsController::class, 'locationSettings'])->name('settings.location');

    // Page Analyzer Routes
    Route::prefix('/page-analyzer')->name('page-analyzer.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PageAnalyzerController::class, 'index'])->name('index');
        Route::get('/dashboard', [\App\Http\Controllers\Admin\PageAnalyzerController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [\App\Http\Controllers\Admin\PageAnalyzerController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PageAnalyzerController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\PageAnalyzerController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PageAnalyzerController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\PageAnalyzerController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\PageAnalyzerController::class, 'destroy'])->name('destroy');

        // Additional routes
        Route::get('/export/{id?}', [\App\Http\Controllers\Admin\PageAnalyzerController::class, 'export'])->name('export');
        Route::post('/rerun/{id}', [\App\Http\Controllers\Admin\PageAnalyzerController::class, 'rerun'])->name('rerun');
        Route::get('/metrics', [\App\Http\Controllers\Admin\PageAnalyzerController::class, 'metrics'])->name('metrics');
        Route::get('/health', [\App\Http\Controllers\Admin\PageAnalyzerController::class, 'health'])->name('health');
        Route::get('/recommendations', [\App\Http\Controllers\Admin\PageAnalyzerController::class, 'recommendations'])->name('recommendations');
    });
});

// Context7 AI Features - Public routes (no auth required)
Route::prefix('admin/ozellikler')->name('admin.ozellikler.')->group(function () {
    Route::post('/context7/suggest', [\App\Http\Controllers\Admin\FeatureController::class, 'suggestFeatures'])->name('context7.suggest');
    Route::post('/context7/search', [\App\Http\Controllers\Admin\FeatureController::class, 'smartSearch'])->name('context7.search');
    Route::post('/context7/categorize', [\App\Http\Controllers\Admin\FeatureController::class, 'categorizeFeatures'])->name('context7.categorize');
    Route::post('/context7/category-analysis', [\App\Http\Controllers\Admin\FeatureController::class, 'analyzeCategories'])->name('context7.category-analysis');

    // AI Training Routes
    Route::post('/context7/train/categories', [\App\Http\Controllers\Admin\FeatureController::class, 'trainCategories'])->name('context7.train.categories');
    Route::post('/context7/train/behavior', [\App\Http\Controllers\Admin\FeatureController::class, 'trainUserBehavior'])->name('context7.train.behavior');
    Route::post('/context7/train/market', [\App\Http\Controllers\Admin\FeatureController::class, 'trainMarketTrends'])->name('context7.train.market');
    Route::get('/context7/training-status', [\App\Http\Controllers\Admin\FeatureController::class, 'getTrainingStatus'])->name('context7.training-status');
});

// Bulk Kisi Management Routes
Route::prefix('admin/bulk-kisi')->name('admin.bulk-kisi.')->middleware(['web'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\BulkKisiController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\BulkKisiController::class, 'create'])->name('create');
    Route::post('/store', [\App\Http\Controllers\Admin\BulkKisiController::class, 'store'])->name('store');
    Route::get('/edit', [\App\Http\Controllers\Admin\BulkKisiController::class, 'edit'])->name('edit');
    Route::put('/update', [\App\Http\Controllers\Admin\BulkKisiController::class, 'update'])->name('update');
    Route::delete('/destroy', [\App\Http\Controllers\Admin\BulkKisiController::class, 'destroy'])->name('destroy');
    Route::get('/export', [\App\Http\Controllers\Admin\BulkKisiController::class, 'export'])->name('export');
    Route::post('/import', [\App\Http\Controllers\Admin\BulkKisiController::class, 'import'])->name('import');
});

// Yazlik Kiralama Management Routes
Route::prefix('admin/yazlik-kiralama')->name('admin.yazlik-kiralama.')->middleware(['web'])->group(function () {

    // ⚠️ CRITICAL: Specific routes BEFORE dynamic {id} routes!

    // Bookings Management (MUST be first!)
    Route::get('/bookings/{id?}', [\App\Http\Controllers\Admin\YazlikKiralamaController::class, 'bookings'])->name('bookings');
    Route::put('/bookings/{id}/status', [\App\Http\Controllers\Admin\YazlikKiralamaController::class, 'updateBookingStatus'])->name('bookings.update-status');

    // Takvim - Calendar View (MUST be second!)
    Route::prefix('takvim')->name('takvim.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TakvimController::class, 'index'])->name('index');
        Route::get('/sezonlar', [\App\Http\Controllers\Admin\TakvimController::class, 'sezonlar'])->name('sezonlar');
        Route::post('/sezon/store', [\App\Http\Controllers\Admin\TakvimController::class, 'storeSezon'])->name('sezon.store');
        Route::put('/sezon/{id}', [\App\Http\Controllers\Admin\TakvimController::class, 'updateSezon'])->name('sezon.update');
        Route::delete('/sezon/{id}', [\App\Http\Controllers\Admin\TakvimController::class, 'destroySezon'])->name('sezon.destroy');
    });

    // Resource routes (LAST - {id} catches everything else!)
    Route::get('/', [\App\Http\Controllers\Admin\YazlikKiralamaController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\YazlikKiralamaController::class, 'create'])->name('create');
    Route::post('/store', [\App\Http\Controllers\Admin\YazlikKiralamaController::class, 'store'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\Admin\YazlikKiralamaController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\YazlikKiralamaController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\YazlikKiralamaController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\YazlikKiralamaController::class, 'destroy'])->name('destroy');
});

// DanismanAI Management Routes
Route::prefix('admin/danisman-ai')->name('admin.danisman-ai.')->middleware(['web'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DanismanAIController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\DanismanAIController::class, 'create'])->name('create');
    Route::post('/store', [\App\Http\Controllers\Admin\DanismanAIController::class, 'store'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\Admin\DanismanAIController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\DanismanAIController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\DanismanAIController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\DanismanAIController::class, 'destroy'])->name('destroy');
    Route::post('/chat', [\App\Http\Controllers\Admin\DanismanAIController::class, 'chat'])->name('chat');
    Route::post('/analyze', [\App\Http\Controllers\Admin\DanismanAIController::class, 'analyze'])->name('analyze');
    Route::post('/suggest', [\App\Http\Controllers\Admin\DanismanAIController::class, 'suggest'])->name('suggest');
    Route::get('/analytics/data', [\App\Http\Controllers\Admin\DanismanAIController::class, 'analytics'])->name('analytics');
    Route::get('/export/{type}', [\App\Http\Controllers\Admin\DanismanAIController::class, 'export'])->name('export');
});

// KisiNot Management Routes
Route::prefix('admin/kisi-not')->name('admin.kisi-not.')->middleware(['web'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\KisiNotController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\KisiNotController::class, 'create'])->name('create');
    Route::post('/store', [\App\Http\Controllers\Admin\KisiNotController::class, 'store'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\Admin\KisiNotController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\KisiNotController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\KisiNotController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\KisiNotController::class, 'destroy'])->name('destroy');
    Route::post('/bulk', [\App\Http\Controllers\Admin\KisiNotController::class, 'bulk'])->name('bulk');
    Route::get('/export', [\App\Http\Controllers\Admin\KisiNotController::class, 'export'])->name('export');
    Route::get('/search', [\App\Http\Controllers\Admin\KisiNotController::class, 'search'])->name('search');
});

// AI Category Suggestions Routes
Route::prefix('admin/ai-category')->group(function () {
    Route::post('/suggest', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'suggestCategories']);
    Route::get('/trends', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'getTrends']);
    Route::get('/performance', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'getPerformance']);
});

// Performance Monitoring Routes

// Analytics Dashboard Routes
Route::prefix('admin/analytics')->name('admin.analytics.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('index');
    Route::get('/data', [\App\Http\Controllers\Admin\AnalyticsController::class, 'data'])->name('data');
    Route::get('/{id}', [\App\Http\Controllers\Admin\AnalyticsController::class, 'show'])->name('show');
});

// Feature Category Management Routes
Route::prefix('admin/feature-categories')->name('admin.feature-categories.')->middleware(['web'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\FeatureCategoryController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\FeatureCategoryController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\FeatureCategoryController::class, 'store'])->name('store');
    Route::get('/{featureCategory}', [\App\Http\Controllers\Admin\FeatureCategoryController::class, 'show'])->name('show');
    Route::get('/{featureCategory}/edit', [\App\Http\Controllers\Admin\FeatureCategoryController::class, 'edit'])->name('edit');
    Route::put('/{featureCategory}', [\App\Http\Controllers\Admin\FeatureCategoryController::class, 'update'])->name('update');
    Route::delete('/{featureCategory}', [\App\Http\Controllers\Admin\FeatureCategoryController::class, 'destroy'])->name('destroy');
});

// Address Management Routes
Route::prefix('admin/address')->name('admin.address.')->middleware(['web'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AddressController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\AddressController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\AddressController::class, 'store'])->name('store');
    Route::get('/{address}', [\App\Http\Controllers\Admin\AddressController::class, 'show'])->name('show');
    Route::get('/{address}/edit', [\App\Http\Controllers\Admin\AddressController::class, 'edit'])->name('edit');
    Route::put('/{address}', [\App\Http\Controllers\Admin\AddressController::class, 'update'])->name('update');
    Route::delete('/{address}', [\App\Http\Controllers\Admin\AddressController::class, 'destroy'])->name('destroy');

    // AJAX endpoints
    Route::get('/districts', [\App\Http\Controllers\Admin\AddressController::class, 'getDistricts'])->name('districts');
    Route::get('/neighborhoods', [\App\Http\Controllers\Admin\AddressController::class, 'getNeighborhoods'])->name('neighborhoods');
    Route::post('/bulk', [\App\Http\Controllers\Admin\AddressController::class, 'bulkAction'])->name('bulk');
});

// Etiket Management Routes
Route::prefix('admin/etiket')->name('admin.etiket.')->middleware(['web'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\EtiketController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\EtiketController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\EtiketController::class, 'store'])->name('store');
    Route::get('/{etiket}', [\App\Http\Controllers\Admin\EtiketController::class, 'show'])->name('show');
    Route::get('/{etiket}/edit', [\App\Http\Controllers\Admin\EtiketController::class, 'edit'])->name('edit');
    Route::put('/{etiket}', [\App\Http\Controllers\Admin\EtiketController::class, 'update'])->name('update');
    Route::delete('/{etiket}', [\App\Http\Controllers\Admin\EtiketController::class, 'destroy'])->name('destroy');

    // Additional routes
    Route::post('/bulk-action', [\App\Http\Controllers\Admin\EtiketController::class, 'bulkAction'])->name('bulk-action');
    Route::get('/export', [\App\Http\Controllers\Admin\EtiketController::class, 'export'])->name('export');
});

// Location Management Routes
Route::prefix('admin/locations')->name('admin.locations.')->middleware(['web'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\LocationController::class, 'index'])->name('index');
    Route::get('/{id}', [\App\Http\Controllers\Admin\LocationController::class, 'show'])->name('show');

    // API endpoints
    Route::get('/api/provinces', [\App\Http\Controllers\Admin\LocationController::class, 'getProvinces'])->name('provinces');
    Route::get('/api/districts', [\App\Http\Controllers\Admin\LocationController::class, 'getDistricts'])->name('districts');
    Route::get('/api/neighborhoods', [\App\Http\Controllers\Admin\LocationController::class, 'getNeighborhoods'])->name('neighborhoods');
});

// KisiNot Management Routes
Route::prefix('admin/kisi-not')->name('admin.kisi-not.')->middleware(['web'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\KisiNotController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\KisiNotController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\KisiNotController::class, 'store'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\Admin\KisiNotController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\KisiNotController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\KisiNotController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\KisiNotController::class, 'destroy'])->name('destroy');

    // Additional routes
    Route::post('/bulk', [\App\Http\Controllers\Admin\KisiNotController::class, 'bulk'])->name('bulk');
    Route::get('/export', [\App\Http\Controllers\Admin\KisiNotController::class, 'export'])->name('export');
    Route::post('/search', [\App\Http\Controllers\Admin\KisiNotController::class, 'search'])->name('search');
});

// Ayarlar Management Routes
Route::prefix('admin/ayarlar')->name('admin.ayarlar.')->middleware(['web'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AyarlarController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\AyarlarController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\AyarlarController::class, 'store'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\Admin\AyarlarController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\AyarlarController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\AyarlarController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\AyarlarController::class, 'destroy'])->name('destroy');
});

// Pazar İstihbaratı (Market Intelligence) Routes
Route::prefix('admin/market-intelligence')->name('admin.market-intelligence.')->middleware(['web'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\MarketIntelligenceController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [\App\Http\Controllers\Admin\MarketIntelligenceController::class, 'settings'])->name('settings');
    Route::get('/compare/{ilan?}', [\App\Http\Controllers\Admin\MarketIntelligenceController::class, 'compare'])->name('compare');
    Route::get('/trends', [\App\Http\Controllers\Admin\MarketIntelligenceController::class, 'trends'])->name('trends');
});

// Pazar İstihbaratı API Routes (n8n bot ve AJAX için)
Route::prefix('api/market-intelligence')->name('api.market-intelligence.')->middleware(['web', 'auth'])->group(function () {
    // n8n bot için: Aktif bölgeleri getir
    Route::get('/active-regions', [\App\Http\Controllers\Admin\MarketIntelligenceController::class, 'getActiveRegions'])->name('active-regions');

    // Bölge ayarları yönetimi
    Route::post('/settings', [\App\Http\Controllers\Admin\MarketIntelligenceController::class, 'saveSettings'])->name('settings.save');
    Route::delete('/settings/{id}', [\App\Http\Controllers\Admin\MarketIntelligenceController::class, 'deleteSetting'])->name('settings.delete');
    Route::patch('/settings/{id}/toggle', [\App\Http\Controllers\Admin\MarketIntelligenceController::class, 'toggleSetting'])->name('settings.toggle');
});

// n8n Bot Sync Endpoint (CSRF exempt - n8n secret middleware ile korumalı)
Route::prefix('api/admin/market-intelligence')->name('admin.api.market-intelligence.')->group(function () {
    Route::post('/sync', [\App\Http\Controllers\Admin\MarketIntelligenceController::class, 'sync'])->name('sync');
});

// Diagnostic Test Route (Yalıhan Bekçi)
Route::get('/test-minimal', function () {
    return view('admin.test-minimal');
})->name('test-minimal');
