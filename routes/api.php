<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\IlanSearchController;
use App\Http\Controllers\Api\CurrencyRateController;
use App\Http\Controllers\Api\UnifiedSearchController;
use App\Http\Controllers\Api\EnvironmentAnalysisController;
use App\Http\Controllers\Api\PhotoController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\SeasonController;
use App\Http\Controllers\Api\BookingRequestController;
use App\Http\Controllers\Api\BulkOperationsController;
use App\Http\Controllers\Api\ExchangeRateController;
use App\Http\Controllers\Api\Frontend\PropertyFeedController;
use App\Http\Controllers\Api\GeoProxyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Health check
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is healthy',
        'timestamp' => now()->toISOString()
    ]);
});

// Geo Proxy (Context7) - Nominatim/Overpass via server with cache
Route::prefix('geo')->group(function () {
    Route::post('/geocode', [GeoProxyController::class, 'geocode']);
    Route::post('/reverse-geocode', [GeoProxyController::class, 'reverseGeocode']);
    Route::get('/nearby', [GeoProxyController::class, 'nearby']);
});

// Feature API Routes (Modal Selector) - MOVED TO web.php (Blade template'lerinden çağrılıyor, CSRF token gerekiyor)

// QR Code API Routes
Route::prefix('qrcode')->middleware(['web'])->group(function () {
    Route::get('/listing/{ilanId}', [\App\Http\Controllers\Api\QRCodeController::class, 'generateForListing'])->name('api.qrcode.listing');
    Route::get('/whatsapp/{ilanId}', [\App\Http\Controllers\Api\QRCodeController::class, 'generateForWhatsApp'])->name('api.qrcode.whatsapp');
    Route::get('/ai-suggestions/{ilanId}', [\App\Http\Controllers\Api\QRCodeController::class, 'getAISuggestions'])->name('api.qrcode.ai-suggestions');
    Route::get('/statistics', [\App\Http\Controllers\Api\QRCodeController::class, 'getStatistics'])->name('api.qrcode.statistics')->middleware('auth');
});

// Listing Navigation API Routes
Route::prefix('navigation')->middleware(['web'])->group(function () {
    Route::get('/listing/{ilanId}', [\App\Http\Controllers\Api\ListingNavigationController::class, 'getNavigation'])->name('api.navigation.listing');
    Route::get('/similar/{ilanId}', [\App\Http\Controllers\Api\ListingNavigationController::class, 'getSimilar'])->name('api.navigation.similar');
    Route::get('/ai-suggestions/{ilanId}', [\App\Http\Controllers\Api\ListingNavigationController::class, 'getAISuggestions'])->name('api.navigation.ai-suggestions');
});

// Frontend Property Feed API
Route::prefix('frontend/properties')->name('api.frontend.properties.')->group(function () {
    Route::get('/', [PropertyFeedController::class, 'index'])->name('index');
    Route::get('/featured', [PropertyFeedController::class, 'featured'])->name('featured');
    Route::get('/{propertyId}', [PropertyFeedController::class, 'show'])->name('show');
});

// PHASE 2.3: Bulk Operations API (Context7 Compliant)
Route::prefix('admin/bulk')->middleware(['web', 'auth'])->group(function () {
    Route::post('/assign-category', [BulkOperationsController::class, 'assignCategory'])->name('api.bulk.assign-category');
    Route::post('/toggle-status', [BulkOperationsController::class, 'toggleStatus'])->name('api.bulk.toggle-status');
    Route::post('/delete', [BulkOperationsController::class, 'bulkDelete'])->name('api.bulk.delete');
    Route::post('/reorder', [BulkOperationsController::class, 'reorder'])->name('api.bulk.reorder');
});

// ✅ Category API - DEPRECATED: Closure routes kaldırıldı, CategoriesController kullanılmalı
// Alt kategori ve yayın tipi endpoint'leri artık CategoriesController'da:
// - /api/categories/sub/{parentId} → CategoriesController::getSubcategories()
// - /api/categories/publication-types/{categoryId} → CategoriesController::getPublicationTypes()

// Location API (Context7 Standard - İzolasyon Sistemi)
Route::get('/ilceler', function () {
    $ilceler = \App\Models\Ilce::with('il')->orderBy('ilce_adi')->get(['id', 'il_id', 'ilce_adi']);
    return response()->json([
        'success' => true,
        'data' => $ilceler,
        'districts' => $ilceler // Context7: Dual format for compatibility
    ]);
});
Route::get('/ilceler/{ilId}', function ($ilId) {
    $ilceler = \App\Models\Ilce::where('il_id', $ilId)->orderBy('ilce_adi')->get(['id', 'il_id', 'ilce_adi']);
    return response()->json([
        'success' => true,
        'data' => $ilceler,
        'districts' => $ilceler // Context7: Dual format for compatibility
    ]);
});
Route::get('/mahalleler', function () {
    $mahalleler = \App\Models\Mahalle::with('ilce')->orderBy('mahalle_adi')->get(['id', 'ilce_id', 'mahalle_adi']);
    return response()->json([
        'success' => true,
        'data' => $mahalleler,
        'neighborhoods' => $mahalleler // Context7: Dual format for compatibility
    ]);
});
Route::get('/mahalleler/{ilceId}', function ($ilceId) {
    $mahalleler = \App\Models\Mahalle::where('ilce_id', $ilceId)->orderBy('mahalle_adi')->get(['id', 'ilce_id', 'mahalle_adi']);
    return response()->json([
        'success' => true,
        'data' => $mahalleler,
        'neighborhoods' => $mahalleler // Context7: Dual format for compatibility
    ]);
});

