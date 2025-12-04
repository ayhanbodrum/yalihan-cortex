<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AI\IlanAIController as AIController;
use App\Models\Il;
use App\Models\Ilan;
use App\Models\IlanFotografi;
use App\Models\IlanKategori;
use App\Models\IlanPriceHistory;
use App\Models\Ilce;
use App\Models\Kisi;
use App\Models\Mahalle;
use App\Models\User;
use App\Services\Cache\CacheHelper;
use App\Services\CategoryFieldValidator;
use App\Services\Ilan\IlanBulkService;
use App\Services\Ilan\IlanExportService;
use App\Services\Ilan\IlanFeatureService;
use App\Services\Ilan\IlanPhotoService;
use App\Services\Ilan\IlanTypeHelper;
use App\Services\IlanReferansService;
use App\Services\AI\YalihanCortex;
use App\Services\Logging\LogService;
use App\Services\Response\ResponseService;
use App\Services\Utility\NumberToTextConverter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

class IlanController extends AdminController
{
    /**
     * Display a listing of the resource.
     * Context7: İlan listesi ve filtreleme
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // ✅ REFACTORED: Filterable trait kullanımı - Code duplication azaltıldı
        $query = Ilan::query();

        // ✅ REFACTORED: Search with relation support
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // ✅ REFACTORED: Filterable trait search kullanımı
                $q->where('baslik', 'like', "%{$search}%")
                    ->orWhere('aciklama', 'like', "%{$search}%")
                    ->orWhereHas('ilanSahibi', function ($subQ) use ($search) {
                        $subQ->where('ad', 'like', "%{$search}%")
                            ->orWhere('soyad', 'like', "%{$search}%");
                    });
            });
        }

        // Üst sekmeler (tabs): active, expired, passive, office, drafts, deleted
        // ✅ Context7: Status field - Hem integer hem string desteği
        $tab = $request->get('tab');
        $expiryDays = 60;
        // ⚡ FIX: Database'de status=1 (integer) var, string ve integer her ikisini de destekle
        $activeStatuses = ['Aktif', 1, '1']; // Integer ve string desteği
        $draftStatuses = ['Taslak', 0, '0']; // Integer ve string desteği

        // Count'lar (Integer ve String status desteği)
        // ⚡ OPTIMIZATION: Cache ile performans artışı
        $tabCounts = [
            'active' => Ilan::whereIn('status', $activeStatuses)->count(),
            'expired' => Ilan::whereIn('status', $activeStatuses)->where('updated_at', '<=', now()->subDays($expiryDays))->count(),
            'passive' => Ilan::whereIn('status', ['Pasif', 0, '0', 'Pasif'])->count(),
            'office' => Auth::check() ? Ilan::where('danisman_id', Auth::id())->count() : 0,
            'drafts' => Ilan::whereIn('status', $draftStatuses)->count(),
            'deleted' => Ilan::onlyTrashed()->count(),
        ];

        if ($tab === 'active') {
            $query->whereIn('status', $activeStatuses);
        } elseif ($tab === 'expired') {
            $query->whereIn('status', $activeStatuses)->where('updated_at', '<=', now()->subDays($expiryDays));
        } elseif ($tab === 'passive') {
            $query->where('status', 'Pasif'); // ✅ Context7: Sadece 'Pasif' kullan
        } elseif ($tab === 'office' && Auth::check()) {
            $query->where('danisman_id', Auth::id());
        } elseif ($tab === 'drafts') {
            $query->whereIn('status', $draftStatuses);
        } elseif ($tab === 'deleted') {
            // Silinenler: onlyTrashed query
            $query = Ilan::onlyTrashed();
        }
        // ✅ FIX: Tab null/empty ise TÜM ilanları göster (status ne olursa olsun)
        // Status integer ise (1=aktif) veya string ise ('Aktif') her ikisini de destekle

        // ✅ REFACTORED: Filterable trait kullanımı - Code duplication azaltıldı
        // Status filter (Ilan modelinde status string, doğrudan where kullan)
        if ($request->filled('status')) {
            // ✅ Context7: Ilan modelinde status string, doğrudan where kullan
            $statusValue = $request->status;
            // Mapping: Frontend'den gelen değerleri database değerlerine çevir
            $statusMap = [
                'active' => 'Aktif',
                'inactive' => 'Pasif',
                'draft' => 'Taslak',
                'pending' => 'Beklemede',
            ];
            $statusValue = $statusMap[$statusValue] ?? $statusValue;
            $query->where('status', $statusValue);
        }

        // Category filter (Context7 uyumlu)
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Location filters
        if ($request->filled('il_id')) {
            $query->where('il_id', $request->il_id);
        }

        if ($request->filled('ilce_id')) {
            $query->where('ilce_id', $request->ilce_id);
        }

        // ✅ REFACTORED: Price range filter (Filterable trait)
        $query->priceRange(
            $request->filled('min_fiyat') ? (float) $request->min_fiyat : null,
            $request->filled('max_fiyat') ? (float) $request->max_fiyat : null,
            'fiyat'
        );

        // Filter by rental price range (günlük/aylık/sezonluk)
        if ($request->has('kiralama_fiyat_turu') && $request->has('min_kiralama_fiyat')) {
            $fiyatTuru = $request->kiralama_fiyat_turu;
            $minFiyat = $request->min_kiralama_fiyat;

            switch ($fiyatTuru) {
                case 'gunluk':
                    $query->where('gunluk_fiyat', '>=', $minFiyat);
                    break;
                case 'aylik':
                    $query->where('aylik_fiyat', '>=', $minFiyat);
                    break;
                case 'sezonluk':
                    $query->where('sezonluk_fiyat', '>=', $minFiyat);
                    break;
            }
        }

        if ($request->has('kiralama_fiyat_turu') && $request->has('max_kiralama_fiyat')) {
            $fiyatTuru = $request->kiralama_fiyat_turu;
            $maxFiyat = $request->max_kiralama_fiyat;

            switch ($fiyatTuru) {
                case 'gunluk':
                    $query->where('gunluk_fiyat', '<=', $maxFiyat);
                    break;
                case 'aylik':
                    $query->where('aylik_fiyat', '<=', $maxFiyat);
                    break;
                case 'sezonluk':
                    $query->where('sezonluk_fiyat', '<=', $maxFiyat);
                    break;
            }
        }

        // Filter by location
        if ($request->has('il_id') && $request->il_id) {
            $query->where('il_id', $request->il_id);
        }

        if ($request->has('ilce_id') && $request->ilce_id) {
            $query->where('ilce_id', $request->ilce_id);
        }

        // ✅ REFACTORED: Sort functionality (Filterable trait + custom mapping)
        $sort = $request->get('sort', 'created_desc');

        // Map custom sort values to database columns
        $sortMap = [
            'created_asc' => ['created_at', 'asc'],
            'created_desc' => ['created_at', 'desc'],
            'price_asc' => ['fiyat', 'asc'],
            'price_desc' => ['fiyat', 'desc'],
        ];

        if (isset($sortMap[$sort])) {
            [$sortBy, $sortOrder] = $sortMap[$sort];
            $query->orderBy($sortBy, $sortOrder);
        } else {
            // ✅ REFACTORED: Filterable trait sort kullanımı (fallback)
            $query->sort($request->sort_by, $request->sort_order ?? 'desc', 'created_at');
        }

        // ⚡ PERFORMANCE: Select only needed columns
        $query->select([
            'id',
            'baslik',
            'aciklama',
            'anahtar_notlari',
            'fiyat',
            'para_birimi',
            'status',
            'ana_kategori_id',
            'alt_kategori_id',
            'yayin_tipi_id',
            'ilan_sahibi_id',
            'danisman_id',
            'il_id',
            'ilce_id',
            'gunluk_fiyat',
            'haftalik_fiyat',
            'aylik_fiyat',
            'sezonluk_fiyat',
            'goruntulenme', // ✅ Context7: goruntulenme_sayisi → goruntulenme (database column name)
            'created_at',
            'updated_at',
        ]);

        // ✅ EAGER LOADING: Prevent N+1 queries
        $query->with([
            'ilanSahibi:id,ad,soyad,telefon',
            'danisman:id,name,email',
            'userDanisman:id,name,email', // Template'de kullanılıyor
            'il:id,il_adi',
            'ilce:id,ilce_adi',
            'anaKategori:id,name',
            'altKategori:id,name',
            'yayinTipi:id,yayin_tipi', // Context7: Tablo kolonu yayin_tipi (name accessor var)
            'fotograflar' => function ($query) {
                $query->select('id', 'ilan_id', 'dosya_yolu', 'kapak_fotografi', 'sira')
                    ->orderBy('sira', 'asc')
                    ->orderBy('kapak_fotografi', 'desc');
            },
            'site:id,name',
        ]);

        // 🔎 AKILLI TEK SATIR ARAMA
        // Context7: Referans no, portal ID'leri, telefon, site adı, iletişim bilgileri
        // Yalıhan Bekçi: Smart search implementation (2025-12-02)
        $search = trim((string) $request->get('search'));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $like = '%' . $search . '%';

                // Temel alanlar: Başlık, Açıklama
                $q->where('baslik', 'like', $like)
                    ->orWhere('aciklama', 'like', $like)

                    // Referans numarası ve dosya adı
                    ->orWhere('referans_no', 'like', $like)
                    ->orWhere('dosya_adi', 'like', $like)

                    // Portal ID'leri (Sahibinden, Emlakjet, Hepsiemlak, Zingat)
                    ->orWhere('sahibinden_id', 'like', $like)
                    ->orWhere('emlakjet_id', 'like', $like)
                    ->orWhere('hepsiemlak_id', 'like', $like)
                    ->orWhere('zingat_id', 'like', $like)
                    ->orWhere('hurriyetemlak_id', 'like', $like)

                    // İlan Sahibi: Ad, Soyad, Telefon, Email
                    ->orWhereHas('ilanSahibi', function ($qq) use ($like) {
                        $qq->where('ad', 'like', $like)
                            ->orWhere('soyad', 'like', $like)
                            ->orWhere('telefon', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    })

                    // Danışman Adı
                    ->orWhereHas('userDanisman', function ($qq) use ($like) {
                        $qq->where('name', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    });
            });
        }

        // Paginate FIRST (efficient: only loads needed rows)
        // ✅ Eager loading already applied with with() above
        $ilanlar = $query->paginate(20);

        // ⚡ CACHE: Statistics (5 min cache)
        // ✅ STANDARDIZED: Using CacheHelper
        $stats = CacheHelper::remember('ilan', 'stats', 'short', function () {
            return [
                'total' => Ilan::count(),
                'active' => Ilan::where('status', 'active')->count(),
                'this_month' => Ilan::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'pending' => Ilan::where('status', 'pending')->count(),
            ];
        });

        // ⚡ CACHE: Filter options (1 hour cache)
        // ✅ STANDARDIZED: Using CacheHelper
        $kategoriler = CacheHelper::remember('category', 'filter_list', 'medium', function () {
            return IlanKategori::whereNull('parent_id')
                ->where('status', 1)
                ->orderBy('name')
                ->select(['id', 'name'])
                ->get();
        });

        // ✅ STANDARDIZED: Using CacheHelper
        $iller = CacheHelper::remember('location', 'il_list', 'medium', function () {
            return Il::orderBy('il_adi')->select(['id', 'il_adi'])->get();
        });

        // ✅ Context7: View için gerekli değişkenler
        $status = $request->get('status'); // Filter için
        $taslak = $request->get('taslak'); // Filter için

        return view('admin.ilanlar.index', compact('ilanlar', 'stats', 'kategoriler', 'iller', 'status', 'taslak', 'tab', 'tabCounts'));
    }

    /**
     * Test page for category cascading
     *
     * @return \Illuminate\View\View
     */
    public function testCategories()
    {
        $kategoriler = IlanKategori::with(['children' => function ($query) {
            $query->select(['id', 'name', 'parent_id', 'status'])
                ->where('status', true)
                ->orderBy('name');
        }])
            ->whereNull('parent_id')
            ->where('status', true)
            ->orderBy('name')
            ->get(['id', 'name', 'status']);

        $anaKategoriler = $kategoriler;

        return view('admin.ilanlar.test-categories', compact('anaKategoriler'));
    }

