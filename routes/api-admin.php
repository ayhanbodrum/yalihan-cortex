<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for Admin
|--------------------------------------------------------------------------
*/

// Kategori Özellik API (Geçici olarak public - test için)
Route::prefix('admin/api/kategori-ozellik')->name('admin.api.kategori-ozellik.')->group(function () {
    Route::get('/category-data', [\App\Http\Controllers\Admin\KategoriOzellikApiController::class, 'getCategoryData'])->name('category-data');
    Route::get('/features-by-category', [\App\Http\Controllers\Admin\KategoriOzellikApiController::class, 'getFeaturesByCategory'])->name('features-by-category');
    Route::get('/features-by-publishing-type', [\App\Http\Controllers\Admin\KategoriOzellikApiController::class, 'getFeaturesByPublishingType'])->name('features-by-publishing-type');
    Route::get('/feature-categories', [\App\Http\Controllers\Admin\KategoriOzellikApiController::class, 'getFeatureCategories'])->name('feature-categories');
    Route::post('/update-category-features', [\App\Http\Controllers\Admin\KategoriOzellikApiController::class, 'updateCategoryFeatures'])->name('update-category-features');
    Route::get('/frontend-features', [\App\Http\Controllers\Admin\KategoriOzellikApiController::class, 'getFeaturesForFrontend'])->name('frontend-features');
    Route::get('/ilan-features', [\App\Http\Controllers\Admin\KategoriOzellikApiController::class, 'getIlanFeatures'])->name('ilan-features');
});

// Features API (Ilan Create için basitleştirilmiş)
Route::prefix('admin')->name('admin.api.')->middleware(['web','auth'])->group(function () {
    // ✅ NEW: Category-specific features with applies_to filtering
    Route::get('/features', [\App\Http\Controllers\Api\FeatureController::class, 'index'])->name('features');
    Route::get('/features/category/{categoryId}', [\App\Http\Controllers\Admin\IlanController::class, 'getFeaturesByCategory'])
        ->whereNumber('categoryId')
        ->name('features.category');
});

// Feature API Routes (Modal Selector) - Blade template'lerinden çağrılıyor
// ✅ Context7: /api/admin/features/category/{slug} endpoint'i (JavaScript'ten çağrılıyor)
// ✅ Context7: api-admin.php zaten /api prefix'i ile başlıyor, bu yüzden sadece 'admin/features' yazıyoruz
Route::prefix('admin/features')->middleware(['web','auth'])->group(function () {
    // ✅ Context7: Blade template'lerinde 'api.features.category.slug' route name'i kullanılıyor
    Route::get('/category/{categorySlug}', [\App\Http\Controllers\Api\FeatureController::class, 'getByCategory'])->name('api.features.category.slug');
    Route::get('/categories', [\App\Http\Controllers\Api\FeatureController::class, 'getCategories'])->name('api.features.categories');
});

// AI-Powered Smart Field Generation API
Route::prefix('admin/api/smart-fields')->name('admin.api.smart-fields.')->group(function () {
    Route::get('/smart-fields', [\App\Http\Controllers\Api\SmartFieldController::class, 'getSmartFields'])->name('get-smart-fields');
    Route::get('/category-matrix', [\App\Http\Controllers\Api\SmartFieldController::class, 'getCategoryMatrix'])->name('get-category-matrix');
    Route::post('/generate-smart-form', [\App\Http\Controllers\Api\SmartFieldController::class, 'generateSmartForm'])->name('generate-smart-form');
    Route::post('/analyze-property', [\App\Http\Controllers\Api\SmartFieldController::class, 'analyzeProperty'])->name('analyze-property');
    Route::get('/ai-suggestions', [\App\Http\Controllers\Api\SmartFieldController::class, 'getAISuggestions'])->name('get-ai-suggestions');
});