// Site/Apartman Live Search API (Public - no auth required)
// Context7: C7-SITE-LIVE-SEARCH-2025-10-17 + İzolasyon Sistemi
Route::get('/sites/search', [\App\Http\Controllers\Admin\SiteController::class, 'search'])->name('api.sites.search');
Route::get('/site-apartman/search', function (\Illuminate\Http\Request $request) {
    // Context7: Dual endpoint for compatibility (site-apartman-selection.blade.php uses this)
    $controller = app(\App\Http\Controllers\Admin\SiteController::class);
    return $controller->search($request);
})->name('api.site-apartman.search');

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/sites/detail/{id}', [\App\Http\Controllers\Admin\SiteController::class, 'show'])->name('api.sites.detail');
});

// Page Analyzer API
Route::prefix('admin/page-analyzer')->name('admin.page-analyzer.api.')->group(function () {
    Route::post('/analyze', [\App\Http\Controllers\Admin\PageAnalyzerController::class, 'analyze'])->name('analyze');
    Route::get('/export/{id?}', [\App\Http\Controllers\Admin\PageAnalyzerController::class, 'export'])->name('export');
    Route::post('/rerun/{id}', [\App\Http\Controllers\Admin\PageAnalyzerController::class, 'rerun'])->name('rerun');
});

// Currency Rate API (Real-time döviz kurları)
Route::prefix('currency')->group(function () {
    Route::get('/rates', [CurrencyRateController::class, 'getRates']);
    Route::post('/convert', [CurrencyRateController::class, 'convert']);
    Route::get('/supported', [CurrencyRateController::class, 'getSupportedCurrencies']);
    Route::post('/refresh', [CurrencyRateController::class, 'refresh']);
});

// İlan Arama API (Context7 uyumlu)
Route::prefix('ilanlar')->group(function () {
    // Unified search
    Route::get('/search', [IlanSearchController::class, 'search']);

    // Özel arama endpoint'leri
    Route::get('/by-referans/{referansNo}', [IlanSearchController::class, 'findByReferans']);
    Route::get('/by-telefon', [IlanSearchController::class, 'findByTelefon']);
    Route::get('/by-portal', [IlanSearchController::class, 'findByPortalId']);
    Route::get('/by-site', [IlanSearchController::class, 'findBySite']);
});

// AI API Routes (CSRF exempt)
Route::prefix('admin/ai')->group(function () {
    Route::post('/analyze', [\App\Http\Controllers\Api\AIController::class, 'analyze']);
    Route::post('/suggest', [\App\Http\Controllers\Api\AIController::class, 'suggest']);
    Route::post('/generate', [\App\Http\Controllers\Api\AIController::class, 'generate']);
    Route::get('/health', [\App\Http\Controllers\Api\AIController::class, 'healthCheck']);
    Route::get('/providers', [\App\Http\Controllers\Api\AIController::class, 'getProviders']);
    Route::post('/switch-provider', [\App\Http\Controllers\Api\AIController::class, 'switchProvider']);
    Route::get('/stats', [\App\Http\Controllers\Api\AIController::class, 'getStats']);
    Route::get('/logs', [\App\Http\Controllers\Api\AIController::class, 'getLogs']);

    // 🤖 Talepler Create - AI Assistant Endpoints (2025-11-01)
    Route::post('/suggest-price', [\App\Http\Controllers\Api\AIController::class, 'suggestPrice']);
    Route::post('/find-matches', [\App\Http\Controllers\Api\AIController::class, 'findMatches']);
    Route::post('/generate-description', [\App\Http\Controllers\Api\AIController::class, 'generateDescription']);
});

// AI Assist Routes for İlan Creation (CSRF exempt)
Route::prefix('admin/ai-assist')->group(function () {
    Route::post('/auto-categorize', [\App\Http\Controllers\Api\IlanAIController::class, 'autoDetectCategory']);
    Route::post('/price-suggest', [\App\Http\Controllers\Api\IlanAIController::class, 'suggestOptimalPrice']);
    Route::post('/description-generate', [\App\Http\Controllers\Api\IlanAIController::class, 'generateDescription']);
    Route::post('/seo-optimize', [\App\Http\Controllers\Api\IlanAIController::class, 'optimizeForSEO']);
    Route::post('/image-analyze', [\App\Http\Controllers\Api\IlanAIController::class, 'analyzeUploadedImages']);
});

// AI Analysis API (CSRF exempt)
Route::post('/admin/ilan-kategorileri/ai-analysis', [\App\Http\Controllers\Admin\IlanKategoriController::class, 'aiAnalysis']);

