<?php

namespace App\Http\Controllers\Admin;

use App\Models\IlanKategori;
use App\Models\IlanKategoriYayinTipi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class IlanKategoriController extends AdminController
{
    /**
     * Kategori listesi sayfası
     * GET /admin/ilan-kategorileri
     * @context7: İstatistikler ve filtreleme ile kategori listesi
     */
    public function index(Request $request)
    {
        // Filtreleme parametreleri
        $search = $request->get('search');
        $seviye = $request->get('seviye');
        $durum = $request->get('status');

        // Kategori sorgusu
        $query = IlanKategori::with(['parent', 'children']);

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
     */
    public function create()
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
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:ilan_kategorileri,id',
            'seviye' => 'required|integer|in:0,1,2',
            'status' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0'
        ]);

        try {
            $seviye = (int) $request->input('seviye');
            $parentId = $request->input('parent_id');

            if (($seviye == 1 || $seviye == 2) && !$parentId) {
                return back()->withInput()->with('error', 'Alt kategori veya Yayın Tipi için Üst Kategori seçmelisiniz.');
            }

            if ($seviye == 0 && $parentId) {
                return back()->withInput()->with('error', 'Ana kategorinin üst kategorisi olamaz.');
            }

            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $counter = 1;

            while (IlanKategori::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $kategori = IlanKategori::create([
                'name' => $request->name,
                'slug' => $slug,
                'seviye' => $seviye,
                'parent_id' => $parentId,
                'status' => $request->boolean('status', true),
                'order' => $request->input('order') ?: 0,
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
     */
    public function show($id)
    {
        $kategori = IlanKategori::with(['parent', 'children', 'ilans'])
            ->findOrFail($id);

        // Kategori istatistikleri
        $stats = [
            'toplam_ilan' => $kategori->ilanlar()->count(),
            'aktif_ilan' => $kategori->ilanlar()->where('is_published', true)->count(),
            'son_30_gun' => $kategori->ilanlar()->where('created_at', '>=', now()->subDays(30))->count(),
            'alt_kategoriler' => $kategori->children()->count(),
        ];

        // Son ilanlar
        $son_ilanlar = $kategori->ilanlar()
            ->with(['kullanici', 'lokasyon'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.ilan-kategorileri.show', compact('kategori', 'stats', 'son_ilanlar'));
    }

    /**
     * Kategori düzenleme formu
     * GET /admin/ilan-kategorileri/{id}/edit
     * @context7: Kategori düzenleme formu sayfası
     */
    public function edit($id)
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
     */
    public function update(Request $request, $id)
    {
        $kategori = IlanKategori::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:ilan_kategorileri,id|not_in:' . $id,
            'seviye' => 'required|integer|in:0,1,2',
            'status' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0'
        ]);

        try {
            $seviye = (int) $request->input('seviye');
            $parentId = $request->input('parent_id');

            if (($seviye == 1 || $seviye == 2) && !$parentId) {
                return back()->withInput()->with('error', 'Alt kategori veya Yayın Tipi için Üst Kategori seçmelisiniz.');
            }

            if ($seviye == 0 && $parentId) {
                return back()->withInput()->with('error', 'Ana kategorinin üst kategorisi olamaz.');
            }

            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $counter = 1;

            while (IlanKategori::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $kategori->update([
                'name' => $request->name,
                'slug' => $slug,
                'seviye' => $seviye,
                'parent_id' => $parentId,
                'status' => $request->boolean('status', true),
                'order' => $request->input('order') ?: 0,
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
     */
    public function destroy($id)
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
     */
    public function suggestCategories(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'features' => 'nullable|array'
        ]);

        try {
            // Basit kategori önerisi mantığı
            $suggestions = $this->generateCategorySuggestions(
                $request->title,
                $request->description,
                $request->features ?? []
            );

            return response()->json($suggestions);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Kategori trendleri
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
        return DB::table('ilans')
            ->where('kategori_id', $categoryId)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
    }

    private function calculateCategoryGrowth($categoryId)
    {
        $thisMonth = DB::table('ilans')
            ->where('kategori_id', $categoryId)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        $lastMonth = DB::table('ilans')
            ->where('kategori_id', $categoryId)
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
        return DB::table('ilan_kategorileri')
            ->select('name', DB::raw('COUNT(*) as ilan_count'))
            ->join('ilans', 'ilan_kategorileri.id', '=', 'ilans.kategori_id')
            ->where('ilans.created_at', '>=', now()->subDays(30))
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
            $count = DB::table('ilans')
                ->where('created_at', '>=', $month->startOfMonth())
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
            'spring' => DB::table('ilans')->whereMonth('created_at', '>=', 3)->whereMonth('created_at', '<=', 5)->count(),
            'summer' => DB::table('ilans')->whereMonth('created_at', '>=', 6)->whereMonth('created_at', '<=', 8)->count(),
            'autumn' => DB::table('ilans')->whereMonth('created_at', '>=', 9)->whereMonth('created_at', '<=', 11)->count(),
            'winter' => DB::table('ilans')->whereIn(DB::raw('MONTH(created_at)'), [12, 1, 2])->count(),
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
     */
    public function inlineUpdate(Request $request, $id)
    {
        try {
            $kategori = IlanKategori::findOrFail($id);

            $request->validate([
                'field' => 'required|string|in:name,description,status,sort_order',
                'value' => 'required'
            ]);

            $field = $request->field;
            $value = $request->value;

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
        $yayinTipleri = DB::table('ilan_kategori_yayin_tipleri')
            ->select(['id', 'yayin_tipi as name']) // Database: yayin_tipi → API: name
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
            'most_active_category' => DB::table('ilan_kategorileri')
                ->select('name', DB::raw('COUNT(*) as count'))
                ->join('ilans', 'ilan_kategorileri.id', '=', 'ilans.kategori_id')
                ->groupBy('ilan_kategorileri.id', 'name')
                ->orderBy('count', 'desc')
                ->first(),
            'least_active_category' => DB::table('ilan_kategorileri')
                ->select('name', DB::raw('COUNT(*) as count'))
                ->leftJoin('ilans', 'ilan_kategorileri.id', '=', 'ilans.kategori_id')
                ->groupBy('ilan_kategorileri.id', 'name')
                ->orderBy('count', 'asc')
                ->first()
        ];
    }

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

            foreach ($ids as $id) {
                $kategori = IlanKategori::find($id);

                if (!$kategori) continue;

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
                        if ($kategori->children()->count() === 0 && $kategori->ilanlar()->count() === 0) {
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

            \Log::error('Bulk action error', [
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
