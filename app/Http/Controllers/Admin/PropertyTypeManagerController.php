<?php

namespace App\Http\Controllers\Admin;

use App\Models\IlanKategori;
use App\Models\IlanKategoriYayinTipi;
use App\Models\Feature;
use App\Models\FeatureCategory;
use App\Models\FeatureAssignment;
use App\Models\KategoriYayinTipiFieldDependency;
use App\Models\AltKategoriYayinTipi;
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
                'bindings' => $query->getBindings()
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
                'bindings' => $kategoriler->getBindings()
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
                        })->toArray()
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
                    })->toArray()
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
        $kategori = IlanKategori::findOrFail($kategoriId);
        $kategoriId = (int)$kategoriId;

        // ✅ Tüm kategoriler için tutarlılık: Ana kategori kontrolü (seviye=0 olmalı)
        if ($kategori->seviye !== 0) {
            Log::warning('PropertyTypeManager: Ana kategori beklenirken farklı seviye tespit edildi', [
                'kategori_id' => $kategoriId,
                'kategori_name' => $kategori->name,
                'seviye' => $kategori->seviye,
                'beklenen_seviye' => 0
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
                        'display_order' => $k->display_order
                    ];
                })->toArray()
            ]);

            // Tüm parent_id değerlerini kontrol et (debug için)
            $tumParentIdler = IlanKategori::where('seviye', 1)
                ->distinct()
                ->pluck('parent_id')
                ->toArray();

            Log::info('PropertyTypeManager: Veritabanında mevcut parent_id değerleri', [
                'parent_ids' => $tumParentIdler,
                'aradigimiz_id' => $kategoriId,
                'eslesme_var_mi' => in_array($kategoriId, $tumParentIdler)
            ]);

            if ($yanlisEklenenYayinTipleri->isNotEmpty()) {
                Log::warning('PropertyTypeManager: Yanlış eklenen yayın tipleri tespit edildi', [
                    'yanlis_kayitlar' => $yanlisEklenenYayinTipleri->map(function ($k) {
                        return [
                            'id' => $k->id,
                            'name' => $k->name,
                            'seviye' => $k->seviye,
                            'parent_id' => $k->parent_id
                        ];
                    })->toArray()
                ]);
            }
        }

        // ✅ Context7: YENİ SİSTEM - ilan_kategori_yayin_tipleri tablosunu kullan
        // Yayın tipleri artık ana kategoriye bağlı (seviye=2 DEPRECATED!)
        // ✅ Tüm kategoriler için tutarlı: Ana kategori ID'si kullanılır
        $this->ensureDefaultYayinTipleri((int)$kategoriId);

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
                })->toArray()
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
                    'yayin_tipleri' => []
                ];
            }

            // ✅ OPTIMIZED: N+1 query önlendi - Tüm slug'ları tek query'de al
            $yayinTipiSlugs = $fieldDependenciesRaw
                ->filter(fn($dep) => !is_numeric($dep->yayin_tipi))
                ->pluck('yayin_tipi')
                ->unique()
                ->toArray();

            $yayinTipiSlugToId = [];
            if (!empty($yayinTipiSlugs)) {
                // ✅ N+1 FIX: Select optimization
                $yayinTipiSlugToId = IlanKategori::whereIn('slug', $yayinTipiSlugs)
                    ->where('seviye', 2)
                    ->select(['id', 'slug'])
                    ->pluck('id', 'slug')
                    ->toArray();

                // Slug'da bulunamazsa yayin_tipi field'ına göre ara
                $missingSlugs = array_diff($yayinTipiSlugs, array_keys($yayinTipiSlugToId));
                if (!empty($missingSlugs)) {
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
                        $yayinTipiId = (int)$dep->yayin_tipi;
                    } else {
                        $yayinTipiId = $yayinTipiSlugToId[$dep->yayin_tipi] ?? null;
                    }

                    if ($yayinTipiId) {
                        // ✅ Context7: enabled → status (backward compat: dep->enabled fallback)
                        $fieldDependencies[$dep->field_slug]['yayin_tipleri'][$yayinTipiId] = $dep->status ?? $dep->enabled ?? false;
                    }
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
        if ($kategoriSlug === 'arsa') {
            // Arsa için sadece "Arsa Özellikleri" ve "Genel Özellikler" kategorilerini göster
            $featureCategoriesQuery->whereIn('name', ['Arsa Özellikleri', 'Genel Özellikler']);
            $featuresQuery->whereHas('category', function ($q) {
                $q->whereIn('name', ['Arsa Özellikleri', 'Genel Özellikler']);
            });
        } elseif ($kategoriSlug === 'konut') {
            // Konut için "Konut Özellikleri" ve "Genel Özellikler" göster
            $featureCategoriesQuery->whereIn('name', ['Konut Özellikleri', 'Genel Özellikler']);
            $featuresQuery->whereHas('category', function ($q) {
                $q->whereIn('name', ['Konut Özellikleri', 'Genel Özellikler']);
            });
        } elseif ($kategoriSlug === 'yazlik') {
            // Yazlık için "Yazlık Özellikleri" ve "Genel Özellikler" göster
            $featureCategoriesQuery->whereIn('name', ['Yazlık Özellikleri', 'Genel Özellikler']);
            $featuresQuery->whereHas('category', function ($q) {
                $q->whereIn('name', ['Yazlık Özellikleri', 'Genel Özellikler']);
            });
        } elseif ($kategoriSlug === 'isyeri') {
            // İşyeri için "Ticari Özellikler" ve "Genel Özellikler" göster
            $featureCategoriesQuery->whereIn('name', ['Ticari Özellikler', 'Genel Özellikler']);
            $featuresQuery->whereHas('category', function ($q) {
                $q->whereIn('name', ['Ticari Özellikler', 'Genel Özellikler']);
            });
        }
        // Diğer kategoriler için tüm feature'ları göster

        $features = $featuresQuery->get();
        $featureCategories = $featureCategoriesQuery->get();

        return view('admin.property-type-manager.show', compact(
            'kategori',
            'altKategoriler',
            'altKategoriYayinTipleri',
            'allYayinTipleri',
            'fieldDependencies',
            'features',
            'featureCategories',
            'yanlisEklenenYayinTipleri'
        ));
    }

    /**
     * Context7: Tüm kategoriler için eksik yayın tiplerini otomatik ekle
     *
     * Bu metod tüm ana kategoriler için standart yayın tiplerini kontrol eder
     * ve eksik olanları ekler.
     *
     * Context7 Uyumluluk:
     * - ✅ status field kullanımı (aktif/is_active YASAK)
     * - ✅ display_order kullanımı (order YASAK)
     * - ✅ yayin_tipi field kullanımı (name YASAK)
     */
    public function ensureAllYayinTipleri()
    {
        try {
            $anaKategoriler = IlanKategori::where('seviye', 0)
                ->where('status', true)
                ->get(['id', 'name']);

            $eklenenSayisi = 0;
            $guncellenenSayisi = 0;
            $kategoriler = [];

            foreach ($anaKategoriler as $kategori) {
                $oncekiSayi = IlanKategoriYayinTipi::where('kategori_id', $kategori->id)->count();

                $this->ensureDefaultYayinTipleri($kategori->id);

                $sonrakiSayi = IlanKategoriYayinTipi::where('kategori_id', $kategori->id)->count();

                if ($sonrakiSayi > $oncekiSayi) {
                    $eklenenSayisi += ($sonrakiSayi - $oncekiSayi);
                } else {
                    $guncellenenSayisi += $sonrakiSayi;
                }

                $kategoriler[] = [
                    'id' => $kategori->id,
                    'name' => $kategori->name,
                    'yayin_tipi_sayisi' => $sonrakiSayi
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Tüm kategoriler için yayın tipleri kontrol edildi ve eksikler eklendi',
                'data' => [
                    'toplam_kategori' => $anaKategoriler->count(),
                    'eklenen_yayin_tipi' => $eklenenSayisi,
                    'guncellenen_yayin_tipi' => $guncellenenSayisi,
                    'kategoriler' => $kategoriler
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('ensureAllYayinTipleri failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Context7: Tüm kategoriler için standart yayın tiplerini oluştur/güncelle
     *
     * Yayın Tipleri:
     * - Satılık (display_order: 1)
     * - Kiralık (display_order: 2)
     * - Yazlık Kiralık (display_order: 3) - Konut için özellikle önemli
     *
     * Context7 Uyumluluk:
     * - ✅ status field kullanımı (aktif/is_active YASAK)
     * - ✅ display_order kullanımı (order YASAK)
     * - ✅ yayin_tipi field kullanımı (name YASAK)
     */
    private function ensureDefaultYayinTipleri(int $kategoriId): void
    {
        try {
            // ✅ Context7: Kategori bazlı yayın tipleri
            $kategori = IlanKategori::find($kategoriId);
            $kategoriSlug = $kategori ? $kategori->slug : null;

            // Standart yayın tipleri (tüm kategoriler için)
            $defaults = [
                ['yayin_tipi' => 'Satılık', 'display_order' => 1, 'icon' => '💰'],
                ['yayin_tipi' => 'Kiralık', 'display_order' => 2, 'icon' => '🔑'],
            ];

            // ✅ Context7: Arsa kategorisi için "Yazlık Kiralık" EKLEME
            // Yazlık Kiralık sadece Konut ve Yazlık kategorileri için geçerli
            if ($kategoriSlug !== 'arsa') {
                $defaults[] = ['yayin_tipi' => 'Yazlık Kiralık', 'display_order' => 3, 'icon' => '🏖️'];
            }

            // Debug log - Geliştirme modunda çalışır
            if (config('app.debug')) {
                $existingRecords = IlanKategoriYayinTipi::where('kategori_id', $kategoriId)->get();
                Log::info('PropertyTypeManager: ensureDefaultYayinTipleri kontrolü', [
                    'kategori_id' => $kategoriId,
                    'mevcut_kayit_sayisi' => $existingRecords->count(),
                    'mevcut_kayitlar' => $existingRecords->map(function ($r) {
                        return ['id' => $r->id, 'yayin_tipi' => $r->yayin_tipi, 'status' => $r->status, 'display_order' => $r->display_order];
                    })->toArray()
                ]);
            }

            // ✅ PERFORMANCE FIX: N+1 query önlendi - Tüm kayıtları tek query'de al
            $existingRecords = IlanKategoriYayinTipi::withTrashed()
                ->where('kategori_id', $kategoriId)
                ->whereIn('yayin_tipi', array_column($defaults, 'yayin_tipi'))
                ->get()
                ->keyBy('yayin_tipi');

            foreach ($defaults as $default) {
                $yayinTipi = $default['yayin_tipi'];
                $displayOrder = $default['display_order'];
                $icon = $default['icon'] ?? null;

                $record = $existingRecords->get($yayinTipi);

                if ($record) {
                    // ✅ Context7: Sadece aktif kayıtları güncelle
                    // Soft-deleted kayıtları restore etme (kullanıcı silmişse tekrar oluşturma)
                    if (!$record->trashed()) {
                        // Aktif kayıt varsa sadece güncelle
                        $record->update([
                            'status' => true,
                            'display_order' => $displayOrder,
                            'icon' => $icon,
                        ]);
                    }
                    // Soft-deleted kayıt varsa hiçbir şey yapma (kullanıcı silmişse tekrar oluşturma)
                } else {
                    // Yeni kayıt oluştur (sadece hiç kayıt yoksa)
                    IlanKategoriYayinTipi::create([
                        'kategori_id' => $kategoriId,
                        'yayin_tipi' => $yayinTipi,
                        'status' => true, // ✅ Context7: status field
                        'display_order' => $displayOrder, // ✅ Context7: display_order field
                        'icon' => $icon,
                    ]);
                }
            }

            // Debug log - Yayın tipleri oluşturuldu/güncellendi
            if (config('app.debug')) {
                $createdRecords = IlanKategoriYayinTipi::where('kategori_id', $kategoriId)
                    ->orderBy('display_order')
                    ->get();
                Log::info('PropertyTypeManager: Yayın tipleri oluşturuldu/güncellendi', [
                    'kategori_id' => $kategoriId,
                    'olusturulan_kayit_sayisi' => $createdRecords->count(),
                    'olusturulan_kayitlar' => $createdRecords->map(function ($r) {
                        return [
                            'id' => $r->id,
                            'yayin_tipi' => $r->yayin_tipi,
                            'status' => $r->status,
                            'display_order' => $r->display_order,
                            'icon' => $r->icon,
                        ];
                    })->toArray()
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('ensureDefaultYayinTipleri failed', [
                'kategori_id' => $kategoriId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Yayın tipi toggle
     * ✅ FIX: Pivot tablo kullan (alt_kategori_yayin_tipi)
     */
    public function toggleYayinTipi(Request $request, $kategoriId)
    {
        try {
            $validated = $request->validate([
                'alt_kategori_id' => 'required|integer|exists:ilan_kategorileri,id',
                'yayin_tipi_id' => 'required|integer|exists:ilan_kategori_yayin_tipleri,id',
                'status' => 'required|boolean' // Context7: enabled → status
            ]);

            $altKategoriId = $validated['alt_kategori_id'];
            $yayinTipiId = $validated['yayin_tipi_id'];
            $status = $validated['status']; // Context7: enabled → status

            // Debug log
            Log::info('toggleYayinTipi called', [
                'kategori_id' => $kategoriId,
                'alt_kategori_id' => $altKategoriId,
                'yayin_tipi_id' => $yayinTipiId,
                'status' => $status // Context7: enabled → status
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('toggleYayinTipi validation failed', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('toggleYayinTipi error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }

        $altKategoriId = $request->alt_kategori_id;
        $yayinTipiId = $request->yayin_tipi_id;
        $status = $request->status ?? $request->enabled ?? true; // Context7: enabled → status (backward compat)

        if ($status) {
            // ✅ Context7: İlişkiyi ekle veya güncelle (tablo kontrolü ile)
            if (Schema::hasTable('alt_kategori_yayin_tipi')) {
                AltKategoriYayinTipi::updateOrCreate(
                    [
                        'alt_kategori_id' => $altKategoriId,
                        'yayin_tipi_id' => $yayinTipiId
                    ],
                    [
                        'status' => true, // Context7: enabled → status
                        'display_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        } else {
            // ✅ Context7: İlişkiyi devre dışı bırak (status = false) (tablo kontrolü ile)
            if (Schema::hasTable('alt_kategori_yayin_tipi')) {
                AltKategoriYayinTipi::where('alt_kategori_id', $altKategoriId)
                    ->where('yayin_tipi_id', $yayinTipiId)
                    ->update([
                        'status' => false, // Context7: enabled → status
                        'updated_at' => now()
                    ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Yayın tipi ilişkisi güncellendi',
            'data' => [
                'alt_kategori_id' => $altKategoriId,
                'yayin_tipi_id' => $yayinTipiId,
                'status' => $status // Context7: enabled → status
            ]
        ]);
    }

    /**
     * Context7: Yeni yayın tipi oluştur (kategori bazlı)
     *
     * Context7 Uyumluluk:
     * - ✅ status field kullanımı (aktif/is_active YASAK)
     * - ✅ display_order kullanımı (order YASAK)
     * - ✅ yayin_tipi field kullanımı (name YASAK - request'te 'name' kabul edilir ama DB'de 'yayin_tipi' kullanılır)
     */
    public function createYayinTipi(Request $request, $kategoriId)
    {
        // Context7: Backward compatibility - 'name' parametresini kabul et ama 'yayin_tipi' olarak kaydet
        $validated = $request->validate([
            'name' => 'required_without:yayin_tipi|string|max:100', // Backward compat
            'yayin_tipi' => 'required_without:name|string|max:255', // Context7: yayin_tipi field
            'display_order' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:10',
            'status' => 'nullable|boolean',
        ]);

        $kategori = IlanKategori::findOrFail($kategoriId);

        // Context7: 'name' veya 'yayin_tipi' parametresini kullan
        $yayinTipiAdi = trim($validated['yayin_tipi'] ?? $validated['name']);

        // Zaten varsa (soft-deleted dahil) tekrar oluşturma; geri getir/güncelle
        $existing = IlanKategoriYayinTipi::withTrashed()
            ->where('kategori_id', $kategori->id)
            ->where('yayin_tipi', $yayinTipiAdi)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->update([
                'status' => $validated['status'] ?? true, // ✅ Context7: status field
                'display_order' => $validated['display_order'] ?? $existing->display_order, // ✅ Context7: display_order field
                'icon' => $validated['icon'] ?? $existing->icon,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Yayın tipi zaten mevcut; etkinleştirildi',
                'data' => [
                    'id' => $existing->id,
                    'yayin_tipi' => $existing->yayin_tipi, // ✅ Context7: yayin_tipi field
                    'display_order' => $existing->display_order // ✅ Context7: display_order field
                ]
            ]);
        }

        // Context7: Sıra belirle - display_order kullan
        $nextOrder = $validated['display_order'] ?? ((int) (IlanKategoriYayinTipi::where('kategori_id', $kategoriId)->max('display_order') ?? 0) + 1);

        $tip = IlanKategoriYayinTipi::create([
            'kategori_id' => $kategori->id,
            'yayin_tipi' => $yayinTipiAdi, // ✅ Context7: yayin_tipi field
            'status' => $validated['status'] ?? true, // ✅ Context7: status field
            'display_order' => $nextOrder, // ✅ Context7: display_order field
            'icon' => $validated['icon'] ?? null,
        ]);

        Log::info('Yayın tipi oluşturuldu', [
            'kategori_id' => $kategoriId,
            'yayin_tipi_id' => $tip->id,
            'yayin_tipi' => $tip->yayin_tipi
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Yayın tipi başarıyla oluşturuldu',
            'data' => [
                'id' => $tip->id,
                'yayin_tipi' => $tip->yayin_tipi, // ✅ Context7: yayin_tipi field
                'display_order' => $tip->display_order // ✅ Context7: display_order field
            ]
        ]);
    }

    /**
     * Yayın tipini sil (soft delete)
     */
    public function destroyYayinTipi(Request $request, $kategoriId, $yayinTipiId)
    {
        try {
            $yayinTipi = IlanKategoriYayinTipi::findOrFail($yayinTipiId);

            // Kategori kontrolü
            if ($yayinTipi->kategori_id != $kategoriId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Yayın tipi bu kategoriye ait değil!'
                ], 403);
            }

            // Bu yayın tipine ait ilan var mı kontrol et
            $ilanCount = $yayinTipi->ilanlar()->count();
            if ($ilanCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Bu yayın tipine ait {$ilanCount} ilan bulunuyor. Önce ilanları silin veya başka bir yayın tipine taşıyın."
                ], 422);
            }

            // ✅ Context7: Alt kategori yayın tipi ilişkilerini kaldır (tablo kontrolü ile)
            if (Schema::hasTable('alt_kategori_yayin_tipi')) {
                AltKategoriYayinTipi::where('yayin_tipi_id', $yayinTipiId)
                    ->delete();
            }

            // ✅ Context7: Feature assignment ilişkilerini kaldır (tablo kontrolü ile)
            if (Schema::hasTable('feature_assignments')) {
                FeatureAssignment::where('assignable_type', IlanKategoriYayinTipi::class)
                    ->where('assignable_id', $yayinTipiId)
                    ->delete();
            }

            // ✅ Context7: Yayın tipini kalıcı olarak sil (force delete)
            // Not: ensureDefaultYayinTipleri soft-deleted kayıtları restore ediyor,
            // bu yüzden kalıcı silme yapıyoruz
            $yayinTipiAdi = $yayinTipi->yayin_tipi;
            $yayinTipi->forceDelete();

            Log::info('Yayın tipi kalıcı olarak silindi', [
                'yayin_tipi_id' => $yayinTipiId,
                'kategori_id' => $kategoriId,
                'yayin_tipi' => $yayinTipiAdi
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Yayın tipi başarıyla silindi',
                'data' => [
                    'id' => $yayinTipiId,
                    'name' => $yayinTipiAdi
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Yayın tipi silme hatası', [
                'yayin_tipi_id' => $yayinTipiId,
                'kategori_id' => $kategoriId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Yayın tipi silinirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alt kategoriyi sil (soft delete)
     */
    public function destroyAltKategori(Request $request, $kategoriId, $altKategoriId)
    {
        try {
            $altKategori = IlanKategori::findOrFail($altKategoriId);

            // Kategori kontrolü
            if ($altKategori->parent_id != $kategoriId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alt kategori bu ana kategoriye ait değil!'
                ], 403);
            }

            // Seviye kontrolü
            if ($altKategori->seviye != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu kategori bir alt kategori değil!'
                ], 403);
            }

            // Bu alt kategoriye ait ilan var mı kontrol et
            $ilanCount = $altKategori->ilanlar()->count();
            if ($ilanCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Bu alt kategoriye ait {$ilanCount} ilan bulunuyor. Önce ilanları silin veya başka bir kategoriye taşıyın."
                ], 422);
            }

            // Alt kategoriye ait çocuk kategoriler var mı kontrol et
            $cocukKategoriCount = IlanKategori::where('parent_id', $altKategoriId)->count();
            if ($cocukKategoriCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Bu alt kategoriye ait {$cocukKategoriCount} alt kategori bulunuyor. Önce alt kategorileri silin."
                ], 422);
            }

            // ✅ Context7: Alt kategori yayın tipi ilişkilerini kaldır (tablo kontrolü ile)
            if (Schema::hasTable('alt_kategori_yayin_tipi')) {
                AltKategoriYayinTipi::where('alt_kategori_id', $altKategoriId)
                    ->delete();
            }

            // Alt kategoriyi soft delete yap
            $altKategori->delete();

            return response()->json([
                'success' => true,
                'message' => 'Alt kategori başarıyla silindi',
                'data' => [
                    'id' => $altKategoriId,
                    'name' => $altKategori->name
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Alt kategori silme hatası', [
                'alt_kategori_id' => $altKategoriId,
                'kategori_id' => $kategoriId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Alt kategori silinirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Field Dependencies Management - Index (Polymorphic Feature System)
     * ✅ Yeni polymorphic feature assignment sistemi
     */
    public function fieldDependenciesIndex($kategoriId)
    {
        $kategori = IlanKategori::findOrFail($kategoriId);

        // Yayın tipleri (Property Types)
        $yayinTipleri = IlanKategoriYayinTipi::where('kategori_id', $kategoriId)
            ->where('status', 1)
            ->orderBy('display_order')
            ->get();

        // ✅ POLYMORPHIC: Her yayın tipi için feature assignments
        $fieldDependencies = [];

        // Önce tüm yayın tipleri için boş collection'lar oluştur
        foreach ($yayinTipleri as $yayinTipi) {
            $fieldDependencies[$yayinTipi->slug ?? $yayinTipi->yayin_tipi] = collect([]);
        }

        // Context7: Tablo kontrolü ile güvenli sorgulama
        // ✅ OPTIMIZED: N+1 query önlendi - Tüm feature assignments'ları eager load ile al
        if (Schema::hasTable('feature_assignments') && $yayinTipleri->isNotEmpty() && method_exists($yayinTipleri->first(), 'featureAssignments')) {
            $yayinTipiIds = $yayinTipleri->pluck('id')->toArray();

            // Tüm yayın tipleri için feature assignments'ları tek query'de al
            $allAssignments = \App\Models\FeatureAssignment::whereIn('assignable_id', $yayinTipiIds)
                ->where('assignable_type', get_class($yayinTipleri->first()))
                ->with(['feature' => function ($q) {
                    $q->with('category');
                }])
                ->visible()
                ->ordered()
                ->get()
                ->groupBy('assignable_id');

            foreach ($yayinTipleri as $yayinTipi) {
                try {
                    $assignments = $allAssignments->get($yayinTipi->id, collect([]));
                    $fieldDependencies[$yayinTipi->slug ?? $yayinTipi->yayin_tipi] = $assignments;

                    Log::info('Feature assignments loaded for property type', [
                        'property_type' => $yayinTipi->yayin_tipi,
                        'assignments_count' => $assignments->count()
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Feature assignments yüklenemedi', [
                        'error' => $e->getMessage(),
                        'yayin_tipi_id' => $yayinTipi->id
                    ]);
                    $fieldDependencies[$yayinTipi->slug ?? $yayinTipi->yayin_tipi] = collect([]);
                }
            }
        }

        // ✅ Context7: Eski sistem desteği - kategori_yayin_tipi_field_dependencies tablosundan veri çek
        // Eğer polymorphic sistem boşsa, eski sistemden veri çek
        $hasPolymorphicData = false;
        foreach ($fieldDependencies as $assignments) {
            if ($assignments->count() > 0) {
                $hasPolymorphicData = true;
                break;
            }
        }

        if (!$hasPolymorphicData && Schema::hasTable('kategori_yayin_tipi_field_dependencies')) {
            try {
                $legacyFieldDeps = KategoriYayinTipiFieldDependency::where('kategori_slug', $kategori->slug)
                    ->where('status', true)
                    ->get();

                // Yayın tipi ID'lerini slug/yayin_tipi ile eşleştir
                $yayinTipiMap = [];
                foreach ($yayinTipleri as $yt) {
                    $yayinTipiMap[$yt->id] = $yt->slug ?? $yt->yayin_tipi;
                    $yayinTipiMap[$yt->yayin_tipi] = $yt->slug ?? $yt->yayin_tipi;
                }

                // Legacy field dependencies'i polymorphic format'a dönüştür
                foreach ($legacyFieldDeps as $dep) {
                    $yayinTipiKey = null;

                    // Yayın tipi ID veya slug/yayin_tipi ile eşleştir
                    if (is_numeric($dep->yayin_tipi)) {
                        $yayinTipiKey = $yayinTipiMap[$dep->yayin_tipi] ?? null;
                    } else {
                        $yayinTipiKey = $dep->yayin_tipi;
                    }

                    if ($yayinTipiKey && isset($fieldDependencies[$yayinTipiKey])) {
                        // Feature'ı bul, yoksa field dependency'den oluştur
                        $feature = Feature::where('slug', $dep->field_slug)->first();

                        if (!$feature) {
                            // Feature yoksa, field dependency'den mock feature oluştur
                            $feature = new \stdClass();
                            $feature->id = $dep->id;
                            $feature->name = $dep->field_name;
                            $feature->slug = $dep->field_slug;
                            $feature->field_type = $dep->field_type ?? 'text';
                            $feature->field_icon = $dep->field_icon ?? '📋';

                            // ✅ Context7: AI capabilities için default değerler (hasAiCapabilities() method'u için)
                            $feature->ai_auto_fill = false;
                            $feature->ai_suggestion = false;
                            $feature->ai_calculation = false;

                            // Field category'den feature category oluştur
                            $featureCategory = null;
                            if ($dep->field_category) {
                                $featureCategory = FeatureCategory::where('name', $dep->field_category)->first();
                                if (!$featureCategory) {
                                    // Mock category oluştur
                                    $featureCategory = new \stdClass();
                                    $featureCategory->name = $dep->field_category;
                                }
                            }
                            $feature->category = $featureCategory;
                        }

                        // Polymorphic assignment oluştur (geçici olarak)
                        $assignment = new \stdClass();
                        $assignment->id = $dep->id;
                        $assignment->feature = $feature;
                        $assignment->is_visible = $dep->status ?? true;
                        $assignment->is_required = $dep->required ?? false;
                        $assignment->group_name = $dep->field_category ?? null;

                        $fieldDependencies[$yayinTipiKey]->push($assignment);
                    }
                }

                Log::info('Legacy field dependencies loaded', [
                    'kategori_slug' => $kategori->slug,
                    'legacy_count' => $legacyFieldDeps->count()
                ]);
            } catch (\Exception $e) {
                Log::warning('Legacy field dependencies yüklenemedi', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        // ✅ Context7: Kategori bazlı feature filtreleme
        // Arsa kategorisi için sadece "Arsa Özellikleri" ve "Genel Özellikler" göster
        $availableFeaturesQuery = Feature::with('category')
            ->enabled()
            ->ordered();

        // ✅ Context7: Kategori bazlı filtreleme
        $kategoriSlug = $kategori->slug;
        if ($kategoriSlug === 'arsa') {
            // Arsa için sadece "Arsa Özellikleri" ve "Genel Özellikler" kategorilerini göster
            $availableFeaturesQuery->whereHas('category', function ($q) {
                $q->whereIn('name', ['Arsa Özellikleri', 'Genel Özellikler']);
            });
        } elseif ($kategoriSlug === 'konut') {
            // Konut için "Konut Özellikleri" ve "Genel Özellikler" göster
            $availableFeaturesQuery->whereHas('category', function ($q) {
                $q->whereIn('name', ['Konut Özellikleri', 'Genel Özellikler']);
            });
        } elseif ($kategoriSlug === 'yazlik') {
            // Yazlık için "Yazlık Özellikleri" ve "Genel Özellikler" göster
            $availableFeaturesQuery->whereHas('category', function ($q) {
                $q->whereIn('name', ['Yazlık Özellikleri', 'Genel Özellikler']);
            });
        } elseif ($kategoriSlug === 'isyeri') {
            // İşyeri için "Ticari Özellikler" ve "Genel Özellikler" göster
            $availableFeaturesQuery->whereHas('category', function ($q) {
                $q->whereIn('name', ['Ticari Özellikler', 'Genel Özellikler']);
            });
        }
        // Diğer kategoriler için tüm feature'ları göster

        $availableFeatures = $availableFeaturesQuery->get()
            ->groupBy(function ($feature) {
                return $feature->category?->name ?? 'Genel';
            });

        return view('admin.property-type-manager.field-dependencies', compact(
            'kategori',
            'yayinTipleri',
            'fieldDependencies',
            'availableFeatures'
        ));
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
        $validated['yayin_tipi'] = (string)($request->input('yayin_tipi_id') ?? $request->input('yayin_tipi'));

        KategoriYayinTipiFieldDependency::create($validated);

        return redirect()
            ->route('admin.property-type-manager.field-dependencies', $kategoriId)
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
                    'field' => $field
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

        $field->update($validated);

        // AJAX için JSON response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Alan başarıyla güncellendi',
                'field' => $field
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
                $yayinKey = (string)($request->input('yayin_tipi_id') ?? $request->input('yayin_tipi'));
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
                    'status' => $status
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Field dependency toggle failed:', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Güncelleme sırasında bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Field Dependencies - Update Order (Sıralama)
     */
    public function updateFieldOrder(Request $request)
    {
        $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:kategori_yayin_tipi_field_dependencies,id',
            'fields.*.order' => 'required|integer|min:0', // Backward compat
            'fields.*.display_order' => 'nullable|integer|min:0'
        ]);

        DB::beginTransaction();
        try {
            // ✅ PERFORMANCE FIX: N+1 query önlendi - Gerçek bulk update kullanıldı
            $updates = [];
            $ids = [];
            foreach ($request->fields as $fieldData) {
                $displayOrder = $fieldData['display_order'] ?? $fieldData['order'] ?? 0;
                $id = $fieldData['id'];
                $updates[$id] = $displayOrder;
                $ids[] = $id;
            }

            // ✅ PERFORMANCE FIX: CASE WHEN ile gerçek bulk update (N query → 1 query)
            if (!empty($ids)) {
                $cases = [];
                $bindings = [];
                foreach ($updates as $id => $displayOrder) {
                    $cases[] = "WHEN ? THEN ?";
                    $bindings[] = $id;
                    $bindings[] = $displayOrder;
                }
                $idsPlaceholder = implode(',', array_fill(0, count($ids), '?'));
                $casesSql = implode(' ', $cases);

                DB::statement(
                    "UPDATE kategori_yayin_tipi_field_dependencies
                     SET display_order = CASE id {$casesSql} END
                     WHERE id IN ({$idsPlaceholder})",
                    array_merge($bindings, $ids)
                );
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => '✅ Sıralama güncellendi!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage()
            ], 500);
        }
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
            'enabled' => 'required_without:status|boolean' // Backward compatibility
        ]);

        // ✅ Context7: Backward compatibility - accept 'enabled' but use 'status'
        $status = $request->has('status') ? $request->boolean('status') : $request->boolean('enabled');

        Feature::where('id', $request->feature_id)
            ->update(['status' => $status]);

        return response()->json(['success' => true]);
    }

    /**
     * Bulk save
     */
    public function bulkSave(Request $request, $kategoriId)
    {
        try {
            $request->validate([
                'yayin_tipleri' => 'nullable|array',
                'yayin_tipleri.*.kategori_id' => 'required_with:yayin_tipleri',
                'yayin_tipleri.*.yayin_tipi' => 'required_with:yayin_tipleri',
                'yayin_tipleri.*.status' => 'required_with:yayin_tipleri|boolean',
                'field_dependencies' => 'nullable|array',
                'field_dependencies.*.kategori_slug' => 'required_with:field_dependencies',
                'field_dependencies.*.yayin_tipi' => 'required_with:field_dependencies',
                'field_dependencies.*.field_slug' => 'required_with:field_dependencies',
                'field_dependencies.*.status' => 'required_without:field_dependencies.*.enabled|boolean', // ✅ Context7: status field
                'field_dependencies.*.enabled' => 'required_without:field_dependencies.*.status|boolean', // Backward compatibility
                'features' => 'nullable|array',
                'features.*.id' => 'required_with:features|exists:features,id',
                'features.*.status' => 'required_without:features.*.enabled|boolean', // ✅ Context7: status field
                'features.*.enabled' => 'required_without:features.*.status|boolean' // Backward compatibility
            ]);

            DB::transaction(function () use ($request, $kategoriId) {
                // Yayın tipleri
                if ($request->has('yayin_tipleri')) {
                    foreach ($request->yayin_tipleri as $data) {
                        // Status is boolean in database - convert to boolean
                        $status = $data['status'];
                        if (is_string($status)) {
                            $status = $status === 'Aktif' ? true : false;
                        } elseif (is_int($status)) {
                            $status = $status === 1;
                        } elseif (!is_bool($status)) {
                            $status = (bool) $status;
                        }

                        // Check for existing record including soft-deleted ones
                        $existing = IlanKategoriYayinTipi::withTrashed()
                            ->where('kategori_id', $data['kategori_id'])
                            ->where('yayin_tipi', $data['yayin_tipi'])
                            ->first();

                        if ($existing) {
                            // Restore if soft-deleted
                            if ($existing->trashed()) {
                                $existing->restore();
                            }
                            // Update existing record
                            $displayOrder = $data['display_order'] ?? $data['order'] ?? 1;
                            $existing->update([
                                'status' => $status,
                                'display_order' => $displayOrder
                            ]);
                        } else {
                            // Create new record
                            $displayOrder = $data['display_order'] ?? $data['order'] ?? 1;
                            IlanKategoriYayinTipi::create([
                                'kategori_id' => $data['kategori_id'],
                                'yayin_tipi' => $data['yayin_tipi'],
                                'status' => $status,
                                'display_order' => $displayOrder
                            ]);
                        }
                    }
                }

                // Field dependencies
                if ($request->has('field_dependencies')) {
                    foreach ($request->field_dependencies as $data) {
                        // ✅ Context7: Backward compatibility - accept 'enabled' but use 'status'
                        $status = $data['status'] ?? $data['enabled'] ?? true;

                        KategoriYayinTipiFieldDependency::updateOrCreate(
                            [
                                'kategori_slug' => $data['kategori_slug'],
                                'yayin_tipi' => $data['yayin_tipi'],
                                'field_slug' => $data['field_slug']
                            ],
                            [
                                'status' => $status, // ✅ Context7: enabled → status
                                'field_name' => $data['field_name'] ?? 'Field',
                                'field_type' => $data['field_type'] ?? 'text',
                                'field_category' => $data['field_category'] ?? 'general'
                            ]
                        );
                    }
                }

                // Features
                if ($request->has('features')) {
                    // ✅ PERFORMANCE FIX: N+1 query önlendi - Gerçek bulk update kullanıldı
                    // ✅ Context7: Backward compatibility - accept 'enabled' but use 'status'
                    $featureUpdates = [];
                    $featureIds = [];
                    foreach ($request->features as $data) {
                        $id = $data['id'];
                        $status = $data['status'] ?? $data['enabled'] ?? true; // ✅ Context7: enabled → status
                        $featureUpdates[$id] = $status;
                        $featureIds[] = $id;
                    }

                    // ✅ PERFORMANCE FIX: CASE WHEN ile gerçek bulk update (N query → 1 query)
                    if (!empty($featureIds)) {
                        $cases = [];
                        $bindings = [];
                        foreach ($featureUpdates as $id => $status) {
                            $cases[] = "WHEN ? THEN ?";
                            $bindings[] = $id;
                            $bindings[] = $status ? 1 : 0;
                        }
                        $idsPlaceholder = implode(',', array_fill(0, count($featureIds), '?'));
                        $casesSql = implode(' ', $cases);

                        DB::statement(
                            "UPDATE features
                             SET status = CASE id {$casesSql} END
                             WHERE id IN ({$idsPlaceholder})",
                            array_merge($bindings, $featureIds)
                        );
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Tüm değişiklikler kaydedildi'
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk save error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Kayıt sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
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
                'assignment_id' => $assignment->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Özellik başarıyla atandı',
                'data' => [
                    'assignment_id' => $assignment->id,
                    'feature' => $feature->only(['id', 'name', 'slug', 'field_type'])
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Feature assignment failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Özellik atama hatası: ' . $e->getMessage()
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
                'feature_id' => $request->feature_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Özellik kaldırıldı'
            ]);
        } catch (\Exception $e) {
            Log::error('Feature unassignment failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Özellik kaldırma hatası: ' . $e->getMessage()
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
                'value' => $value
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Özellik güncellendi',
                'data' => [
                    'assignment_id' => $assignment->id,
                    $field => $value
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Feature assignment toggle failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Güncelleme hatası: ' . $e->getMessage()
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
                'feature_count' => count($request->feature_ids)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Özellikler güncellendi',
                'data' => [
                    'synced_count' => count($request->feature_ids)
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Feature sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Senkronizasyon hatası: ' . $e->getMessage()
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
                'group_name'
            ]));

            Log::info('Feature assignment updated', [
                'assignment_id' => $assignmentId,
                'updates' => $request->only(['is_required', 'is_visible', 'display_order', 'group_name']) // ✅ Context7: order → display_order
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Özellik ayarları güncellendi',
                'data' => $assignment->only(['id', 'is_required', 'is_visible', 'display_order', 'group_name']) // ✅ Context7: order → display_order
            ]);
        } catch (\Exception $e) {
            Log::error('Feature assignment update failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Güncelleme hatası: ' . $e->getMessage()
            ], 500);
        }
    }
}