// Feature API (CSRF exempt) - REMOVED: Duplicate routes, using routes/api.php:33 instead
// Route::prefix('admin/features')->group(function () {
//     Route::get('/category/{categoryId}', [\App\Http\Controllers\Api\FeatureController::class, 'getFeaturesByCategory']);
//     Route::get('/categories', [\App\Http\Controllers\Api\FeatureController::class, 'getCategories']);
//     Route::get('/suggest', [\App\Http\Controllers\Api\FeatureController::class, 'suggestFeatures']);
// });

// Kişi CRM API (Context7 uyumlu)
Route::prefix('kisiler')->group(function () {
    // ✅ Context7 & Yalıhan Bekçi: Live Search endpoint'i
    Route::get('/search', [\App\Http\Controllers\Api\KisiController::class, 'search'])->name('api.kisiler.search');

    // İlan geçmişi ve analizi
    Route::get('/{id}/ilan-gecmisi', [\App\Http\Controllers\Api\KisiController::class, 'getIlanGecmisi']);

    // Profil bilgisi
    Route::get('/{id}/profil', [\App\Http\Controllers\Api\KisiController::class, 'getProfil']);

    // AI İlan Geçmişi Analizi (Context7 Kural #69)
    Route::get('/{id}/ai-gecmis-analiz', [\App\Http\Controllers\Api\KisiController::class, 'getAIGecmisAnaliz']);

    // Kişi oluştur (modal'dan) - KisiController kullan (mevcut)
    Route::post('/', [\App\Http\Controllers\Api\KisiController::class, 'store']);
});

// Users API (Sistem Danışmanları - users tablosu)
Route::prefix('users')->group(function () {
    // Danışman araması (Context7 Live Search için)
    Route::get('/search', [\App\Http\Controllers\Api\UserController::class, 'search'])->name('api.users.search');

    // Tüm danışmanlar
    Route::get('/danismanlar', [\App\Http\Controllers\Api\UserController::class, 'danismanlar'])->name('api.users.danismanlar');
});

// Takvim Senkronizasyon API
Route::prefix('admin/calendars')->middleware(['web', 'auth'])->group(function () {
    Route::get('/{ilan}/syncs', [\App\Http\Controllers\Admin\CalendarSyncController::class, 'getSyncs']);
    Route::post('/{ilan}/syncs', [\App\Http\Controllers\Admin\CalendarSyncController::class, 'createSync']);
    Route::post('/{ilan}/syncs/{sync}', [\App\Http\Controllers\Admin\CalendarSyncController::class, 'updateSync']);
    Route::delete('/{ilan}/syncs/{sync}', [\App\Http\Controllers\Admin\CalendarSyncController::class, 'deleteSync']);
    Route::post('/{ilan}/manual-sync', [\App\Http\Controllers\Admin\CalendarSyncController::class, 'manualSync']);
    Route::get('/{ilan}/calendar', [\App\Http\Controllers\Admin\CalendarSyncController::class, 'getCalendar']);
    Route::post('/{ilan}/block', [\App\Http\Controllers\Admin\CalendarSyncController::class, 'blockDates']);
});

// TKGM Parsel Sorgulama API - Geçici Endpoint (Context7 Kural #70)
Route::prefix('admin/api/tkgm-parsel')->middleware(['web', 'auth', 'throttle:20,1'])->group(function () {
    Route::post('/query', [\App\Http\Controllers\Admin\TKGMParselController::class, 'query'])->name('api.tkgm-parsel.query');
    Route::post('/bulk-query', [\App\Http\Controllers\Admin\TKGMParselController::class, 'bulkQuery'])->name('api.tkgm-parsel.bulk-query');
    Route::get('/history', [\App\Http\Controllers\Admin\TKGMParselController::class, 'history'])->name('api.tkgm-parsel.history');
    Route::get('/stats', [\App\Http\Controllers\Admin\TKGMParselController::class, 'stats'])->name('api.tkgm-parsel.stats');
});

// TKGM Parsel Sorgulama API (Context7 Kural #70)
Route::prefix('tkgm')->middleware(['throttle:20,1'])->group(function () {
    // Parsel sorgulama
    Route::post('/parsel-sorgu', [\App\Http\Controllers\Api\TKGMController::class, 'parselSorgula']);

    // Yatırım analizi
    Route::post('/yatirim-analizi', [\App\Http\Controllers\Api\TKGMController::class, 'yatirimAnalizi']);

    // Health check
    Route::get('/health', [\App\Http\Controllers\Api\TKGMController::class, 'healthCheck']);

    // Cache temizle (admin only)
    Route::post('/clear-cache', [\App\Http\Controllers\Api\TKGMController::class, 'clearCache'])
        ->middleware('can:admin');
});

