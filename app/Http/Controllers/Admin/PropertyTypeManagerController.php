<?php

namespace App\Http\Controllers\Admin;

use App\Models\AltKategoriYayinTipi;
use App\Models\Feature;
use App\Models\FeatureAssignment;
use App\Models\FeatureCategory;
use App\Models\IlanKategori;
use App\Models\IlanKategoriYayinTipi;
use App\Models\KategoriYayinTipiFieldDependency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PropertyTypeManagerController extends AdminController
{
    public function __construct()
    {
        // CSRF middleware otomatik eklendi
    }

    private function allowedFeatureCategoryNames(string $slug): array
    {
        switch ($slug) {
            case 'arsa':
                return ['Arsa Özellikleri', 'Genel Özellikler', 'Olanaklar'];
            case 'konut':
                return ['Konut Özellikleri', 'Genel Özellikler', 'Olanaklar', 'Yazlık Özellikleri'];
            case 'yazlik':
                return ['Yazlık Özellikleri', 'Genel Özellikler', 'Olanaklar'];
            case 'isyeri':
                return ['Ticari Özellikler', 'Genel Özellikler', 'Olanaklar'];
            case 'turistik-tesis':
                return ['Turistik Tesis Özellikleri', 'Genel Özellikler', 'Olanaklar'];
            default:
                return ['Genel Özellikler', 'Olanaklar', 'Yazlık Özellikleri'];
        }
    }

    private function ensureDefaultYayinTipleri(int $kategoriId): void
    {
        if (! Schema::hasTable('ilan_kategori_yayin_tipleri')) {
            return;
        }
        if (IlanKategoriYayinTipi::where('kategori_id', $kategoriId)->exists()) {
            return;
        }
        $now = now();
        DB::table('ilan_kategori_yayin_tipleri')->insert([
            ['kategori_id' => $kategoriId, 'yayin_tipi' => 'Satılık', 'status' => true, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['kategori_id' => $kategoriId, 'yayin_tipi' => 'Kiralık', 'status' => true, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    /**
     * Ana sayfa - Kategori listesi ve yönetim
     * YENİ: 3-seviye sistem - sadece ana kategoriler (seviye=0)
     */
    public function index()
    {
        // Migration çalıştırıldı: order → display_order rename edildi
        // Artık sadece display_order kullan (değişken kullanmadan doğrudan yazıyoruz)

        $query = IlanKategori::where('seviye', 0);

        // Context7: Schema kontrolü ile status kolonu
        if (Schema::hasColumn('ilan_kategorileri', 'status')) {
            $query->where('status', true); // ✅ Sadece aktif ana kategoriler
        }

        // DEBUG: SQL sorgusunu logla
        if (config('app.debug')) {
            \Illuminate\Support\Facades\Log::info('PropertyTypeManager Index SQL', [
                'before_select' => $query->toSql(),
                'bindings' => $query->getBindings(),
            ]);
        }

        $kategoriler = $query->with(['children' => function ($q) {
            $q->where('seviye', 1);
            if (Schema::hasColumn('ilan_kategorileri', 'status')) {
                $q->where('status', true);
            }
            if (Schema::hasColumn('ilan_kategorileri', 'display_order')) {
                $q->select(['id', 'name', 'slug', 'parent_id', 'seviye', 'status', 'display_order'])
                    ->orderByRaw('COALESCE(display_order, 999999) ASC')
                    ->orderBy('name', 'ASC');
            } else {
                $q->select(['id', 'name', 'slug', 'parent_id', 'seviye', 'status'])
                    ->orderBy('name', 'ASC');
            }
        }])
            // ✅ Context7: Yayın tiplerini de yükle
            ->with(['yayinTipleri' => function ($q) {
                $q->where('status', true);
                if (Schema::hasColumn('ilan_kategori_yayin_tipleri', 'display_order')) {
                    $q->orderBy('display_order', 'ASC');
                }
                $q->orderBy('yayin_tipi', 'ASC');
            }])
            // ✅ N+1 FIX: Select optimization - display_order doğrudan kullanılıyor
            ->select(Schema::hasColumn('ilan_kategorileri', 'display_order')
                ? ['id', 'name', 'slug', 'seviye', 'status', 'display_order']
                : ['id', 'name', 'slug', 'seviye', 'status'])
            ->when(Schema::hasColumn('ilan_kategorileri', 'display_order'), function ($q) {
                $q->orderByRaw('COALESCE(display_order, 999999) ASC');
            })
            ->orderBy('name', 'ASC');

        // DEBUG: Final SQL sorgusunu logla
        if (config('app.debug')) {
            \Illuminate\Support\Facades\Log::info('PropertyTypeManager Index Final SQL', [
                'sql' => $kategoriler->toSql(),
                'bindings' => $kategoriler->getBindings(),
            ]);
        }

        $kategoriler = $kategoriler->get();

        // ✅ Context7: Tüm kategoriler için eksik yayın tiplerini otomatik ekle
        foreach ($kategoriler as $kategori) {
            $this->ensureDefaultYayinTipleri($kategori->id);
        }

        // Debug log - Geliştirme modunda çalışır
        if (config('app.debug')) {
            Log::info('PropertyTypeManager Index: Ana kategoriler sorgulandı', [
                'bulunan_kategori_sayisi' => $kategoriler->count(),
                'kategoriler' => $kategoriler->map(function ($k) {
                    return [
                        'id' => $k->id,
                        'name' => $k->name,
                        'status' => $k->status,
                        'alt_kategori_sayisi' => $k->children->count(),
                        'alt_kategoriler' => $k->children->map(function ($alt) {
                            return ['id' => $alt->id, 'name' => $alt->name, 'status' => $alt->status];
                        })->toArray(),
                    ];
                })->toArray(),
                // Tüm alt kategorileri de kontrol et (debug için)
                'tum_alt_kategoriler' => (function () {
                    $query = IlanKategori::where('seviye', 1);
                    if (Schema::hasColumn('ilan_kategorileri', 'status')) {
                        $query->where('status', true);
                    }

                    return $query->select(['id', 'name', 'parent_id', 'seviye', 'status'])->get();
                })()
                    ->map(function ($alt) {
                        return ['id' => $alt->id, 'name' => $alt->name, 'parent_id' => $alt->parent_id, 'seviye' => $alt->seviye, 'status' => $alt->status];
                    })->toArray(),
            ]);
        }

        return view('admin.property-type-manager.index', compact('kategoriler'));
    }

    /**
     * Kategori detay - Yayın tipleri ve relations yönetimi
     * YENİ: 3-seviye sistem - Alt kategoriler (seviye=1) ve Yayın Tipleri (seviye=2)
     * ✅ Tüm kategori ID'leri için tutarlı çalışır
     */
    public function show($kategoriId)
    {
        try {
            $kategoriId = (int) $kategoriId;
            $kategori = IlanKategori::find($kategoriId);
            if (! $kategori) {
                abort(404);
            }

            // ✅ Tüm kategoriler için tutarlılık: Ana kategori kontrolü (seviye=0 olmalı)
            if ($kategori->seviye !== 0) {
                Log::warning('PropertyTypeManager: Ana kategori beklenirken farklı seviye tespit edildi', [
                    'kategori_id' => $kategoriId,
                    'kategori_name' => $kategori->name,
                    'seviye' => $kategori->seviye,
                    'beklenen_seviye' => 0,
                ]);

                // Eğer alt kategori veya yayın tipi ise, ana kategoriye yönlendir
                if ($kategori->parent_id) {
                    $anaKategori = IlanKategori::find($kategori->parent_id);
                    if ($anaKategori && $anaKategori->seviye === 0) {
                        return redirect()->route('admin.property_types.show', $anaKategori->id)
                            ->with('info', 'Ana kategori sayfasına yönlendirildiniz.');
                    }
                }
            }

            // Alt kategoriler (seviye=1) - İyileştirilmiş sorgu
            // ✅ Status filtresi eklendi (opsiyonel - varsayılan: sadece aktif kategoriler)
            $altKategorilerQuery = IlanKategori::where('parent_id', $kategoriId)
                ->where('seviye', 1);
            // Migration çalıştırıldı: order → display_order rename edildi
            // Artık sadece display_order kullan (değişken kullanmadan doğrudan yazıyoruz)

            if (Schema::hasColumn('ilan_kategorileri', 'status')) {
                $altKategorilerQuery->where('status', true); // ✅ Plan: Status filtresi eklendi (aktif kategoriler)
            }
            $altKategoriler = $altKategorilerQuery->with(['children' => function ($query) {
                $query->where('seviye', 2);
                if (Schema::hasColumn('ilan_kategorileri', 'status')) {
                    $query->where('status', true);
                }
                if (Schema::hasColumn('ilan_kategorileri', 'display_order')) {
                    $query->select(['id', 'name', 'slug', 'parent_id', 'seviye', 'status', 'display_order'])
                        ->orderByRaw('COALESCE(display_order, 999999) ASC');
                } else {
                    $query->select(['id', 'name', 'slug', 'parent_id', 'seviye', 'status'])
                        ->orderBy('name', 'ASC');
                }
            }])
                ->select(Schema::hasColumn('ilan_kategorileri', 'display_order')
                    ? ['id', 'name', 'slug', 'parent_id', 'seviye', 'status', 'display_order']
                    : ['id', 'name', 'slug', 'parent_id', 'seviye', 'status'])
                ->when(Schema::hasColumn('ilan_kategorileri', 'display_order'), function ($q) {
                    $q->orderByRaw('COALESCE(display_order, 999999) ASC');
                })
                ->orderBy('name', 'ASC')
                ->get();

            // ✅ Tüm kategoriler için tutarlı: Yanlış eklenen yayın tiplerini tespit et
            // Seviye=1 olarak eklenmiş ama yayın tipi olmalı (seviye kontrolü yapılmadan)
            // NOT: "Günlük Kiralama", "Haftalık Kiralama", "Aylık Kiralama" geçerli alt kategorilerdir
            $yanlisEklenenYayinTipleriQuery = IlanKategori::where('parent_id', $kategoriId)
                ->where('seviye', 1);
            // Context7: Schema kontrolü ile status kolonu
            if (Schema::hasColumn('ilan_kategorileri', 'status')) {
                $yanlisEklenenYayinTipleriQuery->where('status', true); // ✅ Aktif olanları kontrol et
            }
            $yanlisEklenenYayinTipleri = $yanlisEklenenYayinTipleriQuery
                // ✅ N+1 FIX: Select optimization
                ->select(['id', 'name', 'slug', 'parent_id', 'seviye', 'status'])
                ->whereIn('name', ['Satılık', 'Kiralık', 'Kat Karşılığı', 'Günlük', 'Haftalık', 'Aylık'])
                ->whereNotIn('name', ['Günlük Kiralama', 'Haftalık Kiralama', 'Aylık Kiralama']) // ✅ Geçerli alt kategorileri hariç tut
                ->get();

            // Debug log - Geliştirme modunda çalışır
            if (config('app.debug')) {
                Log::info('PropertyTypeManager: Alt kategoriler sorgulandı', [
                    'kategori_id' => $kategoriId,
                    'kategori_name' => $kategori->name,
                    'bulunan_alt_kategori_sayisi' => $altKategoriler->count(),
                    'alt_kategoriler' => $altKategoriler->map(function ($k) {
                        return [
                            'id' => $k->id,
                            'name' => $k->name,
                            'parent_id' => $k->parent_id,
                            'seviye' => $k->seviye,
                            'status' => $k->status,
                            'display_order' => $k->display_order,
                        ];
                    })->toArray(),
                ]);

                // Tüm parent_id değerlerini kontrol et (debug için)
                $tumParentIdler = IlanKategori::where('seviye', 1)
                    ->distinct()
                    ->pluck('parent_id')
                    ->toArray();

                Log::info('PropertyTypeManager: Veritabanında mevcut parent_id değerleri', [
                    'parent_ids' => $tumParentIdler,
                    'aradigimiz_id' => $kategoriId,
                    'eslesme_var_mi' => in_array($kategoriId, $tumParentIdler),
                ]);

                if ($yanlisEklenenYayinTipleri->isNotEmpty()) {
                    Log::warning('PropertyTypeManager: Yanlış eklenen yayın tipleri tespit edildi', [
                        'yanlis_kayitlar' => $yanlisEklenenYayinTipleri->map(function ($k) {
                            return [
                                'id' => $k->id,
                                'name' => $k->name,
                                'seviye' => $k->seviye,
                                'parent_id' => $k->parent_id,
                            ];
                        })->toArray(),
                    ]);
                }
            }

            // ✅ Context7: YENİ SİSTEM - ilan_kategori_yayin_tipleri tablosunu kullan
            // Yayın tipleri artık ana kategoriye bağlı (seviye=2 DEPRECATED!)
            // ✅ Tüm kategoriler için tutarlı: Ana kategori ID'si kullanılır
            $this->ensureDefaultYayinTipleri((int) $kategoriId);

            // Migration çalıştırıldı: order → display_order rename edildi
            // Artık sadece display_order kullan
            $yayinTipiOrderColumn = 'display_order';

            // ✅ Context7: Arsa kategorisi için "Yazlık Kiralık" filtrelenmeli
            $allYayinTipleriQuery = IlanKategoriYayinTipi::where('kategori_id', $kategoriId)
                ->where('status', true); // ✅ Status boolean - tüm kategoriler için tutarlı

            // ✅ Context7: Arsa kategorisi için "Yazlık Kiralık" yayın tipini filtrele
            if ($kategori->slug === 'arsa') {
                $allYayinTipleriQuery->where('yayin_tipi', '!=', 'Yazlık Kiralık');
            }

            $allYayinTipleri = $allYayinTipleriQuery
                ->when(Schema::hasColumn('ilan_kategori_yayin_tipleri', 'display_order'), function ($q) {
                    $q->orderByRaw('COALESCE(display_order, 999999) ASC');
                })
                ->orderBy('yayin_tipi', 'ASC')
                ->get();

            // Debug log - Yayın tipleri sorgulandı
            if (config('app.debug')) {
                Log::info('PropertyTypeManager: Yayın tipleri sorgulandı', [
                    'kategori_id' => $kategoriId,
                    'bulunan_yayin_tipi_sayisi' => $allYayinTipleri->count(),
                    'yayin_tipleri' => $allYayinTipleri->map(function ($yt) {
                        return ['id' => $yt->id, 'yayin_tipi' => $yt->yayin_tipi, 'status' => $yt->status, 'display_order' => $yt->display_order]; // ✅ Context7: order → display_order
                    })->toArray(),
                ]);
            }

            // Her alt kategori için hangi yayın tipleri aktif?
            // ✅ OPTIMIZED: N+1 query önlendi - Tüm alt kategori yayın tiplerini tek query'de al
            $altKategoriYayinTipleri = [];
            if (Schema::hasTable('alt_kategori_yayin_tipi')) {
                try {
                    $altKategoriIds = $altKategoriler->pluck('id')->toArray();

                    // ✅ Context7 FIX: enabled → status
                    // ✅ N+1 FIX: Select optimization
                    $altKategoriYayinTipleriRaw = AltKategoriYayinTipi::whereIn('alt_kategori_id', $altKategoriIds)
                        ->where('status', true) // Context7: enabled → status
                        ->select(['id', 'alt_kategori_id', 'yayin_tipi_id', 'status'])
                        ->get()
                        ->groupBy('alt_kategori_id')
                        ->map(function ($items) {
                            return $items->pluck('yayin_tipi_id');
                        });

                    // Her alt kategori için yayın tiplerini ata
                    foreach ($altKategoriler as $altKat) {
                        $altKategoriYayinTipleri[$altKat->id] = $altKategoriYayinTipleriRaw->get($altKat->id, collect([]));
                    }
                } catch (\Exception $e) {
                    // Tablo henüz yoksa veya hata varsa boş array
                    Log::warning('alt_kategori_yayin_tipi tablosu sorgulanamadı', [
                        'error' => $e->getMessage(),
                    ]);
                    foreach ($altKategoriler as $altKat) {
                        $altKategoriYayinTipleri[$altKat->id] = collect([]);
                    }
                }
            } else {
                // Tablo yoksa tüm alt kategoriler için boş array
                foreach ($altKategoriler as $altKat) {
                    $altKategoriYayinTipleri[$altKat->id] = collect([]);
                }
            }

            // Field dependencies - Grouped by yayin_tipi (Opsiyonel - tablo yoksa boş array)
            $fieldDependencies = [];

            try {
                $fieldDependenciesRaw = KategoriYayinTipiFieldDependency::where('kategori_slug', $kategori->slug)->get();

                // Grupla: field_slug => [yayin_tipi => status] (Context7: enabled → status)
                foreach ($fieldDependenciesRaw as $dep) {
                    $fieldDependencies[$dep->field_slug] = [
                        'field_name' => $dep->field_name,
                        'field_type' => $dep->field_type,
                        'field_icon' => $dep->field_icon ?? '📋',
                        'yayin_tipleri' => [],
                    ];
                }

                // ✅ OPTIMIZED: N+1 query önlendi - Tüm slug'ları tek query'de al
                $yayinTipiSlugs = $fieldDependenciesRaw
                    ->filter(fn($dep) => ! is_numeric($dep->yayin_tipi))
                    ->pluck('yayin_tipi')
                    ->unique()
                    ->toArray();

                $yayinTipiSlugToId = [];
                if (! empty($yayinTipiSlugs)) {
                    // ✅ N+1 FIX: Select optimization
                    $yayinTipiSlugToId = IlanKategori::whereIn('slug', $yayinTipiSlugs)
                        ->where('seviye', 2)
                        ->select(['id', 'slug'])
                        ->pluck('id', 'slug')
                        ->toArray();

                    // Slug'da bulunamazsa yayin_tipi field'ına göre ara
                    $missingSlugs = array_diff($yayinTipiSlugs, array_keys($yayinTipiSlugToId));
                    if (! empty($missingSlugs)) {
                        // ✅ N+1 FIX: Select optimization
                        $additionalYayinTipleri = IlanKategori::whereIn('yayin_tipi', $missingSlugs)
                            ->where('seviye', 2)
                            ->select(['id', 'yayin_tipi'])
                            ->pluck('id', 'yayin_tipi')
                            ->toArray();
                        $yayinTipiSlugToId = array_merge($yayinTipiSlugToId, $additionalYayinTipleri);
                    }
                }

                // Her field için yayın tipi durumları
                foreach ($fieldDependenciesRaw as $dep) {
                    if (isset($fieldDependencies[$dep->field_slug])) {
                        // Yayın tipi değeri: ID veya slug olabilir; her iki durumu da destekle
                        if (is_numeric($dep->yayin_tipi)) {
                            $yayinTipiId = (int) $dep->yayin_tipi;
                        } else {
                            $yayinTipiId = $yayinTipiSlugToId[$dep->yayin_tipi] ?? null;
                        }

                        if ($yayinTipiId) {
                            // ✅ Context7: enabled → status (backward compat: dep->enabled fallback)
                            $fieldDependencies[$dep->field_slug]['yayin_tipleri'][$yayinTipiId] = $dep->status ?? $dep->enabled ?? false;
                        }
                    }
                }

                $idToSlug = array_flip($yayinTipiSlugToId);
                $propertyTypeCounts = [];
                $dependenciesByType = [];
                foreach ($fieldDependenciesRaw as $dep) {
                    $key = is_numeric($dep->yayin_tipi) ? ($idToSlug[(int) $dep->yayin_tipi] ?? null) : (string) $dep->yayin_tipi;
                    if ($key) {
                        $propertyTypeCounts[$key] = ($propertyTypeCounts[$key] ?? 0) + 1;
                        $dependenciesByType[$key] = $dependenciesByType[$key] ?? [];
                        $dependenciesByType[$key][] = $dep;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Field dependencies table not found', ['error' => $e->getMessage()]);
                // Tablo yoksa boş array ile devam et
            }

            // ✅ Context7: Features - Kategori bazlı filtreleme
            $featuresQuery = Feature::with('category')->enabled();
            $featureCategoriesQuery = FeatureCategory::with(['features' => function ($q) {
                $q->enabled();
            }]);

            // ✅ Context7: Kategori bazlı feature filtreleme (fieldDependenciesIndex ile aynı mantık)
            $kategoriSlug = $kategori->slug;
            $allowed = $this->allowedFeatureCategoryNames($kategoriSlug);
            if (empty($allowed)) {
                $allowed = ['Genel Özellikler'];
            }
            $availableFeaturesQuery = Feature::with('category')->enabled();
            $availableFeaturesQuery->whereHas('category', function ($q) use ($allowed, $kategoriSlug) {
                $q->whereIn('name', $allowed)->orWhere('applies_to', $kategoriSlug);
            });

            $availableFeaturesData = $availableFeaturesQuery->get();
            if ($availableFeaturesData->isEmpty()) {
                $availableFeaturesData = Feature::with('category')->enabled()->get();
            }
            $availableFeatures = $availableFeaturesData->groupBy(function ($feature) {
                return $feature->category ? $feature->category->name : 'Genel Özellikler';
            });

            if ($allYayinTipleri->isEmpty()) {
                $yayinTipleri = \Illuminate\Support\Collection::empty();
            } else {
                $yayinTipleri = $allYayinTipleri;
            }
            if ($availableFeatures->isEmpty()) {
                $availableFeatures = \Illuminate\Support\Collection::empty();
            }
            if (empty($fieldDependencies)) {
                $fieldDependencies = \Illuminate\Support\Collection::empty();
            }

            // Assignments by property type
            $assignmentCounts = [];
            $assignmentsByType = [];
            if ($yayinTipleri instanceof \Illuminate\Support\Collection && $yayinTipleri->count() > 0) {
                $typeIds = $yayinTipleri->pluck('id')->all();
                $allAssignments = \App\Models\FeatureAssignment::whereIn('assignable_id', $typeIds)
                    ->where('assignable_type', IlanKategoriYayinTipi::class)
                    ->with(['feature.category'])
                    ->get();
                foreach ($typeIds as $tid) {
                    $group = $allAssignments->where('assignable_id', $tid);
                    $assignmentCounts[$tid] = $group->count();
                    $assignmentsByType[$tid] = $group;
                }
            }

            return view('admin.property-type-manager.field-dependencies', [
                'kategori' => $kategori,
                'kategoriId' => (int) $kategoriId,
                'yayinTipleri' => $yayinTipleri,
                'fieldDependencies' => $fieldDependencies,
                'availableFeatures' => $availableFeatures,
                'propertyTypeCounts' => $propertyTypeCounts ?? [],
                'dependenciesByType' => $dependenciesByType ?? [],
                'assignmentCounts' => $assignmentCounts,
                'assignmentsByType' => $assignmentsByType,
                'propertyTypesSummary' => ($yayinTipleri instanceof \Illuminate\Support\Collection)
                    ? ['count' => $yayinTipleri->count(), 'aktif' => $yayinTipleri->where('status', true)->count()]
                    : [],
            ]);
        } catch (\Throwable $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                throw $e;
            }
            Log::channel('module_errors')->debug([
                'event' => 'property_type_manager_show_error',
                'kategori_id' => (int) $kategoriId,
                'message' => $e->getMessage(),
            ]);
            abort(500);
        }
    }

    /**
     * Field Dependencies - Store (Yeni alan ekle)
     */
    public function storeFieldDependency(Request $request, $kategoriId)
    {
        $kategori = IlanKategori::findOrFail($kategoriId);

        $validated = $request->validate([
            'yayin_tipi' => 'nullable|string',
            'yayin_tipi_id' => 'nullable',
            'field_slug' => 'required|string|max:100',
            'field_name' => 'required|string|max:255',
            'field_type' => 'required|in:text,number,boolean,select,textarea,date,price',
            'field_category' => 'required|string|max:50',
            'field_options' => 'nullable|json',
            'field_unit' => 'nullable|string|max:20',
            'field_icon' => 'nullable|string|max:10',
            'status' => 'boolean', // Context7: enabled → status
            'required' => 'boolean',
            'display_order' => 'nullable|integer|min:0', // ✅ Context7: order → display_order
            'ai_auto_fill' => 'boolean',
            'ai_suggestion' => 'boolean',
            'searchable' => 'boolean',
            'show_in_card' => 'boolean',
        ]);

        $validated['kategori_slug'] = $kategori->slug;
        // Context7: Backward compatibility - accept 'enabled' but use 'status'
        $validated['status'] = $request->boolean('status', $request->boolean('enabled', true));
        $validated['required'] = $request->boolean('required', false);
        $validated['ai_auto_fill'] = $request->boolean('ai_auto_fill', false);
        $validated['ai_suggestion'] = $request->boolean('ai_suggestion', false);
        $validated['searchable'] = $request->boolean('searchable', false);
        $validated['show_in_card'] = $request->boolean('show_in_card', false);
        // Yayın tipi anahtarını ID öncelikli kaydet
        $validated['yayin_tipi'] = (string) ($request->input('yayin_tipi_id') ?? $request->input('yayin_tipi'));

        $allowed = $this->allowedFeatureCategoryNames($kategori->slug);
        if (! in_array($validated['field_category'], $allowed, true)) {
            return redirect()->route('admin.property_types.show', $kategoriId)->withErrors(['field_category' => 'Geçersiz kategori']);
        }

        KategoriYayinTipiFieldDependency::create($validated);

        return redirect()
            ->route('admin.property_types.show', $kategoriId)
            ->with('success', '✅ Alan ilişkisi başarıyla eklendi!');
    }

    /**
     * Field Dependencies - Update
     */
    public function updateFieldDependency(Request $request, $kategoriId, $fieldId)
    {
        $field = KategoriYayinTipiFieldDependency::findOrFail($fieldId);

        // ✅ FIX: Inline rename için sadece field_name güncellenebilir
        // Eğer sadece field_name varsa, hızlı güncelleme yap
        if ($request->has('field_name') && count($request->keys()) <= 3) { // field_name + _method + csrf
            $request->validate([
                'field_name' => 'required|string|max:255',
            ]);

            $field->update(['field_name' => $request->field_name]);

            // AJAX için JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Alan adı güncellendi',
                    'field' => $field,
                ]);
            }

            return redirect()
                ->route('admin.property-type-manager.field-dependencies', $kategoriId)
                ->with('success', '✅ Alan adı başarıyla güncellendi!');
        }

        // Full update (tüm alanlar)
        // ⚠️ NOT: kategori_slug, yayin_tipi ve field_slug UNIQUE constraint'i oluşturur
        // Bu alanlar update edilemez (identity fields)
        $validated = $request->validate([
            // 'yayin_tipi' => REMOVED - Identity field (unique constraint part)
            // 'field_slug' => REMOVED - Identity field (unique constraint part)
            // 'kategori_slug' => REMOVED - Identity field (unique constraint part)
            'field_name' => 'sometimes|required|string|max:255',
            'field_type' => 'sometimes|required|in:text,number,boolean,select,textarea,date,price',
            'field_category' => 'sometimes|required|string|max:50',
            'field_options' => 'nullable|json',
            'field_unit' => 'nullable|string|max:20',
            'field_icon' => 'nullable|string|max:10',
            'status' => 'boolean', // Context7: enabled → status
            'required' => 'boolean',
            'display_order' => 'nullable|integer|min:0', // ✅ Context7: order → display_order
            'ai_auto_fill' => 'boolean',
            'ai_suggestion' => 'boolean',
            'searchable' => 'boolean',
            'show_in_card' => 'boolean',
        ]);

        // Boolean fields - explicit conversion
        // Context7: Backward compatibility - accept 'enabled' but use 'status'
        $validated['status'] = $request->boolean('status', $request->boolean('enabled', $field->status ?? true));
        $validated['required'] = $request->boolean('required', $field->required);
        $validated['ai_auto_fill'] = $request->boolean('ai_auto_fill', $field->ai_auto_fill ?? false);
        $validated['ai_suggestion'] = $request->boolean('ai_suggestion', $field->ai_suggestion ?? false);
        $validated['searchable'] = $request->boolean('searchable', $field->searchable);
        $validated['show_in_card'] = $request->boolean('show_in_card', $field->show_in_card);
        $allowed = $this->allowedFeatureCategoryNames($field->kategori_slug);
        if (array_key_exists('field_category', $validated) && ! in_array($validated['field_category'], $allowed, true)) {
            return redirect()->route('admin.property-type-manager.field-dependencies', $kategoriId)->withErrors(['field_category' => 'Geçersiz kategori']);
        }

        $field->update($validated);

        // AJAX için JSON response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Alan başarıyla güncellendi',
                'field' => $field,
            ]);
        }

        return redirect()
            ->route('admin.property-type-manager.field-dependencies', $kategoriId)
            ->with('success', '✅ Alan ilişkisi başarıyla güncellendi!');
    }

    /**
     * Field Dependencies - Delete
     */
    public function destroyFieldDependency($kategoriId, $fieldId)
    {
        $field = KategoriYayinTipiFieldDependency::findOrFail($fieldId);
        $field->delete();

        return redirect()
            ->route('admin.property-type-manager.field-dependencies', $kategoriId)
            ->with('success', '✅ Alan ilişkisi başarıyla silindi!');
    }

    /**
     * Field dependency toggle (AJAX)
     */
    public function toggleFieldDependency(Request $request)
    {
        // İki mod: 1) field_id ile güncelle 2) yoksa upsert ile oluştur ve güncelle
        $request->validate([
            'status' => 'required_without:enabled|boolean', // Context7: enabled → status
            'enabled' => 'required_without:status|boolean', // Backward compatibility
            'field_id' => 'nullable|integer',
            'kategori_slug' => 'required_without:field_id|string',
            // yayin_tipi_id veya yayin_tipi (slug) ikilisinden en az biri
            'yayin_tipi_id' => 'required_without_all:field_id,yayin_tipi|nullable',
            'yayin_tipi' => 'required_without_all:field_id,yayin_tipi_id|nullable|string',
            'field_slug' => 'required_without:field_id|string',
            'field_name' => 'sometimes|string|max:255',
            'field_type' => 'sometimes|string|max:50',
            'field_category' => 'sometimes|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            // Context7: Backward compatibility - accept 'enabled' but use 'status'
            $status = $request->has('status') ? ($request->status ? 1 : 0) : ($request->enabled ? 1 : 0);
            $fieldId = $request->input('field_id');

            if (empty($fieldId)) {
                // Kayıt yoksa oluştur veya mevcut olanı bul
                $yayinKey = (string) ($request->input('yayin_tipi_id') ?? $request->input('yayin_tipi'));
                $defaults = [
                    'field_name' => $request->input('field_name', 'Field'),
                    'field_type' => $request->input('field_type', 'text'),
                    'field_category' => $request->input('field_category', 'general'),
                    'status' => $status, // Context7: enabled → status
                    'display_order' => 0, // ✅ Context7: order → display_order
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $existing = KategoriYayinTipiFieldDependency::where('kategori_slug', $request->kategori_slug)
                    ->where('yayin_tipi', $yayinKey)
                    ->where('field_slug', $request->field_slug)
                    ->first();

                if ($existing) {
                    $fieldId = $existing->id;
                    $existing->update(['status' => $status, 'updated_at' => now()]); // Context7: enabled → status
                } else {
                    $field = KategoriYayinTipiFieldDependency::create(array_merge([
                        'kategori_slug' => $request->kategori_slug,
                        // Yayın tipi ID olarak saklanır (string de olabilir)
                        'yayin_tipi' => $yayinKey,
                        'field_slug' => $request->field_slug,
                    ], $defaults));
                    $fieldId = $field->id;
                }
            } else {
                // Doğrudan güncelle
                $field = KategoriYayinTipiFieldDependency::find($fieldId);
                if ($field) {
                    $field->update(['status' => $status, 'updated_at' => now()]); // Context7: enabled → status
                    $updated = 1;
                } else {
                    $updated = 0;
                }
                if ($updated === 0) {
                    throw new \Exception('Field not found or update failed');
                }
            }

            DB::commit();

            Log::info('✅ Field dependency toggled/upserted', [
                'field_id' => $fieldId,
                'status' => $status,
                'kategori_slug' => $request->input('kategori_slug'),
                'yayin_tipi' => $request->input('yayin_tipi'),
                'field_slug' => $request->input('field_slug'),
            ]);

            return response()->json([
                'success' => true,
                'message' => $status ? 'Alan aktif edildi' : 'Alan pasif edildi',
                'data' => [
                    'field_id' => $fieldId,
                    'status' => $status,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Field dependency toggle failed:', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Güncelleme sırasında bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Context7: order → display_order (forbidden pattern)
    public function updateFieldSequence($kategoriId, Request $request)
    {
        // Context7: order → display_order (forbidden pattern)
        $items = $request->input('display_order') ?? $request->input('items') ?? [];
        if (empty($items)) {
            return response()->json(['success' => true, 'message' => 'Sıralama güncellendi'], 200);
        }
        DB::beginTransaction();
        $ids = [];
        $bindings = [];
        $cases = [];
        foreach ($items as $item) {
            $ids[] = $item['id'];
            $cases[] = 'WHEN ? THEN ?';
            $bindings[] = $item['id'];
            $bindings[] = $item['display_order'];
        }
        if (! empty($ids)) {
            $idsPlaceholder = implode(',', array_fill(0, count($ids), '?'));
            $casesSql = implode(' ', $cases);
            $table = Schema::hasColumn('ozellikler', 'display_order') ? 'ozellikler' : 'ilan_kategori_yayin_tipleri';
            $where = "WHERE id IN ({$idsPlaceholder})";
            $finalBindings = array_merge($bindings, $ids);
            if ($table === 'ilan_kategori_yayin_tipleri' && $kategoriId !== null) {
                $where .= ' AND kategori_id = ?';
                $finalBindings[] = $kategoriId;
            }
            DB::statement(
                "UPDATE {$table} SET display_order = CASE id {$casesSql} END {$where}",
                $finalBindings
            );
        }
        DB::commit();

        return response()->json(['success' => true, 'message' => 'Sıralama güncellendi']);
    }

    /**
     * Feature toggle
     * ✅ Context7: enabled → status (backward compatibility)
     */
    public function toggleFeature(Request $request)
    {
        $request->validate([
            'feature_id' => 'required|exists:features,id',
            'status' => 'required_without:enabled|boolean', // ✅ Context7: status field
            'enabled' => 'required_without:status|boolean', // Backward compatibility
        ]);

        // ✅ Context7: Backward compatibility - accept 'enabled' but use 'status'
        $status = $request->has('status') ? $request->boolean('status') : $request->boolean('enabled');

        Feature::where('id', $request->feature_id)
            ->update(['status' => $status]);

        return response()->json(['success' => true]);
    }

    public function bulkSave($kategoriId, Request $request)
    {
        $yayinTipiUpdates = $request->input('yayin_tipi_updates', $request->input('yayin_tipleri', []));
        $featureUpdates = $request->input('feature_updates', $request->input('features', []));
        $fieldDepUpdates = $request->input('field_dependency_updates', $request->input('field_dependencies', []));
        if (empty($yayinTipiUpdates) && empty($featureUpdates) && empty($fieldDepUpdates)) {
            return response()->json(['success' => true, 'message' => 'Toplu kayıtlar güncellendi']);
        }
        DB::transaction(function () use ($yayinTipiUpdates, $featureUpdates, $fieldDepUpdates, $kategoriId) {
            if (! empty($yayinTipiUpdates)) {
                foreach ($yayinTipiUpdates as $u) {
                    $where = [];
                    if (isset($u['id'])) {
                        $where['id'] = (int) $u['id'];
                    } else {
                        if (! isset($u['kategori_id'])) {
                            $u['kategori_id'] = (int) $kategoriId;
                        }
                        if (isset($u['kategori_id'])) {
                            $where['kategori_id'] = (int) $u['kategori_id'];
                        }
                        if (isset($u['yayin_tipi'])) {
                            $where['yayin_tipi'] = $u['yayin_tipi'];
                        }
                    }
                    if ($where) {
                        $data = [];
                        if (array_key_exists('status', $u)) {
                            $data['status'] = (bool) $u['status'];
                        }
                        if (array_key_exists('display_order', $u)) {
                            $data['display_order'] = (int) $u['display_order'];
                        }
                        if (! empty($data)) {
                            IlanKategoriYayinTipi::where($where)->update($data);
                        }
                    }
                }
            }
            if (! empty($featureUpdates)) {
                foreach ($featureUpdates as $u) {
                    if (! isset($u['id'])) {
                        continue;
                    }
                    $data = [];
                    if (array_key_exists('status', $u)) {
                        $data['status'] = (bool) $u['status'];
                    }
                    if (array_key_exists('display_order', $u)) {
                        $data['display_order'] = (int) $u['display_order'];
                    }
                    if (Schema::hasColumn('features', 'visible') && array_key_exists('visible', $u)) {
                        $data['visible'] = (bool) $u['visible'];
                    }
                    if (! empty($data)) {
                        Feature::where('id', (int) $u['id'])->update($data);
                    }
                }
            }
            if (! empty($fieldDepUpdates)) {
                foreach ($fieldDepUpdates as $u) {
                    if (! isset($u['kategori_slug'], $u['yayin_tipi'], $u['field_slug'])) {
                        continue;
                    }
                    $data = [];
                    if (array_key_exists('status', $u)) {
                        $data['status'] = (bool) $u['status'];
                    }
                    if (! empty($data)) {
                        KategoriYayinTipiFieldDependency::where([
                            'kategori_slug' => $u['kategori_slug'],
                            'yayin_tipi' => $u['yayin_tipi'],
                            'field_slug' => $u['field_slug'],
                        ])->update($data);
                    }
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Toplu kayıtlar güncellendi']);
    }

    /**
     * ========================================
     * POLYMORPHIC FEATURE ASSIGNMENT METHODS
     * ========================================
     */

    /**
     * Assign feature to property type
     */
    public function assignFeature(Request $request, $propertyTypeId)
    {
        $request->validate([
            'feature_id' => 'required|exists:features,id',
            'is_required' => 'nullable|boolean',
            'is_visible' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0', // ✅ Context7: order → display_order
            'group_name' => 'nullable|string|max:100',
        ]);

        try {
            $propertyType = IlanKategoriYayinTipi::findOrFail($propertyTypeId);
            $feature = Feature::findOrFail($request->feature_id);

            $assignment = $propertyType->assignFeature($feature, [
                'is_required' => $request->boolean('is_required', false),
                'is_visible' => $request->boolean('is_visible', true),
                'display_order' => $request->input('display_order', 0), // ✅ Context7: order → display_order
                'group_name' => $request->input('group_name'),
            ]);

            Log::info('Feature assigned to property type', [
                'property_type_id' => $propertyTypeId,
                'feature_id' => $request->feature_id,
                'assignment_id' => $assignment->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Özellik başarıyla atandı',
                'data' => [
                    'assignment_id' => $assignment->id,
                    'feature' => $feature->only(['id', 'name', 'slug', 'field_type']),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Feature assignment failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Özellik atama hatası: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unassign feature from property type
     */
    public function unassignFeature(Request $request, $propertyTypeId)
    {
        $request->validate([
            'feature_id' => 'required|exists:features,id',
        ]);

        try {
            $propertyType = IlanKategoriYayinTipi::findOrFail($propertyTypeId);
            $feature = Feature::findOrFail($request->feature_id);

            $propertyType->unassignFeature($feature);

            Log::info('Feature unassigned from property type', [
                'property_type_id' => $propertyTypeId,
                'feature_id' => $request->feature_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Özellik kaldırıldı',
            ]);
        } catch (\Exception $e) {
            Log::error('Feature unassignment failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Özellik kaldırma hatası: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle feature assignment visibility/requirement
     */
    public function toggleFeatureAssignment(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:feature_assignments,id',
            'field' => 'required|in:is_visible,is_required',
            'value' => 'required|boolean',
        ]);

        try {
            $assignment = FeatureAssignment::findOrFail($request->assignment_id);
            $field = $request->field;
            $value = $request->boolean('value');

            $assignment->update([$field => $value]);

            Log::info('Feature assignment toggled', [
                'assignment_id' => $request->assignment_id,
                'field' => $field,
                'value' => $value,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Özellik güncellendi',
                'data' => [
                    'assignment_id' => $assignment->id,
                    $field => $value,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Feature assignment toggle failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Güncelleme hatası: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync features for property type (bulk)
     */
    public function syncFeatures(Request $request, $propertyTypeId)
    {
        $request->validate([
            'feature_ids' => 'required|array',
            'feature_ids.*' => 'exists:features,id',
        ]);

        DB::beginTransaction();
        try {
            $propertyType = IlanKategoriYayinTipi::findOrFail($propertyTypeId);
            $propertyType->syncFeatures($request->feature_ids);

            DB::commit();

            Log::info('Features synced for property type', [
                'property_type_id' => $propertyTypeId,
                'feature_count' => count($request->feature_ids),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Özellikler güncellendi',
                'data' => [
                    'synced_count' => count($request->feature_ids),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Feature sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Senkronizasyon hatası: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update feature assignment configuration
     */
    public function updateFeatureAssignment(Request $request, $assignmentId)
    {
        $request->validate([
            'is_required' => 'nullable|boolean',
            'is_visible' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0', // ✅ Context7: order → display_order
            'group_name' => 'nullable|string|max:100',
        ]);

        try {
            $assignment = FeatureAssignment::findOrFail($assignmentId);

            $assignment->update($request->only([
                'is_required',
                'is_visible',
                'display_order', // ✅ Context7: order → display_order
                'group_name',
            ]));

            Log::info('Feature assignment updated', [
                'assignment_id' => $assignmentId,
                'updates' => $request->only(['is_required', 'is_visible', 'display_order', 'group_name']), // ✅ Context7: order → display_order
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Özellik ayarları güncellendi',
                'data' => $assignment->only(['id', 'is_required', 'is_visible', 'display_order', 'group_name']), // ✅ Context7: order → display_order
            ]);
        } catch (\Exception $e) {
            Log::error('Feature assignment update failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Güncelleme hatası: ' . $e->getMessage(),
            ], 500);
        }
    }
}
