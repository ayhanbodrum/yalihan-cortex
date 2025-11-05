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
        $query = IlanKategori::where('seviye', 0);

        // Context7: Schema kontrolü ile status kolonu
        if (Schema::hasColumn('ilan_kategorileri', 'status')) {
            $query->where('status', true); // ✅ Sadece aktif ana kategoriler
        }

        $kategoriler = $query->with(['children' => function($q) {
                $q->where('seviye', 1);
                // Context7: Schema kontrolü ile status kolonu
                if (Schema::hasColumn('ilan_kategorileri', 'status')) {
                    $q->where('status', true); // ✅ Sadece aktif alt kategoriler
                }
                $q->orderByRaw('COALESCE(`order`, 999999) ASC') // ✅ Order null değerleri sona
                    ->orderBy('name', 'ASC'); // ✅ İkincil sıralama
            }])
            ->orderByRaw('COALESCE(`order`, 999999) ASC') // ✅ Order null değerleri sona
            ->orderBy('name', 'ASC') // ✅ İkincil sıralama
            ->get();

        // Debug log - Geliştirme modunda çalışır
        if (config('app.debug')) {
            Log::info('PropertyTypeManager Index: Ana kategoriler sorgulandı', [
                'bulunan_kategori_sayisi' => $kategoriler->count(),
                'kategoriler' => $kategoriler->map(function($k) {
                    return [
                        'id' => $k->id,
                        'name' => $k->name,
                        'status' => $k->status,
                        'alt_kategori_sayisi' => $k->children->count(),
                        'alt_kategoriler' => $k->children->map(function($alt) {
                            return ['id' => $alt->id, 'name' => $alt->name, 'status' => $alt->status];
                        })->toArray()
                    ];
                })->toArray(),
                // Tüm alt kategorileri de kontrol et (debug için)
                'tum_alt_kategoriler' => (function() {
                    $query = IlanKategori::where('seviye', 1);
                    if (Schema::hasColumn('ilan_kategorileri', 'status')) {
                        $query->where('status', true);
                    }
                    return $query->select(['id', 'name', 'parent_id', 'seviye', 'status'])->get();
                })()
                    ->map(function($alt) {
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
                    return redirect()->route('admin.property-type-manager.show', $anaKategori->id)
                        ->with('info', 'Ana kategori sayfasına yönlendirildiniz.');
                }
            }
        }

        // Alt kategoriler (seviye=1) - İyileştirilmiş sorgu
        // ✅ Status filtresi eklendi (opsiyonel - varsayılan: sadece aktif kategoriler)
        $altKategorilerQuery = IlanKategori::where('parent_id', $kategoriId)
            ->where('seviye', 1);
        // Context7: Schema kontrolü ile status kolonu
        if (Schema::hasColumn('ilan_kategorileri', 'status')) {
            $altKategorilerQuery->where('status', true); // ✅ Plan: Status filtresi eklendi (aktif kategoriler)
        }
        $altKategoriler = $altKategorilerQuery->with(['children' => function($query) {
                $query->where('seviye', 2);
                // Context7: Schema kontrolü ile status kolonu
                if (Schema::hasColumn('ilan_kategorileri', 'status')) {
                    $query->where('status', true); // ✅ Alt kategori çocukları için de status filtresi
                }
                $query->orderByRaw('COALESCE(`order`, 999999) ASC');
            }])
            ->orderByRaw('COALESCE(`order`, 999999) ASC') // ✅ Plan: Order null değerleri sona alındı
            ->orderBy('name', 'ASC') // ✅ Plan: İkincil sıralama eklendi
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
            ->whereIn('name', ['Satılık', 'Kiralık', 'Kat Karşılığı', 'Günlük', 'Haftalık', 'Aylık'])
            ->whereNotIn('name', ['Günlük Kiralama', 'Haftalık Kiralama', 'Aylık Kiralama']) // ✅ Geçerli alt kategorileri hariç tut
            ->get();

        // Debug log - Geliştirme modunda çalışır
        if (config('app.debug')) {
            Log::info('PropertyTypeManager: Alt kategoriler sorgulandı', [
                'kategori_id' => $kategoriId,
                'kategori_name' => $kategori->name,
                'bulunan_alt_kategori_sayisi' => $altKategoriler->count(),
                'alt_kategoriler' => $altKategoriler->map(function($k) {
                    return [
                        'id' => $k->id,
                        'name' => $k->name,
                        'parent_id' => $k->parent_id,
                        'seviye' => $k->seviye,
                        'status' => $k->status,
                        'order' => $k->order
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
                    'yanlis_kayitlar' => $yanlisEklenenYayinTipleri->map(function($k) {
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
        $allYayinTipleri = IlanKategoriYayinTipi::where('kategori_id', $kategoriId)
            ->where('status', true) // ✅ Status boolean - tüm kategoriler için tutarlı
            ->orderByRaw('COALESCE(`order`, 999999) ASC') // ✅ Order null değerleri sona
            ->orderBy('yayin_tipi', 'ASC') // ✅ İkincil sıralama
            ->get();

        // Debug log - Yayın tipleri sorgulandı
        if (config('app.debug')) {
            Log::info('PropertyTypeManager: Yayın tipleri sorgulandı', [
                'kategori_id' => $kategoriId,
                'bulunan_yayin_tipi_sayisi' => $allYayinTipleri->count(),
                'yayin_tipleri' => $allYayinTipleri->map(function($yt) {
                    return ['id' => $yt->id, 'yayin_tipi' => $yt->yayin_tipi, 'status' => $yt->status, 'order' => $yt->order];
                })->toArray()
            ]);
        }

        // Her alt kategori için hangi yayın tipleri aktif?
        // ✅ OPTIMIZED: N+1 query önlendi - Tüm alt kategori yayın tiplerini tek query'de al
        $altKategoriYayinTipleri = [];
        if (Schema::hasTable('alt_kategori_yayin_tipi')) {
            try {
                $altKategoriIds = $altKategoriler->pluck('id')->toArray();
                
                // ✅ FIX: Farklı değişken adı kullan (allYayinTipleri ile çakışmasın)
                $altKategoriYayinTipleriRaw = AltKategoriYayinTipi::whereIn('alt_kategori_id', $altKategoriIds)
                    ->where('enabled', 1)
                    ->get()
                    ->groupBy('alt_kategori_id')
                    ->map(function ($items) {
                        return $items->pluck('yayin_tipi_id');
                    });
                
                // Her alt kategori için yayın tiplerini ata
                foreach($altKategoriler as $altKat) {
                    $altKategoriYayinTipleri[$altKat->id] = $altKategoriYayinTipleriRaw->get($altKat->id, collect([]));
                }
            } catch (\Exception $e) {
                // Tablo henüz yoksa veya hata varsa boş array
                Log::warning('alt_kategori_yayin_tipi tablosu sorgulanamadı', [
                    'error' => $e->getMessage(),
                ]);
                foreach($altKategoriler as $altKat) {
                    $altKategoriYayinTipleri[$altKat->id] = collect([]);
                }
            }
        } else {
            // Tablo yoksa tüm alt kategoriler için boş array
            foreach($altKategoriler as $altKat) {
                $altKategoriYayinTipleri[$altKat->id] = collect([]);
            }
        }

        // Field dependencies - Grouped by yayin_tipi (Opsiyonel - tablo yoksa boş array)
        $fieldDependencies = [];

        try {
            $fieldDependenciesRaw = KategoriYayinTipiFieldDependency::where('kategori_slug', $kategori->slug)->get();

            // Grupla: field_slug => [yayin_tipi => enabled]
            foreach($fieldDependenciesRaw as $dep) {
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
                $yayinTipiSlugToId = IlanKategori::whereIn('slug', $yayinTipiSlugs)
                    ->where('seviye', 2)
                    ->pluck('id', 'slug')
                    ->toArray();
                
                // Slug'da bulunamazsa yayin_tipi field'ına göre ara
                $missingSlugs = array_diff($yayinTipiSlugs, array_keys($yayinTipiSlugToId));
                if (!empty($missingSlugs)) {
                    $additionalYayinTipleri = IlanKategori::whereIn('yayin_tipi', $missingSlugs)
                        ->where('seviye', 2)
                        ->pluck('id', 'yayin_tipi')
                        ->toArray();
                    $yayinTipiSlugToId = array_merge($yayinTipiSlugToId, $additionalYayinTipleri);
                }
            }

            // Her field için yayın tipi durumları
            foreach($fieldDependenciesRaw as $dep) {
                if(isset($fieldDependencies[$dep->field_slug])) {
                    // Yayın tipi değeri: ID veya slug olabilir; her iki durumu da destekle
                    if (is_numeric($dep->yayin_tipi)) {
                        $yayinTipiId = (int)$dep->yayin_tipi;
                    } else {
                        $yayinTipiId = $yayinTipiSlugToId[$dep->yayin_tipi] ?? null;
                    }

                    if($yayinTipiId) {
                        $fieldDependencies[$dep->field_slug]['yayin_tipleri'][$yayinTipiId] = $dep->enabled;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Field dependencies table not found', ['error' => $e->getMessage()]);
            // Tablo yoksa boş array ile devam et
        }

        // Features
        $features = Feature::with('category')->get();
        $featureCategories = FeatureCategory::with('features')->get();

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

    private function ensureDefaultYayinTipleri(int $kategoriId): void
    {
        try {
            $existingCount = IlanKategoriYayinTipi::where('kategori_id', $kategoriId)->count();

            // Debug log - Geliştirme modunda çalışır
            if (config('app.debug')) {
                $existingRecords = IlanKategoriYayinTipi::where('kategori_id', $kategoriId)->get();
                Log::info('PropertyTypeManager: ensureDefaultYayinTipleri kontrolü', [
                    'kategori_id' => $kategoriId,
                    'mevcut_kayit_sayisi' => $existingCount,
                    'mevcut_kayitlar' => $existingRecords->map(function($r) {
                        return ['id' => $r->id, 'yayin_tipi' => $r->yayin_tipi, 'status' => $r->status];
                    })->toArray()
                ]);
            }

            if ($existingCount > 0) {
                return;
            }

            $defaults = ['Satılık', 'Kiralık'];
            $order = 1;
            foreach ($defaults as $name) {
                $record = IlanKategoriYayinTipi::withTrashed()
                    ->where('kategori_id', $kategoriId)
                    ->where('yayin_tipi', $name)
                    ->first();

                if ($record) {
                    if ($record->trashed()) {
                        $record->restore();
                    }
                    $record->update(['status' => true, 'order' => $order]);
                } else {
                    IlanKategoriYayinTipi::create([
                        'kategori_id' => $kategoriId,
                        'yayin_tipi' => $name,
                        'status' => true,
                        'order' => $order,
                    ]);
                }
                $order++;
            }

            // Debug log - Yayın tipleri oluşturuldu
            if (config('app.debug')) {
                $createdRecords = IlanKategoriYayinTipi::where('kategori_id', $kategoriId)->get();
                Log::info('PropertyTypeManager: Yayın tipleri oluşturuldu', [
                    'kategori_id' => $kategoriId,
                    'olusturulan_kayitlar' => $createdRecords->map(function($r) {
                        return ['id' => $r->id, 'yayin_tipi' => $r->yayin_tipi, 'status' => $r->status];
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
                'enabled' => 'required|boolean'
            ]);

            $altKategoriId = $validated['alt_kategori_id'];
            $yayinTipiId = $validated['yayin_tipi_id'];
            $enabled = $validated['enabled'];

            // Debug log
            Log::info('toggleYayinTipi called', [
                'kategori_id' => $kategoriId,
                'alt_kategori_id' => $altKategoriId,
                'yayin_tipi_id' => $yayinTipiId,
                'enabled' => $enabled
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
        $enabled = $request->enabled;

        if ($enabled) {
            // İlişkiyi ekle veya güncelle
            AltKategoriYayinTipi::updateOrCreate(
                [
                    'alt_kategori_id' => $altKategoriId,
                    'yayin_tipi_id' => $yayinTipiId
                ],
                [
                    'enabled' => true,
                    'order' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        } else {
            // İlişkiyi kaldır veya disabled yap
            AltKategoriYayinTipi::where('alt_kategori_id', $altKategoriId)
                ->where('yayin_tipi_id', $yayinTipiId)
                ->update([
                    'enabled' => false,
                    'updated_at' => now()
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Yayın tipi ilişkisi güncellendi',
            'data' => [
                'alt_kategori_id' => $altKategoriId,
                'yayin_tipi_id' => $yayinTipiId,
                'enabled' => $enabled
            ]
        ]);
    }

    /**
     * Yeni yayın tipi oluştur (kategori bazlı)
     */
    public function createYayinTipi(Request $request, $kategoriId)
    {
        $request->validate([
            'name' => 'required|string|max:100'
        ]);

        $kategori = IlanKategori::findOrFail($kategoriId);

        $name = trim($request->name);

        // Zaten varsa (soft-deleted dahil) tekrar oluşturma; geri getir/güncelle
        $existing = \App\Models\IlanKategoriYayinTipi::withTrashed()
            ->where('kategori_id', $kategori->id)
            ->where('yayin_tipi', $name)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->update([
                'status' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Yayın tipi zaten mevcut; etkinleştirildi',
                'data' => [
                    'id' => $existing->id,
                    'name' => $existing->yayin_tipi,
                    'order' => $existing->order ?? 0
                ]
            ]);
        }

        // Sıra belirle
        $nextOrder = (int) (\App\Models\IlanKategoriYayinTipi::where('kategori_id', $kategoriId)->max('order') ?? 0) + 1;

        $tip = \App\Models\IlanKategoriYayinTipi::create([
            'kategori_id' => $kategori->id,
            'yayin_tipi' => $name,
            'status' => true,
            'order' => $nextOrder,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Yayın tipi oluşturuldu',
            'data' => [
                'id' => $tip->id,
                'name' => $tip->yayin_tipi,
                'order' => $tip->order
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

            // Alt kategori ilişkilerini kaldır
            AltKategoriYayinTipi::where('yayin_tipi_id', $yayinTipiId)
                ->delete();

            // Feature assignment ilişkilerini kaldır
            if (Schema::hasTable('feature_assignments')) {
                FeatureAssignment::where('assignable_type', IlanKategoriYayinTipi::class)
                    ->where('assignable_id', $yayinTipiId)
                    ->delete();
            }

            // Yayın tipini soft delete yap
            $yayinTipi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Yayın tipi başarıyla silindi',
                'data' => [
                    'id' => $yayinTipiId,
                    'name' => $yayinTipi->yayin_tipi
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

            // Alt kategori yayın tipi ilişkilerini kaldır
            AltKategoriYayinTipi::where('alt_kategori_id', $altKategoriId)
                ->delete();

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
            ->orderBy('order')
            ->get();

        // ✅ POLYMORPHIC: Her yayın tipi için feature assignments
        $fieldDependencies = [];

        // Context7: Tablo kontrolü ile güvenli sorgulama
        // ✅ OPTIMIZED: N+1 query önlendi - Tüm feature assignments'ları eager load ile al
        if (Schema::hasTable('feature_assignments') && method_exists($yayinTipleri->first(), 'featureAssignments')) {
            $yayinTipiIds = $yayinTipleri->pluck('id')->toArray();
            
            // Tüm yayın tipleri için feature assignments'ları tek query'de al
            $allAssignments = \App\Models\FeatureAssignment::whereIn('assignable_id', $yayinTipiIds)
                ->where('assignable_type', get_class($yayinTipleri->first()))
                ->with(['feature' => function($q) {
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
        } else {
            // Tablo yoksa veya method yoksa boş array
            foreach ($yayinTipleri as $yayinTipi) {
                $fieldDependencies[$yayinTipi->slug ?? $yayinTipi->yayin_tipi] = collect([]);
            }
        }

        // Tüm mevcut features (assignment için)
        $availableFeatures = Feature::with('category')
            ->enabled()
            ->ordered()
            ->get()
            ->groupBy(function($feature) {
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
            'enabled' => 'boolean',
            'required' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'ai_auto_fill' => 'boolean',
            'ai_suggestion' => 'boolean',
            'searchable' => 'boolean',
            'show_in_card' => 'boolean',
        ]);

        $validated['kategori_slug'] = $kategori->slug;
        $validated['enabled'] = $request->boolean('enabled', true);
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
            'enabled' => 'boolean',
            'required' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'ai_auto_fill' => 'boolean',
            'ai_suggestion' => 'boolean',
            'searchable' => 'boolean',
            'show_in_card' => 'boolean',
        ]);

        // Boolean fields - explicit conversion
        $validated['enabled'] = $request->boolean('enabled', $field->enabled);
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
            'enabled' => 'required|boolean',
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
            $enabled = $request->enabled ? 1 : 0; // Explicit integer conversion
            $fieldId = $request->input('field_id');

            if (empty($fieldId)) {
                // Kayıt yoksa oluştur veya mevcut olanı bul
                $yayinKey = (string)($request->input('yayin_tipi_id') ?? $request->input('yayin_tipi'));
                $defaults = [
                    'field_name' => $request->input('field_name', 'Field'),
                    'field_type' => $request->input('field_type', 'text'),
                    'field_category' => $request->input('field_category', 'general'),
                    'enabled' => $enabled,
                    'order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $existing = KategoriYayinTipiFieldDependency::where('kategori_slug', $request->kategori_slug)
                    ->where('yayin_tipi', $yayinKey)
                    ->where('field_slug', $request->field_slug)
                    ->first();

                if ($existing) {
                    $fieldId = $existing->id;
                    $existing->update(['enabled' => $enabled, 'updated_at' => now()]);
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
                    $field->update(['enabled' => $enabled, 'updated_at' => now()]);
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
                'enabled' => $enabled,
                'kategori_slug' => $request->input('kategori_slug'),
                'yayin_tipi' => $request->input('yayin_tipi'),
                'field_slug' => $request->input('field_slug'),
            ]);

            return response()->json([
                'success' => true,
                'message' => $enabled ? 'Alan aktif edildi' : 'Alan pasif edildi',
                'data' => [
                    'field_id' => $fieldId,
                    'enabled' => $enabled
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
            'fields.*.order' => 'required|integer|min:0'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->fields as $fieldData) {
                KategoriYayinTipiFieldDependency::where('id', $fieldData['id'])
                    ->update(['order' => $fieldData['order']]);
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
     */
    public function toggleFeature(Request $request)
    {
        $request->validate([
            'feature_id' => 'required|exists:features,id',
            'enabled' => 'required|boolean'
        ]);

        Feature::where('id', $request->feature_id)
            ->update(['status' => $request->enabled]);

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
                'field_dependencies.*.enabled' => 'required_with:field_dependencies|boolean',
                'features' => 'nullable|array',
                'features.*.id' => 'required_with:features|exists:features,id',
                'features.*.enabled' => 'required_with:features|boolean'
            ]);

            DB::transaction(function() use ($request, $kategoriId) {
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
                        $existing->update([
                            'status' => $status,
                            'order' => $data['order'] ?? 1
                        ]);
                    } else {
                        // Create new record
                        IlanKategoriYayinTipi::create([
                            'kategori_id' => $data['kategori_id'],
                            'yayin_tipi' => $data['yayin_tipi'],
                            'status' => $status,
                            'order' => $data['order'] ?? 1
                        ]);
                    }
                }
            }

            // Field dependencies
            if ($request->has('field_dependencies')) {
                foreach ($request->field_dependencies as $data) {
                    KategoriYayinTipiFieldDependency::updateOrCreate(
                        [
                            'kategori_slug' => $data['kategori_slug'],
                            'yayin_tipi' => $data['yayin_tipi'],
                            'field_slug' => $data['field_slug']
                        ],
                        [
                            'enabled' => $data['enabled'],
                            'field_name' => $data['field_name'] ?? 'Field',
                            'field_type' => $data['field_type'] ?? 'text',
                            'field_category' => $data['field_category'] ?? 'general'
                        ]
                    );
                }
            }

            // Features
            if ($request->has('features')) {
                foreach ($request->features as $data) {
                    Feature::where('id', $data['id'])
                        ->update(['status' => $data['enabled']]);
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
            'order' => 'nullable|integer|min:0',
            'group_name' => 'nullable|string|max:100',
        ]);

        try {
            $propertyType = IlanKategoriYayinTipi::findOrFail($propertyTypeId);
            $feature = Feature::findOrFail($request->feature_id);

            $assignment = $propertyType->assignFeature($feature, [
                'is_required' => $request->boolean('is_required', false),
                'is_visible' => $request->boolean('is_visible', true),
                'order' => $request->input('order', 0),
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
            'order' => 'nullable|integer|min:0',
            'group_name' => 'nullable|string|max:100',
        ]);

        try {
            $assignment = FeatureAssignment::findOrFail($assignmentId);

            $assignment->update($request->only([
                'is_required',
                'is_visible',
                'order',
                'group_name'
            ]));

            Log::info('Feature assignment updated', [
                'assignment_id' => $assignmentId,
                'updates' => $request->only(['is_required', 'is_visible', 'order', 'group_name'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Özellik ayarları güncellendi',
                'data' => $assignment->only(['id', 'is_required', 'is_visible', 'order', 'group_name'])
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