// AI Suggestions API (Context7 uyumlu)
Route::prefix('ai')->middleware(['throttle:30,1'])->group(function () {
    // AI Health Check (Context7 v3.5.0 - Stable-Create Enhancement)
    Route::get('/health', [\App\Http\Controllers\Api\AiHealthController::class, 'health']);

    // AI İlan Analizi (Context7 uyumlu)
    Route::post('/analyze-listing', function (Request $request) {
        try {
            // Form verilerini al
            $data = $request->all();

            // Simulated AI analysis delay
            sleep(1);

            // AI analiz sonuçları (simulated)
            $analysis = [
                'price_analysis' => [
                    'suggested_price' => rand(800000, 1500000),
                    'market_comparison' => 'Bölge ortalamalarına uygun',
                    'price_confidence' => rand(75, 95) . '%'
                ],
                'title_suggestions' => [
                    'Merkezi Konumda Satılık ' . ($data['category'] ?? 'Daire'),
                    'Yatırım Fırsatı ' . ($data['category'] ?? 'Emlak'),
                    'Özel Tasarım ' . ($data['category'] ?? 'Konut')
                ],
                'description_improvements' => [
                    'Lokasyon avantajları vurgulanabilir',
                    'Yatırım potansiyeli eklenebilir',
                    'Çevre imkanları detaylandırılabilir'
                ],
                'seo_keywords' => [
                    'satılık daire',
                    'merkezi konum',
                    'yatırım fırsatı',
                    'modern tasarım'
                ],
                'market_insights' => [
                    'Bu bölgede talep yüksek',
                    'Benzer ilanlar 15 gün içinde satılıyor',
                    'Fiyat artış trendi: %8 (yıllık)'
                ]
            ];

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'message' => 'AI analizi tamamlandı',
                'processing_time' => '1.2s'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI analizi sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    });

    Route::post('/suggestions', function (Request $request) {
        return response()->json([
            'success' => true,
            'suggestions' => [
                'title' => 'AI ile ilan başlığı önerisi',
                'description' => 'AI ile ilan açıklaması önerisi',
                'keywords' => ['emlak', 'satılık', 'kiralık', 'ev', 'daire']
            ],
            'message' => 'AI suggestions endpoint aktif (Context7 uyumlu)'
        ]);
    });
});

// Categories API (Context7 uyumlu)
Route::prefix('categories')->group(function () {
    // Get subcategories by parent category ID
    Route::get('/sub/{id}', function ($id) {
        try {
            // ✅ Context7: Gerçek kategori verilerini getir
            $subcategories = \App\Models\IlanKategori::where('parent_id', $id)
                ->where('status', true)
                ->orderBy('name')
                ->get(['id', 'name', 'parent_id']);

            return response()->json([
                'success' => true,
                'subcategories' => $subcategories,
                'count' => $subcategories->count(),
                'message' => 'Subcategories loaded (Context7 uyumlu)'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Alt kategoriler yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    });

    // Get publication types by category ID (Context7 - Fixed Logic)
    Route::get('/publication-types/{id}', function ($id) {
        try {
            \Illuminate\Support\Facades\Log::info("🔍 Getting publication types", ['kategori_id' => $id]);

            // ✅ Context7: Önce kategoriyi çek
            $kategori = \App\Models\IlanKategori::find($id);

            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori bulunamadı'
                ], 404);
            }

            // ✅ FIX: Alt kategori ise parent'ın yayın tiplerini kullan!
            $targetKategoriId = $kategori->parent_id ?: $id;

            \Illuminate\Support\Facades\Log::info("🎯 Yayın tipi arama", [
                'original_id' => $id,
                'target_id' => $targetKategoriId,
                'kategori_name' => $kategori->name,
                'parent_id' => $kategori->parent_id,
                'seviye' => $kategori->seviye
            ]);

            // ✅ Yayın tiplerini parent'tan çek
            $publicationTypes = \App\Models\IlanKategoriYayinTipi::where('kategori_id', $targetKategoriId)
                ->where('status', 1) // ✅ Status artık TINYINT(1) - migration uygulandı
                ->orderBy('display_order') // Context7: order → display_order
                ->orderBy('yayin_tipi')
                ->get();

            \Illuminate\Support\Facades\Log::info("✅ Yayın tipleri bulundu", [
                'count' => $publicationTypes->count(),
                'types' => $publicationTypes->pluck('yayin_tipi')->toArray()
            ]);

            return response()->json([
                'success' => true,
                'types' => $publicationTypes->map(function ($type) {
                    return [
                        'id' => $type->id,
                        'name' => $type->yayin_tipi,
                    ];
                }),
                'count' => $publicationTypes->count(),
                'message' => $publicationTypes->isEmpty()
                    ? 'Bu kategori için yayın tipi bulunamadı'
                    : 'Yayın tipleri yüklendi',
                'debug' => [
                    'kategori_name' => $kategori->name,
                    'seviye' => $kategori->seviye,
                    'parent_id' => $kategori->parent_id,
                    'target_kategori_id' => $targetKategoriId
                ]
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Yayın tipi yükleme hatası:', [
                'kategori_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Yayın tipleri yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    });
});

// Kişi Search API (Context7 uyumlu)
Route::prefix('kisiler')->group(function () {
    Route::get('/search', function (\Illuminate\Http\Request $request) {
        try {
            $query = $request->input('q', '');
            $limit = $request->input('limit', 20);
            $filterType = $request->input('filter_type', ''); // Danışman filtresi için

            // Context7: Tabloda 'kisi_tipi' var (NOT musteri_tipi!)
            $kisilerQuery = \App\Models\Kisi::where(function ($q) {
                $q->where('status', 'Aktif')
                    ->orWhere('status', '1')
                    ->orWhere('status', 1);
            });

            // Eğer filter_type varsa (örn: Danışman) ve veritabanında yoksa, tüm kişileri göster
            if ($filterType && $filterType !== '') {
                // Danışman tipi filtresi (eğer varsa uygulanır)
                // Şimdilik tüm kişiler gösterilecek çünkü veritabanında Danışman tipi yok
                // $kisilerQuery->where('kisi_tipi', $filterType);
            }

            $kisiler = $kisilerQuery
                ->where(function ($q) use ($query) {
                    $q->where('ad', 'like', "%{$query}%")
                        ->orWhere('soyad', 'like', "%{$query}%")
                        ->orWhere('telefon', 'like', "%{$query}%")
                        ->orWhereRaw("CONCAT(ad, ' ', soyad) like ?", ["%{$query}%"]);
                })
                ->limit($limit)
                ->get(['id', 'ad', 'soyad', 'telefon', 'email', 'kisi_tipi']); // ✅ kisi_tipi (DOĞRU kolon adı!)

            return response()->json([
                'success' => true,
                'data' => $kisiler->map(function ($kisi) {
                    /** @var \App\Models\Kisi $kisi */
                    return [
                        'id' => $kisi->id,
                        'text' => $kisi->ad . ' ' . $kisi->soyad . ' - ' . $kisi->telefon,
                        'ad' => $kisi->ad,
                        'soyad' => $kisi->soyad,
                        'telefon' => $kisi->telefon,
                        'kisi_tipi' => $kisi->kisi_tipi instanceof \App\Enums\KisiTipi
                            ? $kisi->kisi_tipi->label()
                            : ($kisi->kisi_tipi ?? null), // Enum'dan label'a çevir veya direkt string kullan
                    ];
                }),
                'count' => $kisiler->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kişi arama hatası: ' . $e->getMessage()
            ], 500);
        }
    });
});

// İlan Search API (Context7 uyumlu - Liste sayfası için)
Route::prefix('ilanlar')->group(function () {
    Route::get('/search', function (\Illuminate\Http\Request $request) {
        try {
            $query = $request->input('q', '');
            $limit = $request->input('limit', 15);

            // Context7: Sadece tabloda VAR olan kolonları SELECT et!
            $ilanlar = \App\Models\Ilan::with(['anaKategori', 'altKategori', 'il', 'ilce'])
                ->where(function ($q) use ($query) {
                    $q->where('baslik', 'like', "%{$query}%")
                        ->orWhere('referans_no', 'like', "%{$query}%")
                        ->orWhereHas('ilanSahibi', function ($subQ) use ($query) {
                            $subQ->where('ad', 'like', "%{$query}%")
                                ->orWhere('soyad', 'like', "%{$query}%");
                        });
                })
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $ilanlar->map(function ($ilan) {
                    return [
                        'id' => $ilan->id,
                        'text' => $ilan->baslik . ($ilan->referans_no ? ' (' . $ilan->referans_no . ')' : ''),
                        'baslik' => $ilan->baslik,
                        'referans_no' => $ilan->referans_no,
                        'kategori' => $ilan->altKategori->name ?? '',
                        'lokasyon' => ($ilan->il->name ?? '') . ', ' . ($ilan->ilce->name ?? ''),
                        'fiyat' => number_format($ilan->fiyat, 0, ',', '.') . ' ' . $ilan->para_birimi,
                    ];
                }),
                'count' => $ilanlar->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İlan arama hatası: ' . $e->getMessage()
            ], 500);
        }
    });
});

// Site/Apartman Search API (Context7 uyumlu - Kişi ile aynı mantık!)
Route::prefix('sites')->group(function () {
    Route::get('/search', function (\Illuminate\Http\Request $request) {
        try {
            $query = $request->input('q', '');
            $limit = $request->input('limit', 20);

            // Context7: Sadece tabloda VAR olan ve GEREKLİ kolonları SELECT et!
            $sites = \App\Models\SiteApartman::where('name', 'like', "%{$query}%")
                ->orWhere('adres', 'like', "%{$query}%")
                ->limit($limit)
                ->get(['id', 'name', 'adres', 'toplam_daire_sayisi']); // ✅ Sadece gerekli kolonlar

            return response()->json([
                'success' => true,
                'data' => $sites->map(function ($site) {
                    return [
                        'id' => $site->id,
                        'text' => $site->name . ($site->adres ? ' - ' . $site->adres : ''),
                        'name' => $site->name,
                        'adres' => $site->adres,
                        'daire_sayisi' => $site->toplam_daire_sayisi,
                    ];
                }),
                'count' => $sites->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Site arama hatası: ' . $e->getMessage()
            ], 500);
        }
    });
});

// Categories API (Context7 uyumlu)
Route::prefix('categories')->group(function () {
    // Get subcategories for a parent category
    Route::get('/sub/{parentId}', function ($parentId) {
        try {
            $subcategories = \App\Models\IlanKategori::where('parent_id', $parentId)
                ->where('seviye', 1)
                ->where('status', true)
                ->orderBy('display_order') // Context7: order → display_order
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'icon']);

            return response()->json([
                'success' => true,
                'subcategories' => $subcategories->map(function ($cat) {
                    return [
                        'id' => $cat->id,
                        'name' => $cat->name,
                        'slug' => $cat->slug,
                        'icon' => $cat->icon
                    ];
                }),
                'count' => $subcategories->count(),
                'message' => $subcategories->isEmpty() ? 'Bu kategori için alt kategori bulunamadı' : 'Alt kategoriler yüklendi'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Alt kategori yükleme hatası:', [
                'parent_id' => $parentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Alt kategoriler yüklenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    });

    Route::get('/fields/{ana_kategori_id}/{alt_kategori_id}/{yayin_tipi_id}', function ($ana_kategori_id, $alt_kategori_id, $yayin_tipi_id) {
        try {
            // Kategori bazlı dinamik alanları döndür
            $fields = [];

            // Ana kategori kontrolü
            if ($ana_kategori_id == 1) { // Konut
                if ($alt_kategori_id == 1) { // Villa
                    $fields = [
                        'oda_sayisi' => ['required' => true, 'label' => 'Oda Sayısı'],
                        'banyo_sayisi' => ['required' => true, 'label' => 'Banyo Sayısı'],
                        'arsa_m2' => ['required' => true, 'label' => 'Arsa m²'],
                        'bina_m2' => ['required' => true, 'label' => 'Bina m²'],
                        'havuz' => ['required' => false, 'label' => 'Havuz'],
                        'bahce' => ['required' => false, 'label' => 'Bahçe'],
                    ];
                } elseif ($alt_kategori_id == 2) { // Daire
                    $fields = [
                        'oda_sayisi' => ['required' => true, 'label' => 'Oda Sayısı'],
                        'banyo_sayisi' => ['required' => true, 'label' => 'Banyo Sayısı'],
                        'net_m2' => ['required' => true, 'label' => 'Net m²'],
                        'brut_m2' => ['required' => false, 'label' => 'Brüt m²'],
                        'kat' => ['required' => false, 'label' => 'Kat'],
                        'toplam_kat' => ['required' => false, 'label' => 'Toplam Kat'],
                    ];
                }
            } elseif ($ana_kategori_id == 2) { // Arsa
                $fields = [
                    'ada_no' => ['required' => true, 'label' => 'Ada No'],
                    'parsel_no' => ['required' => true, 'label' => 'Parsel No'],
                    'imar_durumu' => ['required' => true, 'label' => 'İmar Durumu'],
                    'taks' => ['required' => false, 'label' => 'TAKS'],
                    'kaks' => ['required' => false, 'label' => 'KAKS'],
                ];
            }

            return response()->json([
                'success' => true,
                'fields' => $fields,
                'category_info' => [
                    'ana_kategori_id' => $ana_kategori_id,
                    'alt_kategori_id' => $alt_kategori_id,
                    'yayin_tipi_id' => $yayin_tipi_id,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Kategori alanları yüklenemedi',
                'fields' => []
            ]);
        }
    });
});

// Locations API (Context7 uyumlu)
Route::prefix('location')->group(function () {
    // ✅ Context7: LocationController kullan (doğru format: name field)
    Route::get('/districts/{id}', [\App\Http\Controllers\Api\LocationController::class, 'getDistrictsByProvince']);
    Route::get('/neighborhoods/{id}', [\App\Http\Controllers\Api\LocationController::class, 'getNeighborhoodsByDistrict']);

    // İller endpoint'i (JavaScript'ten çağrılıyor)
    Route::get('/iller', function () {
        $result = app('App\Http\Controllers\Api\ListingSearchController')->getProvinces();
        $data = json_decode($result->getContent(), true);

        return response()->json([
            'success' => $data['success'],
            'iller' => $data['data'] ?? []
        ]);
    });

    // İlçeler endpoint'i (JavaScript'ten çağrılıyor)
    Route::get('/ilceler/{id}', function ($id) {
        return app('App\Http\Controllers\Api\ListingSearchController')->getDistricts($id);
    });

    // Mahalleler endpoint'i (JavaScript'ten çağrılıyor)
    Route::get('/mahalleler/{id}', function ($id) {
        return app('App\Http\Controllers\Api\ListingSearchController')->getNeighborhoods($id);
    });

    // 📍 Gelişmiş Lokasyon API (Context7 Kural #75)
    Route::post('/geocode', [App\Http\Controllers\Api\LocationController::class, 'geocode']);
    Route::post('/reverse-geocode', [App\Http\Controllers\Api\LocationController::class, 'reverseGeocode']);
    Route::get('/nearby/{lat}/{lng}/{radius?}', [App\Http\Controllers\Api\LocationController::class, 'findNearby']);
    Route::post('/validate-address', [App\Http\Controllers\Api\LocationController::class, 'validateAddress']);
});

// Unified Search Routes
Route::prefix('api/search')->group(function () {
    Route::get('/unified', [UnifiedSearchController::class, 'search']);
    Route::get('/suggestions', [UnifiedSearchController::class, 'suggestions']);
    Route::get('/analytics', [UnifiedSearchController::class, 'analytics']);
    Route::post('/cache', [UnifiedSearchController::class, 'updateCache']);
});

// Features API Routes
Route::prefix('features')->group(function () {
    Route::get('/category/{categoryId}', [\App\Http\Controllers\Api\FeaturesController::class, 'getFeaturesByCategory'])
        ->name('api.features.category');
});

// ✅ Context7: Categories API Routes (Standardized)
Route::prefix('categories')->group(function () {
    // ✅ Alt kategorileri getir (Ana kategori ID'sine göre)
    Route::get('/sub/{parentId}', [\App\Http\Controllers\Api\CategoriesController::class, 'getSubcategories'])
        ->name('api.categories.subcategories');

    // ✅ Yayın tiplerini getir (Alt kategori ID'sine göre - ilan_kategori_yayin_tipleri tablosundan)
    Route::get('/publication-types/{categoryId}', [\App\Http\Controllers\Api\CategoriesController::class, 'getPublicationTypes'])
        ->name('api.categories.publication-types');
});

// ✅ Context7: Demirbaşlar API Routes (Hiyerarşik Yapı)
Route::prefix('demirbas')->group(function () {
    // ✅ Demirbaş kategorilerini getir (Kategori ve yayın tipi filtreleme ile)
    Route::get('/categories', [\App\Http\Controllers\Api\DemirbasController::class, 'getCategories'])
        ->name('api.demirbas.categories');
});

// Geocoding API Routes (CORS proxy)
Route::prefix('geocoding')->group(function () {
    Route::get('/search', [\App\Http\Controllers\Api\GeocodingController::class, 'search'])
        ->name('api.geocoding.search');
    Route::get('/reverse', [\App\Http\Controllers\Api\GeocodingController::class, 'reverse'])
        ->name('api.geocoding.reverse');
});

// Site/Apartman API Routes
Route::prefix('site-apartman')->group(function () {
    Route::get('/search', [\App\Http\Controllers\Api\SiteApartmanController::class, 'search'])
        ->name('api.site-apartman.search');
    Route::get('/{id}', [\App\Http\Controllers\Api\SiteApartmanController::class, 'show'])
        ->name('api.site-apartman.show');
});

// Site Özellikleri API Routes (Admin)
Route::prefix('admin/site-ozellikleri')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\SiteOzellikleriController::class, 'index'])
        ->name('admin.api.site-ozellikleri.index');
    Route::get('/active', [\App\Http\Controllers\Api\SiteOzellikleriController::class, 'active'])
        ->name('admin.api.site-ozellikleri.active');
});

// AI Content Generation API Routes - Admin Only
Route::prefix('ai')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/generate-titles', [\App\Http\Controllers\Api\AIContentController::class, 'generateTitles'])
        ->name('api.ai.generate-titles');
    Route::post('/generate-description', [\App\Http\Controllers\Api\AIContentController::class, 'generateDescription'])
        ->name('api.ai.generate-description');
    Route::post('/generate-features', [\App\Http\Controllers\Api\AIContentController::class, 'generateFeatures'])
        ->name('api.ai.generate-features');
    Route::post('/generate-seo', [\App\Http\Controllers\Api\AIContentController::class, 'generateSEO'])
        ->name('api.ai.generate-seo');
    Route::get('/status', [\App\Http\Controllers\Api\AIContentController::class, 'getStatus'])
        ->name('api.ai.status');
});