// AI API Endpoints
Route::prefix('admin/ai')->name('admin.ai.')->group(function () {
    Route::post('/chat', [\App\Http\Controllers\Api\AdminAIController::class, 'chat'])->name('chat');
    Route::post('/price/predict', [\App\Http\Controllers\Api\AdminAIController::class, 'pricePredict'])->name('price.predict');
    Route::get('/analytics', [\App\Http\Controllers\Api\AdminAIController::class, 'analytics'])->name('analytics');
    Route::post('/suggest-title', [\App\Http\Controllers\Api\AIController::class, 'suggestTitle'])->name('suggest-title');
    Route::post('/generate-description', [\App\Http\Controllers\Api\AIController::class, 'generateDescription'])->name('generate-description');
    Route::post('/suggest-price', [\App\Http\Controllers\Api\AIController::class, 'suggestPrice'])->name('suggest-price');
    Route::get('/health', [\App\Http\Controllers\Api\AIController::class, 'health'])->name('health');

    // ═══════════════════════════════════════════════════════════
    // 🎯 2D MATRIX FIELD DEPENDENCY + AI
    // ═══════════════════════════════════════════════════════════

    // Field Dependency API
    Route::prefix('field-dependency')->name('field-dependency.')->group(function () {
        // ⚠️ ÖNEMLİ: Spesifik route'lar ÖNCE olmalı (wildcard'dan önce!)

        // Matrix management (spesifik route)
        Route::get('/get-matrix/{kategoriSlug}', [\App\Http\Controllers\Api\FieldDependencyController::class, 'getMatrix'])->name('get-matrix');
        Route::post('/update', [\App\Http\Controllers\Api\FieldDependencyController::class, 'updateDependency'])->name('update');

        // AI-powered suggestions
        Route::post('/suggest', [\App\Http\Controllers\Api\FieldDependencyController::class, 'suggestField'])->name('suggest');
        Route::post('/auto-fill', [\App\Http\Controllers\Api\FieldDependencyController::class, 'autoFillAll'])->name('auto-fill');
        Route::post('/smart-calculate', [\App\Http\Controllers\Api\FieldDependencyController::class, 'smartCalculate'])->name('smart-calculate');

        // Get fields for kategori × yayin tipi (wildcard route - EN SONDA!)
        Route::get('/{kategoriSlug}/{yayinTipi}', [\App\Http\Controllers\Api\FieldDependencyController::class, 'getFields'])->name('get-fields');
    });

    // 🤖 AI Feature Suggestion Endpoints (NEW!)
    Route::post('/suggest-features', [\App\Http\Controllers\Api\AIFeatureSuggestionController::class, 'suggestFeatureValues'])->name('suggest-features');
    Route::post('/suggest-feature', [\App\Http\Controllers\Api\AIFeatureSuggestionController::class, 'suggestSingleFeature'])->name('suggest-feature');
    Route::post('/analyze-property', [\App\Http\Controllers\Api\AIFeatureSuggestionController::class, 'analyzePropertyType'])->name('analyze-property');
    Route::get('/smart-defaults', [\App\Http\Controllers\Api\AIFeatureSuggestionController::class, 'getSmartDefaults'])->name('smart-defaults');
});

// AI Image Analysis Endpoints
Route::prefix('admin/ai/image')->name('admin.ai.image.')->group(function () {
    Route::post('/analyze', [\App\Http\Controllers\Api\ImageAIController::class, 'analyzeImage'])->name('analyze');
    Route::post('/generate-tags', [\App\Http\Controllers\Api\ImageAIController::class, 'generateTags'])->name('generate-tags');
    Route::post('/analyze-quality', [\App\Http\Controllers\Api\ImageAIController::class, 'analyzeQuality'])->name('analyze-quality');
    Route::post('/analyze-batch', [\App\Http\Controllers\Api\ImageAIController::class, 'analyzeBatch'])->name('analyze-batch');
});

// Property Feature Suggestion Endpoints
Route::prefix('admin/property-features')->name('admin.property-features.')->group(function () {
    Route::get('/suggestions', [\App\Http\Controllers\Api\PropertyFeatureSuggestionController::class, 'getFeatureSuggestions'])->name('suggestions');
    Route::get('/smart-suggestions', [\App\Http\Controllers\Api\PropertyFeatureSuggestionController::class, 'getSmartSuggestions'])->name('smart-suggestions');
    Route::post('/validate', [\App\Http\Controllers\Api\PropertyFeatureSuggestionController::class, 'validateFeatures'])->name('validate');
});