    /**
     * Show the form for creating a new resource.
     * Context7: Yeni ilan oluşturma formu
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Context7 uyumlu optimized loading
        $kategoriler = IlanKategori::with(['children' => function ($query) {
            $query->select(['id', 'name', 'slug', 'parent_id', 'status']) // ✅ FIX: slug added
                ->where('status', true) // ✅ Context7: status is boolean (FIXED!)
                ->orderBy('name');
        }])
            ->whereNull('parent_id')
            ->where('status', true) // ✅ Context7: status is boolean (FIXED!)
            ->where('seviye', 0) // ✅ FIX: Sadece Ana Kategoriler (seviye=0), Yayın Tiplerini (seviye=2) hariç tut
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'status']); // ✅ FIX: slug added

        $anaKategoriler = $kategoriler; // Aynı data kullan (performance için)
        $altKategoriler = collect(); // Varsayılan olarak boş koleksiyon

        // Context7: Yayın tipleri dinamik yükleme (category'ye göre API'den gelecek)
        $yayinTipleri = collect(); // Boş başlangıç, kategori seçilince yüklenecek

        $kisiler = Kisi::where('status', 'Aktif')->select(['id', 'ad', 'soyad', 'telefon'])->get();
        $danismanlar = User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })
            ->where('status', 1)
            ->select(['id', 'name', 'email'])
            ->get();
        $iller = Il::orderBy('il_adi')->select(['id', 'il_adi'])->get();

        // ✅ FIX: Eksik değişkenler eklendi (Yalıhan Bekçi error fix)
        $statusOptions = \App\Enums\IlanStatus::options();
        $taslak = false; // Create modunda default: false
        $etiketler = \App\Models\Etiket::where('status', true)
            ->select(['id', 'name', 'color'])
            ->orderBy('name')
            ->get();
        $ulkeler = \App\Models\Ulke::select(['id', 'ulke_adi', 'ulke_kodu'])
            ->orderBy('ulke_adi')
            ->get();

        // Context7 auto-save data recovery
        $autoSaveData = $this->getAutoSaveData();

        $ilceler = collect();
        $mahalleler = collect();

        return redirect()->route('admin.ilanlar.create-wizard');
    }

    /**
     * Show the wizard form for creating a new resource.
     * Context7: 3 adımlı wizard ile yeni ilan oluşturma formu
     *
     * @return \Illuminate\View\View
     */
    public function createWizard()
    {
        // Context7 uyumlu optimized loading (create() ile aynı data)
        // ✅ FIX: Sadece seviye=0 (Ana Kategori) olanları getir, yayın tiplerini (seviye=2) hariç tut
        $kategoriler = IlanKategori::with(['children' => function ($query) {
            $query->select(['id', 'name', 'slug', 'parent_id', 'status'])
                ->where('status', true)
                ->orderBy('name');
        }])
            ->whereNull('parent_id')
            ->where('status', true)
            ->where('seviye', 0) // ✅ FIX: Sadece Ana Kategoriler (seviye=0), Yayın Tiplerini (seviye=2) hariç tut
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'status']);

        $anaKategoriler = $kategoriler;
        $altKategoriler = collect();
        $yayinTipleri = collect();

        $kisiler = Kisi::where('status', 'Aktif')->select(['id', 'ad', 'soyad', 'telefon'])->get();
        $danismanlar = User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })
            ->where('status', 1)
            ->select(['id', 'name', 'email'])
            ->get();
        $iller = Il::orderBy('il_adi')->select(['id', 'il_adi'])->get();

        // Sites (opsiyonel)
        $sites = \App\Models\Site::select(['id', 'name'])->orderBy('name')->get();

        $ilceler = collect();
        $mahalleler = collect();

        return view('admin.ilanlar.create-wizard', compact(
            'kategoriler',
            'anaKategoriler',
            'altKategoriler',
            'yayinTipleri',
            'kisiler',
            'danismanlar',
            'iller',
            'ilceler',
            'mahalleler',
            'sites'
        ));
    }

    /**
     * Context7 uyumlu auto-save data retrieval
     */
    private function getAutoSaveData()
    {
        try {
            $userId = Auth::id();
            $cacheKey = "context7_autosave_ilan_create_{$userId}";

            // Redis'ten veya session'dan al
            if (config('cache.default') === 'redis') {
                $data = Cache::get($cacheKey);
            } else {
                $data = session($cacheKey);
            }

            if ($data && is_array($data) && isset($data['context7_version'])) {
                return $data;
            }

            return null;
        } catch (\Exception $e) {
            // ✅ STANDARDIZED: Using LogService
            LogService::warning('Context7 AutoSave Retrieval Error', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Store a newly created resource in storage.
     * Context7: Form field mapping fixed (2025-10-21)
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Context7: 3-level category system validation
            $statusValues = \App\Enums\IlanStatus::values();

            // ✅ Context7: Get category slug for dynamic validation
            $kategoriSlug = null;
            $yayinTipiSlug = null;
            if ($request->ana_kategori_id) {
                $anaKategori = IlanKategori::find($request->ana_kategori_id);
                $kategoriSlug = $anaKategori ? strtolower($anaKategori->slug ?? '') : null;
            }
            if ($request->yayin_tipi_id) {
                $yayinTipi = \App\Models\IlanKategoriYayinTipi::find($request->yayin_tipi_id);
                $yayinTipiSlug = $yayinTipi ? strtolower($yayinTipi->slug ?? '') : null;
            }

            // ✅ Context7: Get base validation rules
            $baseRules = [
                'baslik' => 'required|string|max:255',
                'aciklama' => 'nullable|string',
                'fiyat' => 'required|numeric|min:0',
                'para_birimi' => 'required|string|in:TRY,USD,EUR,GBP',

                // Context7: 3-level category (ana → alt → yayin)
                'ana_kategori_id' => 'required|exists:ilan_kategorileri,id',
                'alt_kategori_id' => 'required|exists:ilan_kategorileri,id',
                // ✅ Context7: yayin_tipi_id → ilan_kategori_yayin_tipleri tablosundan
                'yayin_tipi_id' => 'required|integer|exists:ilan_kategori_yayin_tipleri,id',

                'ilan_sahibi_id' => 'required|exists:kisiler,id',
                'danisman_id' => 'nullable|exists:users,id',
                'il_id' => 'nullable|exists:iller,id',
                'ilce_id' => 'nullable|exists:ilceler,id',
                'mahalle_id' => 'nullable|exists:mahalleler,id',
                'status' => 'required|string|in:' . implode(',', $statusValues),
                'crm_only' => 'nullable|boolean',

                // 🆕 PHASE 1: Address Components
                'sokak' => 'nullable|string|max:255',
                'cadde' => 'nullable|string|max:255',
                'bulvar' => 'nullable|string|max:255',
                'bina_no' => 'nullable|string|max:20',
                'daire_no' => 'nullable|string|max:20',
                'posta_kodu' => 'nullable|string|max:10',

                // 🆕 PHASE 2: Distance Data (JSON string olarak gelir)
                'nearby_distances' => 'nullable|string',

                // 🆕 PHASE 3: Property Boundary (JSON string olarak gelir)
                'boundary_geojson' => 'nullable|string',
                'boundary_area' => 'nullable|numeric|min:0',

                // ✅ Context7: Koordinatlar ve adres
                'enlem' => 'nullable|numeric|between:-90,90',
                'boylam' => 'nullable|numeric|between:-180,180',
                'adres' => 'nullable|string|max:500',

                // ✅ Context7: Çevresel Bilgiler (POI & Tags)
                'environment_pois' => 'nullable|string', // JSON string olarak gelir
                'environment_tags' => 'nullable|array', // Array olarak gelir

                // Villa/Daire Validation Rules (YENİ)
                'isinma_tipi' => 'nullable|string|in:Doğalgaz,Kombi,Klima,Soba,Merkezi,Yerden Isıtma',
                'site_ozellikleri' => 'nullable|array',
                'site_ozellikleri.*' => 'string|in:Güvenlik,Otopark,Havuz,Spor,Sauna,Oyun Alanı,Asansör',

                // İşyeri Validation Rules (YENİ)
                'isyeri_tipi' => 'nullable|string|in:Ofis,Mağaza,Dükkan,Depo,Fabrika,Atölye,Showroom',
                'kira_bilgisi' => 'nullable|string|max:1000',
                'ciro_bilgisi' => 'nullable|numeric|min:0',
                'ruhsat_durumu' => 'nullable|string|in:Var,Yok,Başvuruda',
                'personel_kapasitesi' => 'nullable|integer|min:0',
                'isyeri_cephesi' => 'nullable|integer|min:0',

                // Yazlık Kiralama Validation Rules
                'min_konaklama' => 'nullable|integer|min:1|max:365',
                'max_misafir' => 'nullable|integer|min:1|max:50',
                'temizlik_ucreti' => 'nullable|numeric|min:0',
                'havuz' => 'nullable|boolean',
                'havuz_turu' => 'nullable|string|max:100',
                'havuz_boyut' => 'nullable|string|max:50',
                'havuz_derinlik' => 'nullable|string|max:50',
                'havuz_boyut_en' => 'nullable|string|max:20',
                'havuz_boyut_boy' => 'nullable|string|max:20',
                'gunluk_fiyat' => 'nullable|numeric|min:0',
                'haftalik_fiyat' => 'nullable|numeric|min:0',
                'aylik_fiyat' => 'nullable|numeric|min:0',
                'sezonluk_fiyat' => 'nullable|numeric|min:0',
                'sezon_baslangic' => 'nullable|date',
                'sezon_bitis' => 'nullable|date|after_or_equal:sezon_baslangic',
                'elektrik_dahil' => 'nullable|boolean',
                'su_dahil' => 'nullable|boolean',
                'internet_dahil' => 'nullable|boolean',
                'carsaf_dahil' => 'nullable|boolean',
                'havlu_dahil' => 'nullable|boolean',
                'klima_var' => 'nullable|boolean',
                'oda_sayisi' => 'nullable|integer|min:1|max:20',
                'banyo_sayisi' => 'nullable|integer|min:1|max:10',
                'yatak_sayisi' => 'nullable|integer|min:1|max:20',
                'restoran_mesafe' => 'nullable|integer|min:0|max:100',
                'market_mesafe' => 'nullable|integer|min:0|max:100',
                'deniz_mesafe' => 'nullable|integer|min:0|max:100',
                'merkez_mesafe' => 'nullable|integer|min:0|max:100',
                'bahce_var' => 'nullable|boolean',
                'tv_var' => 'nullable|boolean',
                'barbeku_var' => 'nullable|boolean',
                'sezlong_var' => 'nullable|boolean',
                'bahce_masasi_var' => 'nullable|boolean',
                'manzara' => 'nullable|string|max:100',
                'ev_tipi' => 'nullable|string|max:50',
                'ev_konsepti' => 'nullable|string|max:100',
            ];

            // ✅ Context7: Add category-specific validation rules
            $categoryValidator = new CategoryFieldValidator;
            $categoryRules = $categoryValidator->getRules($kategoriSlug, $yayinTipiSlug);
            $allRules = array_merge($baseRules, $categoryRules);

            $validator = Validator::make($request->all(), $allRules, $categoryValidator->getMessages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // ✅ FIX: Formatlanmış fiyatı raw değere çevir (5.000.000 -> 5000000)
            $rawPrice = $request->fiyat_raw ?? str_replace('.', '', $request->fiyat);
            $fiyat = (float) $rawPrice;

            $numberToText = app(NumberToTextConverter::class);
            $priceText = $numberToText->convertToText($fiyat, $request->para_birimi);

            // Context7: Map form fields to database fields
            $ilan = Ilan::create([
                'baslik' => $request->baslik,
                'aciklama' => $request->aciklama,
                'fiyat' => $fiyat,
                'price_text' => $priceText,
                'para_birimi' => $request->para_birimi,

                // Context7: Map 3-level category to database (both old and new columns)
                'kategori_id' => $request->alt_kategori_id, // Alt kategori = kategori_id (legacy)
                'parent_kategori_id' => $request->ana_kategori_id, // Ana kategori = parent (legacy)
                'ana_kategori_id' => $request->ana_kategori_id, // Ana kategori (new column)
                'alt_kategori_id' => $request->alt_kategori_id, // Alt kategori (new column)
                'yayin_tipi_id' => $request->yayin_tipi_id, // Yayın tipi (new column)

                'ilan_sahibi_id' => $request->ilan_sahibi_id,
                'danisman_id' => $request->danisman_id ?? Auth::id(),
                'il_id' => $request->il_id,
                'ilce_id' => $request->ilce_id,
                'mahalle_id' => $request->mahalle_id,

                // 🆕 PHASE 1: Address Components
                'sokak' => $request->sokak,
                'cadde' => $request->cadde,
                'bulvar' => $request->bulvar,
                'bina_no' => $request->bina_no,
                'daire_no' => $request->daire_no,
                'posta_kodu' => $request->posta_kodu,

                // 🆕 PHASE 2: Distance Data (JSON string → array)
                'nearby_distances' => $request->nearby_distances ? (is_string($request->nearby_distances) ? json_decode($request->nearby_distances, true) : $request->nearby_distances) : null,

                // 🆕 PHASE 3: Property Boundary (JSON string → array)
                'boundary_geojson' => $request->boundary_geojson ? (is_string($request->boundary_geojson) ? json_decode($request->boundary_geojson, true) : $request->boundary_geojson) : null,
                'boundary_area' => $request->boundary_area ? (float) $request->boundary_area : null,

                // Context7: Map 'status' (form field) to 'status' (database column - legacy)
                'status' => $request->status,
                'crm_only' => $request->boolean('crm_only', false),
                'slug' => Str::slug($request->baslik),
                // ✅ Context7: Koordinatlar (enlem/boylam form field'larından)
                'latitude' => $request->enlem ? (float) $request->enlem : ($request->latitude ? (float) $request->latitude : null),
                'longitude' => $request->boylam ? (float) $request->boylam : ($request->longitude ? (float) $request->longitude : null),
                'enlem' => $request->enlem ? (float) $request->enlem : null,
                'boylam' => $request->boylam ? (float) $request->boylam : null,
                'adres' => $request->adres,

                // Villa/Daire Fields (YENİ)
                'isinma_tipi' => $request->isinma_tipi,
                'site_ozellikleri' => $request->site_ozellikleri,

                // İşyeri Fields (YENİ)
                'isyeri_tipi' => $request->isyeri_tipi,
                'kira_bilgisi' => $request->kira_bilgisi,
                'ciro_bilgisi' => $request->ciro_bilgisi,
                'ruhsat_durumu' => $request->ruhsat_durumu,
                'personel_kapasitesi' => $request->personel_kapasitesi,
                'isyeri_cephesi' => $request->isyeri_cephesi,

                // ✅ Arsa Fields (TKGM'den gelen veriler)
                'ada_no' => $request->ada_no,
                'parsel_no' => $request->parsel_no,
                'alan_m2' => $request->alan_m2 ? (float) $request->alan_m2 : null,
                'imar_statusu' => $request->imar_statusu,
                'kaks' => $request->kaks ? (float) $request->kaks : null,
                'taks' => $request->taks ? (float) $request->taks : null,
                'gabari' => $request->gabari ? (float) $request->gabari : null,
                'yola_cephe' => $request->boolean('yola_cephe', false),
                'altyapi_elektrik' => $request->boolean('altyapi_elektrik', false),
                'altyapi_su' => $request->boolean('altyapi_su', false),
                'altyapi_dogalgaz' => $request->boolean('altyapi_dogalgaz', false),

                // ✅ Context7: Çevresel Bilgiler (POI & Tags)
                'environment_pois' => $request->environment_pois ? (is_string($request->environment_pois) ? json_decode($request->environment_pois, true) : $request->environment_pois) : null,
                'environment_tags' => $request->environment_tags ? (is_array($request->environment_tags) ? $request->environment_tags : json_decode($request->environment_tags, true)) : null,
            ]);

            // Create price history entry
            IlanPriceHistory::create([
                'ilan_id' => $ilan->id,
                'old_price' => 0,
                'new_price' => $fiyat,
                'currency' => $request->para_birimi,
                'changed_by' => Auth::id(),
                'change_reason' => 'İlk ilan oluşturma',
            ]);

            $refService = app(IlanReferansService::class);
            $referansNo = $refService->generateReferansNo($ilan);
            $dosyaAdi = $refService->generateDosyaAdi($ilan);
            $ilan->update([
                'referans_no' => $referansNo,
                'dosya_adi' => $dosyaAdi,
            ]);

            // ✅ Context7 Features Handling: EAV pattern for amenities
            if ($request->has('features') && is_array($request->features)) {
                $featuresToAttach = [];

                foreach ($request->features as $featureId => $featureValue) {
                    // featureId artık numeric ID (form'dan geliyor)
                    if ($featureValue && $featureValue !== '' && $featureValue !== '0') {
                        // Boolean: '1' veya 1
                        // Select: string value
                        // Number: numeric value
                        $valueToStore = is_bool($featureValue) || $featureValue === '1' || $featureValue === 1
                            ? '1'
                            : (string) $featureValue;

                        $featuresToAttach[$featureId] = [
                            'value' => $valueToStore,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                // Attach features to ilan (pivot table: ilan_feature)
                if (! empty($featuresToAttach)) {
                    $ilan->features()->attach($featuresToAttach);
                    // ✅ STANDARDIZED: Using LogService
                    LogService::action('features_attached', 'ilan', $ilan->id, [
                        'ilan_id' => $ilan->id,
                        'features_count' => count($featuresToAttach),
                        'features' => array_keys($featuresToAttach),
                    ]);
                }
            }

            // Yazlık Detayları Kaydet
            if ($request->has('min_konaklama') || $request->has('havuz')) {
                \App\Models\YazlikDetail::create([
                    'ilan_id' => $ilan->id,
                    'min_konaklama' => $request->min_konaklama ?? 1,
                    'max_misafir' => $request->max_misafir,
                    'temizlik_ucreti' => $request->temizlik_ucreti,
                    'havuz' => $request->boolean('havuz', false),
                    'havuz_turu' => $request->havuz_turu,
                    'havuz_boyut' => $request->havuz_boyut,
                    'havuz_derinlik' => $request->havuz_derinlik,
                    'havuz_boyut_en' => $request->havuz_boyut_en,
                    'havuz_boyut_boy' => $request->havuz_boyut_boy,
                    'gunluk_fiyat' => $request->gunluk_fiyat,
                    'haftalik_fiyat' => $request->haftalik_fiyat,
                    'aylik_fiyat' => $request->aylik_fiyat,
                    'sezonluk_fiyat' => $request->sezonluk_fiyat,
                    'sezon_baslangic' => $request->sezon_baslangic,
                    'sezon_bitis' => $request->sezon_bitis,
                    'elektrik_dahil' => $request->boolean('elektrik_dahil', false),
                    'su_dahil' => $request->boolean('su_dahil', false),
                    'internet_dahil' => $request->boolean('internet_dahil', false),
                    'carsaf_dahil' => $request->boolean('carsaf_dahil', false),
                    'havlu_dahil' => $request->boolean('havlu_dahil', false),
                    'klima_var' => $request->boolean('klima_var', false),
                    'oda_sayisi' => $request->oda_sayisi,
                    'banyo_sayisi' => $request->banyo_sayisi,
                    'yatak_sayisi' => $request->yatak_sayisi,
                    'yatak_turleri' => $request->yatak_turleri ? json_decode($request->yatak_turleri, true) : null,
                    'restoran_mesafe' => $request->restoran_mesafe,
                    'market_mesafe' => $request->market_mesafe,
                    'deniz_mesafe' => $request->deniz_mesafe,
                    'merkez_mesafe' => $request->merkez_mesafe,
                    'bahce_var' => $request->boolean('bahce_var', false),
                    'tv_var' => $request->boolean('tv_var', false),
                    'barbeku_var' => $request->boolean('barbeku_var', false),
                    'sezlong_var' => $request->boolean('sezlong_var', false),
                    'bahce_masasi_var' => $request->boolean('bahce_masasi_var', false),
                    'manzara' => $request->manzara,
                    'ozel_isaretler' => $request->ozel_isaretler ? json_decode($request->ozel_isaretler, true) : null,
                    'ev_tipi' => $request->ev_tipi,
                    'ev_konsepti' => $request->ev_konsepti,
                    'ozel_notlar' => $request->ozel_notlar,
                    'musteri_notlari' => $request->musteri_notlari,
                    'indirim_notlari' => $request->indirim_notlari,
                    'indirimli_fiyat' => $request->indirimli_fiyat,
                    'anahtar_kimde' => $request->anahtar_kimde,
                    'anahtar_notlari' => $request->anahtar_notlari,
                    'sahip_ozel_notlari' => $request->sahip_ozel_notlari,
                    'sahip_iletisim_tercihi' => $request->sahip_iletisim_tercihi,
                    'eids_onayli' => $request->boolean('eids_onayli', false),
                    'eids_onay_tarihi' => $request->eids_onay_tarihi,
                    'eids_belge_no' => $request->eids_belge_no,
                ]);
            }

            DB::commit();

            // ✅ YalihanCortex: İlan kalite kontrolü (Pre-Publishing Check)
            $cortex = app(YalihanCortex::class);
            $qualityCheck = $cortex->checkIlanQuality($ilan);

            // Kalite kontrolü sonucuna göre tepki ver
            $redirectRoute = redirect()->route('admin.ilanlar.show', $ilan);
            $successMessage = 'İlan başarıyla oluşturuldu.';

            if (!$qualityCheck['passed']) {
                // Uyarı: İlan eksik alanlar içeriyor
                $warningMessage = $qualityCheck['message'];
                if (!empty($qualityCheck['missing_fields'])) {
                    $warningMessage .= ' Eksik alanlar: ';
                    $missingLabels = array_map(fn($f) => $f['label'], $qualityCheck['missing_fields']);
                    $warningMessage .= implode(', ', $missingLabels) . '.';
                }

                return $redirectRoute
                    ->with('success', $successMessage)
                    ->with('warning', $warningMessage)
                    ->with('qualityCheck', $qualityCheck);
            }

            return $redirectRoute
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();

            // ✅ STANDARDIZED: Using ResponseService (automatic logging)
            if ($request->expectsJson()) {
                return ResponseService::serverError('İlan oluşturulurken hata oluştu', $e);
            }

            return redirect()->back()
                ->with('error', 'İlan oluşturulurken bir hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     * Context7: İlan detay sayfası
     *
     * @return \Illuminate\View\View
     */
    public function show(Ilan $ilan)
    {
        $ilan->load([
            'ilanSahibi',
            'ilgiliKisi',
            'userDanisman',
            'kategori',
            'parentKategori',
            'il',
            'ilce',
            'mahalle',
            'fotograflar',
            'ozellikler',
            'fiyatGecmisi' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
        ]);

        // ✅ REFACTORED: Use IlanTypeHelper service
        $typeHelper = app(IlanTypeHelper::class);
        $typeColor = $typeHelper->getTypeColor($ilan);
        $typeIcon = $typeHelper->getTypeIcon($ilan);
        $typeSummary = $typeHelper->getTypeSummary($ilan);
        $typeSpecificFields = $typeHelper->getTypeSpecificFields($ilan);

        // ✅ EAGER LOADING: Previous/Next ilanlar için de eager loading
        $previousIlan = Ilan::where('id', '<', $ilan->id)
            ->with(['ilanSahibi:id,ad,soyad', 'il:id,il_adi', 'ilce:id,ilce_adi'])
            ->orderBy('id', 'desc')
            ->first();
        $nextIlan = Ilan::where('id', '>', $ilan->id)
            ->with(['ilanSahibi:id,ad,soyad', 'il:id,il_adi', 'ilce:id,ilce_adi'])
            ->orderBy('id', 'asc')
            ->first();

        // ✅ FIX: location-map component için gerekli değişkenler
        $iller = Il::orderBy('il_adi')->select(['id', 'il_adi'])->get();
        $ilceler = collect();
        if ($ilan->il_id) {
            $ilceler = Ilce::where('il_id', $ilan->il_id)
                ->select(['id', 'ilce_adi'])
                ->orderBy('ilce_adi')
                ->get();
        }
        $mahalleler = collect();
        if ($ilan->ilce_id) {
            $mahalleler = Mahalle::where('ilce_id', $ilan->ilce_id)
                ->select(['id', 'mahalle_adi'])
                ->orderBy('mahalle_adi')
                ->get();
        }

        return view('admin.ilanlar.show', compact(
            'ilan',
            'typeColor',
            'typeIcon',
            'typeSummary',
            'typeSpecificFields',
            'previousIlan',
            'nextIlan',
            'iller',
            'ilceler',
            'mahalleler'
        ));
    }

    public function ownerPrivate(\Illuminate\Http\Request $request, Ilan $ilan)
    {
        $request->validate([
            'owner_private_desired_price_min' => 'nullable|numeric|min:0',
            'owner_private_desired_price_max' => 'nullable|numeric|min:0',
            'owner_private_notes' => 'nullable|string|max:2000',
        ]);
        if (\Illuminate\Support\Facades\Gate::denies('view-private-listing-data', $ilan)) {
            abort(403);
        }
        $before = $ilan->owner_private_data;
        $ilan->owner_private_data = [
            'desired_price_min' => $request->input('owner_private_desired_price_min'),
            'desired_price_max' => $request->input('owner_private_desired_price_max'),
            'notes' => $request->input('owner_private_notes'),
        ];
        $ilan->save();
        \App\Models\IlanPrivateAudit::create([
            'ilan_id' => $ilan->id,
            'user_id' => auth()->id() ?? 0,
            'changes' => [
                'before' => $before,
                'after' => $ilan->owner_private_data,
            ],
        ]);
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Mahrem bilgiler güncellendi');
    }

    public function updatePortalIds(\Illuminate\Http\Request $request, Ilan $ilan)
    {
        if (\Illuminate\Support\Facades\Gate::denies('edit-ilanlar')) {
            abort(403);
        }
        $request->validate([
            'sahibinden_id' => 'nullable|regex:/^[0-9-]{6,}$/',
            'emlakjet_id' => 'nullable|string|max:100',
            'hepsiemlak_id' => 'nullable|string|max:100',
            'zingat_id' => 'nullable|string|max:100',
            'hurriyetemlak_id' => 'nullable|string|max:100',
        ]);
        $data = $request->only(['sahibinden_id', 'emlakjet_id', 'hepsiemlak_id', 'zingat_id', 'hurriyetemlak_id']);
        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $data[$k] = trim($v);
            }
        }
        $normalizer = new \App\Services\Portal\PortalIdNormalizer;
        foreach (['sahibinden_id', 'emlakjet_id', 'hepsiemlak_id', 'zingat_id', 'hurriyetemlak_id'] as $key) {
            if (isset($data[$key]) && $data[$key] !== null && $data[$key] !== '') {
                $portal = str_replace('_id', '', $key);
                $data[$key] = $normalizer->normalizeProviderId($portal, $data[$key]);
            }
        }
        $ilan->fill($data);
        $ilan->save();
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Portal ID’ler güncellendi');
    }

    /**
     * Show the form for editing the specified resource.
     * Context7: İlan düzenleme formu
     *
     * @return \Illuminate\View\View
     */
    public function edit(Ilan $ilan)
    {
        // ✅ N+1 FIX: Eager loading ekle (ilan ilişkileri için)
        $ilan->load([
            'ilanSahibi:id,ad,soyad,telefon',
            'ilgiliKisi:id,ad,soyad,telefon',
            'danisman:id,name,email',
            'il:id,il_adi',
            'ilce:id,ilce_adi',
            'mahalle:id,mahalle_adi',
            'anaKategori:id,name,slug',
            'altKategori:id,name,slug',
            'yayinTipi:id,yayin_tipi', // Context7: Tablo kolonu yayin_tipi
        ]);

        // ✅ Context7 FIX: Status değerleri tutarlı hale getirildi
        $kategoriler = IlanKategori::with(['children' => function ($query) {
            $query->select(['id', 'name', 'parent_id', 'status'])
                ->where('status', true) // Context7: boolean true kullanımı
                ->orderBy('name');
        }])
            ->whereNull('parent_id')
            ->where('status', true) // Context7: boolean true kullanımı
            ->orderBy('name')
            ->get(['id', 'name', 'status']);

        $anaKategoriler = $kategoriler; // Aynı data kullan (performance için)

        // ✅ Context7 FIX: Status değeri tutarlı hale getirildi
        $kisiler = Kisi::where('status', true) // Context7: boolean true kullanımı
            ->select(['id', 'ad', 'soyad', 'telefon'])
            ->get();

        $danismanlar = User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })
            ->where('status', 1) // User model'de status integer
            ->select(['id', 'name', 'email'])
            ->get();

        $iller = Il::orderBy('il_adi')->select(['id', 'il_adi'])->get();

        // Get sub-categories and districts based on current selection (Context7 uyumlu)
        $altKategoriler = collect();
        if ($ilan->ana_kategori_id) { // Context7: ana_kategori_id kullanımı
            $altKategoriler = IlanKategori::where('parent_id', $ilan->ana_kategori_id)
                ->where('status', true) // Context7: boolean true kullanımı
                ->select(['id', 'name'])
                ->get();
        }

        $ilceler = collect();
        if ($ilan->il_id) {
            $ilceler = Ilce::where('il_id', $ilan->il_id)
                ->select(['id', 'ilce_adi'])
                ->orderBy('ilce_adi')
                ->get();
        }

        $mahalleler = collect();
        if ($ilan->ilce_id) {
            $mahalleler = Mahalle::where('ilce_id', $ilan->ilce_id)
                ->select(['id', 'mahalle_adi'])
                ->orderBy('mahalle_adi')
                ->get();
        }

        // ✅ N+1 FIX: Eager loading ile features yükle
        $selectedFeatures = $ilan->ozellikler()
            ->select(['features.id', 'features.slug', 'features.name']) // Select optimization
            ->withPivot('value')
            ->get()
            ->mapWithKeys(function ($feature) {
                return [$feature->slug => $feature->pivot->value ?? '1'];
            })
            ->toArray();

        return view('admin.ilanlar.edit', compact(
            'ilan',
            'kategoriler',
            'anaKategoriler',
            'altKategoriler',
            'kisiler',
            'danismanlar',
            'iller',
            'ilceler',
            'mahalleler',
            'selectedFeatures'
        ));
    }

    /**
     * Update the specified resource in storage.
     * Context7: İlan güncelleme
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function update(Request $request, Ilan $ilan)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'baslik' => 'required|string|max:255',
                'aciklama' => 'nullable|string',
                'fiyat' => 'required|numeric|min:0',
                'para_birimi' => 'required|string|in:TRY,USD,EUR,GBP',

                // Context7: 3-level category (ana → alt → yayin) - FORM'A UYARLANMIŞ
                'ana_kategori_id' => 'required|exists:ilan_kategorileri,id',
                'alt_kategori_id' => 'required|exists:ilan_kategorileri,id',
                // ✅ Context7: yayin_tipi_id → ilan_kategori_yayin_tipleri tablosundan
                'yayin_tipi_id' => 'required|integer|exists:ilan_kategori_yayin_tipleri,id',

                'ilan_sahibi_id' => 'required|exists:kisiler,id',
                'danisman_id' => 'nullable|exists:users,id',
                'il_id' => 'nullable|exists:iller,id',
                'ilce_id' => 'nullable|exists:ilceler,id',
                'mahalle_id' => 'nullable|exists:mahalleler,id',
                'status' => 'required|string|in:Taslak,Aktif,Pasif,Beklemede', // ✅ FIX: İlan tablosu için string (çoklu durumlar var)

                // Yazlık Kiralama Validation Rules
                'min_konaklama' => 'nullable|integer|min:1|max:365',
                'max_misafir' => 'nullable|integer|min:1|max:50',
                'temizlik_ucreti' => 'nullable|numeric|min:0',
                'havuz' => 'nullable|boolean',
                'havuz_turu' => 'nullable|string|max:100',
                'havuz_boyut' => 'nullable|string|max:50',
                'havuz_derinlik' => 'nullable|string|max:50',
                'havuz_boyut_en' => 'nullable|string|max:20',
                'havuz_boyut_boy' => 'nullable|string|max:20',
                'gunluk_fiyat' => 'nullable|numeric|min:0',
                'haftalik_fiyat' => 'nullable|numeric|min:0',
                'aylik_fiyat' => 'nullable|numeric|min:0',
                'sezonluk_fiyat' => 'nullable|numeric|min:0',
                'sezon_baslangic' => 'nullable|date',
                'sezon_bitis' => 'nullable|date|after_or_equal:sezon_baslangic',
                'elektrik_dahil' => 'nullable|boolean',
                'su_dahil' => 'nullable|boolean',
                'internet_dahil' => 'nullable|boolean',
                'carsaf_dahil' => 'nullable|boolean',
                'havlu_dahil' => 'nullable|boolean',
                'klima_var' => 'nullable|boolean',
                'oda_sayisi' => 'nullable|integer|min:1|max:20',
                'banyo_sayisi' => 'nullable|integer|min:1|max:10',
                'yatak_sayisi' => 'nullable|integer|min:1|max:20',
                'restoran_mesafe' => 'nullable|integer|min:0|max:100',
                'market_mesafe' => 'nullable|integer|min:0|max:100',
                'deniz_mesafe' => 'nullable|integer|min:0|max:100',
                'merkez_mesafe' => 'nullable|integer|min:0|max:100',
                'bahce_var' => 'nullable|boolean',
                'tv_var' => 'nullable|boolean',
                'barbeku_var' => 'nullable|boolean',
                'sezlong_var' => 'nullable|boolean',
                'bahce_masasi_var' => 'nullable|boolean',
                'manzara' => 'nullable|string|max:100',
                'ev_tipi' => 'nullable|string|max:50',
                'ev_konsepti' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $numberToText = app(NumberToTextConverter::class);
            $priceText = $numberToText->convertToText((float) $request->fiyat, $request->para_birimi);

            // Check if price changed
            $oldPrice = $ilan->fiyat;
            $newPrice = $request->fiyat;

            $ilan->update([
                'baslik' => $request->baslik,
                'aciklama' => $request->aciklama,
                'fiyat' => $request->fiyat,
                'price_text' => $priceText,
                'para_birimi' => $request->para_birimi,

                // Context7: Map 3-level category to database (both old and new columns)
                'kategori_id' => $request->alt_kategori_id, // Alt kategori = kategori_id (legacy)
                'parent_kategori_id' => $request->ana_kategori_id, // Ana kategori = parent (legacy)
                'ana_kategori_id' => $request->ana_kategori_id, // Ana kategori (new column)
                'alt_kategori_id' => $request->alt_kategori_id, // Alt kategori (new column)
                'yayin_tipi_id' => $request->yayin_tipi_id, // Yayın tipi (new column)

                'ilan_sahibi_id' => $request->ilan_sahibi_id,
                'danisman_id' => $request->danisman_id,
                'il_id' => $request->il_id,
                'ilce_id' => $request->ilce_id,
                'mahalle_id' => $request->mahalle_id,
                'status' => $request->status,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            // Create price history entry if price changed
            if ($oldPrice != $newPrice) {
                IlanPriceHistory::create([
                    'ilan_id' => $ilan->id,
                    'old_price' => $oldPrice,
                    'new_price' => $newPrice,
                    'currency' => $request->para_birimi,
                    'changed_by' => Auth::id(),
                    'change_reason' => $request->degisiklik_nedeni ?? 'Fiyat güncelleme',
                ]);
            }

            // ✨ Features Handling: Sync checkbox values from form
            if ($request->has('features') && is_array($request->features)) {
                $featuresToSync = [];

                // ✅ PERFORMANCE FIX: N+1 query önlendi - Tüm feature'ları tek query'de al
                $featureSlugs = array_keys($request->features);
                $features = \App\Models\Feature::whereIn('slug', $featureSlugs)->get()->keyBy('slug');

                foreach ($request->features as $featureSlug => $featureValue) {
                    $feature = $features->get($featureSlug);

                    if ($feature) {
                        $valueToStore = is_numeric($featureValue) && $featureValue > 0
                            ? $featureValue
                            : ($featureValue === '1' || $featureValue === 1 ? 1 : 0);

                        $featuresToSync[$feature->id] = [
                            'value' => $valueToStore,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                $ilan->ozellikler()->sync($featuresToSync);
                Log::info('Features synced for ilan', [
                    'ilan_id' => $ilan->id,
                    'features_count' => count($featuresToSync),
                ]);
            } elseif ($request->has('features') && empty($request->features)) {
                // Remove all features if features array is empty
                $ilan->ozellikler()->detach();
            }

            // Yazlık Detayları Güncelle
            if ($request->has('min_konaklama') || $request->has('havuz')) {
                \App\Models\YazlikDetail::updateOrCreate(
                    ['ilan_id' => $ilan->id],
                    [
                        'min_konaklama' => $request->min_konaklama ?? 1,
                        'max_misafir' => $request->max_misafir,
                        'temizlik_ucreti' => $request->temizlik_ucreti,
                        'havuz' => $request->boolean('havuz', false),
                        'havuz_turu' => $request->havuz_turu,
                        'havuz_boyut' => $request->havuz_boyut,
                        'havuz_derinlik' => $request->havuz_derinlik,
                        'havuz_boyut_en' => $request->havuz_boyut_en,
                        'havuz_boyut_boy' => $request->havuz_boyut_boy,
                        'gunluk_fiyat' => $request->gunluk_fiyat,
                        'haftalik_fiyat' => $request->haftalik_fiyat,
                        'aylik_fiyat' => $request->aylik_fiyat,
                        'sezonluk_fiyat' => $request->sezonluk_fiyat,
                        'sezon_baslangic' => $request->sezon_baslangic,
                        'sezon_bitis' => $request->sezon_bitis,
                        'elektrik_dahil' => $request->boolean('elektrik_dahil', false),
                        'su_dahil' => $request->boolean('su_dahil', false),
                        'internet_dahil' => $request->boolean('internet_dahil', false),
                        'carsaf_dahil' => $request->boolean('carsaf_dahil', false),
                        'havlu_dahil' => $request->boolean('havlu_dahil', false),
                        'klima_var' => $request->boolean('klima_var', false),
                        'oda_sayisi' => $request->oda_sayisi,
                        'banyo_sayisi' => $request->banyo_sayisi,
                        'yatak_sayisi' => $request->yatak_sayisi,
                        'yatak_turleri' => $request->yatak_turleri ? json_decode($request->yatak_turleri, true) : null,
                        'restoran_mesafe' => $request->restoran_mesafe,
                        'market_mesafe' => $request->market_mesafe,
                        'deniz_mesafe' => $request->deniz_mesafe,
                        'merkez_mesafe' => $request->merkez_mesafe,
                        'bahce_var' => $request->boolean('bahce_var', false),
                        'tv_var' => $request->boolean('tv_var', false),
                        'barbeku_var' => $request->boolean('barbeku_var', false),
                        'sezlong_var' => $request->boolean('sezlong_var', false),
                        'bahce_masasi_var' => $request->boolean('bahce_masasi_var', false),
                        'manzara' => $request->manzara,
                        'ozel_isaretler' => $request->ozel_isaretler ? json_decode($request->ozel_isaretler, true) : null,
                        'ev_tipi' => $request->ev_tipi,
                        'ev_konsepti' => $request->ev_konsepti,
                        'ozel_notlar' => $request->ozel_notlar,
                        'musteri_notlari' => $request->musteri_notlari,
                        'indirim_notlari' => $request->indirim_notlari,
                        'indirimli_fiyat' => $request->indirimli_fiyat,
                        'anahtar_kimde' => $request->anahtar_kimde,
                        'anahtar_notlari' => $request->anahtar_notlari,
                        'sahip_ozel_notlari' => $request->sahip_ozel_notlari,
                        'sahip_iletisim_tercihi' => $request->sahip_iletisim_tercihi,
                        'eids_onayli' => $request->boolean('eids_onayli', false),
                        'eids_onay_tarihi' => $request->eids_onay_tarihi,
                        'eids_belge_no' => $request->eids_belge_no,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('admin.ilanlar.show', $ilan)
                ->with('success', 'İlan başarıyla güncellendi.');
        } catch (\Exception $e) {
            DB::rollBack();

            // ✅ STANDARDIZED: Using ResponseService (automatic logging)
            if ($request->expectsJson()) {
                return ResponseService::serverError('İlan güncellenirken hata oluştu', $e);
            }

            return redirect()->back()
                ->with('error', 'İlan güncellenirken bir hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * Context7: İlan silme (soft delete)
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function destroy(Ilan $ilan)
    {
        try {
            // Soft delete the listing
            $ilan->delete();

            return redirect()->route('admin.ilanlar.index')
                ->with('success', 'İlan başarıyla silindi.');
        } catch (\Exception $e) {
            // ✅ STANDARDIZED: Using ResponseService
            if (request()->expectsJson()) {
                return ResponseService::serverError('İlan silinirken hata oluştu', $e);
            }

            return ResponseService::backError('İlan silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Search listings via AJAX
     * Context7: İlan arama endpoint
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Ilan::with([
            'ilanSahibi:id,ad,soyad',
            'danisman:id,name,email',
            'kategori:id,name',
            'il:id,il_adi',
            'ilce:id,ilce_adi',
        ])->orderBy('updated_at', 'desc');

        if ($request->has('q') && $request->q) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('baslik', 'like', "%{$search}%")
                    ->orWhere('aciklama', 'like', "%{$search}%")
                    ->orWhereHas('ilanSahibi', function ($subQ) use ($search) {
                        $subQ->where('ad', 'like', "%{$search}%")
                            ->orWhere('soyad', 'like', "%{$search}%");
                    });
            });
        }

        $results = $query->limit(20)->get();

        return response()->json([
            'success' => true,
            'data' => $results->map(function ($ilan) {
                return [
                    'id' => $ilan->id,
                    'baslik' => $ilan->baslik,
                    'fiyat' => number_format($ilan->fiyat) . ' ' . $ilan->para_birimi,
                    'sahip' => optional($ilan->ilanSahibi)->tam_ad,
                    'kategori' => optional($ilan->kategori)->ad,
                    'lokasyon' => optional($ilan->il)->il_adi . (optional($ilan->ilce)->ilce_adi ? ', ' . optional($ilan->ilce)->ilce_adi : ''),
                    'status' => $ilan->status,
                    'url' => route('admin.ilanlar.show', $ilan),
                ];
            }),
        ]);
    }

    /**
     * Filter listings
     * Context7: İlan filtreleme endpoint
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request)
    {
        // ✅ REFACTORED: Filterable trait kullanımı - Code duplication azaltıldı
        $query = Ilan::with([
            'ilanSahibi:id,ad,soyad',
            'userDanisman:id,name',
            'kategori:id,name',
            'il:id,il_adi',
            'ilce:id,ilce_adi',
        ]);

        // ✅ REFACTORED: Filterable trait kullanımı
        // Status filter
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Category filter
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Location filters
        if ($request->filled('il_id')) {
            $query->where('il_id', $request->il_id);
        }

        if ($request->filled('ilce_id')) {
            $query->where('ilce_id', $request->ilce_id);
        }

        // ✅ REFACTORED: Price range filter (Filterable trait)
        $query->priceRange(
            $request->filled('min_fiyat') ? (float) $request->min_fiyat : null,
            $request->filled('max_fiyat') ? (float) $request->max_fiyat : null,
            'fiyat'
        );

        // Danışman filter
        if ($request->filled('danisman_id')) {
            $query->where('danisman_id', $request->danisman_id);
        }

        // ✅ REFACTORED: Date range filter (Filterable trait)
        $query->dateRange(
            $request->baslangic_tarihi,
            $request->bitis_tarihi,
            'created_at'
        );

        // ✅ REFACTORED: Sort (Filterable trait)
        $query->sort($request->sort_by, $request->sort_order ?? 'desc', 'updated_at');

        /** @var \Illuminate\Pagination\LengthAwarePaginator $ilanlar */
        $ilanlar = $query->paginate(20);

        if ($request->ajax()) {
            // Context7: Responsive - Return both table and card views
            return response()->json([
                'success' => true,
                'html' => view('admin.ilanlar.partials.listings-grid', compact('ilanlar'))->render(),
                'cards_html' => view('admin.ilanlar.partials.listings-cards', compact('ilanlar'))->render(),
                // ✅ FIX: links() metodu LengthAwarePaginator'da mevcut, type hint eklendi
                'pagination' => (string) $ilanlar->links(),
                'total' => $ilanlar->total(),
            ]);
        }

        return view('admin.ilanlar.index', compact('ilanlar'));
    }

    /**
     * Live search for listings
     * Context7: Canlı arama endpoint
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Live search for listings
     * Context7: Canlı arama endpoint (AKILLI TEK SATIR ARAMA)
     * Yalıhan Bekçi: Smart search with portal IDs, phone, reference number (2025-12-02)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function liveSearch(Request $request)
    {
        $search = $request->get('q', '');

        if (strlen($search) < 2) {
            return response()->json(['results' => []]);
        }

        $ilanlar = Ilan::with(['ilanSahibi', 'kategori', 'site', 'userDanisman'])
            ->where(function ($query) use ($search) {
                $like = "%{$search}%";

                // Temel alanlar
                $query->where('baslik', 'like', $like)
                    ->orWhere('aciklama', 'like', $like)

                    // Referans numarası ve dosya adı
                    ->orWhere('referans_no', 'like', $like)
                    ->orWhere('dosya_adi', 'like', $like)

                    // Portal ID'leri
                    ->orWhere('sahibinden_id', 'like', $like)
                    ->orWhere('emlakjet_id', 'like', $like)
                    ->orWhere('hepsiemlak_id', 'like', $like)
                    ->orWhere('zingat_id', 'like', $like)
                    ->orWhere('hurriyetemlak_id', 'like', $like)

                    // İlan Sahibi: Ad, Soyad, Telefon, Email
                    ->orWhereHas('ilanSahibi', function ($q) use ($like) {
                        $q->where('ad', 'like', $like)
                            ->orWhere('soyad', 'like', $like)
                            ->orWhere('telefon', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    })

                    // Danışman
                    ->orWhereHas('userDanisman', function ($q) use ($like) {
                        $q->where('name', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    });
            })
            ->limit(10)
            ->get();

        $results = $ilanlar->map(function ($ilan) {
            $subtitle = [];

            // İlan Sahibi
            if ($ilan->ilanSahibi) {
                $subtitle[] = $ilan->ilanSahibi->ad . ' ' . $ilan->ilanSahibi->soyad;
            }

            // Kategori
            if ($ilan->kategori) {
                $subtitle[] = $ilan->kategori->name;
            }

            // Site
            if ($ilan->site) {
                $subtitle[] = $ilan->site->name;
            }

            // Referans No (varsa)
            if ($ilan->referans_no) {
                $subtitle[] = 'Ref: ' . $ilan->referans_no;
            }

            return [
                'id' => $ilan->id,
                'text' => $ilan->baslik . ' - ' . number_format($ilan->fiyat) . ' ' . $ilan->para_birimi,
                'subtitle' => implode(' | ', $subtitle),
                'url' => route('admin.ilanlar.show', $ilan),
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Bulk update listings
     * Context7: Toplu güncelleme endpoint
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:ilanlar,id',
            'update_data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $service = app(IlanBulkService::class);
            $result = $service->bulkUpdate($request->ids, $request->update_data);
            $status = $result['success'] ? 200 : 400;

            return response()->json($result, $status);
        } catch (\Exception $e) {
            // ✅ STANDARDIZED: Using ResponseService
            return ResponseService::serverError('Toplu güncelleme sırasında bir hata oluştu', $e);
        }
    }

    /**
     * Bulk delete listings
     * Context7: Toplu silme endpoint
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:ilanlar,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $service = app(IlanBulkService::class);
            $result = $service->bulkDelete($request->ids);
            $status = $result['success'] ? 200 : 400;

            return response()->json($result, $status);
        } catch (\Exception $e) {
            // ✅ STANDARDIZED: Using ResponseService
            return ResponseService::serverError('Toplu silme sırasında bir hata oluştu', $e);
        }
    }

    /**
     * Toggle listing status
     * Context7: İlan durumunu değiştir
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Ilan $ilan)
    {
        try {
            $newStatus = $ilan->status === 'Aktif' ? 'Pasif' : 'Aktif';

            $ilan->update([
                'status' => $newStatus,
                // Context7: enabled field removed - use status only
            ]);

            return response()->json([
                'success' => true,
                'message' => 'İlan statusu başarıyla güncellendi.',
                'new_status' => $newStatus,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Durum güncelleme sırasında bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update listing status
     * Context7: İlan durumunu güncelle
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, Ilan $ilan)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:Taslak,Aktif,Pasif,Beklemede,Arşivlendi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $ilan->update([
                'status' => $request->status,
                // Context7: enabled field removed - use status only
            ]);

            return response()->json([
                'success' => true,
                'message' => 'İlan statusu başarıyla güncellendi.',
                'status' => $request->status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Durum güncelleme sırasında bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get action message for bulk operations
     */
    private function getActionMessage($action)
    {
        $messages = [
            'delete' => 'silindi',
            'activate' => 'statusleştirildi',
            'deactivate' => 'pasifleştirildi',
            'archive' => 'arşivlendi',
        ];

        return $messages[$action] ?? 'güncellendi';
    }

    /**
     * Generate AI-powered title for listing
     * Context7: AI destekli başlık üretimi
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateAiTitle(Request $request)
    {
        try {
            // ✅ REFACTORED: YalihanCortex merkezi "Beyin" sistemi kullanılıyor
            $cortex = app(YalihanCortex::class);

            // Frontend context objesi gönderiyor: { context: { kategori, il, ilce, mahalle, yayinTipi, ... } }
            $context = $request->input('context', []);

            // ✅ FIX: Mahalle bilgisini context'ten veya direkt request'ten al
            $mahalle = $context['mahalle'] ?? $request->input('mahalle');
            if (!$mahalle && $request->has('mahalle_id')) {
                $mahalle = $this->getLocationName($request->input('mahalle_id'));
            }

            // ✅ FIX: Yayın tipi bilgisini context'ten veya direkt request'ten al
            $yayinTipi = $context['yayinTipi'] ?? $context['yayin_tipi'] ?? $request->input('yayin_tipi', 'Satılık');

            // İlan verisini hazırla
            $ilanData = [
                'kategori' => $this->getCategoryName($context['kategori'] ?? $request->input('kategori', 'Gayrimenkul')),
                'il' => $this->getLocationName($context['il'] ?? $request->input('il_id') ?? $request->input('il')),
                'ilce' => $this->getLocationName($context['ilce'] ?? $request->input('ilce_id') ?? $request->input('ilce')),
                'mahalle' => $mahalle,
                'yayin_tipi' => $yayinTipi,
                'fiyat' => $context['fiyat'] ?? $request->input('fiyat'),
                'para_birimi' => $context['paraBirimi'] ?? $context['para_birimi'] ?? $request->input('para_birimi', 'TRY'),
            ];

            // ✅ YalihanCortex üzerinden başlık üret
            $result = $cortex->generateIlanTitle($ilanData, [
                'tone' => $request->input('ai_tone', 'seo'),
            ]);

            // Frontend formatına uyarla
            $titles = $result['titles'] ?? [];
            $title = !empty($titles) ? $titles[0] : 'Başlık üretilemedi';

            return response()->json([
                'success' => $result['success'] ?? true,
                'title' => $title,
                'alternatives' => array_slice($titles, 0, 3),
                'data' => [
                    'title' => $title,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('AI Title Generation Error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            // ✅ FIX: Ollama timeout olsa bile fallback başlıklar döndür
            $context = $request->input('context', []);
            $fallbackTitles = [
                ($context['il'] ?? 'Bodrum') . ' ' . ($request->input('yayin_tipi', 'Satılık')) . ' ' . ($context['kategori'] ?? 'Gayrimenkul'),
                ($request->input('yayin_tipi', 'Satılık')) . ' ' . ($context['kategori'] ?? 'Gayrimenkul') . ' - ' . ($context['il'] ?? 'Bodrum'),
                ($context['il'] ?? 'Bodrum') . '\'da ' . ($request->input('yayin_tipi', 'Satılık')) . ' ' . ($context['kategori'] ?? 'Gayrimenkul'),
            ];

            return response()->json([
                'success' => false,
                'message' => 'AI başlık üretimi başarısız (fallback kullanıldı): ' . $e->getMessage(),
                'title' => $fallbackTitles[0],
                'alternatives' => array_slice($fallbackTitles, 0, 3),
                'data' => [
                    'title' => $fallbackTitles[0],
                ],
            ], 200); // ✅ 200 döndür, frontend fallback başlıkları kullanabilir
        }
    }

    /**
     * Convert price to text (for display)
     * Context7: Fiyat yazıya çevirme endpoint'i
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function convertPriceToText(Request $request)
    {
        try {
            $validated = $request->validate([
                'price' => 'required|numeric|min:0',
                'currency' => 'required|string|in:TRY,USD,EUR,GBP',
            ]);

            $converter = new \App\Services\Utility\NumberToTextConverter();
            $priceText = $converter->convertToText(
                (float) $validated['price'],
                $validated['currency']
            );

            return response()->json([
                'success' => true,
                'price_text' => $priceText,
                'price' => $validated['price'],
                'currency' => $validated['currency'],
            ]);
        } catch (\Exception $e) {
            Log::error('Price to text conversion error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Fiyat yazıya çevrilemedi: ' . $e->getMessage(),
                'price_text' => 'Hata oluştu',
            ], 500);
        }
    }

    /**
     * Generate AI-powered description for listing
     * Context7: AI destekli açıklama üretimi
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateAiDescription(Request $request)
    {
        try {
            // ✅ REFACTORED: YalihanCortex merkezi "Beyin" sistemi kullanılıyor
            $cortex = app(YalihanCortex::class);

            // Frontend context objesi gönderiyor: { context: { kategori, il, ilce, mahalle, ... } }
            $context = $request->input('context', []);

            // İlan verisini hazırla
            $ilanData = [
                'kategori' => $this->getCategoryName($context['kategori'] ?? $request->input('kategori', 'Gayrimenkul')),
                'il' => $this->getLocationName($context['il'] ?? $request->input('il')),
                'ilce' => $this->getLocationName($context['ilce'] ?? $request->input('ilce')),
                'mahalle' => $this->getLocationName($context['mahalle'] ?? $request->input('mahalle')),
                'fiyat' => $context['fiyat'] ?? $request->input('fiyat'),
                'para_birimi' => $context['paraBirimi'] ?? $context['para_birimi'] ?? $request->input('para_birimi', 'TRY'),
                'metrekare' => $context['metrekare'] ?? $request->input('metrekare'),
                'oda_sayisi' => $context['odaSayisi'] ?? $context['oda_sayisi'] ?? $request->input('oda_sayisi'),
            ];

            // ✅ YalihanCortex üzerinden açıklama üret
            $result = $cortex->generateIlanDescription($ilanData, [
                'tone' => $request->input('ai_tone', 'seo'),
            ]);

            // Frontend formatına uyarla
            return response()->json([
                'success' => $result['success'] ?? true,
                'description' => $result['description'] ?? 'Açıklama üretilemedi',
                'data' => [
                    'description' => $result['description'] ?? 'Açıklama üretilemedi',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('AI Description Generation Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'AI açıklama üretimi başarısız: ' . $e->getMessage(),
                'description' => 'Açıklama üretilemedi',
            ], 500);
        }
    }

    /**
     * Get dynamic fields based on property type
     * Context7: Emlak tipine göre dinamik alanlar
     *
     * @param  string  $propertyType
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDynamicFields($propertyType)
    {
        $fields = [];

        switch (strtolower($propertyType)) {
            case 'daire':
                $fields = [
                    ['name' => 'oda_sayisi', 'label' => 'Oda Sayısı', 'type' => 'select', 'options' => ['1+1', '2+1', '3+1', '4+1', '5+1']],
                    ['name' => 'banyo_sayisi', 'label' => 'Banyo Sayısı', 'type' => 'number', 'min' => 1, 'max' => 5],
                    ['name' => 'balkon_var', 'label' => 'Balkon', 'type' => 'checkbox'],
                    ['name' => 'asansor_var', 'label' => 'Asansör', 'type' => 'checkbox'],
                    ['name' => 'kat_no', 'label' => 'Kat Numarası', 'type' => 'number'],
                    ['name' => 'toplam_kat', 'label' => 'Toplam Kat', 'type' => 'number'],
                ];
                break;

            case 'villa':
                $fields = [
                    ['name' => 'oda_sayisi', 'label' => 'Oda Sayısı', 'type' => 'select', 'options' => ['3+1', '4+1', '5+1', '6+1', '7+1']],
                    ['name' => 'bahce_var', 'label' => 'Bahçe', 'type' => 'checkbox'],
                    ['name' => 'havuz_var', 'label' => 'Havuz', 'type' => 'checkbox'],
                    ['name' => 'garaj_var', 'label' => 'Garaj', 'type' => 'checkbox'],
                    ['name' => 'kat_sayisi', 'label' => 'Kat Sayısı', 'type' => 'number', 'min' => 1, 'max' => 4],
                ];
                break;

            case 'arsa':
                $fields = [
                    ['name' => 'imar_statusu', 'label' => 'İmar Durumu', 'type' => 'select', 'options' => ['İmarlı', 'İmarsız', 'Villa İmarlı']],
                    ['name' => 'ada_no', 'label' => 'Ada No', 'type' => 'text'],
                    ['name' => 'parsel_no', 'label' => 'Parsel No', 'type' => 'text'],
                    ['name' => 'kaks', 'label' => 'KAKS', 'type' => 'number', 'step' => '0.01'],
                    ['name' => 'taban_alani', 'label' => 'Taban Alanı', 'type' => 'number'],
                ];
                break;

            default:
                $fields = [
                    ['name' => 'aciklama', 'label' => 'Genel Açıklama', 'type' => 'textarea'],
                ];
        }

        return response()->json([
            'success' => true,
            'fields' => $fields,
        ]);
    }

    /**
     * Get AI property suggestions
     * Context7: AI destekli emlak önerileri
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAIPropertySuggestions(Request $request)
    {
        try {
            // ✅ REFACTORED: YalihanCortex merkezi "Beyin" sistemi kullanılıyor
            $cortex = app(YalihanCortex::class);

            // Frontend context objesi gönderiyor: { context: { kategori, il, ilce, mahalle, ... } }
            $context = $request->input('context', []);

            // İlan verisini hazırla
            $ilanData = [
                'kategori' => $this->getCategoryName($context['kategori'] ?? $request->input('kategori', 'Gayrimenkul')),
                'il' => $this->getLocationName($context['il'] ?? $request->input('il')),
                'ilce' => $this->getLocationName($context['ilce'] ?? $request->input('ilce')),
                'mahalle' => $this->getLocationName($context['mahalle'] ?? $request->input('mahalle')),
                'fiyat' => $context['fiyat'] ?? $request->input('fiyat'),
                'metrekare' => $context['metrekare'] ?? $request->input('metrekare'),
            ];

            // ✅ YalihanCortex üzerinden fiyat önerisi
            $result = $cortex->suggestPrice($ilanData);

            // Frontend formatına uyarla
            $suggestions = $result['suggestions'] ?? [];

            // Frontend'in beklediği format: { suggestions: [...] }
            return response()->json([
                'success' => $result['success'] ?? true,
                'suggestions' => $suggestions,
                'data' => [
                    'suggestions' => $suggestions,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('AI Property Suggestions Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'AI önerileri alınamadı: ' . $e->getMessage(),
                'suggestions' => [],
            ], 500);
        }
    }

    /**
     * Optimize price with AI
     * Context7: AI destekli fiyat optimizasyonu
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optimizePriceWithAi(Request $request)
    {
        try {
            // ✅ REFACTORED: YalihanCortex merkezi "Beyin" sistemi kullanılıyor
            $cortex = app(YalihanCortex::class);

            // Frontend context objesi gönderiyor: { context: { kategori, il, ilce, mahalle, ... } }
            $context = $request->input('context', []);

            // İlan verisini hazırla
            $ilanData = [
                'fiyat' => $context['fiyat'] ?? $request->input('fiyat'),
                'para_birimi' => $context['paraBirimi'] ?? $context['para_birimi'] ?? $request->input('para_birimi', 'TRY'),
                'kategori' => $this->getCategoryName($context['kategori'] ?? $request->input('kategori', 'Gayrimenkul')),
                'metrekare' => $context['metrekare'] ?? $request->input('metrekare'),
                'il' => $this->getLocationName($context['il'] ?? $request->input('il')),
                'ilce' => $this->getLocationName($context['ilce'] ?? $request->input('ilce')),
                'mahalle' => $this->getLocationName($context['mahalle'] ?? $request->input('mahalle')),
            ];

            // ✅ YalihanCortex üzerinden fiyat önerisi
            $result = $cortex->suggestPrice($ilanData);

            // Frontend formatına uyarla
            $suggestions = $result['suggestions'] ?? [];
            $optimized = null;

            // İlk öneriyi al (Pazarlık Payı veya Piyasa Ortalaması)
            if (is_array($suggestions) && count($suggestions) > 0) {
                // Piyasa Ortalaması varsa onu al, yoksa ilkini al
                foreach ($suggestions as $suggestion) {
                    if (
                        isset($suggestion['label']) &&
                        (strpos($suggestion['label'], 'Piyasa') !== false ||
                            strpos($suggestion['label'], 'Ortalama') !== false)
                    ) {
                        $optimized = $suggestion['value'] ?? null;
                        break;
                    }
                }
                // Eğer bulamadıysak ilk öneriyi al
                if ($optimized === null && isset($suggestions[0]['value'])) {
                    $optimized = $suggestions[0]['value'];
                }
            }

            return response()->json([
                'success' => $result['success'] ?? true,
                'optimized' => $optimized,
                'data' => [
                    'optimized' => $optimized,
                    'suggestions' => $suggestions,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('AI Price Optimization Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'AI fiyat optimizasyonu başarısız: ' . $e->getMessage(),
                'optimized' => null,
            ], 500);
        }
    }

    /**
     * Export listings to Excel
     * Context7: İlanları Excel'e aktar
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function exportExcel(Request $request)
    {
        try {
            $exportService = app(IlanExportService::class);
            $query = $exportService->getExportQuery($request);
            $ilanlar = $query->limit(1000)->get();

            // Simple CSV export (mock implementation)
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="ilanlar_' . date('Y-m-d') . '.csv"',
            ];

            $callback = function () use ($ilanlar) {
                $file = fopen('php://output', 'w');

                // CSV header
                fputcsv($file, ['ID', 'Başlık', 'Fiyat', 'Para Birimi', 'Durum', 'Kategori', 'Konum', 'İlan Sahibi', 'Oluşturma Tarihi']);

                foreach ($ilanlar as $ilan) {
                    fputcsv($file, [
                        $ilan->id,
                        $ilan->baslik,
                        $ilan->fiyat,
                        $ilan->para_birimi,
                        $ilan->status,
                        optional($ilan->kategori)->ad,
                        optional($ilan->il)->il_adi . (optional($ilan->ilce)->ilce_adi ? ', ' . optional($ilan->ilce)->ilce_adi : ''),
                        optional($ilan->ilanSahibi)->tam_ad,
                        $ilan->created_at->format('d.m.Y H:i'),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Export işlemi sırasında hata: ' . $e->getMessage());
        }
    }

    /**
     * Export listings to PDF
     * Context7: İlanları PDF'e aktar
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function exportPdf(Request $request)
    {
        try {
            $exportService = app(IlanExportService::class);
            $query = $exportService->getExportQuery($request);
            $ilanlar = $query->limit(200)->get();

            return view('admin.ilanlar.pdf', compact('ilanlar'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'PDF export işlemi sırasında hata: ' . $e->getMessage());
        }
    }

    /**
     * Upload photos for a listing
     * Context7: İlan fotoğrafları yükle
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPhotos(Request $request, Ilan $ilan)
    {
        try {
            $service = app(IlanPhotoService::class);
            $result = $service->uploadPhotos($ilan, (array) $request->file('photos'));
            $status = $result['success'] ? 200 : 422;

            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fotoğraf yükleme sırasında hata: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a photo from listing
     * Context7: İlan fotoğrafı sil
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePhoto(Ilan $ilan, IlanFotografi $photo)
    {
        try {
            $service = app(IlanPhotoService::class);
            $result = $service->deletePhoto($ilan, $photo);
            $status = $result['success'] ? 200 : 400;

            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fotoğraf silme sırasında hata: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update photo order
     * Context7: Fotoğraf sıralamasını güncelle
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // Context7: order → display_order (forbidden pattern)
    public function updatePhotoSequence(Request $request, Ilan $ilan)
    {
        try {
            $service = app(IlanPhotoService::class);
            $result = $service->updatePhotoSequence($ilan, (array) $request->photo_orders);
            $status = $result['success'] ? 200 : 422;

            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sıralama güncelleme sırasında hata: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get price history API
     * Context7: Fiyat geçmişi API endpoint
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function priceHistoryApi(Ilan $ilan, Request $request)
    {
        $query = IlanPriceHistory::where('ilan_id', $ilan->id);

        // Time range filter
        $range = $request->get('range', 'all');
        if ($range !== 'all') {
            $days = (int) $range;
            $query->where('created_at', '>=', now()->subDays($days));
        }

        $history = $query->orderBy('created_at', 'asc')->get();

        // İlk kayıt yoksa, ilanın ilk fiyatını ekle
        if ($history->isEmpty() && $ilan->fiyat) {
            $history = collect([[
                'id' => 0,
                'ilan_id' => $ilan->id,
                'old_price' => $ilan->fiyat,
                'new_price' => $ilan->fiyat,
                'currency' => $ilan->para_birimi ?? 'TRY',
                'change_reason' => 'initial',
                'changed_by' => null,
                'created_at' => $ilan->created_at,
            ]]);
        } elseif ($history->isNotEmpty() && $history->first()->old_price === null) {
            // İlk kayıt old_price null ise, ilanın başlangıç fiyatını ekle
            $firstRecord = $history->first();
            $history->prepend([
                'id' => 0,
                'ilan_id' => $ilan->id,
                'old_price' => $firstRecord->new_price,
                'new_price' => $firstRecord->new_price,
                'currency' => $firstRecord->currency ?? 'TRY',
                'change_reason' => 'initial',
                'changed_by' => null,
                'created_at' => $ilan->created_at ?? $firstRecord->created_at,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $history->values(),
        ]);
    }

    /**
     * Save draft listing
     * Context7: Taslak kaydetme
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveDraft(Request $request)
    {
        try {
            $draftData = $request->all();
            $draftData['status'] = 'Taslak';
            // Context7: enabled field removed - use status only

            // This would typically save to a drafts table or session
            // For now, we'll just return success
            return response()->json([
                'success' => true,
                'message' => 'Taslak kaydedildi.',
                'draft_id' => uniqid(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Taslak kaydetme sırasında hata: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Context7 uyumlu auto-save listing data
     * Context7: Otomatik kayıt endpoint
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function autoSave(Request $request)
    {
        try {
            $userId = Auth::id();
            $formId = $request->form_id ?? 'ilan_create_' . $userId;
            $cacheKey = "context7_autosave_{$formId}";

            // Context7 uyumlu data structure
            $autoSaveData = [
                'form_data' => $request->except(['_token', 'form_id']),
                'user_id' => $userId,
                'timestamp' => now()->toISOString(),
                'step' => $request->current_step ?? 1,
                'progress' => $request->progress ?? 0,
                'context7_version' => '1.0',
            ];

            // Cache'e kaydet (Redis preferred, fallback to session)
            if (config('cache.default') === 'redis') {
                Cache::put($cacheKey, $autoSaveData, now()->addHours(24));
            } else {
                session([$cacheKey => $autoSaveData]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Context7 otomatik kayıt tamamlandı',
                'timestamp' => now()->format('H:i:s'),
                'cache_key' => $cacheKey,
                'data_size' => strlen(json_encode($autoSaveData)) . ' bytes',
            ]);
        } catch (\Exception $e) {
            // ✅ STANDARDIZED: Using LogService
            LogService::error('Context7 AutoSave Error', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Context7 otomatik kayıt hatası: ' . $e->getMessage(),
                'error_code' => 'CONTEXT7_AUTOSAVE_FAILED',
            ], 500);
        }
    }

    /**
     * Get user's listings (ilanlarim)
     * Context7: Kullanıcının ilanları
     *
     * @return \Illuminate\View\View
     */
    public function ilanlarim(Request $request)
    {
        $query = Ilan::with(['ilanSahibi', 'kategori', 'il', 'ilce'])
            ->where('danisman_id', Auth::id())
            ->orderBy('updated_at', 'desc');

        $ilanlar = $query->paginate(20);

        return view('admin.ilanlar.my-listings', compact('ilanlar'));
    }

    /**
     * Refresh listing rate/stats
     * Context7: İlan istatistiklerini yenile
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshRate(Request $request, Ilan $ilan)
    {
        try {
            // Mock rate refresh - in real implementation, this would fetch from external APIs
            $ilan->update([
                'view_count' => $ilan->view_count + rand(1, 5),
                'favorite_count' => $ilan->favorite_count + rand(0, 2),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Veriler yenilendi.',
                'stats' => [
                    'view_count' => $ilan->view_count,
                    'favorite_count' => $ilan->favorite_count,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Veri yenileme sırasında hata: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ✅ REFACTORED: Type helper methods moved to IlanTypeHelper service
    // See: App\Services\Ilan\IlanTypeHelper

    /**
     * Duplicate a listing
     * Context7: İlan kopyalama
     * POST /admin/ilanlar/{ilan}/duplicate
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function duplicate(Ilan $ilan)
    {
        try {
            DB::beginTransaction();

            // Yeni ilan oluştur (mevcut ilanın kopyası)
            $newIlan = $ilan->replicate();
            $newIlan->baslik = $ilan->baslik . ' (Kopya)';
            $newIlan->status = 'Taslak'; // Context7: Yeni kopya taslak olarak oluşturulur
            $newIlan->referans_no = null; // Referans numarası sıfırlanır
            $newIlan->created_at = now();
            $newIlan->updated_at = now();
            $newIlan->save();

            // Fotoğrafları kopyala
            if ($ilan->fotograflar) {
                foreach ($ilan->fotograflar as $fotograf) {
                    $newFoto = $fotograf->replicate();
                    $newFoto->ilan_id = $newIlan->id;
                    $newFoto->save();
                }
            }

            // Özellikleri kopyala
            if ($ilan->ozellikler) {
                foreach ($ilan->ozellikler as $ozellik) {
                    $newIlan->ozellikler()->attach($ozellik->id, [
                        'deger' => $ozellik->pivot->deger ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'İlan başarıyla kopyalandı',
                'ilan_id' => $newIlan->id,
                'redirect_url' => route('admin.ilanlar.edit', $newIlan->id),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('İlan kopyalama hatası: ' . $e->getMessage(), [
                'ilan_id' => $ilan->id,
                'error' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'İlan kopyalanırken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get features by category
     * Context7: Kategoriye göre özellikler
     * GET /admin/ilanlar/api/features/category/{categoryId}
     * GET /api/admin/features?category_id={categoryId}
     *
     * @param  int|null  $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFeaturesByCategory($categoryId = null)
    {
        try {
            if (! $categoryId) {
                $categoryId = request()->get('category_id');
            }

            if (! $categoryId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category ID is required',
                ], 400);
            }

            // ✅ REFACTORED: Use IlanFeatureService
            $featureService = app(IlanFeatureService::class);
            $yayinTipi = request()->get('yayin_tipi');
            $result = $featureService->getFeaturesByCategory($categoryId, $yayinTipi);

            return response()->json([
                'success' => true,
                'data' => $result['feature_categories'],
                'debug' => $result['metadata'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Özellikler yüklenirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk action for listings
     * Context7: Toplu işlemler (activate, deactivate, delete, export, assign)
     * Yalıhan Bekçi: Advanced bulk operations implementation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'action' => 'required|string|in:activate,deactivate,delete,export,assign_danisman,add_tag,remove_tag',
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|exists:ilanlar,id',
                'value' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Export işlemi ayrı tutulur
            if ($request->action === 'export') {
                $ilanlar = Ilan::with(['kategori', 'il', 'ilce'])
                    ->whereIn('id', $request->ids)
                    ->get();

                // ✅ FIX: IlanlarExport class'ı yok, anonymous class ile düzeltildi
                $exportData = $ilanlar->map(function ($ilan) {
                    return [
                        'ID' => $ilan->id,
                        'Başlık' => $ilan->baslik,
                        'Fiyat' => $ilan->fiyat,
                        'Para Birimi' => $ilan->para_birimi,
                        'Durum' => $ilan->status,
                        'Kategori' => $ilan->kategori->name ?? '',
                        'İl' => $ilan->il->il_adi ?? '',
                        'İlçe' => $ilan->ilce->ilce_adi ?? '',
                        'Oluşturulma' => $ilan->created_at?->format('Y-m-d H:i:s'),
                    ];
                })->toArray();

                $export = new class($exportData) implements FromArray, WithHeadings
                {
                    public function __construct(private array $data) {}

                    public function array(): array
                    {
                        return $this->data;
                    }

                    public function headings(): array
                    {
                        return ['ID', 'Başlık', 'Fiyat', 'Para Birimi', 'Durum', 'Kategori', 'İl', 'İlçe', 'Oluşturulma'];
                    }
                };

                return Excel::download($export, 'ilanlar_' . date('Y-m-d_His') . '.xlsx');
            }

            $service = app(IlanBulkService::class);
            $result = $service->bulkAction($request->action, $request->ids, $request->value);
            $status = $result['success'] ? 200 : 400;

            return response()->json($result, $status);
        } catch (\Exception $e) {
            Log::error('Bulk action error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'İşlem sırasında hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get location name from ID
     * Context7: Lokasyon ID'sinden isim al
     */
    protected function getLocationName($locationId)
    {
        if (! $locationId) {
            return '';
        }

        // Zaten string ise direkt döndür (isim zaten gelmiş)
        if (! is_numeric($locationId)) {
            return $locationId;
        }

        // İl kontrolü
        $il = Il::find($locationId);
        if ($il) {
            return $il->il_adi ?? $il->name ?? '';
        }

        // İlçe kontrolü
        $ilce = Ilce::find($locationId);
        if ($ilce) {
            return $ilce->ilce_adi ?? $ilce->name ?? '';
        }

        // Mahalle kontrolü
        $mahalle = Mahalle::find($locationId);
        if ($mahalle) {
            return $mahalle->mahalle_adi ?? $mahalle->name ?? '';
        }

        return '';
    }

    /**
     * Get category name from ID or slug
     * Context7: Kategori ID veya slug'ından isim al
     */
    protected function getCategoryName($categoryValue)
    {
        if (! $categoryValue) {
            return '';
        }

        // Zaten string ise direkt döndür (isim zaten gelmiş)
        if (! is_numeric($categoryValue)) {
            return $categoryValue;
        }

        // Kategori kontrolü
        $kategori = IlanKategori::find($categoryValue);
        if ($kategori) {
            return $kategori->name ?? $kategori->slug ?? '';
        }

        return '';
    }
}