// 🎯 Environment Analysis API Routes (AI-Powered)
Route::prefix('environment')->middleware(['throttle:120,1'])->group(function () {
    Route::get('/analyze', [EnvironmentAnalysisController::class, 'analyze'])
        ->name('api.environment.analyze');

    Route::get('/category/{category}', [EnvironmentAnalysisController::class, 'analyzeCategory'])
        ->name('api.environment.category');

    Route::post('/value-prediction', [EnvironmentAnalysisController::class, 'predictLocationValue'])
        ->name('api.environment.value-prediction');
});

// Wikimapia API Test
Route::prefix('wikimapia')->name('wikimapia.')->group(function () {
    Route::get('/test', function () {
        $service = new \App\Services\WikimapiaService();

        // Test Istanbul coordinates
        $lat = 41.0082;
        $lon = 28.9744;

        // Test search
        $results = $service->searchPlaces('Kadıköy', $lat, $lon, ['count' => 5]);

        return response()->json([
            'success' => true,
            'data' => $results,
            'message' => 'Wikimapia API test successful'
        ]);
    });

    Route::get('/search', function (\Illuminate\Http\Request $request) {
        $service = new \App\Services\WikimapiaService();

        $query = $request->input('q', '');
        $lat = $request->input('lat', 41.0082);
        $lon = $request->input('lon', 28.9744);

        $results = $service->searchPlaces($query, $lat, $lon);

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    });

    Route::post('/places/nearby', function (\Illuminate\Http\Request $request) {
        $service = new \App\Services\WikimapiaService();

        $lat = $request->input('lat');
        $lon = $request->input('lon');
        $radius = $request->input('radius', 0.01); // ~1km

        $lonMin = $lon - $radius;
        $latMin = $lat - $radius;
        $lonMax = $lon + $radius;
        $latMax = $lat + $radius;

        $places = $service->getPlacesByArea($lonMin, $latMin, $lonMax, $latMax);

        return response()->json([
            'success' => true,
            'data' => $places
        ]);
    });
});

