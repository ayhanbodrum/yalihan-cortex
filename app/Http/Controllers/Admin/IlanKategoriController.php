<?php

namespace App\Http\Controllers\Admin;

use App\Enums\IlanStatus;
use App\Http\Requests\Admin\IlanKategoriRequest;
use App\Http\Requests\Admin\IlanKategoriSuggestRequest;
use App\Http\Requests\Admin\IlanKategoriFieldUpdateRequest;
use App\Models\Ilan;
use App\Models\IlanKategori;
use App\Models\IlanKategoriYayinTipi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class IlanKategoriController extends AdminController
{
    /**
     * Kategori listesi sayfası
     * GET /admin/ilan-kategorileri
     * @context7: İstatistikler ve filtreleme ile kategori listesi
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): \Illuminate\View\View
    {
        // Filtreleme parametreleri
        $search = $request->get('search');
        $seviye = $request->get('seviye');
        $durum = $request->get('status');

        // ✅ EAGER LOADING: Select optimization ile birlikte
        // ✅ Context7: display_order kullan (order değil)
        // CRITICAL: select() metodunu eager loading'den ÖNCE çağırmalıyız
        // CRITICAL: Eager loading closure'ında orderBy kullanıldığında Laravel 'order' kolonunu seçmeye çalışabilir
        // Bu yüzden eager loading closure'ında orderBy kullanmamalıyız, sıralama ana sorguda yapılmalı
        // CRITICAL: 'order' kolonunu açıkça SEÇMEYİN - sadece 'display_order' kullanın

        // ✅ FIX: DB facade ile doğrudan sorgu oluştur - Eloquent'in 'order' kolonunu seçmesini önle
        // CRITICAL: Tablo adını açıkça belirt ve 'order' kolonunu ASLA seçme
        $query = DB::table('ilan_kategorileri')
            ->select([
                'ilan_kategorileri.id',
                'ilan_kategorileri.name',
                'ilan_kategorileri.slug',
                'ilan_kategorileri.parent_id',
                'ilan_kategorileri.status',
                'ilan_kategorileri.seviye',
                'ilan_kategorileri.display_order', // ✅ Context7: display_order kullan (order değil)
                'ilan_kategorileri.created_at',
                'ilan_kategorileri.updated_at'
            ]);

        // Arama filtresi
        if ($search) {
            $query->where('ilan_kategorileri.name', 'like', '%' . $search . '%');
        }

        // Seviye filtresi
        // ✅ Context7: View'dan "1" (Ana) veya "2" (Alt) gelebilir, controller'da kontrol et
        if ($seviye) {
            if ($seviye === 'ana' || $seviye === '1') {
                $query->whereNull('ilan_kategorileri.parent_id');
            } elseif ($seviye === 'alt' || $seviye === '2') {
                $query->whereNotNull('ilan_kategorileri.parent_id');
            }
        }

        // Durum filtresi (Context7: status field)
        if ($durum) {
            $query->where('ilan_kategorileri.status', $durum === 'aktif');
        }

        // ✅ Yalıhan Bekçi: orderByRaw kullan - DB facade ile 'order' kolonu seçilmez
        $query->orderByRaw('ilan_kategorileri.display_order ASC')
            ->orderByRaw('ilan_kategorileri.name ASC');

        // DEBUG: SQL sorgusunu logla
        if (config('app.debug')) {
            \Illuminate\Support\Facades\Log::info('IlanKategori Index SQL BEFORE pagination', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);
        }

        // CRITICAL: Query'yi kopyala ve kontrol et
        $sqlBeforePagination = $query->toSql();
        $bindingsBeforePagination = $query->getBindings();

        // Sayfalama - DB facade ile paginate
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $total = $query->count();
        $items = $query->skip(($currentPage - 1) * $perPage)->take($perPage)->get();

        // ✅ Eloquent modellere dönüştür - hydrate() kullanmadan, manuel olarak
        $models = collect($items)->map(function ($item) {
            $model = new IlanKategori();
            $model->id = $item->id;
            $model->name = $item->name;
            $model->slug = $item->slug;
            $model->parent_id = $item->parent_id;
            $model->status = $item->status;
            $model->seviye = $item->seviye;
            $model->display_order = $item->display_order;
            $model->created_at = $item->created_at;
            $model->updated_at = $item->updated_at;
            $model->exists = true;
            $model->wasRecentlyCreated = false;
            return $model;
        });

        // ✅ Eager loading ekle - DB facade ile (model kullanmadan)
        $ids = $models->pluck('id')->toArray();
        $parentIds = $models->pluck('parent_id')->filter()->unique()->toArray();

        // Parent'ları DB facade ile al
        $parentsData = [];
        if (!empty($parentIds)) {
            $parentsData = DB::table('ilan_kategorileri')
                ->select(['id', 'name', 'slug'])
                ->whereIn('id', $parentIds)
                ->get()
                ->keyBy('id')
                ->map(function ($item) {
                    $model = new IlanKategori();
                    $model->id = $item->id;
                    $model->name = $item->name;
                    $model->slug = $item->slug;
                    $model->exists = true;
                    return $model;
                });
        }

        // Children'ları DB facade ile al
        $childrenData = [];
        if (!empty($ids)) {
            $childrenData = DB::table('ilan_kategorileri')
                ->select(['id', 'name', 'slug', 'parent_id', 'status', 'display_order'])
                ->whereIn('parent_id', $ids)
                ->orderBy('display_order', 'asc')
                ->orderBy('name', 'asc')
                ->get()
                ->groupBy('parent_id')
                ->map(function ($group) {
                    return $group->map(function ($item) {
                        $model = new IlanKategori();
                        $model->id = $item->id;
                        $model->name = $item->name;
                        $model->slug = $item->slug;
                        $model->parent_id = $item->parent_id;
                        $model->status = $item->status;
                        $model->display_order = $item->display_order;
                        $model->exists = true;
                        return $model;
                    });
                });
        }

        // ✅ İlan sayılarını hesapla (ilanlar_count için)
        // ✅ Context7: kategori_id YOK, yeni sistem kullanılmalı (ana_kategori_id, alt_kategori_id, yayin_tipi_id)
        $ilanCounts = [];
        if (!empty($ids)) {
            // Ana kategori ilanları
            $anaKategoriCounts = DB::table('ilanlar')
                ->select('ana_kategori_id as kategori_id', DB::raw('COUNT(*) as count'))
                ->whereIn('ana_kategori_id', $ids)
                ->whereNull('deleted_at')
                ->groupBy('ana_kategori_id')
                ->pluck('count', 'kategori_id')
                ->toArray();

            // Alt kategori ilanları
            $altKategoriCounts = DB::table('ilanlar')
                ->select('alt_kategori_id as kategori_id', DB::raw('COUNT(*) as count'))
                ->whereIn('alt_kategori_id', $ids)
                ->whereNull('deleted_at')
                ->groupBy('alt_kategori_id')
                ->pluck('count', 'kategori_id')
                ->toArray();

            // Yayın tipi ilanları
            $yayinTipiCounts = DB::table('ilanlar')
                ->select('yayin_tipi_id as kategori_id', DB::raw('COUNT(*) as count'))
                ->whereIn('yayin_tipi_id', $ids)
                ->whereNull('deleted_at')
                ->groupBy('yayin_tipi_id')
                ->pluck('count', 'kategori_id')
                ->toArray();

            // Tüm sayıları birleştir
            foreach ($ids as $id) {
                $ilanCounts[$id] =
                    ($anaKategoriCounts[$id] ?? 0) +
                    ($altKategoriCounts[$id] ?? 0) +
                    ($yayinTipiCounts[$id] ?? 0);
            }
        }

        // İlişkileri manuel olarak ekle
        foreach ($models as $model) {
            if ($model->parent_id && isset($parentsData[$model->parent_id])) {
                $model->setRelation('parent', $parentsData[$model->parent_id]);
            }
            if (isset($childrenData[$model->id])) {
                $model->setRelation('children', $childrenData[$model->id]);
            }
            // ✅ İlan sayısını ekle
            $model->ilanlar_count = $ilanCounts[$model->id] ?? 0;
        }

        // Pagination oluştur
        $kategoriler = new \Illuminate\Pagination\LengthAwarePaginator(
            $models,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // İstatistikler (Context7: status field) - DB facade ile (model kullanmadan)
        $stats = [
            'toplam' => DB::table('ilan_kategorileri')->count(),
            'ana_kategoriler' => DB::table('ilan_kategorileri')->whereNull('parent_id')->count(),
            'alt_kategoriler' => DB::table('ilan_kategorileri')->whereNotNull('parent_id')->count(),
            'aktif' => DB::table('ilan_kategorileri')->where('status', true)->count(),
            'pasif' => DB::table('ilan_kategorileri')->where('status', false)->count(),
        ];

        // View'da $istatistikler bekleniyor (Türkçe değişken adı)
        $istatistikler = $stats;
        return view('admin.ilan-kategorileri.index', compact('kategoriler', 'istatistikler'));
    }

    /**
     * Yeni kategori oluşturma formu
     * GET /admin/ilan-kategorileri/create
     * @context7: Kategori oluşturma formu sayfası
     *
     * @return \Illuminate\View\View
     */
    public function create(): \Illuminate\View\View
    {
        $anaKategoriler = IlanKategori::whereNull('parent_id')
            ->where('status', true)
            ->orderBy('name')
            ->get();

        return view('admin.ilan-kategorileri.create', compact('anaKategoriler'));
    }

    /**
     * Yeni kategori kaydetme
     * POST /admin/ilan-kategorileri
     * @context7: Form verilerini doğrulayıp yeni kategori oluşturur
     *
     * @param IlanKategoriRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(IlanKategoriRequest $request): \Illuminate\Http\RedirectResponse
    {
        // ✅ STANDARDIZED: Using Form Request
        $validated = $request->validated();

        try {
            $seviye = (int) $validated['seviye'];
            $parentId = $validated['parent_id'] ?? null;

            if (($seviye == 1 || $seviye == 2) && !$parentId) {
                return back()->withInput()->with('error', 'Alt kategori veya Yayın Tipi için Üst Kategori seçmelisiniz.');
            }

            if ($seviye == 0 && $parentId) {
                return back()->withInput()->with('error', 'Ana kategorinin üst kategorisi olamaz.');
            }

            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;

            while (IlanKategori::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $kategori = IlanKategori::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'seviye' => $seviye,
                'parent_id' => $parentId,
                'status' => $validated['status'] ?? true,
                'display_order' => $validated['order'] ?? 0, // ✅ Context7: order → display_order
                'aciklama' => ''
            ]);

            return redirect()->route('admin.ilan-kategorileri.index')
                ->with('success', 'Kategori başarıyla oluşturuldu.');
        } catch (Exception $e) {
            return back()->withInput()
                ->with('error', 'Kategori oluşturulurken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Tekil kategori görüntüleme
     * GET /admin/ilan-kategorileri/{kategori}
     * @context7: Kategori detay sayfası ve istatistikleri
     *
     * @param int|string $kategori Route parameter (can be id or slug)
     * @return \Illuminate\View\View
     */
    public function show($kategori): \Illuminate\View\View
    {
        // ✅ ROUTE PARAMETER FIX: $kategori can be id (int) or slug (string)
        // Convert to int if it's numeric, otherwise treat as slug
        $kategoriId = is_numeric($kategori) ? (int)$kategori : null;

        // ✅ EAGER LOADING: Tüm ilişkileri önceden yükle
        // ✅ Context7: İlişki adı 'ilanlar' olmalı (model'de ilanlar() fonksiyonu)
        // ✅ Context7: is_published kolonu YOK, sadece status var (IlanStatus enum)
        // ✅ YALIHAN BEKÇİ: Nested eager loading ile ilanSahibi ve danisman ilişkileri de yüklendi
        $kategoriQuery = IlanKategori::with([
            'parent:id,name,slug',
            'children:id,name,slug,parent_id,status',
            'ilanlar' => function ($query) {
                $query->select([
                    'id',
                    'baslik',
                    'fiyat',
                    'para_birimi',
                    'status', // ✅ Context7: status kolonu (IlanStatus enum: 'yayinda', 'Aktif', etc.)
                    // ✅ Context7: kategori_id kolonu YOK (legacy/deprecated), yeni sistem kullanılmalı
                    'ana_kategori_id', // ✅ Context7: Ana kategori (yeni sistem)
                    'alt_kategori_id', // ✅ Context7: Alt kategori (yeni sistem)
                    'yayin_tipi_id', // ✅ Context7: Yayın tipi (yeni sistem)
                    'created_at',
                    'ilan_sahibi_id',
                    'danisman_id'
                ]);
            },
            'ilanlar.ilanSahibi:id,ad,soyad,telefon', // ✅ YALIHAN BEKÇİ: Nested eager loading
            'ilanlar.danisman:id,name,email' // ✅ YALIHAN BEKÇİ: Nested eager loading
        ]);

        // Find by ID or slug
        if ($kategoriId) {
            $kategori = $kategoriQuery->findOrFail($kategoriId);
        } else {
            $kategori = $kategoriQuery->where('slug', $kategori)->firstOrFail();
        }

        // ✅ OPTIMIZED: İstatistikleri tek query'de hesapla
        // ✅ Context7: İlişki adı 'ilanlar' olmalı (model'de ilanlar() fonksiyonu)
        // ✅ Context7: is_published YOK, status kullanılmalı (IlanStatus::YAYINDA)
        $stats = [
            'toplam_ilan' => $kategori->ilanlar->count(),
            'aktif_ilan' => $kategori->ilanlar->filter(function ($ilan) {
                // ✅ Context7: status == 'yayinda' kontrolü (Context7 standard)
                return $ilan->status === IlanStatus::YAYINDA->value;
            })->count(),
            'son_30_gun' => $kategori->ilanlar->filter(function ($ilan) {
                return $ilan->created_at >= now()->subDays(30);
            })->count(),
            'alt_kategoriler' => $kategori->children->count(),
        ];

        // ✅ OPTIMIZED: Eager loaded ilişkileri kullan
        // ✅ YALIHAN BEKÇİ: Eager loaded collection'ı kullan (N+1 query önlendi)
        // ✅ FIX: load() yerine eager loaded collection kullan (zaten yukarıda eager load edildi)
        $son_ilanlar = $kategori->ilanlar
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        return view('admin.ilan-kategorileri.show', compact('kategori', 'stats', 'son_ilanlar'));
    }

    /**
     * Kategori düzenleme formu
     * GET /admin/ilan-kategorileri/{id}/edit
     * @context7: Kategori düzenleme formu sayfası
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id): \Illuminate\View\View
    {
        $kategori = IlanKategori::findOrFail($id);

        $parentCategories = IlanKategori::whereNull('parent_id')
            ->where('status', true) // Context7: is_active → status
            ->where('id', '!=', $id) // Kendi kendinin parent'ı olamaz
            ->orderBy('name')
            ->get();

        return view('admin.ilan-kategorileri.edit', compact('kategori', 'parentCategories'));
    }

    /**
     * Kategori güncelleme
     * PUT /admin/ilan-kategorileri/{id}
     * @context7: Kategori bilgilerini günceller
     *
     * @param IlanKategoriRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(IlanKategoriRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $kategori = IlanKategori::findOrFail($id);

        // ✅ STANDARDIZED: Using Form Request
        $validated = $request->validated();

        try {
            $seviye = (int) $validated['seviye'];
            $parentId = $validated['parent_id'] ?? null;

            if (($seviye == 1 || $seviye == 2) && !$parentId) {
                return back()->withInput()->with('error', 'Alt kategori veya Yayın Tipi için Üst Kategori seçmelisiniz.');
            }

            if ($seviye == 0 && $parentId) {
                return back()->withInput()->with('error', 'Ana kategorinin üst kategorisi olamaz.');
            }

            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;

            while (IlanKategori::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $kategori->update([
                'name' => $validated['name'],
                'slug' => $slug,
                'seviye' => $seviye,
                'parent_id' => $parentId,
                'status' => $validated['status'] ?? true,
                'display_order' => $validated['order'] ?? 0, // ✅ Context7: order → display_order
                'aciklama' => ''
            ]);

            return redirect()->route('admin.ilan-kategorileri.index')
                ->with('success', $kategori->name . ' başarıyla güncellendi!');
        } catch (Exception $e) {
            return back()->withInput()
                ->with('error', 'Kategori güncellenirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Kategori silme
     * DELETE /admin/ilan-kategorileri/{id}
     * @context7: Kategori silme işlemi (soft delete)
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            // ✅ PERFORMANCE FIX: N+1 query önlendi - withCount() kullan
            $kategori = IlanKategori::withCount(['children', 'ilanlar'])->findOrFail($id);

            // Alt kategorileri kontrol et
            if ($kategori->children_count > 0) {
                return back()->with('error', 'Bu kategorinin alt kategorileri var. Önce alt kategorileri silin.');
            }

            // İlanları kontrol et
            if ($kategori->ilanlar_count > 0) {
                return back()->with('error', 'Bu kategoride ilanlar var. Kategori silinemez.');
            }

            $kategori->delete();

            return redirect()->route('admin.ilan-kategorileri.index')
                ->with('success', 'Kategori başarıyla silindi.');
        } catch (Exception $e) {
            return back()->with('error', 'Kategori silinirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Kategori performans metrikleri
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPerformance(Request $request)
    {
        try {
            $categoryId = $request->get('category_id');

            if (!$categoryId) {
                return response()->json(['error' => 'Kategori ID gerekli'], 400);
            }

            $category = IlanKategori::find($categoryId);
            if (!$category) {
                return response()->json(['error' => 'Kategori bulunamadı'], 404);
            }

            $performance = [
                'popularity' => $this->calculateCategoryPopularity($categoryId),
                'growth' => $this->calculateCategoryGrowth($categoryId),
                'seo_score' => $this->calculateSEOScores($categoryId)
            ];

            return response()->json($performance);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * AI destekli kategori önerileri
     *
     * @param IlanKategoriSuggestRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestCategories(IlanKategoriSuggestRequest $request): \Illuminate\Http\JsonResponse
    {
        // ✅ STANDARDIZED: Using Form Request
        $validated = $request->validated();

        try {
            // Basit kategori önerisi mantığı
            $suggestions = $this->generateCategorySuggestions(
                $validated['title'],
                $validated['description'] ?? '',
                $validated['features'] ?? []
            );

            return response()->json($suggestions);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Kategori trendleri
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTrends(Request $request)
    {
        try {
            $trends = [
                'popular_categories' => $this->getPopularCategories(),
                'growth_trends' => $this->getGrowthTrends(),
                'seasonal_data' => $this->getSeasonalData()
            ];

            return response()->json($trends);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * AI analiz
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function aiAnalysis(Request $request)
    {
        try {
            $analysis = [
                'category_health' => $this->analyzeCategoryHealth(),
                'optimization_suggestions' => $this->getOptimizationSuggestions(),
                'performance_insights' => $this->getPerformanceInsights()
            ];

            return response()->json($analysis);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Yardımcı metodlar

    private function calculateCategoryPopularity($categoryId)
    {
        // Basit popülarlik hesaplama
        return Ilan::where('kategori_id', $categoryId)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
    }

    private function calculateCategoryGrowth($categoryId)
    {
        $thisMonth = Ilan::where('kategori_id', $categoryId)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        $lastMonth = Ilan::where('kategori_id', $categoryId)
            ->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])
            ->count();

        return $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;
    }

    private function calculateSEOScores($categoryId)
    {
        // Basit SEO skoru hesaplama
        $category = IlanKategori::find($categoryId);

        $score = 0;
        if ($category->meta_description) $score += 30;
        if ($category->meta_keywords) $score += 20;
        if (strlen($category->name) >= 3 && strlen($category->name) <= 60) $score += 25;
        if ($category->description && strlen($category->description) >= 50) $score += 25;

        return $score;
    }

    private function generateCategorySuggestions($title, $description, $features)
    {
        // Basit öneri sistemi
        $suggestions = [];

        // Başlık bazlı öneriler
        $titleWords = explode(' ', strtolower($title));
        $existingCategories = IlanKategori::whereIn('name', $titleWords)->get();

        foreach ($existingCategories as $category) {
            $suggestions[] = [
                'name' => $category->name,
                'confidence' => 0.8,
                'reason' => 'Başlık benzerliği'
            ];
        }

        return $suggestions;
    }

    private function getPopularCategories()
    {
        // ✅ Context7: kategori_id kolonu YOK, yeni sistem kullanılmalı (ana_kategori_id, alt_kategori_id, yayin_tipi_id)
        return IlanKategori::select('name', DB::raw('COUNT(*) as ilan_count'))
            ->join('ilanlar', function ($join) {
                $join->on('ilan_kategorileri.id', '=', 'ilanlar.ana_kategori_id')
                    ->orOn('ilan_kategorileri.id', '=', 'ilanlar.alt_kategori_id')
                    ->orOn('ilan_kategorileri.id', '=', 'ilanlar.yayin_tipi_id');
            })
            ->where('ilanlar.created_at', '>=', now()->subDays(30))
            ->groupBy('ilan_kategorileri.id', 'name')
            ->orderBy('ilan_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getGrowthTrends()
    {
        // Son 6 ayın büyüme trendi
        $trends = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Ilan::where('created_at', '>=', $month->startOfMonth())
                ->where('created_at', '<=', $month->endOfMonth())
                ->count();

            $trends[] = [
                'month' => $month->format('Y-m'),
                'count' => $count
            ];
        }

        return $trends;
    }

    private function getSeasonalData()
    {
        // Mevsimsel veriler
        return [
            'spring' => Ilan::whereMonth('created_at', '>=', 3)->whereMonth('created_at', '<=', 5)->count(),
            'summer' => Ilan::whereMonth('created_at', '>=', 6)->whereMonth('created_at', '<=', 8)->count(),
            'autumn' => Ilan::whereMonth('created_at', '>=', 9)->whereMonth('created_at', '<=', 11)->count(),
            'winter' => Ilan::whereIn(DB::raw('MONTH(created_at)'), [12, 1, 2])->count(),
        ];
    }

    private function analyzeCategoryHealth()
    {
        // ✅ Context7: İlişki adı 'ilanlar' olmalı (model'de ilanlar() fonksiyonu)
        return [
            'healthy_categories' => IlanKategori::whereHas('ilanlar', function ($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            })->count(),
            'stale_categories' => IlanKategori::whereDoesntHave('ilanlar')->count(),
            'total_categories' => IlanKategori::count()
        ];
    }

    private function getOptimizationSuggestions()
    {
        // ✅ Context7: İlişki adı 'ilanlar' olmalı (model'de ilanlar() fonksiyonu)
        return [
            'empty_categories' => IlanKategori::whereDoesntHave('ilanlar')->pluck('name')->toArray(),
            'categories_without_description' => IlanKategori::whereNull('description')->orWhere('description', '')->pluck('name')->toArray(),
            'categories_without_meta' => IlanKategori::whereNull('meta_description')->orWhere('meta_description', '')->pluck('name')->toArray()
        ];
    }

    /**
     * Inline güncelleme (AJAX endpoint)
     * POST /admin/ilan-kategorileri/{id}/inline-update
     * @context7: Tablo üzerinden hızlı düzenleme
     *
     * @param IlanKategoriFieldUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function inlineUpdate(IlanKategoriFieldUpdateRequest $request, int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $kategori = IlanKategori::findOrFail($id);

            // ✅ STANDARDIZED: Using Form Request
            $validated = $request->validated();

            $field = $validated['field'];
            $value = $validated['value'];

            // Field'a göre değeri ayarla
            switch ($field) {
                case 'status':
                    $kategori->status = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'sort_order':
                    $kategori->sort_order = (int)$value;
                    break;
                case 'name':
                    $kategori->name = $value;
                    $kategori->slug = Str::slug($value); // Slug'ı da güncelle
                    break;
                default:
                    $kategori->{$field} = $value;
            }

            $kategori->save();

            return response()->json([
                'success' => true,
                'message' => 'Kategori güncellendi',
                'data' => $kategori
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Güncelleme hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alt kategorileri getir (AJAX endpoint)
     * GET /admin/ilan-kategorileri/alt-kategoriler?parent_id={id}
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAltKategoriler(Request $request)
    {
        $parentId = $request->input('parent_id');

        if (!$parentId) {
            return response()->json([
                'success' => false,
                'message' => 'Parent ID gerekli'
            ], 400);
        }

        $altKategoriler = IlanKategori::where('parent_id', $parentId)
            ->where('status', true) // ✅ Context7: status is boolean (FIXED!)
            ->select(['id', 'name', 'slug'])
            ->orderBy('display_order', 'asc') // ✅ Context7: order → display_order
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $altKategoriler
        ]);
    }

    /**
     * Kategoriye özel özellikleri getir (AJAX endpoint)
     * GET /admin/ilan-kategorileri/{id}/ozellikler
     *
     * @param int|string $kategoriId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOzellikler($kategoriId)
    {
        // ✅ Context7: display_order kullanılıyor (migration: 2025_11_11_103353)
        $ozellikler = \App\Models\Ozellik::where('kategori_id', $kategoriId)
            ->where('status', 'aktif')
            ->orderBy('display_order', 'asc') // ✅ Context7: order → display_order
            ->orderBy('name', 'asc')
            ->select(['id', 'name', 'slug', 'veri_tipi', 'veri_secenekleri', 'birim', 'zorunlu', 'aciklama'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $ozellikler->map(function ($oz) {
                return [
                    'id' => $oz->id,
                    'name' => $oz->name,
                    'slug' => $oz->slug,
                    'type' => $oz->veri_tipi,
                    'options' => $oz->veri_secenekleri ? json_decode($oz->veri_secenekleri, true) : null,
                    'unit' => $oz->birim,
                    'required' => (bool) $oz->zorunlu,
                    'help' => $oz->aciklama
                ];
            })
        ]);
    }

    /**
     * Yayın tiplerini getir (AJAX endpoint)
     * GET /admin/ilan-kategorileri/yayin-tipleri?kategori_id={id}
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getYayinTipleri(Request $request)
    {
        $kategoriId = $request->input('kategori_id');

        if (!$kategoriId) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori ID gerekli'
            ], 400);
        }

        // Yayın tiplerini al (ilan_kategori_yayin_tipleri tablosundan)
        $yayinTipleri = IlanKategoriYayinTipi::select(['id', 'yayin_tipi as name']) // Database: yayin_tipi → API: name
            ->where('kategori_id', $kategoriId)
            ->where(function ($query) {
                // Boolean veya string 'Aktif' kontrolü
                $query->where('status', '=', 1)
                    ->orWhere('status', '=', true)
                    ->orWhere('status', '=', 'Aktif');
            })
            ->orderBy('display_order', 'asc') // ✅ Context7: order → display_order
            ->get();

        return response()->json([
            'success' => true,
            'data' => $yayinTipleri
        ]);
    }

    private function getPerformanceInsights()
    {
        // ✅ Context7: kategori_id kolonu YOK, yeni sistem kullanılmalı (ana_kategori_id, alt_kategori_id, yayin_tipi_id)
        return [
            'most_active_category' => IlanKategori::select('name', DB::raw('COUNT(*) as count'))
                ->join('ilanlar', function ($join) {
                    $join->on('ilan_kategorileri.id', '=', 'ilanlar.ana_kategori_id')
                        ->orOn('ilan_kategorileri.id', '=', 'ilanlar.alt_kategori_id')
                        ->orOn('ilan_kategorileri.id', '=', 'ilanlar.yayin_tipi_id');
                })
                ->groupBy('ilan_kategorileri.id', 'name')
                ->orderBy('count', 'desc')
                ->first(),
            'least_active_category' => IlanKategori::select('name', DB::raw('COUNT(*) as count'))
                ->leftJoin('ilanlar', function ($join) {
                    $join->on('ilan_kategorileri.id', '=', 'ilanlar.ana_kategori_id')
                        ->orOn('ilan_kategorileri.id', '=', 'ilanlar.alt_kategori_id')
                        ->orOn('ilan_kategorileri.id', '=', 'ilanlar.yayin_tipi_id');
                })
                ->groupBy('ilan_kategorileri.id', 'name')
                ->orderBy('count', 'asc')
                ->first()
        ];
    }

    /**
     * Toplu işlem (AJAX endpoint)
     * POST /admin/ilan-kategorileri/bulk-action
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAction(Request $request)
    {
        try {
            $request->validate([
                'action' => 'required|string|in:activate,deactivate,delete',
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|exists:ilan_kategorileri,id'
            ]);

            $action = $request->input('action');
            $ids = $request->input('ids');

            DB::beginTransaction();

            $count = 0;

            switch ($action) {
                case 'activate':
                    // ✅ PERFORMANCE FIX: Bulk update kullan (N+1 query önlendi)
                    $count = IlanKategori::whereIn('id', $ids)->update(['status' => true]);
                    break;

                case 'deactivate':
                    // ✅ PERFORMANCE FIX: Bulk update kullan (N+1 query önlendi)
                    $count = IlanKategori::whereIn('id', $ids)->update(['status' => false]);
                    break;

                case 'delete':
                    // ✅ EAGER LOADING: İlişki kontrolü için eager loading gerekli
                    // ✅ Context7: İlişki adı 'ilanlar' olmalı (model'de ilanlar() fonksiyonu)
                    // ✅ Context7: kategori_id kolonu YOK, yeni sistem kullanılmalı
                    $kategoriler = IlanKategori::with(['children:id,parent_id', 'ilanlar:id,ana_kategori_id,alt_kategori_id,yayin_tipi_id'])
                        ->whereIn('id', $ids)
                        ->get();

                    foreach ($kategoriler as $kategori) {
                        // ✅ OPTIMIZED: Eager loaded ilişkileri kullan
                        if ($kategori->children->isEmpty() && $kategori->ilanlar->isEmpty()) {
                            $kategori->delete();
                            $count++;
                        }
                    }
                    break;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $count . ' kategori başarıyla işlendi',
                'count' => $count
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // ✅ STANDARDIZED: Using ResponseService (automatic logging)
            Log::error('Bulk action error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Toplu işlem hatası: ' . $e->getMessage()
            ], 500);
        }
    }
}