// Akıllı Çevre Analizi Endpoints
Route::prefix('admin/cevre-analizi')->name('admin.cevre-analizi.')->group(function () {
    Route::post('/', [\App\Http\Controllers\Api\AkilliCevreAnaliziController::class, 'analyzeEnvironment'])->name('analyze');
    Route::get('/smart-recommendations', [\App\Http\Controllers\Api\AkilliCevreAnaliziController::class, 'getSmartRecommendations'])->name('smart-recommendations');
    Route::post('/calculate-distance', [\App\Http\Controllers\Api\AkilliCevreAnaliziController::class, 'calculateDistance'])->name('calculate-distance');
    Route::post('/search-poi', [\App\Http\Controllers\Api\AkilliCevreAnaliziController::class, 'searchPOI'])->name('search-poi');
});

// Kişi API (Test için public)
Route::prefix('admin/api')->name('admin.api.')->group(function () {
    Route::get('/kisi/search', function (\Illuminate\Http\Request $request) {
        $searchTerm = $request->get('search', '') ?: $request->get('q', '');
        $limit = $request->get('limit', 10);

        if (empty($searchTerm)) {
            return response()->json(['success' => true, 'data' => []]);
        }

        try {
            $results = \App\Modules\Crm\Services\KisiService::search($searchTerm, $limit);

            return response()->json([
                'success' => true,
                'data' => $results,
                'count' => $results->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    })->name('kisi.search');

    // İlan sahibi olarak uygun kişileri getir
    Route::get('/kisi/owners', function (\Illuminate\Http\Request $request) {
        $searchTerm = $request->get('search', '') ?: $request->get('q', '');
        $limit = $request->get('limit', 20); // Limit'i artırdık

        try {
            // Eğer arama terimi yoksa, tüm status kişileri getir
            if (empty($searchTerm)) {
                $results = \App\Modules\Crm\Models\Kisi::where('status', 'Aktif')
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get()
                    ->map(function ($kisi) {
                        $kisi->owner_score = \App\Modules\Crm\Services\KisiService::calculateOwnerScore($kisi);

                        return $kisi;
                    })
                    ->sortByDesc('owner_score');
            } else {
                $results = \App\Modules\Crm\Services\KisiService::getPotentialOwners($searchTerm, $limit);
            }

            return response()->json([
                'success' => true,
                'data' => $results,
                'count' => $results->count(),
                'search_term' => $searchTerm,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    })->name('kisi.owners');

    // Kişinin ilan sahibi geçmişini getir
    Route::get('/kisi/{id}/owner-history', function (\Illuminate\Http\Request $request, $id) {
        try {
            $history = \App\Modules\Crm\Services\KisiService::getOwnerHistory($id);

            return response()->json([
                'success' => true,
                'data' => $history,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    })->name('kisi.owner-history');
});

Route::middleware(['auth', 'admin'])->prefix('admin/api')->name('admin.api.')->group(function () {
    // Quick Search API (Yeni hızlı arama sistemi) - Geçici olarak devre dışı
    // Route::get('/quick-search', [\App\Http\Controllers\Admin\QuickSearchController::class, 'quickSearch'])->name('quick-search');
    // Route::get('/search-stats', [\App\Http\Controllers\Admin\QuickSearchController::class, 'searchStats'])->name('search-stats');
    // Route::post('/search-clear-cache', [\App\Http\Controllers\Admin\QuickSearchController::class, 'clearCache'])->name('search-clear-cache');

    // Live Search API (Eski sistem - geriye uyumluluk için)
    Route::get('/live-search', [\App\Http\Controllers\Admin\IlanController::class, 'liveSearch'])->name('live-search');
    Route::get('/ilceler', [\App\Http\Controllers\Admin\IlanController::class, 'getIlceler'])->name('ilceler');
    Route::get('/mahalleler', [\App\Http\Controllers\Admin\IlanController::class, 'getMahalleler'])->name('mahalleler');

    // Eşleşmeler için veri endpoint'leri
    Route::get('/kisiler', function (\Illuminate\Http\Request $request) {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = max(1, min($perPage, 100));
        $data = \App\Models\Kisi::where('status', 'Aktif')
            ->select('id', 'ad', 'soyad', 'telefon')
            ->orderByDesc('created_at')
            ->paginate($perPage);
        return \App\Services\Response\ResponseService::success($data, 'Kişiler listesi');
    })->name('kisiler.list');

    Route::get('/danismanlar', function (\Illuminate\Http\Request $request) {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = max(1, min($perPage, 100));
        $data = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->where('status', 1)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->paginate($perPage);
        return \App\Services\Response\ResponseService::success($data, 'Danışmanlar listesi');
    })->name('danismanlar.list');

    Route::get('/talepler', function (\Illuminate\Http\Request $request) {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = max(1, min($perPage, 100));
        $data = \App\Models\Talep::where('status', 'Aktif')
            ->select('id', 'talep_adi', 'kategori_id')
            ->with('kategori:id,name')
            ->orderByDesc('created_at')
            ->paginate($perPage);
        return \App\Services\Response\ResponseService::success($data, 'Talepler listesi');
    })->name('talepler.list');

    Route::get('/ilanlar', function (\Illuminate\Http\Request $request) {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = max(1, min($perPage, 100));
        $data = \App\Models\Ilan::where('status', 'Aktif')
            ->select('id', 'title', 'kategori_id', 'fiyat', 'para_birimi')
            ->with('kategori:id,name')
            ->orderByDesc('created_at')
            ->paginate($perPage);
        return \App\Services\Response\ResponseService::success($data, 'İlanlar listesi');
    })->name('ilanlar.list');

    Route::get('/ai/eslesme-onerileri', function (\Illuminate\Http\Request $request) {
        $suggestions = [
            ['type' => 'kisi', 'id' => 1, 'score' => 95, 'reason' => 'Yüksek uyumluluk'],
            ['type' => 'ilan', 'id' => 2, 'score' => 88, 'reason' => 'Fiyat uyumu'],
        ];
        return \App\Services\Response\ResponseService::success(['items' => $suggestions], 'Eşleşme önerileri', 200, ['total' => count($suggestions)]);
    })->name('ai.eslesme-onerileri');

    // AI Features
    Route::post('/ai-title', [\App\Http\Controllers\Admin\IlanController::class, 'generateAiTitle'])->name('ai-title');
    Route::post('/ai-description', [\App\Http\Controllers\Admin\IlanController::class, 'generateAiDescription'])->name('ai-description');

    Route::post('/kisi/create', function (\Illuminate\Http\Request $request) {
        if (! auth()->check()) {
            return \App\Services\Response\ResponseService::unauthorized();
        }

        $validatedData = $request->validate([
            'ad' => 'required|string|max:255',
            'soyad' => 'required|string|max:255',
            'email' => 'nullable|email|unique:kisiler,email',
            'telefon' => 'nullable|string|max:20',
        ]);

        try {
            $kisiService = new \App\Modules\Crm\Services\KisiService;
            $kisi = $kisiService->createKisi($validatedData);

            return \App\Services\Response\ResponseService::success($kisi, 'Kişi oluşturuldu', 201);
        } catch (\Exception $e) {
            return \App\Services\Response\ResponseService::error($e->getMessage(), 500);
        }
    })->name('kisi.create');

    // Smart Calculator API
    Route::prefix('calculator')->name('calculator.')->group(function () {
        // Hesaplama yapma
        Route::post('/calculate', function (\Illuminate\Http\Request $request) {
            $calculatorService = new \App\Services\SmartCalculatorService;

            $type = $request->input('type');
            $inputs = $request->input('inputs', []);

            try {
                $result = $calculatorService->calculate($type, $inputs);
                return \App\Services\Response\ResponseService::success($result, 'Hesaplama başarıyla tamamlandı');
            } catch (\Exception $e) {
                return \App\Services\Response\ResponseService::error($e->getMessage(), 400);
            }
        })->name('calculate');

        // Hesaplama geçmişi
        Route::get('/history', function (\Illuminate\Http\Request $request) {
            $calculatorService = new \App\Services\SmartCalculatorService;

            $type = $request->get('type');
            $limit = $request->get('limit', 20);

            $history = $calculatorService->getHistory($type, $limit);

            return \App\Services\Response\ResponseService::success($history);
        })->name('history');

        // Favori hesaplamalar
        Route::get('/favorites', function () {
            $calculatorService = new \App\Services\SmartCalculatorService;
            $favorites = $calculatorService->getFavorites();

            return \App\Services\Response\ResponseService::success($favorites);
        })->name('favorites');

        // Favori ekleme
        Route::post('/favorites', function (\Illuminate\Http\Request $request) {
            $calculatorService = new \App\Services\SmartCalculatorService;

            $type = $request->input('type');
            $name = $request->input('name');
            $inputs = $request->input('inputs', []);
            $description = $request->input('description');

            $success = $calculatorService->addFavorite($type, $name, $inputs, $description);

            if ($success) {
                return \App\Services\Response\ResponseService::success(null, 'Favori hesaplama eklendi');
            }
            return \App\Services\Response\ResponseService::error('Favori hesaplama eklenemedi', 400);
        })->name('favorites.store');

        // Favori silme
        Route::delete('/favorites/{id}', function ($id) {
            $calculatorService = new \App\Services\SmartCalculatorService;
            $success = $calculatorService->removeFavorite($id);

            if ($success) {
                return \App\Services\Response\ResponseService::success(null, 'Favori hesaplama silindi');
            }
            return \App\Services\Response\ResponseService::error('Favori hesaplama silinemedi', 400);
        })->name('favorites.destroy');

        // Vergi oranları
        Route::get('/tax-rates', function (\Illuminate\Http\Request $request) {
            $calculatorService = new \App\Services\SmartCalculatorService;
            $type = $request->get('type');
            $rates = $calculatorService->getTaxRates($type);

            return \App\Services\Response\ResponseService::success($rates);
        })->name('tax-rates');

        // Komisyon oranları
        Route::get('/commission-rates', function (\Illuminate\Http\Request $request) {
            $calculatorService = new \App\Services\SmartCalculatorService;
            $type = $request->get('type');
            $rates = $calculatorService->getCommissionRates($type);

            return \App\Services\Response\ResponseService::success($rates);
        })->name('commission-rates');

        // Hesaplama türleri
        Route::get('/types', function () {
            $calculatorService = new \App\Services\SmartCalculatorService;
            $types = $calculatorService->getCalculationTypes();

            return \App\Services\Response\ResponseService::success($types);
        })->name('types');
    });

    // Site API (normalize to search/show/store)
    Route::get('/sites/search', [\App\Http\Controllers\Admin\SiteController::class, 'search'])->name('sites.search');
    Route::get('/sites/{id}', [\App\Http\Controllers\Admin\SiteController::class, 'show'])->name('sites.detail');
    Route::post('/sites/create', [\App\Http\Controllers\Admin\SiteController::class, 'store'])->name('sites.create');

    // Dynamic Form Fields
    Route::prefix('fields')->name('fields.')->group(function () {
        Route::get('/by-category/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'getFieldsByCategory'])->name('by-category');
        Route::get('/render/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'renderCategoryFields'])->name('render');
    });

    // Smart Categories API
    Route::prefix('smart-categories')->name('smart-categories.')->group(function () {
        Route::get('/default-types', [\App\Http\Controllers\Api\SearchController::class, 'getDefaultPropertyTypes'])->name('default-types');
        Route::get('/compatible-types', [\App\Http\Controllers\Api\SearchController::class, 'getCompatiblePropertyTypes'])->name('compatible-types');
    });

    // Nearby preview (admin form konum önizleme)
    Route::get('/nearby/preview', [\App\Http\Controllers\Admin\MapController::class, 'nearbyPreview'])->name('nearby.preview');

    // Categories children (alt kategoriler - canlı yükleme)
    Route::get('/categories/children', function (\Illuminate\Http\Request $request) {
        $parentId = $request->get('parent_id');
        if (! $parentId) {
            return response()->json(['success' => false, 'message' => 'parent_id required'], 400);
        }
        $children = \App\Models\IlanKategori::where('parent_id', $parentId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['success' => true, 'data' => $children]);
    })->name('categories.children');

    // ═══════════════════════════════════════════════════════════
    // 📸 PHOTO UPLOAD API (Context7 uyumlu)
    // ═══════════════════════════════════════════════════════════
    Route::prefix('photos')->name('photos.')->group(function () {
        Route::post('/upload', [\App\Http\Controllers\Admin\PhotoController::class, 'store'])->name('upload');
        Route::get('/{id}', [\App\Http\Controllers\Admin\PhotoController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\Admin\PhotoController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\PhotoController::class, 'destroy'])->name('destroy');
        Route::delete('/bulk-delete', [\App\Http\Controllers\Admin\PhotoController::class, 'bulkAction'])->name('bulk-delete');
    });
});

// API v1 Standardizasyon Grubu (geriye uyumluluk korunur)
Route::middleware(['web', 'auth', 'admin', 'throttle:60,1'])->prefix('admin/api/v1')->name('admin.api.v1.')->group(function () {
    // Konum servisleri
    Route::get('/location/ilceler', [\App\Http\Controllers\Admin\IlanController::class, 'getIlceler'])->name('location.ilceler');
    Route::get('/location/mahalleler', [\App\Http\Controllers\Admin\IlanController::class, 'getMahalleler'])->name('location.mahalleler');

    // Arama servisleri
    Route::get('/search/live', [\App\Http\Controllers\Admin\IlanController::class, 'liveSearch'])->name('search.live');

    // Özellik servisleri
    Route::get('/features', [\App\Http\Controllers\Api\FeatureController::class, 'index'])->name('features.index');
    Route::get('/features/category/{categoryId}', [\App\Http\Controllers\Admin\IlanController::class, 'getFeaturesByCategory'])
        ->whereNumber('categoryId')
        ->name('features.category');

    Route::prefix('ai')->name('ai.')->group(function () {
        Route::post('/suggest-title', [\App\Http\Controllers\Api\AIController::class, 'suggestTitle'])->name('suggest-title');
        Route::post('/generate-description', [\App\Http\Controllers\Api\AIController::class, 'generateDescription'])->name('generate-description');
        Route::post('/suggest-price', [\App\Http\Controllers\Api\AIController::class, 'suggestPrice'])->name('suggest-price');
        Route::get('/health', [\App\Http\Controllers\Api\AIController::class, 'health'])->name('health');

        Route::prefix('field-dependency')->name('field-dependency.')->group(function () {
            Route::get('/get-matrix/{kategoriSlug}', [\App\Http\Controllers\Api\FieldDependencyController::class, 'getMatrix'])->name('get-matrix');
            Route::post('/update', [\App\Http\Controllers\Api\FieldDependencyController::class, 'updateDependency'])->name('update');
            Route::post('/suggest', [\App\Http\Controllers\Api\FieldDependencyController::class, 'suggestField'])->name('suggest');
            Route::post('/auto-fill', [\App\Http\Controllers\Api\FieldDependencyController::class, 'autoFillAll'])->name('auto-fill');
            Route::post('/smart-calculate', [\App\Http\Controllers\Api\FieldDependencyController::class, 'smartCalculate'])->name('smart-calculate');
            Route::get('/{kategoriSlug}/{yayinTipi}', [\App\Http\Controllers\Api\FieldDependencyController::class, 'getFields'])->name('get-fields');
        });
    });

    Route::prefix('ai/image')->name('ai.image.')->group(function () {
        Route::post('/analyze', [\App\Http\Controllers\Api\ImageAIController::class, 'analyzeImage'])->name('analyze');
        Route::post('/generate-tags', [\App\Http\Controllers\Api\ImageAIController::class, 'generateTags'])->name('generate-tags');
        Route::post('/analyze-quality', [\App\Http\Controllers\Api\ImageAIController::class, 'analyzeQuality'])->name('analyze-quality');
        Route::post('/analyze-batch', [\App\Http\Controllers\Api\ImageAIController::class, 'analyzeBatch'])->name('analyze-batch');
    });

    Route::prefix('property-features')->name('property-features.')->group(function () {
        Route::get('/suggestions', [\App\Http\Controllers\Api\PropertyFeatureSuggestionController::class, 'getFeatureSuggestions'])->name('suggestions');
        Route::get('/smart-suggestions', [\App\Http\Controllers\Api\PropertyFeatureSuggestionController::class, 'getSmartSuggestions'])->name('smart-suggestions');
        Route::post('/validate', [\App\Http\Controllers\Api\PropertyFeatureSuggestionController::class, 'validateFeatures'])->name('validate');
    });

    Route::prefix('cevre-analizi')->name('cevre-analizi.')->group(function () {
        Route::post('/', [\App\Http\Controllers\Api\AkilliCevreAnaliziController::class, 'analyzeEnvironment'])->name('analyze');
        Route::get('/smart-recommendations', [\App\Http\Controllers\Api\AkilliCevreAnaliziController::class, 'getSmartRecommendations'])->name('smart-recommendations');
        Route::post('/calculate-distance', [\App\Http\Controllers\Api\AkilliCevreAnaliziController::class, 'calculateDistance'])->name('calculate-distance');
        Route::post('/search-poi', [\App\Http\Controllers\Api\AkilliCevreAnaliziController::class, 'searchPOI'])->name('search-poi');
    });

    Route::prefix('photos')->name('photos.')->group(function () {
        Route::post('/upload', [\App\Http\Controllers\Admin\PhotoController::class, 'store'])->name('upload');
        Route::get('/{id}', [\App\Http\Controllers\Admin\PhotoController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\Admin\PhotoController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\PhotoController::class, 'destroy'])->name('destroy');
        Route::delete('/bulk-delete', [\App\Http\Controllers\Admin\PhotoController::class, 'bulkAction'])->name('bulk-delete');
    });

    Route::prefix('fields')->name('fields.')->group(function () {
        Route::get('/by-category/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'getFieldsByCategory'])->name('by-category');
        Route::get('/render/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'renderCategoryFields'])->name('render');
    });

    Route::prefix('smart-categories')->name('smart-categories.')->group(function () {
        Route::get('/default-types', [\App\Http\Controllers\Api\SearchController::class, 'getDefaultPropertyTypes'])->name('default-types');
        Route::get('/compatible-types', [\App\Http\Controllers\Api\SearchController::class, 'getCompatiblePropertyTypes'])->name('compatible-types');
    });

    Route::prefix('calculator')->name('calculator.')->group(function () {
        Route::post('/calculate', function (\Illuminate\Http\Request $request) {
            $calculatorService = new \App\Services\SmartCalculatorService;
            $type = $request->input('type');
            $inputs = $request->input('inputs', []);
            try {
                $result = $calculatorService->calculate($type, $inputs);
                return \App\Services\Response\ResponseService::success($result, 'Hesaplama başarıyla tamamlandı');
            } catch (\Exception $e) {
                return \App\Services\Response\ResponseService::error($e->getMessage(), 400);
            }
        })->name('calculate');

        Route::get('/history', function (\Illuminate\Http\Request $request) {
            $calculatorService = new \App\Services\SmartCalculatorService;
            $type = $request->get('type');
            $limit = $request->get('limit', 20);
            $history = $calculatorService->getHistory($type, $limit);
            return \App\Services\Response\ResponseService::success($history, 'Hesaplama geçmişi', 200, ['total' => is_countable($history) ? count($history) : 0]);
        })->name('history');

        Route::get('/favorites', function () {
            $calculatorService = new \App\Services\SmartCalculatorService;
            $favorites = $calculatorService->getFavorites();
            return \App\Services\Response\ResponseService::success($favorites, 'Favori hesaplamalar', 200, ['total' => is_countable($favorites) ? count($favorites) : 0]);
        })->name('favorites');

        Route::post('/favorites', function (\Illuminate\Http\Request $request) {
            $calculatorService = new \App\Services\SmartCalculatorService;
            $type = $request->input('type');
            $name = $request->input('name');
            $inputs = $request->input('inputs', []);
            $description = $request->input('description');
            $success = $calculatorService->addFavorite($type, $name, $inputs, $description);
            if ($success) {
                return \App\Services\Response\ResponseService::success(null, 'Favori hesaplama eklendi');
            }
            return \App\Services\Response\ResponseService::error('Favori hesaplama eklenemedi', 400);
        })->name('favorites.store');

        Route::delete('/favorites/{id}', function ($id) {
            $calculatorService = new \App\Services\SmartCalculatorService;
            $success = $calculatorService->removeFavorite($id);
            if ($success) {
                return \App\Services\Response\ResponseService::success(null, 'Favori hesaplama silindi');
            }
            return \App\Services\Response\ResponseService::error('Favori hesaplama silinemedi', 400);
        })->name('favorites.destroy');

        Route::get('/tax-rates', function (\Illuminate\Http\Request $request) {
            $calculatorService = new \App\Services\SmartCalculatorService;
            $type = $request->get('type');
            $rates = $calculatorService->getTaxRates($type);
            return \App\Services\Response\ResponseService::success($rates, 'Vergi oranları');
        })->name('tax-rates');

        Route::get('/commission-rates', function (\Illuminate\Http\Request $request) {
            $calculatorService = new \App\Services\SmartCalculatorService;
            $type = $request->get('type');
            $rates = $calculatorService->getCommissionRates($type);
            return \App\Services\Response\ResponseService::success($rates, 'Komisyon oranları');
        })->name('commission-rates');

        Route::get('/types', function () {
            $calculatorService = new \App\Services\SmartCalculatorService;
            $types = $calculatorService->getCalculationTypes();
            return \App\Services\Response\ResponseService::success($types, 'Hesaplama türleri', 200, ['total' => is_countable($types) ? count($types) : 0]);
        })->name('types');
    });

    Route::prefix('sites')->name('sites.')->group(function () {
        Route::get('/search', [\App\Http\Controllers\Admin\SiteController::class, 'search'])->name('search');
        Route::get('/{id}', [\App\Http\Controllers\Admin\SiteController::class, 'show'])->name('detail');
        Route::post('/create', [\App\Http\Controllers\Admin\SiteController::class, 'store'])->name('create');
    });
});
    Route::get('/kisiler', function (\Illuminate\Http\Request $request) {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = max(1, min($perPage, 100));
        $data = \App\Models\Kisi::where('status', 'Aktif')
            ->select('id', 'ad', 'soyad', 'telefon')
            ->orderByDesc('created_at')
            ->paginate($perPage);
        return \App\Services\Response\ResponseService::success($data, 'Kişiler listesi');
    })->name('kisiler');

    Route::get('/danismanlar', function (\Illuminate\Http\Request $request) {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = max(1, min($perPage, 100));
        $data = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->where('status', 1)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->paginate($perPage);
        return \App\Services\Response\ResponseService::success($data, 'Danışmanlar listesi');
    })->name('danismanlar');

    Route::get('/talepler', function (\Illuminate\Http\Request $request) {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = max(1, min($perPage, 100));
        $data = \App\Models\Talep::where('status', 'Aktif')
            ->select('id', 'talep_adi', 'kategori_id')
            ->with('kategori:id,name')
            ->orderByDesc('created_at')
            ->paginate($perPage);
        return \App\Services\Response\ResponseService::success($data, 'Talepler listesi');
    })->name('talepler');

    Route::get('/ilanlar', function (\Illuminate\Http\Request $request) {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = max(1, min($perPage, 100));
        $data = \App\Models\Ilan::where('status', 'Aktif')
            ->select('id', 'title', 'kategori_id', 'fiyat', 'para_birimi')
            ->with('kategori:id,name')
            ->orderByDesc('created_at')
            ->paginate($perPage);
        return \App\Services\Response\ResponseService::success($data, 'İlanlar listesi');
    })->name('ilanlar');