// 🆕 Field Dependencies API (Category-based dynamic fields system)
Route::prefix('admin')->group(function () {
    Route::get('/field-dependencies', [\App\Http\Controllers\Api\FieldDependencyController::class, 'index']);
    Route::get('/field-dependencies/category/{kategoriId}', [\App\Http\Controllers\Api\FieldDependencyController::class, 'getByCategory']);

    // Photo Management API (Context7 Compliant)
    Route::post('/photos/upload', [PhotoController::class, 'upload'])->name('api.photos.upload');
    Route::get('/ilanlar/{id}/photos', [PhotoController::class, 'index'])->name('api.ilanlar.photos');
    Route::patch('/photos/{id}', [PhotoController::class, 'update'])->name('api.photos.update');
    Route::delete('/photos/{id}', [PhotoController::class, 'destroy'])->name('api.photos.destroy');
    Route::post('/ilanlar/{id}/photos/reorder', [PhotoController::class, 'reorder'])->name('api.ilanlar.photos.reorder');

    // Event/Booking Management API (Context7 Compliant)
    Route::get('/ilanlar/{id}/events', [EventController::class, 'index'])->name('api.ilanlar.events');
    Route::post('/events', [EventController::class, 'store'])->name('api.events.store');
    Route::patch('/events/{id}', [EventController::class, 'update'])->name('api.events.update');
    Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('api.events.destroy');
    Route::post('/events/check-availability', [EventController::class, 'checkAvailability'])->name('api.events.check-availability');

    // Season Pricing Management API (Context7 Compliant)
    Route::get('/ilanlar/{id}/seasons', [SeasonController::class, 'index'])->name('api.ilanlar.seasons');
    Route::post('/seasons', [SeasonController::class, 'store'])->name('api.seasons.store');
    Route::patch('/seasons/{id}', [SeasonController::class, 'update'])->name('api.seasons.update');
    Route::delete('/seasons/{id}', [SeasonController::class, 'destroy'])->name('api.seasons.destroy');
    Route::post('/seasons/calculate-price', [SeasonController::class, 'calculatePrice'])->name('api.seasons.calculate-price');
});

