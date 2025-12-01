<?php

namespace App\Http\Controllers\Admin;

use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;
use App\Models\Ulke;
use App\Services\TurkiyeAPIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AdresYonetimiController extends AdminController
{
    protected TurkiyeAPIService $turkiyeAPI;

    public function __construct(TurkiyeAPIService $turkiyeAPI)
    {
        $this->turkiyeAPI = $turkiyeAPI;
    }

    public function index()
    {
        // ✅ CACHE: Ülkeler ve İller için cache ekle (7200s = 2 saat)
        $ulkeler = Cache::remember('adres_yonetimi_ulkeler', 7200, function () {
            return Ulke::select(['id', 'ulke_adi'])
                ->orderBy('ulke_adi')
                ->get();
        });

        $iller = Cache::remember('adres_yonetimi_iller', 7200, function () {
            return Il::select(['id', 'il_adi'])
                ->orderBy('il_adi')
                ->get();
        });

        return view('admin.adres-yonetimi.index', compact('ulkeler', 'iller'));
    }

    /**
     * Adres öğesi detaylarını göster
     * Context7: Lokasyon sistemi detay sayfası
     */
    public function show($type, $id)
    {
        try {
            switch ($type) {
                case 'ulke':
                    $item = Ulke::findOrFail($id);
                    $relatedData = [
                        'iller_count' => Il::count(), // Context7: ulke_id kolonu olmadığı için tüm illeri say
                        'type' => 'Ülke',
                        'name' => $item->ulke_adi,
                    ];
                    break;

                case 'il':
                    // ✅ N+1 FIX: Select optimization
                    $item = Il::select(['id', 'il_adi'])->findOrFail($id);
                    $relatedData = [
                        'ilceler_count' => Ilce::where('il_id', $id)->count(),
                        'type' => 'İl',
                        'name' => $item->il_adi,
                    ];
                    break;

                case 'ilce':
                    // ✅ N+1 FIX: Eager loading + Select optimization
                    $item = Ilce::select(['id', 'il_id', 'ilce_adi'])
                        ->with('il:id,il_adi')
                        ->findOrFail($id);
                    $relatedData = [
                        'mahalleler_count' => Mahalle::where('ilce_id', $id)->count(),
                        'parent_name' => $item->il->il_adi ?? 'Bilinmiyor',
                        'type' => 'İlçe',
                        'name' => $item->ilce_adi,
                    ];
                    break;

                case 'mahalle':
                    // ✅ N+1 FIX: Eager loading + Select optimization
                    $item = Mahalle::select(['id', 'ilce_id', 'mahalle_adi'])
                        ->with(['ilce:id,il_id,ilce_adi', 'ilce.il:id,il_adi'])
                        ->findOrFail($id);
                    $relatedData = [
                        'parent_name' => $item->ilce->ilce_adi ?? 'Bilinmiyor',
                        'grandparent_name' => $item->ilce->il->il_adi ?? 'Bilinmiyor',
                        'type' => 'Mahalle',
                        'name' => $item->mahalle_adi,
                    ];
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Geçersiz tür',
                    ], 422);
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'item' => $item,
                    'related_data' => $relatedData,
                ]);
            }

            return view('admin.adres-yonetimi.show', compact('item', 'relatedData', 'type'));
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Öğe bulunamadı: '.$e->getMessage(),
                ], 404);
            }

            return redirect()->route('admin.adres-yonetimi.index')
                ->with('error', 'Öğe bulunamadı: '.$e->getMessage());
        }
    }

    /**
     * Yeni adres öğesi oluşturma formu
     * Context7: Lokasyon sistemi yeni öğe ekleme
     */
    public function create($type)
    {
        try {
            $parentOptions = [];

            switch ($type) {
                case 'ulke':
                    // Ülke için parent yok
                    break;

                case 'il':
                    // ✅ CACHE: Ülkeler için cache ekle
                    $parentOptions = Cache::remember('adres_yonetimi_ulkeler', 7200, function () {
                        return Ulke::select(['id', 'ulke_adi'])
                            ->orderBy('ulke_adi')
                            ->get();
                    });
                    break;

                case 'ilce':
                    // ✅ CACHE: İller için cache ekle
                    $parentOptions = Cache::remember('adres_yonetimi_iller', 7200, function () {
                        return Il::select(['id', 'il_adi'])
                            ->orderBy('il_adi')
                            ->get();
                    });
                    break;

                case 'mahalle':
                    // ✅ CACHE: İlçeler için cache ekle (tüm ilçeler)
                    $parentOptions = Cache::remember('adres_yonetimi_all_ilceler', 3600, function () {
                        return Ilce::select(['id', 'il_id', 'ilce_adi'])
                            ->with('il:id,il_adi')
                            ->orderBy('ilce_adi')
                            ->get();
                    });
                    break;

                default:
                    return redirect()->route('admin.adres-yonetimi.index')
                        ->with('error', 'Geçersiz tür');
            }

            return view('admin.adres-yonetimi.create', compact('type', 'parentOptions'));
        } catch (\Exception $e) {
            return redirect()->route('admin.adres-yonetimi.index')
                ->with('error', 'Form yüklenirken hata: '.$e->getMessage());
        }
    }

    /**
     * Adres öğesi düzenleme formu
     * Context7: Lokasyon sistemi öğe düzenleme
     */
    public function edit($type, $id)
    {
        try {
            $parentOptions = [];

            switch ($type) {
                case 'ulke':
                    // ✅ N+1 FIX: Select optimization
                    $item = Ulke::select(['id', 'ulke_adi'])->findOrFail($id);
                    break;

                case 'il':
                    // ✅ N+1 FIX: Select optimization
                    $item = Il::select(['id', 'il_adi'])->findOrFail($id);
                    // ✅ CACHE: Ülkeler için cache ekle
                    $parentOptions = Cache::remember('adres_yonetimi_ulkeler', 7200, function () {
                        return Ulke::select(['id', 'ulke_adi'])
                            ->orderBy('ulke_adi')
                            ->get();
                    });
                    break;

                case 'ilce':
                    // ✅ N+1 FIX: Select optimization
                    $item = Ilce::select(['id', 'il_id', 'ilce_adi'])->findOrFail($id);
                    // ✅ CACHE: İller için cache ekle
                    $parentOptions = Cache::remember('adres_yonetimi_iller', 7200, function () {
                        return Il::select(['id', 'il_adi'])
                            ->orderBy('il_adi')
                            ->get();
                    });
                    break;

                case 'mahalle':
                    // ✅ N+1 FIX: Select optimization
                    $item = Mahalle::select(['id', 'ilce_id', 'mahalle_adi'])->findOrFail($id);
                    // ✅ CACHE: İlçeler için cache ekle
                    $parentOptions = Cache::remember('adres_yonetimi_all_ilceler', 3600, function () {
                        return Ilce::select(['id', 'il_id', 'ilce_adi'])
                            ->with('il:id,il_adi')
                            ->orderBy('ilce_adi')
                            ->get();
                    });
                    break;

                default:
                    return redirect()->route('admin.adres-yonetimi.index')
                        ->with('error', 'Geçersiz tür');
            }

            return view('admin.adres-yonetimi.edit', compact('item', 'type', 'parentOptions'));
        } catch (\Exception $e) {
            return redirect()->route('admin.adres-yonetimi.index')
                ->with('error', 'Öğe bulunamadı: '.$e->getMessage());
        }
    }

    public function getUlkeler()
    {
        // ✅ CACHE: Ülkeler için cache ekle (7200s = 2 saat)
        $ulkeler = Cache::remember('adres_yonetimi_ulkeler', 7200, function () {
            return Ulke::select(['id', 'ulke_adi'])
                ->orderBy('ulke_adi')
                ->get();
        });

        return response()->json(['success' => true, 'ulkeler' => $ulkeler]);
    }

    public function getBolgeler()
    {
        return response()->json(['success' => true, 'bolgeler' => []]);
    }

    public function getIller()
    {
        // ✅ CACHE: İller için cache ekle (7200s = 2 saat)
        $iller = Cache::remember('adres_yonetimi_iller', 7200, function () {
            return Il::select(['id', 'il_adi'])
                ->orderBy('il_adi')
                ->get();
        });

        // Context7: Eğer veritabanında il yoksa, TurkiyeAPI'den otomatik çek
        if ($iller->isEmpty()) {
            try {
                Log::info('TurkiyeAPI: Veritabanında il yok, otomatik sync başlatılıyor...');

                $turkiyeIller = $this->turkiyeAPI->getProvinces();

                if (! empty($turkiyeIller)) {
                    DB::beginTransaction();

                    foreach ($turkiyeIller as $il) {
                        $ilData = [
                            'il_adi' => $il['name'],
                        ];

                        // Context7: plaka_kodu kolonu varsa ekle
                        if (Schema::hasColumn('iller', 'plaka_kodu')) {
                            $plakaKodu = str_pad($il['id'], 2, '0', STR_PAD_LEFT);
                            $ilData['plaka_kodu'] = $plakaKodu;
                        }

                        Il::updateOrCreate(
                            ['id' => $il['id']],
                            $ilData
                        );
                    }

                    DB::commit();

                    // Cache'i temizle ve yeniden yükle
                    Cache::forget('adres_yonetimi_iller');
                    $iller = Il::select(['id', 'il_adi'])
                        ->orderBy('il_adi')
                        ->get();

                    Log::info('TurkiyeAPI: Otomatik sync tamamlandı', ['count' => count($turkiyeIller)]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('TurkiyeAPI: Otomatik sync hatası', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Hata durumunda boş array döndür (kullanıcı manuel sync yapabilir)
            }
        }

        return response()->json(['success' => true, 'iller' => $iller]);
    }

    public function getIllerByUlke($ulkeId)
    {
        // Context7: iller tablosunda ulke_id kolonu yok - tüm illeri döndür
        // TODO: Eğer ulke filtrelemesi gerekiyorsa, migration ile ulke_id kolonu eklenmeli
        $iller = Il::orderBy('il_adi')->get(['id', 'il_adi']);

        return response()->json(['success' => true, 'iller' => $iller]);
    }

    public function getIlceler()
    {
        $ilceler = Ilce::orderBy('ilce_adi')->get(['id', 'il_id', 'ilce_adi']);

        return response()->json(['success' => true, 'ilceler' => $ilceler]);
    }

    public function getIlcelerByIl($ilId)
    {
        // ✅ CACHE: İlçeler için cache ekle (3600s = 1 saat) - İl bazlı cache key
        $ilceler = Cache::remember("adres_yonetimi_ilceler_il_{$ilId}", 3600, function () use ($ilId) {
            return Ilce::select(['id', 'il_id', 'ilce_adi'])
                ->where('il_id', $ilId)
                ->orderBy('ilce_adi')
                ->get();
        });

        return response()->json(['success' => true, 'ilceler' => $ilceler]);
    }

    public function getMahalleler()
    {
        $mahalleler = Mahalle::orderBy('mahalle_adi')->get(['id', 'ilce_id', 'mahalle_adi']);

        return response()->json(['success' => true, 'mahalleler' => $mahalleler]);
    }

    public function getMahallelerByIlce($ilceId)
    {
        // ✅ CACHE: Mahalleler için cache ekle (3600s = 1 saat) - İlçe bazlı cache key
        $mahalleler = Cache::remember("adres_yonetimi_mahalleler_ilce_{$ilceId}", 3600, function () use ($ilceId) {
            return Mahalle::select(['id', 'ilce_id', 'mahalle_adi'])
                ->where('ilce_id', $ilceId)
                ->orderBy('mahalle_adi')
                ->get();
        });

        return response()->json(['success' => true, 'mahalleler' => $mahalleler]);
    }

    public function store(Request $request, $type)
    {
        $name = $request->input('name');
        $parentId = $request->input('parent_id');
        if ($type === 'ulke') {
            $item = Ulke::create(['ulke_adi' => $name]);
            // ✅ CACHE INVALIDATION: Ülkeler cache'ini temizle
            Cache::forget('adres_yonetimi_ulkeler');

            return response()->json(['success' => true, 'item' => $item]);
        }
        if ($type === 'il') {
            // Context7: iller tablosunda ulke_id kolonu yok - sadece il_adi kaydet
            $item = Il::create(['il_adi' => $name]);
            // ✅ CACHE INVALIDATION: İller cache'ini temizle
            Cache::forget('adres_yonetimi_iller');

            return response()->json(['success' => true, 'item' => $item]);
        }
        if ($type === 'ilce') {
            $item = Ilce::create(['il_id' => $parentId, 'ilce_adi' => $name]);
            // ✅ CACHE INVALIDATION: İlçeler cache'lerini temizle
            Cache::forget('adres_yonetimi_all_ilceler');
            Cache::forget("adres_yonetimi_ilceler_il_{$parentId}");

            return response()->json(['success' => true, 'item' => $item]);
        }
        if ($type === 'mahalle') {
            $item = Mahalle::create(['ilce_id' => $parentId, 'mahalle_adi' => $name]);
            // ✅ CACHE INVALIDATION: Mahalleler cache'ini temizle
            Cache::forget("adres_yonetimi_mahalleler_ilce_{$parentId}");

            return response()->json(['success' => true, 'item' => $item]);
        }

        return response()->json(['success' => false, 'message' => 'Geçersiz tür'], 422);
    }

    public function update(Request $request, $type, $id)
    {
        $name = $request->input('name');
        if ($type === 'ulke') {
            $item = Ulke::findOrFail($id);
            $item->update(['ulke_adi' => $name]);
            // ✅ CACHE INVALIDATION: Ülkeler cache'ini temizle
            Cache::forget('adres_yonetimi_ulkeler');

            return response()->json(['success' => true]);
        }
        if ($type === 'il') {
            $item = Il::findOrFail($id);
            $item->update(['il_adi' => $name]);
            // ✅ CACHE INVALIDATION: İller cache'ini temizle
            Cache::forget('adres_yonetimi_iller');

            return response()->json(['success' => true]);
        }
        if ($type === 'ilce') {
            $item = Ilce::findOrFail($id);
            $oldIlId = $item->il_id;
            $item->update(['ilce_adi' => $name]);
            // ✅ CACHE INVALIDATION: İlçeler cache'lerini temizle
            Cache::forget('adres_yonetimi_all_ilceler');
            Cache::forget("adres_yonetimi_ilceler_il_{$oldIlId}");
            if ($item->il_id !== $oldIlId) {
                Cache::forget("adres_yonetimi_ilceler_il_{$item->il_id}");
            }

            return response()->json(['success' => true]);
        }
        if ($type === 'mahalle') {
            $item = Mahalle::findOrFail($id);
            $oldIlceId = $item->ilce_id;
            $item->update(['mahalle_adi' => $name]);
            // ✅ CACHE INVALIDATION: Mahalleler cache'ini temizle
            Cache::forget("adres_yonetimi_mahalleler_ilce_{$oldIlceId}");
            if ($item->ilce_id !== $oldIlceId) {
                Cache::forget("adres_yonetimi_mahalleler_ilce_{$item->ilce_id}");
            }

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Geçersiz tür'], 422);
    }

    public function destroy($type, $id)
    {
        if ($type === 'ulke') {
            Ulke::where('id', $id)->delete();
            // ✅ CACHE INVALIDATION: Ülkeler cache'ini temizle
            Cache::forget('adres_yonetimi_ulkeler');

            return response()->json(['success' => true]);
        }
        if ($type === 'il') {
            Il::where('id', $id)->delete();
            // ✅ CACHE INVALIDATION: İller cache'ini temizle
            Cache::forget('adres_yonetimi_iller');

            return response()->json(['success' => true]);
        }
        if ($type === 'ilce') {
            $ilce = Ilce::find($id);
            $ilId = $ilce?->il_id;
            Ilce::where('id', $id)->delete();
            // ✅ CACHE INVALIDATION: İlçeler cache'lerini temizle
            Cache::forget('adres_yonetimi_all_ilceler');
            if ($ilId) {
                Cache::forget("adres_yonetimi_ilceler_il_{$ilId}");
            }

            return response()->json(['success' => true]);
        }
        if ($type === 'mahalle') {
            $mahalle = Mahalle::find($id);
            $ilceId = $mahalle?->ilce_id;
            Mahalle::where('id', $id)->delete();
            // ✅ CACHE INVALIDATION: Mahalleler cache'ini temizle
            if ($ilceId) {
                Cache::forget("adres_yonetimi_mahalleler_ilce_{$ilceId}");
            }

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Geçersiz tür'], 422);
    }

    /**
     * Bulk delete address items
     * Context7: Toplu silme işlemi
     */
    public function bulkDelete(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:ulke,il,ilce,mahalle',
                'ids' => 'required|array|min:1',
                'ids.*' => 'required|integer',
            ], [
                'type.required' => 'Tip belirtilmelidir',
                'type.in' => 'Geçersiz tip. İzin verilen tipler: ulke, il, ilce, mahalle',
                'ids.required' => 'Silinecek öğe ID\'leri belirtilmelidir',
                'ids.array' => 'ID\'ler bir dizi olmalıdır',
                'ids.min' => 'En az bir öğe seçilmelidir',
                'ids.*.required' => 'Her ID değeri gereklidir',
                'ids.*.integer' => 'Her ID bir tam sayı olmalıdır',
            ]);

            $type = $validated['type'];
            $ids = $validated['ids'];
            $deletedCount = 0;
            $errors = [];

            // ✅ PERFORMANCE FIX: N+1 query önlendi - Bulk delete kullanıldı
            switch ($type) {
                case 'ulke':
                    $deletedCount = Ulke::whereIn('id', $ids)->delete();
                    Cache::forget('adres_yonetimi_ulkeler');
                    break;

                case 'il':
                    $deletedCount = Il::whereIn('id', $ids)->delete();
                    Cache::forget('adres_yonetimi_iller');
                    break;

                case 'ilce':
                    // ✅ PERFORMANCE FIX: N+1 query önlendi - İl ID'leri tek query'de al
                    $ilceler = Ilce::whereIn('id', $ids)->get();
                    $ilIds = $ilceler->pluck('il_id')->unique()->toArray();

                    $deletedCount = Ilce::whereIn('id', $ids)->delete();
                    Cache::forget('adres_yonetimi_all_ilceler');
                    foreach ($ilIds as $ilId) {
                        Cache::forget("adres_yonetimi_ilceler_il_{$ilId}");
                    }
                    break;

                case 'mahalle':
                    // ✅ PERFORMANCE FIX: N+1 query önlendi - İlçe ID'leri tek query'de al
                    $mahalleler = Mahalle::whereIn('id', $ids)->get();
                    $ilceIds = $mahalleler->pluck('ilce_id')->unique()->toArray();

                    $deletedCount = Mahalle::whereIn('id', $ids)->delete();
                    foreach ($ilceIds as $ilceId) {
                        Cache::forget("adres_yonetimi_mahalleler_ilce_{$ilceId}");
                    }
                    break;
            }

            if ($deletedCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "{$deletedCount} öğe başarıyla silindi".(count($errors) > 0 ? '. Bazı öğeler silinemedi.' : ''),
                    'deleted_count' => $deletedCount,
                    'errors' => $errors,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Hiçbir öğe silinemedi. Seçilen ID\'ler veritabanında bulunamadı.',
                    'errors' => $errors,
                ], 422);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation hatası',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Bulk delete hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Toplu silme işlemi sırasında hata: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * TurkiyeAPI'den tüm lokasyon verilerini sync et
     * Context7: Hybrid Approach - TurkiyeAPI sync + Local DB CRUD
     */
    public function syncFromTurkiyeAPI(Request $request)
    {
        try {
            $type = $request->input('type', 'all'); // all, provinces, districts, neighborhoods
            $provinceId = $request->input('province_id');
            $districtId = $request->input('district_id');

            $syncResults = [
                'provinces' => 0,
                'districts' => 0,
                'neighborhoods' => 0,
                'towns' => 0,
                'villages' => 0,
            ];

            DB::beginTransaction();

            // 1. İlleri sync et
            if ($type === 'all' || $type === 'provinces') {
                $iller = $this->turkiyeAPI->getProvinces();

                foreach ($iller as $il) {
                    $ilData = [
                        'il_adi' => $il['name'],
                    ];

                    // Context7: plaka_kodu kolonu zorunlu ve unique
                    // TurkiyeAPI'den gelen id genellikle plaka kodu ile aynı (1-81 arası)
                    if (Schema::hasColumn('iller', 'plaka_kodu')) {
                        // id değerini 2 haneli string formatına çevir (01, 02, ..., 81)
                        $plakaKodu = str_pad($il['id'], 2, '0', STR_PAD_LEFT);
                        $ilData['plaka_kodu'] = $plakaKodu;
                    }

                    Il::updateOrCreate(
                        ['id' => $il['id']],
                        $ilData
                    );
                }

                $syncResults['provinces'] = count($iller);
                Cache::forget('adres_yonetimi_iller');

                Log::info('TurkiyeAPI: İller sync edildi', ['count' => count($iller)]);
            }

            // 2. İlçeleri sync et (tüm iller için veya belirli bir il için)
            if ($type === 'all' || $type === 'districts') {
                $illerToSync = $provinceId
                    ? [['id' => $provinceId]]
                    : ($type === 'all' ? $this->turkiyeAPI->getProvinces() : []);

                foreach ($illerToSync as $il) {
                    $ilceler = $this->turkiyeAPI->getDistricts($il['id']);

                    foreach ($ilceler as $ilce) {
                        $ilceData = [
                            'il_id' => $il['id'],
                            'ilce_adi' => $ilce['name'],
                        ];

                        // Context7: Duplicate önleme - unique constraint ile korumalı
                        try {
                            Ilce::updateOrCreate(
                                [
                                    'il_id' => $il['id'],
                                    'ilce_adi' => $ilce['name'],
                                ],
                                $ilceData
                            );
                        } catch (\Illuminate\Database\QueryException $e) {
                            // Unique constraint hatası - duplicate kayıt zaten var, devam et
                            if ($e->getCode() === '23000') {
                                Log::debug("TurkiyeAPI: Duplicate ilçe atlandı - {$ilce['name']} (İl ID: {$il['id']})");

                                continue;
                            }
                            throw $e;
                        }
                    }

                    $syncResults['districts'] += count($ilceler);
                    Cache::forget("adres_yonetimi_ilceler_il_{$il['id']}");
                }

                Cache::forget('adres_yonetimi_all_ilceler');
                Log::info('TurkiyeAPI: İlçeler sync edildi', ['count' => $syncResults['districts']]);
            }

            // 3. Mahalleleri sync et (tüm ilçeler için veya belirli bir ilçe için)
            if ($type === 'all' || $type === 'neighborhoods') {
                if ($districtId) {
                    // Context7: districtId DB ID'si veya TurkiyeAPI ID'si olabilir
                    // Önce DB'de bu ID ile ilçe var mı kontrol et
                    $dbDistrict = Ilce::find($districtId);
                    $dbDistrictId = $districtId; // Veritabanındaki ilçe ID'si
                    $turkiyeAPIDistrictId = null; // TurkiyeAPI'den mahalle çekmek için kullanılacak ID

                    if ($dbDistrict && $dbDistrict->il_id == $provinceId) {
                        // DB'de ilçe var, şimdi TurkiyeAPI ID'sini bul
                        if ($provinceId) {
                            $turkiyeAPIDistricts = $this->turkiyeAPI->getDistricts($provinceId);
                            // İlçe adına göre TurkiyeAPI ID'sini bul
                            $turkiyeAPIDistrict = collect($turkiyeAPIDistricts)->first(function ($tIlce) use ($dbDistrict) {
                                return mb_strtolower(trim($tIlce['name'])) === mb_strtolower(trim($dbDistrict->ilce_adi));
                            });

                            if ($turkiyeAPIDistrict) {
                                $turkiyeAPIDistrictId = $turkiyeAPIDistrict['id'];
                                Log::info("TurkiyeAPI: İlçe eşleştirildi - DB ID: {$dbDistrictId}, TurkiyeAPI ID: {$turkiyeAPIDistrictId}, İlçe: {$dbDistrict->ilce_adi}");
                            } else {
                                Log::warning("TurkiyeAPI: İlçe TurkiyeAPI'de bulunamadı - DB ID: {$dbDistrictId}, İlçe: {$dbDistrict->ilce_adi}");
                                // TurkiyeAPI'de bulunamadı, districtId'yi kullan (belki zaten TurkiyeAPI ID'si)
                                $turkiyeAPIDistrictId = $districtId;
                            }
                        } else {
                            // provinceId yok, districtId'yi direkt kullan
                            $turkiyeAPIDistrictId = $districtId;
                        }
                    } else {
                        // DB'de yok, muhtemelen TurkiyeAPI ID'si
                        if ($provinceId) {
                            $turkiyeAPIDistricts = $this->turkiyeAPI->getDistricts($provinceId);
                            $turkiyeAPIDistrict = collect($turkiyeAPIDistricts)->firstWhere('id', $districtId);

                            if ($turkiyeAPIDistrict) {
                                // TurkiyeAPI ilçe adına göre veritabanındaki ilçeyi bul
                                $dbDistrict = Ilce::where('il_id', $provinceId)
                                    ->where('ilce_adi', $turkiyeAPIDistrict['name'])
                                    ->first();
                                if ($dbDistrict) {
                                    $dbDistrictId = $dbDistrict->id;
                                    $turkiyeAPIDistrictId = $districtId; // TurkiyeAPI ID'si
                                    Log::info("TurkiyeAPI: İlçe ID eşleştirildi - TurkiyeAPI ID: {$districtId}, DB ID: {$dbDistrictId}, İlçe: {$dbDistrict->ilce_adi}");
                                } else {
                                    Log::warning("TurkiyeAPI: İlçe DB'de bulunamadı - TurkiyeAPI ID: {$districtId}, İlçe Adı: {$turkiyeAPIDistrict['name']}");
                                    // İlçe DB'de yoksa, sync işlemi ilçeyi de oluşturmalı
                                    $dbDistrictId = $districtId; // Geçici olarak TurkiyeAPI ID'sini kullan
                                    $turkiyeAPIDistrictId = $districtId;
                                }
                            } else {
                                // TurkiyeAPI'de de bulunamadı, direkt districtId'yi kullan
                                Log::warning("TurkiyeAPI: İlçe bulunamadı - districtId: {$districtId}");
                                $turkiyeAPIDistrictId = $districtId;
                            }
                        } else {
                            $turkiyeAPIDistrictId = $districtId;
                        }
                    }

                    // Belirli bir ilçe için mahalleleri çek (TurkiyeAPI ID'si ile)
                    if ($turkiyeAPIDistrictId) {
                        $mahalleler = $this->turkiyeAPI->getNeighborhoods($turkiyeAPIDistrictId);
                        Log::info("TurkiyeAPI: İlçe ID {$turkiyeAPIDistrictId} için ".count($mahalleler).' mahalle çekildi');
                    } else {
                        Log::error("TurkiyeAPI: TurkiyeAPI District ID bulunamadı - districtId: {$districtId}");
                        $mahalleler = [];
                    }

                    foreach ($mahalleler as $mahalle) {
                        $mahalleData = [
                            'ilce_id' => $dbDistrictId, // Context7: Veritabanındaki ilçe ID'sini kullan
                            'mahalle_adi' => $mahalle['name'],
                        ];

                        // Context7: mahalle_kodu, posta_kodu, enlem, boylam kolonları varsa ekle
                        if (Schema::hasColumn('mahalleler', 'mahalle_kodu')) {
                            $mahalleData['mahalle_kodu'] = $mahalle['id'] ?? null;
                        }
                        if (Schema::hasColumn('mahalleler', 'posta_kodu')) {
                            $mahalleData['posta_kodu'] = $mahalle['postcode'] ?? null;
                        }
                        if (Schema::hasColumn('mahalleler', 'enlem')) {
                            $mahalleData['enlem'] = $mahalle['latitude'] ?? null;
                        }
                        if (Schema::hasColumn('mahalleler', 'boylam')) {
                            $mahalleData['boylam'] = $mahalle['longitude'] ?? null;
                        }

                        // Context7: Duplicate önleme - unique constraint ile korumalı
                        try {
                            Mahalle::updateOrCreate(
                                [
                                    'ilce_id' => $dbDistrictId, // Context7: Veritabanındaki ilçe ID'sini kullan
                                    'mahalle_adi' => $mahalle['name'],
                                ],
                                $mahalleData
                            );
                        } catch (\Illuminate\Database\QueryException $e) {
                            // Unique constraint hatası - duplicate kayıt zaten var, devam et
                            if ($e->getCode() === '23000') {
                                Log::debug("TurkiyeAPI: Duplicate mahalle atlandı - {$mahalle['name']} (İlçe ID: {$districtId})");

                                continue;
                            }
                            throw $e;
                        }
                    }

                    $syncResults['neighborhoods'] = count($mahalleler);
                    Cache::forget("adres_yonetimi_mahalleler_ilce_{$dbDistrictId}");
                } elseif ($provinceId) {
                    // Sadece seçilen il için mahalleleri sync et
                    Log::info("TurkiyeAPI: İl ID {$provinceId} için mahalleler sync ediliyor");

                    // Seçilen ilin ilçelerini al
                    $ilceler = Ilce::select(['id', 'il_id', 'ilce_adi'])
                        ->where('il_id', $provinceId)
                        ->with('il:id,il_adi')
                        ->get();

                    $totalMahalleler = 0;
                    $processedIlceler = 0;

                    foreach ($ilceler as $ilce) {
                        try {
                            // TurkiyeAPI'den bu ilin ilçelerini çek
                            $turkiyeAPIIlceler = $this->turkiyeAPI->getDistricts($provinceId);

                            // İlçe adına göre eşleştir
                            $turkiyeAPIIlce = collect($turkiyeAPIIlceler)->first(function ($tIlce) use ($ilce) {
                                return mb_strtolower(trim($tIlce['name'])) === mb_strtolower(trim($ilce->ilce_adi));
                            });

                            if (! $turkiyeAPIIlce) {
                                Log::debug("TurkiyeAPI: İlçe '{$ilce->ilce_adi}' (ID: {$ilce->id}) için TurkiyeAPI'de eşleşme bulunamadı");

                                continue;
                            }

                            // TurkiyeAPI ilçe ID'si ile mahalleleri çek
                            $mahalleler = $this->turkiyeAPI->getNeighborhoods($turkiyeAPIIlce['id']);

                            if (empty($mahalleler)) {
                                Log::debug("TurkiyeAPI: İlçe '{$ilce->ilce_adi}' (TurkiyeAPI ID: {$turkiyeAPIIlce['id']}) için mahalle bulunamadı");

                                continue;
                            }

                            foreach ($mahalleler as $mahalle) {
                                $mahalleData = [
                                    'ilce_id' => $ilce->id,
                                    'mahalle_adi' => $mahalle['name'] ?? 'İsimsiz Mahalle',
                                ];

                                // Context7: mahalle_kodu, posta_kodu, enlem, boylam kolonları varsa ekle
                                if (Schema::hasColumn('mahalleler', 'mahalle_kodu')) {
                                    $mahalleData['mahalle_kodu'] = $mahalle['id'] ?? null;
                                }
                                if (Schema::hasColumn('mahalleler', 'posta_kodu')) {
                                    $mahalleData['posta_kodu'] = $mahalle['postcode'] ?? null;
                                }
                                if (Schema::hasColumn('mahalleler', 'enlem')) {
                                    $mahalleData['enlem'] = $mahalle['latitude'] ?? null;
                                }
                                if (Schema::hasColumn('mahalleler', 'boylam')) {
                                    $mahalleData['boylam'] = $mahalle['longitude'] ?? null;
                                }

                                // Context7: Duplicate önleme - unique constraint ile korumalı
                                try {
                                    Mahalle::updateOrCreate(
                                        [
                                            'ilce_id' => $ilce->id,
                                            'mahalle_adi' => $mahalle['name'] ?? 'İsimsiz Mahalle',
                                        ],
                                        $mahalleData
                                    );
                                } catch (\Illuminate\Database\QueryException $e) {
                                    if ($e->getCode() === '23000') {
                                        Log::debug("TurkiyeAPI: Duplicate mahalle atlandı - {$mahalle['name']} (İlçe ID: {$ilce->id})");

                                        continue;
                                    }
                                    throw $e;
                                }

                                $totalMahalleler++;
                            }

                            $processedIlceler++;
                            Cache::forget("adres_yonetimi_mahalleler_ilce_{$ilce->id}");
                        } catch (\Exception $e) {
                            Log::warning("TurkiyeAPI: İlçe {$ilce->id} ({$ilce->ilce_adi}) için mahalle sync hatası", [
                                'error' => $e->getMessage(),
                            ]);

                            continue;
                        }
                    }

                    $syncResults['neighborhoods'] = $totalMahalleler;
                    Log::info("TurkiyeAPI: İl ID {$provinceId} için {$processedIlceler} ilçe, {$totalMahalleler} mahalle sync edildi");
                } else {
                    // Tüm ilçeler için mahalleleri sync et (İLÇE ADI İLE EŞLEŞTİRME)
                    Log::info('TurkiyeAPI: Tüm ilçeler için mahalleler sync ediliyor - bu işlem uzun sürebilir');

                    // Tüm ilçeleri al (il_id ve ilce_adi ile)
                    $allIlceler = Ilce::select(['id', 'il_id', 'ilce_adi'])
                        ->with('il:id,il_adi')
                        ->get();
                    $totalMahalleler = 0;
                    $processedIlceler = 0;
                    // Context7: Tüm ilçeler için sync - limit kaldırıldı
                    // $maxIlceler = 50; // İlk 50 ilçe için test

                    foreach ($allIlceler as $ilce) {
                        try {
                            // İlçe adına göre TurkiyeAPI'den ilçe ID'sini bul
                            $il = $ilce->il;
                            if (! $il) {
                                Log::warning("TurkiyeAPI: İlçe {$ilce->id} için il bulunamadı");

                                continue;
                            }

                            // TurkiyeAPI'den bu ilin ilçelerini çek
                            $turkiyeAPIIlceler = $this->turkiyeAPI->getDistricts($il->id);

                            // İlçe adına göre eşleştir
                            $turkiyeAPIIlce = collect($turkiyeAPIIlceler)->first(function ($tIlce) use ($ilce) {
                                return mb_strtolower(trim($tIlce['name'])) === mb_strtolower(trim($ilce->ilce_adi));
                            });

                            if (! $turkiyeAPIIlce) {
                                Log::debug("TurkiyeAPI: İlçe '{$ilce->ilce_adi}' (ID: {$ilce->id}) için TurkiyeAPI'de eşleşme bulunamadı");

                                continue;
                            }

                            // TurkiyeAPI ilçe ID'si ile mahalleleri çek
                            $mahalleler = $this->turkiyeAPI->getNeighborhoods($turkiyeAPIIlce['id']);

                            if (empty($mahalleler)) {
                                Log::debug("TurkiyeAPI: İlçe '{$ilce->ilce_adi}' (TurkiyeAPI ID: {$turkiyeAPIIlce['id']}) için mahalle bulunamadı");

                                continue;
                            }

                            foreach ($mahalleler as $mahalle) {
                                $mahalleData = [
                                    'ilce_id' => $ilce->id, // Veritabanındaki ilçe ID'si
                                    'mahalle_adi' => $mahalle['name'] ?? 'İsimsiz Mahalle',
                                ];

                                // Context7: mahalle_kodu, posta_kodu, enlem, boylam kolonları varsa ekle
                                if (Schema::hasColumn('mahalleler', 'mahalle_kodu')) {
                                    $mahalleData['mahalle_kodu'] = $mahalle['id'] ?? null;
                                }
                                if (Schema::hasColumn('mahalleler', 'posta_kodu')) {
                                    $mahalleData['posta_kodu'] = $mahalle['postcode'] ?? null;
                                }
                                if (Schema::hasColumn('mahalleler', 'enlem')) {
                                    $mahalleData['enlem'] = $mahalle['latitude'] ?? null;
                                }
                                if (Schema::hasColumn('mahalleler', 'boylam')) {
                                    $mahalleData['boylam'] = $mahalle['longitude'] ?? null;
                                }

                                // TurkiyeAPI ID'sini kullanarak kaydet
                                // Context7: Duplicate önleme - unique constraint ile korumalı
                                try {
                                    Mahalle::updateOrCreate(
                                        [
                                            'ilce_id' => $ilce->id,
                                            'mahalle_adi' => $mahalle['name'] ?? 'İsimsiz Mahalle',
                                        ],
                                        $mahalleData
                                    );
                                } catch (\Illuminate\Database\QueryException $e) {
                                    // Unique constraint hatası - duplicate kayıt zaten var, devam et
                                    if ($e->getCode() === '23000') {
                                        Log::debug("TurkiyeAPI: Duplicate mahalle atlandı - {$mahalle['name']} (İlçe ID: {$ilce->id})");

                                        continue;
                                    }
                                    throw $e;
                                }

                                $totalMahalleler++;
                            }

                            $processedIlceler++;

                            // Her ilçe için cache temizle
                            Cache::forget("adres_yonetimi_mahalleler_ilce_{$ilce->id}");

                            // Her 10 ilçede bir log
                            if ($processedIlceler % 10 === 0) {
                                Log::info("TurkiyeAPI: {$processedIlceler} ilçe işlendi, {$totalMahalleler} mahalle sync edildi");
                            }
                        } catch (\Exception $e) {
                            Log::warning("TurkiyeAPI: İlçe {$ilce->id} ({$ilce->ilce_adi}) için mahalle sync hatası", [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                            ]);

                            // Hata olsa bile devam et
                            continue;
                        }
                    }

                    $syncResults['neighborhoods'] = $totalMahalleler;
                    Log::info('TurkiyeAPI: Mahalleler sync edildi', [
                        'processed_ilceler' => $processedIlceler,
                        'total_ilceler' => count($allIlceler),
                        'total_mahalleler' => $totalMahalleler,
                        'note' => 'Tüm ilçeler sync edildi.',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'TurkiyeAPI\'den veri sync edildi',
                'results' => $syncResults,
                'source' => 'turkiyeapi',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('TurkiyeAPI sync hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sync hatası: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * TurkiyeAPI'den belirli bir ilin ilçelerini getir
     * Context7: Harita sistemi için lokasyon verileri
     */
    public function getIlcelerByIlFromTurkiyeAPI($ilId)
    {
        try {
            $ilceler = $this->turkiyeAPI->getDistricts($ilId);

            return response()->json([
                'success' => true,
                'ilceler' => $ilceler,
                'source' => 'turkiyeapi',
                'count' => count($ilceler),
            ]);
        } catch (\Exception $e) {
            Log::error('TurkiyeAPI ilçe getirme hatası', [
                'il_id' => $ilId,
                'error' => $e->getMessage(),
            ]);

            // Fallback: Local DB'den çek
            $ilceler = Ilce::where('il_id', $ilId)->get();

            return response()->json([
                'success' => true,
                'ilceler' => $ilceler,
                'source' => 'local_db',
                'count' => count($ilceler),
                'warning' => 'TurkiyeAPI kullanılamadı, local DB kullanıldı',
            ]);
        }
    }

    /**
     * TurkiyeAPI'den belirli bir ilçenin tüm lokasyon tiplerini getir
     * Context7: Mahalle + Belde + Köy birlikte
     */
    public function getAllLocationTypesFromTurkiyeAPI($ilceId)
    {
        try {
            $allLocations = $this->turkiyeAPI->getAllLocations($ilceId);

            return response()->json([
                'success' => true,
                'data' => $allLocations,
                'source' => 'turkiyeapi',
                'counts' => [
                    'neighborhoods' => count($allLocations['neighborhoods'] ?? []),
                    'towns' => count($allLocations['towns'] ?? []),
                    'villages' => count($allLocations['villages'] ?? []),
                    'total' => count($allLocations['neighborhoods'] ?? []) +
                        count($allLocations['towns'] ?? []) +
                        count($allLocations['villages'] ?? []),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('TurkiyeAPI lokasyon tipleri getirme hatası', [
                'ilce_id' => $ilceId,
                'error' => $e->getMessage(),
            ]);

            // Fallback: Local DB'den sadece mahalleleri çek
            $mahalleler = Mahalle::where('ilce_id', $ilceId)->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'neighborhoods' => $mahalleler->map(function ($m) {
                        return [
                            'id' => $m->id,
                            'name' => $m->mahalle_adi,
                            'type' => 'mahalle',
                            'type_label' => 'Mahalle',
                            'icon' => '📍',
                        ];
                    })->toArray(),
                    'towns' => [],
                    'villages' => [],
                ],
                'source' => 'local_db',
                'warning' => 'TurkiyeAPI kullanılamadı, sadece mahalleler gösteriliyor',
            ]);
        }
    }

    /**
     * TurkiyeAPI'den seçili il/ilçe/mahalleleri çek (sync etmeden sadece göster)
     * Context7: Seçimli veri çekme - Kullanıcı istediği lokasyonları seçerek çekebilir
     */
    public function fetchFromTurkiyeAPI(Request $request)
    {
        try {
            $provinceId = $request->input('province_id');
            $districtId = $request->input('district_id');
            $fetchType = $request->input('type', 'auto'); // auto, districts, neighborhoods

            $results = [
                'provinces' => [],
                'districts' => [],
                'neighborhoods' => [],
                'towns' => [],
                'villages' => [],
            ];

            // 1. İl seçildiyse ilçeleri çek
            if ($provinceId && ($fetchType === 'auto' || $fetchType === 'districts')) {
                $ilceler = $this->turkiyeAPI->getDistricts($provinceId);
                $results['districts'] = $ilceler;
                Log::info("TurkiyeAPI: İl ID {$provinceId} için ".count($ilceler).' ilçe çekildi');

                // Context7: İlçeler içinde mahalleler varsa onları da çıkar
                // TurkiyeAPI bazen ilçeleri mahalleleriyle birlikte döndürüyor
                foreach ($ilceler as $ilce) {
                    if (isset($ilce['neighborhoods']) && is_array($ilce['neighborhoods'])) {
                        foreach ($ilce['neighborhoods'] as $mahalle) {
                            $results['neighborhoods'][] = [
                                'id' => $mahalle['id'] ?? null,
                                'name' => $mahalle['name'] ?? '',
                                'districtId' => $ilce['id'] ?? null,
                                'population' => $mahalle['population'] ?? null,
                            ];
                        }
                    }
                }

                // Context7: İl seçildiyse ve ilçe seçilmemişse, tüm ilçelerin mahallelerini de çek (opsiyonel)
                // Bu çok fazla veri olabilir, bu yüzden sadece ilk 5 ilçe için yapıyoruz
                if (! $districtId && $fetchType === 'auto' && empty($results['neighborhoods'])) {
                    $firstDistricts = array_slice($ilceler, 0, 5); // İlk 5 ilçe
                    foreach ($firstDistricts as $ilce) {
                        try {
                            $allLocations = $this->turkiyeAPI->getAllLocations($ilce['id']);
                            $results['neighborhoods'] = array_merge($results['neighborhoods'] ?? [], $allLocations['neighborhoods'] ?? []);
                            $results['towns'] = array_merge($results['towns'] ?? [], $allLocations['towns'] ?? []);
                            $results['villages'] = array_merge($results['villages'] ?? [], $allLocations['villages'] ?? []);
                        } catch (\Exception $e) {
                            Log::warning("TurkiyeAPI: İlçe ID {$ilce['id']} için mahalle çekilemedi", ['error' => $e->getMessage()]);
                        }
                    }
                    Log::info("TurkiyeAPI: İl ID {$provinceId} için ilk 5 ilçenin mahalleleri çekildi");
                }
            }

            // 2. İlçe seçildiyse mahalleleri çek
            if ($districtId && ($fetchType === 'auto' || $fetchType === 'neighborhoods')) {
                $allLocations = $this->turkiyeAPI->getAllLocations($districtId);
                $results['neighborhoods'] = array_merge($results['neighborhoods'] ?? [], $allLocations['neighborhoods'] ?? []);
                $results['towns'] = array_merge($results['towns'] ?? [], $allLocations['towns'] ?? []);
                $results['villages'] = array_merge($results['villages'] ?? [], $allLocations['villages'] ?? []);
                Log::info("TurkiyeAPI: İlçe ID {$districtId} için ".
                    (count($allLocations['neighborhoods'] ?? []) + count($allLocations['towns'] ?? []) + count($allLocations['villages'] ?? [])).
                    ' lokasyon çekildi');
            }

            // Debug: Log results
            Log::info('TurkiyeAPI fetch results', [
                'province_id' => $provinceId,
                'district_id' => $districtId,
                'fetch_type' => $fetchType,
                'districts_count' => count($results['districts']),
                'neighborhoods_count' => count($results['neighborhoods']),
                'towns_count' => count($results['towns']),
                'villages_count' => count($results['villages']),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'TurkiyeAPI\'den veriler başarıyla çekildi',
                'data' => $results,
                'counts' => [
                    'districts' => count($results['districts']),
                    'neighborhoods' => count($results['neighborhoods']),
                    'towns' => count($results['towns']),
                    'villages' => count($results['villages']),
                    'total' => count($results['districts']) +
                        count($results['neighborhoods']) +
                        count($results['towns']) +
                        count($results['villages']),
                ],
                'source' => 'turkiyeapi',
                'debug' => [
                    'province_id' => $provinceId,
                    'district_id' => $districtId,
                    'fetch_type' => $fetchType,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('TurkiyeAPI fetch hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Veri çekme hatası: '.$e->getMessage(),
            ], 500);
        }
    }
}
