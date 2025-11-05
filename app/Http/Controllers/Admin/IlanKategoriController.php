<?php

namespace App\Http\Controllers\Admin;

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
        $query = IlanKategori::with([
            'parent:id,name,slug',
            'children:id,name,slug,parent_id,status'
        ])->select([
            'id', 'name', 'slug', 'parent_id', 'status',
            'seviye', 'order', 'created_at', 'updated_at'
        ]);

        // Arama filtresi
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Seviye filtresi
        if ($seviye) {
            if ($seviye === 'ana') {
                $query->whereNull('parent_id');
            } elseif ($seviye === 'alt') {
                $query->whereNotNull('parent_id');
            }
        }

        // Durum filtresi (Context7: status field)
        if ($durum) {
            $query->where('status', $durum === 'aktif');
        }

        // Sayfalama
        $kategoriler = $query->paginate(15);

        // İstatistikler (Context7: status field)
        $stats = [
            'toplam' => IlanKategori::count(),
            'ana_kategoriler' => IlanKategori::whereNull('parent_id')->count(),
            'alt_kategoriler' => IlanKategori::whereNotNull('parent_id')->count(),
            'aktif' => IlanKategori::where('status', true)->count(),
            'pasif' => IlanKategori::where('status', false)->count(),
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
                'order' => $validated['order'] ?? 0,
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
     * GET /admin/ilan-kategorileri/{id}
     * @context7: Kategori detay sayfası ve istatistikleri
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show(int $id): \Illuminate\View\View
    {
        // ✅ EAGER LOADING: Tüm ilişkileri önceden yükle
        $kategori = IlanKategori::with([
            'parent:id,name,slug',
            'children:id,name,slug,parent_id,status',
            'ilans' => function($query) {
                $query->select(['id', 'baslik', 'fiyat', 'para_birimi', 'status', 'is_published',
                               'kategori_id', 'created_at', 'ilan_sahibi_id', 'danisman_id']);
            }
        ])->findOrFail($id);

        // ✅ OPTIMIZED: İstatistikleri tek query'de hesapla
        $stats = [
            'toplam_ilan' => $kategori->ilans->count(),
            'aktif_ilan' => $kategori->ilans->where('is_published', true)->count(),
            'son_30_gun' => $kategori->ilans->filter(function($ilan) {
                return $ilan->created_at >= now()->subDays(30);
            })->count(),
            'alt_kategoriler' => $kategori->children->count(),
        ];

        // ✅ OPTIMIZED: Eager loaded ilişkileri kullan
        $son_ilanlar = $kategori->ilans
            ->load(['ilanSahibi:id,ad,soyad,telefon', 'danisman:id,name,email'])
            ->sortByDesc('created_at')
            ->take(10);

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
                'order' => $validated['order'] ?? 0,
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
            $kategori = IlanKategori::findOrFail($id);

            // Alt kategorileri kontrol et
            if ($kategori->children()->count() > 0) {
                return back()->with('error', 'Bu kategorinin alt kategorileri var. Önce alt kategorileri silin.');
            }

            // İlanları kontrol et
            if ($kategori->ilanlar()->count() > 0) {
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
        return IlanKategori::select('name', DB::raw('COUNT(*) as ilan_count'))
            ->join('ilanlar', 'ilan_kategorileri.id', '=', 'ilanlar.kategori_id')
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
        return [
            'healthy_categories' => IlanKategori::whereHas('ilans', function($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            })->count(),
            'stale_categories' => IlanKategori::whereDoesntHave('ilans')->count(),
            'total_categories' => IlanKategori::count()
        ];
    }

    private function getOptimizationSuggestions()
    {
        return [
            'empty_categories' => IlanKategori::whereDoesntHave('ilans')->pluck('name')->toArray(),
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
            ->orderBy('order', 'asc')
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
        $ozellikler = \App\Models\Ozellik::where('kategori_id', $kategoriId)
            ->where('status', 'aktif')
            ->orderBy('order', 'asc')
            ->orderBy('name', 'asc')
            ->select(['id', 'name', 'slug', 'veri_tipi', 'veri_secenekleri', 'birim', 'zorunlu', 'aciklama'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $ozellikler->map(function($oz) {
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
            ->where(function($query) {
                // Boolean veya string 'Aktif' kontrolü
                $query->where('status', '=', 1)
                      ->orWhere('status', '=', true)
                      ->orWhere('status', '=', 'Aktif');
            })
            ->orderBy('order', 'asc') // Database: order (NOT sira)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $yayinTipleri
        ]);
    }

        private function getPerformanceInsights()
    {
        return [
            'most_active_category' => IlanKategori::select('name', DB::raw('COUNT(*) as count'))
                ->join('ilanlar', 'ilan_kategorileri.id', '=', 'ilanlar.kategori_id')
                ->groupBy('ilan_kategorileri.id', 'name')
                ->orderBy('count', 'desc')
                ->first(),
            'least_active_category' => IlanKategori::select('name', DB::raw('COUNT(*) as count'))
                ->leftJoin('ilanlar', 'ilan_kategorileri.id', '=', 'ilanlar.kategori_id')
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

            // ✅ EAGER LOADING: Tüm kategorileri ilişkileriyle birlikte yükle
            $kategoriler = IlanKategori::with(['children:id,parent_id', 'ilans:id,kategori_id'])
                ->whereIn('id', $ids)
                ->get();

            foreach ($kategoriler as $kategori) {
                switch ($action) {
                    case 'activate':
                        $kategori->update(['status' => true]);
                        $count++;
                        break;

                    case 'deactivate':
                        $kategori->update(['status' => false]);
                        $count++;
                        break;

                    case 'delete':
                        // ✅ OPTIMIZED: Eager loaded ilişkileri kullan
                        if ($kategori->children->isEmpty() && $kategori->ilans->isEmpty()) {
                            $kategori->delete();
                            $count++;
                        }
                        break;
                }
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