// Public Booking Request API (Context7 Compliant)
Route::post('/booking-request', [BookingRequestController::class, 'store'])->name('api.booking-request');
Route::post('/check-availability', [BookingRequestController::class, 'checkAvailability'])->name('api.check-availability');
Route::post('/get-booking-price', [BookingRequestController::class, 'getPrice'])->name('api.get-booking-price');

// Arsa Calculation API Routes
Route::prefix('admin/api/arsa')->middleware(['web', 'auth'])->name('api.arsa.')->group(function () {
    Route::post('/calculate', [\App\Http\Controllers\Admin\ArsaCalculationController::class, 'calculate'])->name('calculate');
    Route::post('/tkgm-query', [\App\Http\Controllers\Admin\ArsaCalculationController::class, 'tkgmQuery'])->name('tkgm-query');
});

// Location API Routes (Context7 Standard)
require __DIR__ . '/api-location.php';

// Exchange Rates API (TCMB Integration - Context7 Compliant)
Route::prefix('exchange-rates')->group(function () {
    Route::get('/', [ExchangeRateController::class, 'index'])->name('api.exchange-rates.index');
    Route::get('/supported', [ExchangeRateController::class, 'supported'])->name('api.exchange-rates.supported');
    Route::get('/{code}', [ExchangeRateController::class, 'show'])->name('api.exchange-rates.show');
    Route::get('/{code}/history', [ExchangeRateController::class, 'history'])->name('api.exchange-rates.history');
    Route::post('/convert', [ExchangeRateController::class, 'convert'])->name('api.exchange-rates.convert');

    // Admin only - force update
    Route::post('/update', [ExchangeRateController::class, 'update'])
        ->middleware(['web', 'auth'])
        ->name('api.exchange-rates.update');
});

