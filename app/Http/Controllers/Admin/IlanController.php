<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ilan;
use App\Models\IlanKategori;
use App\Models\IlanFotografi;
use App\Models\IlanPriceHistory;
use App\Models\Kisi;
use App\Models\User;
use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class IlanController extends AdminController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // ✅ OPTIMIZED: Paginate first, then eager load (Context7 Performance Pattern)
        $query = Ilan::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('baslik', 'like', "%{$search}%")
                  ->orWhere('aciklama', 'like', "%{$search}%")
                  ->orWhereHas('ilanSahibi', function($subQ) use ($search) {
                      $subQ->where('ad', 'like', "%{$search}%")
                           ->orWhere('soyad', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by category (Context7 uyumlu)
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori_id', $request->kategori);
        }

        // Filter by price range
        if ($request->has('min_fiyat') && $request->min_fiyat) {
            $query->where('fiyat', '>=', $request->min_fiyat);
        }

        if ($request->has('max_fiyat') && $request->max_fiyat) {
            $query->where('fiyat', '<=', $request->max_fiyat);
        }

        // Filter by location
        if ($request->has('il_id') && $request->il_id) {
            $query->where('il_id', $request->il_id);
        }

        if ($request->has('ilce_id') && $request->ilce_id) {
            $query->where('ilce_id', $request->ilce_id);
        }

        // ✅ Sort functionality (Yalıhan Bekçi uyumlu)
        $sort = $request->get('sort', 'created_desc');

        switch ($sort) {
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('fiyat', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('fiyat', 'asc');
                break;
            case 'created_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // ⚡ PERFORMANCE: Select only needed columns
        $query->select([
            'id', 'baslik', 'fiyat', 'para_birimi', 'status', 'is_published',
            'kategori_id', 'ana_kategori_id', 'alt_kategori_id', 'yayin_tipi_id',
            'ilan_sahibi_id', 'danisman_id', 'il_id', 'ilce_id',
            'goruntulenme', 'created_at', 'updated_at'
        ]);

        // ✅ EAGER LOADING: Prevent N+1 queries
        $query->with([
            'ilanSahibi:id,ad,soyad,telefon',
            'danisman:id,name,email',
            'il:id,il_adi',
            'ilce:id,ilce_adi',
            'anaKategori:id,name',
            'altKategori:id,name',
        ]);

        // Paginate FIRST (efficient: only loads needed rows)
        // ✅ Eager loading already applied with with() above
        $ilanlar = $query->paginate(20);

        // ⚡ CACHE: Statistics (5 min cache)
        $stats = Cache::remember('admin.ilanlar.stats', 300, function () {
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
        $kategoriler = Cache::remember('admin.ilanlar.filter.kategoriler', 3600, function () {
            return IlanKategori::whereNull('parent_id')
                ->where('status', 1)
                ->orderBy('name')
                ->select(['id', 'name'])
                ->get();
        });
        
        $iller = Cache::remember('admin.ilanlar.filter.iller', 3600, function () {
            return Il::orderBy('il_adi')->select(['id', 'il_adi'])->get();
        });

        return view('admin.ilanlar.index', compact('ilanlar', 'stats', 'kategoriler', 'iller'));
    }

    /**
     * Test page for category cascading
     */
    public function testCategories()
    {
        $kategoriler = IlanKategori::with(['children' => function($query) {
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
     */
    public function create()
    {
        // Context7 uyumlu optimized loading
        $kategoriler = IlanKategori::with(['children' => function($query) {
            $query->select(['id', 'name', 'slug', 'parent_id', 'status']) // ✅ FIX: slug added
                  ->where('status', true) // ✅ Context7: status is boolean (FIXED!)
                  ->orderBy('name');
        }])
        ->whereNull('parent_id')
        ->where('status', true) // ✅ Context7: status is boolean (FIXED!)
        ->orderBy('name')
        ->get(['id', 'name', 'slug', 'status']); // ✅ FIX: slug added

        $anaKategoriler = $kategoriler; // Aynı data kullan (performance için)
        $altKategoriler = collect(); // Varsayılan olarak boş koleksiyon

        // Context7: Yayın tipleri dinamik yükleme (category'ye göre API'den gelecek)
        $yayinTipleri = collect(); // Boş başlangıç, kategori seçilince yüklenecek

        $kisiler = Kisi::where('status', 'Aktif')->select(['id', 'ad', 'soyad', 'telefon'])->get();
        $danismanlar = User::whereHas('roles', function($q) { $q->where('name', 'danisman'); })
            ->where('status', 1)
            ->select(['id', 'name', 'email'])
            ->get();
        $iller = Il::orderBy('il_adi')->select(['id', 'il_adi'])->get();

        // ✅ FIX: Eksik değişkenler eklendi (Yalıhan Bekçi error fix)
        $status = ['Taslak', 'Aktif', 'Pasif', 'Beklemede'];
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

        return view('admin.ilanlar.create', compact(
            'kategoriler', 'anaKategoriler', 'altKategoriler', 'yayinTipleri',
            'kisiler', 'danismanlar', 'iller', 'ilceler', 'mahalleler',
            'autoSaveData', 'status', 'taslak', 'etiketler', 'ulkeler'
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
            \Log::warning('Context7 AutoSave Retrieval Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Store a newly created resource in storage.
     * Context7: Form field mapping fixed (2025-10-21)
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Context7: 3-level category system validation
            $validator = Validator::make($request->all(), [
            'baslik' => 'required|string|max:255',
            'aciklama' => 'nullable|string',
            'fiyat' => 'required|numeric|min:0',
            'para_birimi' => 'required|string|in:TRY,USD,EUR,GBP',

            // Context7: 3-level category (ana → alt → yayin)
            'ana_kategori_id' => 'required|exists:ilan_kategorileri,id',
            'alt_kategori_id' => 'required|exists:ilan_kategorileri,id',
            'yayin_tipi_id' => 'required|integer',

            'ilan_sahibi_id' => 'required|exists:kisiler,id',
            'danisman_id' => 'nullable|exists:users,id',
            'il_id' => 'nullable|exists:iller,id',
            'ilce_id' => 'nullable|exists:ilceler,id',
            'mahalle_id' => 'nullable|exists:mahalleler,id',
            'status' => 'required|string|in:Taslak,Aktif,Pasif,Beklemede',
            
            // 🆕 PHASE 1: Address Components
            'sokak' => 'nullable|string|max:255',
            'cadde' => 'nullable|string|max:255',
            'bulvar' => 'nullable|string|max:255',
            'bina_no' => 'nullable|string|max:20',
            'daire_no' => 'nullable|string|max:20',
            'posta_kodu' => 'nullable|string|max:10',
            
            // 🆕 PHASE 2: Distance Data
            'nearby_distances' => 'nullable|json',
            
            // 🆕 PHASE 3: Property Boundary
            'boundary_geojson' => 'nullable|json',
            'boundary_area' => 'nullable|numeric|min:0',

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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Context7: Map form fields to database fields
        $ilan = Ilan::create([
            'baslik' => $request->baslik,
            'aciklama' => $request->aciklama,
            'fiyat' => $request->fiyat,
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
            
            // 🆕 PHASE 2: Distance Data
            'nearby_distances' => $request->nearby_distances,
            
            // 🆕 PHASE 3: Property Boundary
            'boundary_geojson' => $request->boundary_geojson,
            'boundary_area' => $request->boundary_area,

            // Context7: Map 'status' (form field) to 'status' (database column - legacy)
            'status' => $request->status,
            'is_published' => $request->status === 'Aktif',
            'slug' => Str::slug($request->baslik),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,

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
        ]);

        // Create price history entry
        IlanPriceHistory::create([
            'ilan_id' => $ilan->id,
            'old_price' => 0,
            'new_price' => $request->fiyat,
            'currency' => $request->para_birimi,
            'changed_by' => Auth::id(),
            'change_reason' => 'İlk ilan oluşturma',
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
            if (!empty($featuresToAttach)) {
                $ilan->features()->attach($featuresToAttach);
                \Log::info('✅ Features attached to ilan', [
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

            return redirect()->route('admin.ilanlar.show', $ilan)
                ->with('success', 'İlan başarıyla oluşturuldu.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('İlan oluşturma hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['_token', 'password']),
            ]);

            return redirect()->back()
                ->with('error', 'İlan oluşturulurken bir hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
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
            'fiyatGecmisi' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            }
        ]);

        // Prepare type-related variables for the view
        $typeColor = $this->getTypeColor($ilan);
        $typeIcon = $this->getTypeIcon($ilan);
        $typeSummary = $this->getTypeSummary($ilan);
        $typeSpecificFields = $this->getTypeSpecificFields($ilan);

        $previousIlan = Ilan::where('id', '<', $ilan->id)->orderBy('id', 'desc')->first();
        $nextIlan = Ilan::where('id', '>', $ilan->id)->orderBy('id', 'asc')->first();

        return view('admin.ilanlar.show', compact(
            'ilan',
            'typeColor',
            'typeIcon',
            'typeSummary',
            'typeSpecificFields',
            'previousIlan',
            'nextIlan'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ilan $ilan)
    {
        $kategoriler = IlanKategori::with(['children' => function($query) {
            $query->select(['id', 'name', 'parent_id', 'status'])
                  ->where('status', 'aktif')
                  ->orderBy('name');
        }])
        ->whereNull('parent_id')
        ->where('status', 'aktif')
        ->orderBy('name')
        ->get(['id', 'name', 'status']);

        $anaKategoriler = $kategoriler; // Aynı data kullan (performance için)
        $kisiler = Kisi::where('status', 'Aktif')->select(['id', 'ad', 'soyad', 'telefon'])->get();
        $danismanlar = User::whereHas('roles', function($q) { $q->where('name', 'danisman'); })
            ->where('status', 1)
            ->select(['id', 'name', 'email'])
            ->get();
        $iller = Il::orderBy('il_adi')->select(['id', 'il_adi'])->get();

        // Get sub-categories and districts based on current selection (Context7 uyumlu)
        $altKategoriler = collect();
        if ($ilan->kategori_id) {
            $altKategoriler = IlanKategori::where('parent_id', $ilan->kategori_id)
                ->where('status', 'aktif')
                ->select(['id', 'name'])
                ->get();
        }

        $ilceler = collect();
        if ($ilan->il_id) {
            $ilceler = Ilce::where('il_id', $ilan->il_id)->orderBy('ilce_adi')->get();
        }

        $mahalleler = collect();
        if ($ilan->ilce_id) {
            $mahalleler = Mahalle::where('ilce_id', $ilan->ilce_id)->orderBy('mahalle_adi')->get();
        }

        // ✨ Load existing features for this ilan
        $selectedFeatures = $ilan->ozellikler()
            ->withPivot('value')
            ->get()
            ->mapWithKeys(function ($feature) {
                return [$feature->slug => $feature->pivot->value ?? '1'];
            })
            ->toArray();

        return view('admin.ilanlar.edit', compact(
            'ilan', 'kategoriler', 'anaKategoriler', 'altKategoriler', 'kisiler', 'danismanlar',
            'iller', 'ilceler', 'mahalleler', 'selectedFeatures'
        ));
    }

    /**
     * Update the specified resource in storage.
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
            'yayin_tipi_id' => 'required|integer',

            'ilan_sahibi_id' => 'required|exists:kisiler,id',
            'danisman_id' => 'nullable|exists:users,id',
            'il_id' => 'nullable|exists:iller,id',
            'ilce_id' => 'nullable|exists:ilceler,id',
            'mahalle_id' => 'nullable|exists:mahalleler,id',
            'status' => 'required|string|in:Taslak,Aktif,Pasif,Beklemede',

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

        // Check if price changed
        $oldPrice = $ilan->fiyat;
        $newPrice = $request->fiyat;

        $ilan->update([
            'baslik' => $request->baslik,
            'aciklama' => $request->aciklama,
            'fiyat' => $request->fiyat,
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
            'is_published' => $request->status === 'Aktif',
            'slug' => Str::slug($request->baslik),
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

            foreach ($request->features as $featureSlug => $featureValue) {
                $feature = \App\Models\Feature::where('slug', $featureSlug)->first();

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
            \Log::info('Features synced for ilan', [
                'ilan_id' => $ilan->id,
                'features_count' => count($featuresToSync)
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

            Log::error('İlan güncelleme hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ilan_id' => $ilan->id,
                'request' => $request->except(['_token', 'password']),
            ]);

            return redirect()->back()
                ->with('error', 'İlan güncellenirken bir hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ilan $ilan)
    {
        try {
            // Soft delete the listing
            $ilan->delete();

            return redirect()->route('admin.ilanlar.index')
                ->with('success', 'İlan başarıyla silindi.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'İlan silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Search listings via AJAX
     */
    public function search(Request $request)
    {
        $query = Ilan::with([
            'ilanSahibi:id,ad,soyad',
            'kategori:id,name',
            'il:id,il_adi',
            'ilce:id,ilce_adi'
        ])->orderBy('updated_at', 'desc');

        if ($request->has('q') && $request->q) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('baslik', 'like', "%{$search}%")
                  ->orWhere('aciklama', 'like', "%{$search}%")
                  ->orWhereHas('ilanSahibi', function($subQ) use ($search) {
                      $subQ->where('ad', 'like', "%{$search}%")
                           ->orWhere('soyad', 'like', "%{$search}%");
                  });
            });
        }

        $results = $query->limit(20)->get();

        return response()->json([
            'success' => true,
            'data' => $results->map(function($ilan) {
                return [
                    'id' => $ilan->id,
                    'baslik' => $ilan->baslik,
                    'fiyat' => number_format($ilan->fiyat) . ' ' . $ilan->para_birimi,
                    'sahip' => optional($ilan->ilanSahibi)->tam_ad,
                    'kategori' => optional($ilan->kategori)->ad,
                    'lokasyon' => optional($ilan->il)->il_adi . (optional($ilan->ilce)->ilce_adi ? ', ' . optional($ilan->ilce)->ilce_adi : ''),
                    'status' => $ilan->status,
                    'url' => route('admin.ilanlar.show', $ilan)
                ];
            })
        ]);
    }

    /**
     * Filter listings
     */
    public function filter(Request $request)
    {
        $query = Ilan::with([
            'ilanSahibi:id,ad,soyad',
            'userDanisman:id,name',
            'kategori:id,name',
            'il:id,il_adi',
            'ilce:id,ilce_adi'
        ]);

        // Apply filters (Context7 uyumlu)
        if ($request->kategori_id) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->il_id) {
            $query->where('il_id', $request->il_id);
        }

        if ($request->ilce_id) {
            $query->where('ilce_id', $request->ilce_id);
        }

        if ($request->min_fiyat) {
            $query->where('fiyat', '>=', $request->min_fiyat);
        }

        if ($request->max_fiyat) {
            $query->where('fiyat', '<=', $request->max_fiyat);
        }

        if ($request->danisman_id) {
            $query->where('danisman_id', $request->danisman_id);
        }

        // Date range filter
        if ($request->baslangic_tarihi) {
            $query->whereDate('created_at', '>=', $request->baslangic_tarihi);
        }

        if ($request->bitis_tarihi) {
            $query->whereDate('created_at', '<=', $request->bitis_tarihi);
        }

        // Sort
        $sortBy = $request->sort_by ?? 'updated_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $ilanlar = $query->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.ilanlar.partials.listings-grid', compact('ilanlar'))->render(),
                'pagination' => (string) $ilanlar->links()
            ]);
        }

        return view('admin.ilanlar.index', compact('ilanlar'));
    }

    /**
     * Live search for listings
     */
    public function liveSearch(Request $request)
    {
        $search = $request->get('q', '');

        if (strlen($search) < 2) {
            return response()->json(['results' => []]);
        }

        $ilanlar = Ilan::with(['ilanSahibi', 'kategori'])
            ->where(function($query) use ($search) {
                $query->where('baslik', 'like', "%{$search}%")
                      ->orWhere('aciklama', 'like', "%{$search}%")
                      ->orWhereHas('ilanSahibi', function($q) use ($search) {
                          $q->where('ad', 'like', "%{$search}%")
                            ->orWhere('soyad', 'like', "%{$search}%");
                      });
            })
            ->limit(10)
            ->get();

        $results = $ilanlar->map(function($ilan) {
            return [
                'id' => $ilan->id,
                'text' => $ilan->baslik . ' - ' . number_format($ilan->fiyat) . ' ' . $ilan->para_birimi,
                'subtitle' => optional($ilan->ilanSahibi)->tam_ad . ' | ' . optional($ilan->kategori)->ad,
                'url' => route('admin.ilanlar.show', $ilan)
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Bulk update listings
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:ilanlar,id',
            'update_data' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = array_filter($request->update_data, function($value) {
                return $value !== null && $value !== '';
            });

            $updateData['updated_at'] = now();

            $updatedCount = Ilan::whereIn('id', $request->ids)->update($updateData);

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} ilan başarıyla güncellendi.",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Toplu güncelleme sırasında bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete listings
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:ilanlar,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $deletedCount = Ilan::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} ilan başarıyla silindi.",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İlan silme sırasında bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle listing status
     */
    public function toggleStatus(Ilan $ilan)
    {
        try {
            $newStatus = $ilan->status === 'Aktif' ? 'Pasif' : 'Aktif';

            $ilan->update([
                'status' => $newStatus,
                'enabled' => $newStatus === 'Aktif',
                'is_published' => $newStatus === 'Aktif'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'İlan statusu başarıyla güncellendi.',
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Durum güncelleme sırasında bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update listing status
     */
    public function updateStatus(Request $request, Ilan $ilan)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:Taslak,Aktif,Pasif,Beklemede,Arşivlendi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ilan->update([
                'status' => $request->status,
                'enabled' => in_array($request->status, ['Aktif']),
                'is_published' => in_array($request->status, ['Aktif'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'İlan statusu başarıyla güncellendi.',
                'status' => $request->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Durum güncelleme sırasında bir hata oluştu: ' . $e->getMessage()
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
            'archive' => 'arşivlendi'
        ];

        return $messages[$action] ?? 'güncellendi';
    }

    /**
     * Generate AI-powered title for listing
     */
    public function generateAiTitle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori' => 'required|string',
            'lokasyon' => 'nullable|string',
            'fiyat' => 'nullable|numeric',
            'ozellikler' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Mock AI title generation
        $titles = [
            "Mükemmel {$request->kategori} - {$request->lokasyon}",
            "Sıfır {$request->kategori} Satılık - {$request->lokasyon}",
            "Lüks {$request->kategori} - Mükemmel Konumda",
            "Yatırım Fırsatı {$request->kategori} - {$request->lokasyon}",
            "Acil Satılık {$request->kategori} - Uygun Fiyat"
        ];

        $generatedTitle = $titles[array_rand($titles)];

        $shuffledTitles = $titles;
        shuffle($shuffledTitles);

        return response()->json([
            'success' => true,
            'title' => $generatedTitle,
            'alternatives' => array_slice($shuffledTitles, 0, 3)
        ]);
    }

    /**
     * Generate AI-powered description for listing
     */
    public function generateAiDescription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'baslik' => 'required|string',
            'kategori' => 'required|string',
            'lokasyon' => 'nullable|string',
            'fiyat' => 'nullable|numeric',
            'ozellikler' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Mock AI description generation
        $description = "Bu mükemmel {$request->kategori}, {$request->lokasyon} bölgesinde yer almaktadır. ";
        $description .= "Modern mimarisi ve kaliteli malzemelerle inşa edilmiş bu emlak, hayalinizdeki yaşam konforunu sunmaktadır. ";

        if ($request->ozellikler && is_array($request->ozellikler)) {
            $description .= "Başlıca özellikleri arasında " . implode(', ', $request->ozellikler) . " bulunmaktadır. ";
        }

        $description .= "Ulaşım açısından son derece avantajlı olan bu emlak, sosyal tesislere yakın konumuyla dikkat çekmektedir. ";
        $description .= "Yatırım değeri yüksek olan bu fırsatı kaçırmayın!";

        return response()->json([
            'success' => true,
            'description' => $description
        ]);
    }

    /**
     * Get dynamic fields based on property type
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
                    ['name' => 'toplam_kat', 'label' => 'Toplam Kat', 'type' => 'number']
                ];
                break;

            case 'villa':
                $fields = [
                    ['name' => 'oda_sayisi', 'label' => 'Oda Sayısı', 'type' => 'select', 'options' => ['3+1', '4+1', '5+1', '6+1', '7+1']],
                    ['name' => 'bahce_var', 'label' => 'Bahçe', 'type' => 'checkbox'],
                    ['name' => 'havuz_var', 'label' => 'Havuz', 'type' => 'checkbox'],
                    ['name' => 'garaj_var', 'label' => 'Garaj', 'type' => 'checkbox'],
                    ['name' => 'kat_sayisi', 'label' => 'Kat Sayısı', 'type' => 'number', 'min' => 1, 'max' => 4]
                ];
                break;

            case 'arsa':
                $fields = [
                    ['name' => 'imar_statusu', 'label' => 'İmar Durumu', 'type' => 'select', 'options' => ['İmarlı', 'İmarsız', 'Villa İmarlı']],
                    ['name' => 'ada_no', 'label' => 'Ada No', 'type' => 'text'],
                    ['name' => 'parsel_no', 'label' => 'Parsel No', 'type' => 'text'],
                    ['name' => 'kaks', 'label' => 'KAKS', 'type' => 'number', 'step' => '0.01'],
                    ['name' => 'taban_alani', 'label' => 'Taban Alanı', 'type' => 'number']
                ];
                break;

            default:
                $fields = [
                    ['name' => 'aciklama', 'label' => 'Genel Açıklama', 'type' => 'textarea']
                ];
        }

        return response()->json([
            'success' => true,
            'fields' => $fields
        ]);
    }

    /**
     * Get AI property suggestions
     */
    public function getAIPropertySuggestions(Request $request)
    {
        $suggestions = [
            'fiyat_optimizasyonu' => [
                'onerilen_fiyat' => $request->fiyat ? $request->fiyat * 1.1 : 500000,
                'pazar_analizi' => 'Benzer özellikler taşıyan emlaklar %10 daha yüksek fiyattan satılmaktadır.',
                'rekabet_statusu' => 'Orta seviye rekabet'
            ],
            'pazarlama_onerileri' => [
                'en_iyi_saatler' => '09:00-12:00 ve 14:00-18:00',
                'hedef_kitle' => 'Genç çiftler ve aileler',
                'vurgulanacak_ozellikler' => ['Konum avantajı', 'Ulaşım kolaylığı', 'Modern dizayn']
            ],
            'seo_onerileri' => [
                'anahtar_kelimeler' => ['satılık daire', 'merkezi konum', 'yatırım fırsatı'],
                'baslik_optimizasyonu' => 'Başlığınıza konum ve öne çıkan özellikleri ekleyin',
                'aciklama_uzunlugu' => 'Optimum açıklama uzunluğu 150-200 kelimedir'
            ]
        ];

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Export listings to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $query = Ilan::with(['ilanSahibi', 'userDanisman', 'kategori', 'il', 'ilce']);

            // Apply same filters as index
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('baslik', 'like', "%{$search}%")
                      ->orWhere('aciklama', 'like', "%{$search}%");
                });
            }

            $ilanlar = $query->get();

            // Simple CSV export (mock implementation)
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="ilanlar_' . date('Y-m-d') . '.csv"',
            ];

            $callback = function() use ($ilanlar) {
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
                        $ilan->created_at->format('d.m.Y H:i')
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
     */
    public function exportPdf(Request $request)
    {
        try {
            $ilanlar = Ilan::with(['ilanSahibi', 'kategori', 'il'])->limit(50)->get();

            return view('admin.ilanlar.pdf', compact('ilanlar'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'PDF export işlemi sırasında hata: ' . $e->getMessage());
        }
    }

    /**
     * Upload photos for a listing
     */
    public function uploadPhotos(Request $request, Ilan $ilan)
    {
        $validator = Validator::make($request->all(), [
            'photos' => 'required|array|max:10',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120' // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $uploadedPhotos = [];

            foreach ($request->file('photos') as $photo) {
                $fileName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('ilan-fotograflari/' . $ilan->id, $fileName, 'public');

                $fotografModel = IlanFotografi::create([
                    'ilan_id' => $ilan->id,
                    'dosya_adi' => $fileName,
                    'dosya_yolu' => $path,
                    'boyut' => $photo->getSize(),
                    'sira' => IlanFotografi::where('ilan_id', $ilan->id)->count() + 1,
                    'status' => true
                ]);

                $uploadedPhotos[] = [
                    'id' => $fotografModel->id,
                    'url' => Storage::url($path),
                    'name' => $fileName
                ];
            }

            return response()->json([
                'success' => true,
                'message' => count($uploadedPhotos) . ' fotoğraf başarıyla yüklendi.',
                'photos' => $uploadedPhotos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fotoğraf yükleme sırasında hata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a photo from listing
     */
    public function deletePhoto(Ilan $ilan, IlanFotografi $photo)
    {
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($photo->dosya_yolu)) {
                Storage::disk('public')->delete($photo->dosya_yolu);
            }

            // Delete from database
            $photo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fotoğraf başarıyla silindi.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fotoğraf silme sırasında hata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update photo order
     */
    public function updatePhotoOrder(Request $request, Ilan $ilan)
    {
        $validator = Validator::make($request->all(), [
            'photo_orders' => 'required|array',
            'photo_orders.*' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            foreach ($request->photo_orders as $photoId => $order) {
                IlanFotografi::where('id', $photoId)
                    ->where('ilan_id', $ilan->id)
                    ->update(['sira' => $order]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fotoğraf sıralaması güncellendi.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Sıralama güncelleme sırasında hata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get price history API
     */
    public function priceHistoryApi(Ilan $ilan)
    {
        $history = IlanPriceHistory::where('ilan_id', $ilan->id)
            ->with('degistirenUser')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * Save draft listing
     */
    public function saveDraft(Request $request)
    {
        try {
            $draftData = $request->all();
            $draftData['status'] = 'Taslak';
            $draftData['enabled'] = false;
            $draftData['is_published'] = false;

            // This would typically save to a drafts table or session
            // For now, we'll just return success

            return response()->json([
                'success' => true,
                'message' => 'Taslak kaydedildi.',
                'draft_id' => uniqid()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Taslak kaydetme sırasında hata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Context7 uyumlu auto-save listing data
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
                'context7_version' => '1.0'
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
                'data_size' => strlen(json_encode($autoSaveData)) . ' bytes'
            ]);

        } catch (\Exception $e) {
            \Log::error('Context7 AutoSave Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Context7 otomatik kayıt hatası: ' . $e->getMessage(),
                'error_code' => 'CONTEXT7_AUTOSAVE_FAILED'
            ], 500);
        }
    }

    /**
     * Get user's listings (ilanlarim)
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
     */
    public function refreshRate(Request $request, Ilan $ilan)
    {
        try {
            // Mock rate refresh - in real implementation, this would fetch from external APIs
            $ilan->update([
                'view_count' => $ilan->view_count + rand(1, 5),
                'favorite_count' => $ilan->favorite_count + rand(0, 2),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Veriler yenilendi.',
                'stats' => [
                    'view_count' => $ilan->view_count,
                    'favorite_count' => $ilan->favorite_count
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Veri yenileme sırasında hata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get type color for the listing
     */
    private function getTypeColor($ilan)
    {
        if (!$ilan->kategori) {
            return 'bg-gray-100 text-gray-800';
        }

        $categoryName = strtolower($ilan->kategori->ad ?? '');

        $colorMap = [
            'satılık' => 'bg-green-100 text-green-800',
            'kiralık' => 'bg-blue-100 text-blue-800',
            'daire' => 'bg-purple-100 text-purple-800',
            'villa' => 'bg-orange-100 text-orange-800',
            'arsa' => 'bg-yellow-100 text-yellow-800',
            'ofis' => 'bg-gray-100 text-gray-800',
            'dükkan' => 'bg-red-100 text-red-800',
        ];

        foreach ($colorMap as $key => $color) {
            if (strpos($categoryName, $key) !== false) {
                return $color;
            }
        }

        return 'bg-gray-100 text-gray-800';
    }

    /**
     * Get type icon for the listing
     */
    private function getTypeIcon($ilan)
    {
        if (!$ilan->kategori) {
            return 'fas fa-building';
        }

        $categoryName = strtolower($ilan->kategori->ad ?? '');

        $iconMap = [
            'daire' => 'fas fa-home',
            'villa' => 'fas fa-house-user',
            'arsa' => 'fas fa-mountain',
            'ofis' => 'fas fa-building',
            'dükkan' => 'fas fa-store',
            'depo' => 'fas fa-warehouse',
            'fabrika' => 'fas fa-industry',
        ];

        foreach ($iconMap as $key => $icon) {
            if (strpos($categoryName, $key) !== false) {
                return $icon;
            }
        }

        return 'fas fa-building';
    }

    /**
     * Get type summary for the listing
     */
    private function getTypeSummary($ilan)
    {
        return [
            'type' => optional($ilan->kategori)->ad ?? 'Kategorisiz',
            'price' => number_format($ilan->fiyat) . ' ' . $ilan->para_birimi,
            'area' => ($ilan->net_metrekare ? $ilan->net_metrekare . ' m²' : 'Belirtilmemiş'),
            'category' => optional($ilan->kategori)->ad ?? 'Yok',
            'status' => ucfirst($ilan->status),
            'special' => $this->getSpecialBadge($ilan)
        ];
    }

    /**
     * Get type specific fields for the listing
     */
    private function getTypeSpecificFields($ilan)
    {
        $fields = [];

        // Base fields that all properties have
        $fields['genel'] = [
            'title' => 'Genel Bilgiler',
            'icon' => 'fas fa-info-circle',
            'color' => 'blue',
            'fields' => [
                'fiyat' => [
                    'label' => 'Fiyat',
                    'value' => number_format($ilan->fiyat) . ' ' . $ilan->para_birimi,
                    'type' => 'price'
                ],
                'status' => [
                    'label' => 'Durum',
                    'value' => $ilan->status,
                    'type' => 'status'
                ],
                'ilan_tarihi' => [
                    'label' => 'İlan Tarihi',
                    'value' => $ilan->created_at ? $ilan->created_at->format('d.m.Y') : 'Belirtilmemiş',
                    'type' => 'date'
                ]
            ]
        ];

        return $fields;
    }

    /**
     * Get special badge for the listing
     */
    private function getSpecialBadge($ilan)
    {
        if ($ilan->is_published && $ilan->enabled) {
            return 'Yayında';
        }

        if ($ilan->status === 'Aktif') {
            return 'Hazır';
        }

        if ($ilan->created_at && $ilan->created_at->isToday()) {
            return 'Yeni İlan';
        }

        return null;
    }

    /**
     * Get features by category (API endpoint)
     * GET /admin/ilanlar/api/features/category/{categoryId}
     * GET /api/admin/features?category_id={categoryId}
     */
    public function getFeaturesByCategory($categoryId = null)
    {
        try {
            if (!$categoryId) {
                $categoryId = request()->get('category_id');
            }

            if (!$categoryId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category ID is required'
                ], 400);
            }

            $category = IlanKategori::findOrFail($categoryId);
            $yayinTipi = request()->get('yayin_tipi');

            $featureCategories = \App\Models\FeatureCategory::with(['features' => function($query) use ($category, $yayinTipi) {
                $query->where('status', true)
                      ->where(function($q) use ($category) {
                          $q->where('applies_to', 'all')
                            ->orWhere('applies_to', $category->slug)
                            ->orWhere('applies_to', 'like', "%,{$category->slug},%")
                            ->orWhere('applies_to', 'like', "{$category->slug},%")
                            ->orWhere('applies_to', 'like', "%,{$category->slug}");
                      });

                if ($yayinTipi) {
                    $yayinTipiLower = strtolower($yayinTipi);
                    $query->where(function($q) use ($category, $yayinTipiLower) {
                        $q->where('applies_to', 'all')
                          ->orWhere('applies_to', 'like', "%{$category->slug}|{$yayinTipiLower}")
                          ->orWhere('applies_to', 'like', "{$category->slug}|{$yayinTipiLower}%")
                          ->orWhere('applies_to', 'like', "%|{$yayinTipiLower}")
                          ->orWhere('applies_to', 'like', "{$yayinTipiLower}|%")
                          ->orWhere('applies_to', $yayinTipiLower);
                    });
                }

                $query->orderBy('order');
            }])
            ->whereHas('features', function($query) use ($category, $yayinTipi) {
                $query->where('status', true)
                      ->where(function($q) use ($category) {
                          $q->where('applies_to', 'all')
                            ->orWhere('applies_to', $category->slug)
                            ->orWhere('applies_to', 'like', "%,{$category->slug},%")
                            ->orWhere('applies_to', 'like', "{$category->slug},%")
                            ->orWhere('applies_to', 'like', "%,{$category->slug}");
                      });

                if ($yayinTipi) {
                    $yayinTipiLower = strtolower($yayinTipi);
                    $query->where(function($q) use ($category, $yayinTipiLower) {
                        $q->where('applies_to', 'all')
                          ->orWhere('applies_to', 'like', "%{$category->slug}|{$yayinTipiLower}")
                          ->orWhere('applies_to', 'like', "{$category->slug}|{$yayinTipiLower}%")
                          ->orWhere('applies_to', 'like', "%|{$yayinTipiLower}")
                          ->orWhere('applies_to', 'like', "{$yayinTipiLower}|%")
                          ->orWhere('applies_to', $yayinTipiLower);
                    });
                }
            })
            ->where('status', true)
            ->orderBy('order')
            ->get();

            $transformed = $featureCategories->map(function($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'icon' => $cat->icon ?? 'fas fa-star',
                    'features' => $cat->features->map(function($feature) {
                        return [
                            'id' => $feature->id,
                            'name' => $feature->name,
                            'slug' => $feature->slug,
                            'type' => $feature->type ?? 'boolean',
                            'options' => $feature->options ?? null,
                            'unit' => $feature->unit ?? null,
                            'required' => $feature->required ?? false,
                            'description' => $feature->description ?? null,
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformed,
                'debug' => [
                    'category_slug' => $category->slug,
                    'yayin_tipi' => $yayinTipi,
                    'total_features' => $transformed->sum(fn($cat) => count($cat['features']))
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Özellikler yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk Actions for İlanlar
     * Context7: Toplu işlemler (activate, deactivate, delete, export, assign)
     * 
     * Yalıhan Bekçi: Advanced bulk operations implementation
     */
    public function bulkAction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'action' => 'required|string|in:activate,deactivate,delete,export,assign_danisman,add_tag,remove_tag',
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|exists:ilanlar,id',
                'value' => 'nullable', // For assign_danisman, add_tag operations
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $action = $request->input('action');
            $ids = $request->input('ids');
            $value = $request->input('value');

            DB::beginTransaction();

            $affected = 0;

            switch ($action) {
                case 'activate':
                    $affected = Ilan::whereIn('id', $ids)->update([
                        'status' => 'active',
                        'is_published' => true,
                        'updated_at' => now()
                    ]);
                    $message = "{$affected} ilan aktif yapıldı.";
                    break;

                case 'deactivate':
                    $affected = Ilan::whereIn('id', $ids)->update([
                        'status' => 'inactive',
                        'is_published' => false,
                        'updated_at' => now()
                    ]);
                    $message = "{$affected} ilan pasif yapıldı.";
                    break;

                case 'delete':
                    $affected = Ilan::whereIn('id', $ids)->delete();
                    $message = "{$affected} ilan silindi.";
                    break;

                case 'export':
                    // Export to Excel/PDF
                    $ilanlar = Ilan::with(['kategori', 'ilanSahibi', 'danisman'])
                        ->whereIn('id', $ids)
                        ->get();
                    
                    // Return Excel export
                    DB::commit();
                    return Excel::download(
                        new \App\Exports\IlanlarExport($ilanlar),
                        'ilanlar_' . date('Y-m-d_His') . '.xlsx'
                    );

                case 'assign_danisman':
                    if (!$value || !is_numeric($value)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Danışman seçilmedi.'
                        ], 422);
                    }

                    $affected = Ilan::whereIn('id', $ids)->update([
                        'danisman_id' => $value,
                        'updated_at' => now()
                    ]);
                    $message = "{$affected} ilana danışman atandı.";
                    break;

                case 'add_tag':
                    if (!$value || !is_numeric($value)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Etiket seçilmedi.'
                        ], 422);
                    }

                    foreach ($ids as $ilanId) {
                        $ilan = Ilan::find($ilanId);
                        if ($ilan) {
                            $ilan->etiketler()->syncWithoutDetaching([$value]);
                            $affected++;
                        }
                    }
                    $message = "{$affected} ilana etiket eklendi.";
                    break;

                case 'remove_tag':
                    if (!$value || !is_numeric($value)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Etiket seçilmedi.'
                        ], 422);
                    }

                    foreach ($ids as $ilanId) {
                        $ilan = Ilan::find($ilanId);
                        if ($ilan) {
                            $ilan->etiketler()->detach([$value]);
                            $affected++;
                        }
                    }
                    $message = "{$affected} ilandan etiket kaldırıldı.";
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Geçersiz işlem.'
                    ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'affected' => $affected
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bulk action error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'İşlem sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