// Reference & File Management API (Context7 Compliant)
// Ref numarası, Basename, Portal numarası yönetimi
Route::prefix('reference')->middleware(['web', 'auth'])->group(function () {
    // Ref numarası oluştur
    Route::post('/generate', [\App\Http\Controllers\Api\ReferenceController::class, 'generateRef'])->name('api.reference.generate');

    // Ref numarası doğrula
    Route::get('/validate/{referansNo}', [\App\Http\Controllers\Api\ReferenceController::class, 'validateRef'])->name('api.reference.validate');

    // Basename oluştur/güncelle
    Route::post('/basename', [\App\Http\Controllers\Api\ReferenceController::class, 'generateBasename'])->name('api.reference.basename');

    // Portal numarası güncelle
    Route::post('/portal', [\App\Http\Controllers\Api\ReferenceController::class, 'updatePortalNumber'])->name('api.reference.portal');

    // İlan için ref ve basename bilgilerini getir
    Route::get('/{ilanId}', [\App\Http\Controllers\Api\ReferenceController::class, 'getReferenceInfo'])->name('api.reference.info');

    // Toplu ref numarası oluştur (batch)
    Route::post('/batch-generate', [\App\Http\Controllers\Api\ReferenceController::class, 'batchGenerateRef'])->name('api.reference.batch-generate');
});
