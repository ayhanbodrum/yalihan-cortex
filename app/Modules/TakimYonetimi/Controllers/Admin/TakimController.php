<?php

namespace App\Modules\TakimYonetimi\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\TakimYonetimi\Models\Gorev;
use App\Modules\TakimYonetimi\Models\TakimUyesi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class TakimController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Geçici olarak role middleware'i kaldırıldı
        // $this->middleware('role:admin|super_admin');
    }

    /**
     * Takım listesi
     */
    public function index(Request $request): View
    {
        $query = TakimUyesi::with(['user']);

        // Filtreleme
        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('lokasyon')) {
            $query->where('lokasyon', 'like', "%{$request->lokasyon}%");
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'performans_skoru');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $takimUyeleri = $query->paginate(20);
        $roller = TakimUyesi::getRoller();
        $statuslar = TakimUyesi::getDurumlar();

        // İstatistikler
        $istatistikler = [
            'toplam_uye' => $takimUyeleri->total(),
            'status_uye' => TakimUyesi::where('status', 'active')->count(),
            'toplam_gorev' => \App\Modules\TakimYonetimi\Models\Gorev::count(),
            'ortalama_performans' => TakimUyesi::avg('performans_skoru') ?? 0,
        ];

        // Kullanıcılar (takımda olmayan)
        $kullanicilar = \App\Models\User::whereDoesntHave('takimUyesi')->get();

        // Lokasyonlar
        $lokasyonlar = TakimUyesi::distinct()->pluck('lokasyon')->filter();

        return view('admin.takim-yonetimi.takim.index', compact(
            'takimUyeleri',
            'roller',
            'statuslar',
            'istatistikler',
            'kullanicilar',
            'lokasyonlar'
        ));
    }

    /**
     * Takım üyesi detayı
     */
    public function show(int $takimId): View
    {
        $takimUyesi = TakimUyesi::with(['user', 'gorevler', 'gorevTakip'])->findOrFail($takimId);

        // Son 30 günlük performans
        $son30Gun = now()->subDays(30);
        $gorevler = $takimUyesi->gorevler()
            ->where('created_at', '>=', $son30Gun)
            ->get();

        $performans = [
            'toplam_gorev' => $gorevler->count(),
            'tamamlanan_gorev' => $gorevler->where('status', 'tamamlandi')->count(),
            'devam_eden_gorev' => $gorevler->where('status', 'devam_ediyor')->count(),
            'geciken_gorev' => $gorevler->where('status', '!=', 'tamamlandi')
                ->filter(function ($gorev) {
                    return $gorev->geciktiMi();
                })->count(),
            'basari_orani' => $gorevler->count() > 0 ?
                round(($gorevler->where('status', 'tamamlandi')->count() / $gorevler->count()) * 100, 2) : 0,
        ];

        // Aktif görevler
        $statusGorevler = $takimUyesi->gorevler()
            ->whereIn('status', ['bekliyor', 'devam_ediyor'])
            ->orderBy('deadline', 'asc')
            ->limit(10)
            ->get();

        // Son tamamlanan görevler
        $tamamlananGorevler = $takimUyesi->gorevler()
            ->where('status', 'tamamlandi')
            ->with('gorevTakip')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Görev takip geçmişi
        $gorevTakipGecmisi = $takimUyesi->gorevTakip()
            ->with('gorev')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // İstatistikler
        $istatistikler = [
            'bu_ay_tamamlanan' => $takimUyesi->gorevler()
                ->where('status', 'tamamlandi')
                ->whereYear('updated_at', now()->year)
                ->whereMonth('updated_at', now()->month)
                ->count(),
            'toplam_calisma_saati' => $takimUyesi->gorevTakip()
                ->where('status', 'tamamlandi')
                ->sum('harcanan_sure') / 60 ?? 0,
        ];

        return view('admin.takim-yonetimi.takim.show', compact(
            'takimUyesi',
            'performans',
            'statusGorevler',
            'tamamlananGorevler',
            'gorevTakipGecmisi',
            'istatistikler'
        ));
    }

    /**
     * Takım üyesi ekleme
     */
    public function uyeEkle(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|unique:takim_uyeleri,user_id',
            'rol' => 'required|in:'.implode(',', TakimUyesi::getRoller()),
            'uzmanlik_alani' => 'nullable|array',
            'uzmanlik_alani.*' => 'string|max:100',
            'calisma_saati' => 'nullable|array',
            'lokasyon' => 'nullable|string|max:255',
            'status' => 'required|in:'.implode(',', TakimUyesi::getDurumlar()),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $takimUyesi = TakimUyesi::create($request->all());

            // Kullanıcı rolünü güncelle
            $user = User::find($request->user_id);
            if ($user) {
                $user->update(['role' => $request->rol]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Takım üyesi başarıyla eklendi!',
                'data' => $takimUyesi->load('user'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Takım üyesi ekleme hatası: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Takım üyesi eklenirken hata oluştu: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Takım üyesi çıkarma
     */
    public function uyeCikar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'takim_uye_id' => 'required|exists:takim_uyeleri,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $takimUyesi = TakimUyesi::findOrFail($request->takim_uye_id);

            // Aktif görevleri kontrol et
            $statusGorevler = $takimUyesi->gorevler()
                ->whereIn('status', ['bekliyor', 'devam_ediyor'])
                ->count();

            if ($statusGorevler > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Bu üyenin {$statusGorevler} status görevi bulunmaktadır. Önce görevleri tamamlayın veya başka birine atayın.",
                ], 400);
            }

            // Kullanıcı rolünü güncelle
            $user = $takimUyesi->user;
            if ($user) {
                $user->update(['role' => 'user']);
            }

            $takimUyesi->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Takım üyesi başarıyla çıkarıldı!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Takım üyesi çıkarma hatası: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Takım üyesi çıkarılırken hata oluştu: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Takım performansı
     */
    public function performans(): View
    {
        $takimUyeleri = TakimUyesi::with(['user'])
            ->orderBy('performans_skoru', 'desc')
            ->get();

        $performansIstatistikleri = [
            'toplam_uye' => $takimUyeleri->count(),
            'status_uye' => $takimUyeleri->where('status', 'active')->count(),
            'ortalama_performans' => $takimUyeleri->avg('performans_skoru'),
            'en_yuksek_performans' => $takimUyeleri->max('performans_skoru'),
            'en_dusuk_performans' => $takimUyeleri->min('performans_skoru'),
        ];

        $rolBazliPerformans = $takimUyeleri->groupBy('rol')->map(function ($uyeler) {
            return [
                'toplam' => $uyeler->count(),
                'ortalama_performans' => $uyeler->avg('performans_skoru'),
                'toplam_gorev' => $uyeler->sum('toplam_gorev'),
                'basarili_gorev' => $uyeler->sum('basarili_gorev'),
            ];
        });

        return view('admin.takim-yonetimi.takim.performans', compact(
            'takimUyeleri',
            'performansIstatistikleri',
            'rolBazliPerformans'
        ));
    }

    /**
     * Danışman performansı
     */
    public function danismanPerformans(int $danismanId): View
    {
        $takimUyesi = TakimUyesi::with(['user'])->findOrFail($danismanId);

        // Son 6 ayın performans verileri
        $aylar = collect();
        for ($i = 5; $i >= 0; $i--) {
            $ay = now()->subMonths($i);
            $aylar->push([
                'ay' => $ay->format('F Y'),
                'tarih' => $ay->format('Y-m'),
                'gorev_sayisi' => $takimUyesi->gorevler()
                    ->whereYear('created_at', $ay->year)
                    ->whereMonth('created_at', $ay->month)
                    ->count(),
                'tamamlanan_gorev' => $takimUyesi->gorevler()
                    ->whereYear('updated_at', $ay->year)
                    ->whereMonth('updated_at', $ay->month)
                    ->where('status', 'tamamlandi')
                    ->count(),
            ]);
        }

        // Günlük performans (son 30 gün)
        $gunlukPerformans = collect();
        for ($i = 29; $i >= 0; $i--) {
            $gun = now()->subDays($i);
            $gunlukPerformans->push([
                'gun' => $gun->format('d/m'),
                'tarih' => $gun->format('Y-m-d'),
                'yeni_gorev' => $takimUyesi->gorevler()
                    ->whereDate('created_at', $gun)
                    ->count(),
                'tamamlanan_gorev' => $takimUyesi->gorevler()
                    ->whereDate('updated_at', $gun)
                    ->where('status', 'tamamlandi')
                    ->count(),
            ]);
        }

        return view('admin.takim-yonetimi.takim.danisman-performans', compact(
            'takimUyesi',
            'aylar',
            'gunlukPerformans'
        ));
    }

    /**
     * Takım performansı
     */
    public function takimPerformans(): View
    {
        // Takım genel performansı
        $genelPerformans = [
            'toplam_uye' => TakimUyesi::count(),
            'status_uye' => TakimUyesi::where('status', 'active')->count(),
            'toplam_gorev' => Gorev::count(),
            'tamamlanan_gorev' => Gorev::where('status', 'tamamlandi')->count(),
            'devam_eden_gorev' => Gorev::where('status', 'devam_ediyor')->count(),
            'geciken_gorev' => Gorev::where('deadline', '<', now())
                ->where('status', '!=', 'tamamlandi')
                ->count(),
        ];

        // Rol bazlı dağılım
        $rolDagilimi = TakimUyesi::selectRaw('rol, COUNT(*) as sayi')
            ->groupBy('rol')
            ->get()
            ->pluck('sayi', 'rol');

        // Performans dağılımı
        $performansDagilimi = [
            'mukemmel' => TakimUyesi::where('performans_skoru', '>=', 8.5)->count(),
            'cok_iyi' => TakimUyesi::whereBetween('performans_skoru', [7.0, 8.4])->count(),
            'iyi' => TakimUyesi::whereBetween('performans_skoru', [5.5, 6.9])->count(),
            'orta' => TakimUyesi::whereBetween('performans_skoru', [4.0, 5.4])->count(),
            'dusuk' => TakimUyesi::where('performans_skoru', '<', 4.0)->count(),
        ];

        // Lokasyon bazlı performans
        $lokasyonPerformans = TakimUyesi::selectRaw('lokasyon, AVG(performans_skoru) as ortalama_performans, COUNT(*) as uye_sayisi')
            ->whereNotNull('lokasyon')
            ->groupBy('lokasyon')
            ->orderBy('ortalama_performans', 'desc')
            ->get();

        return view('admin.takim-yonetimi.takim.takim-performans', compact(
            'genelPerformans',
            'rolDagilimi',
            'performansDagilimi',
            'lokasyonPerformans'
        ));
    }

    /**
     * Yeni takım üyesi oluşturma formu
     */
    public function create(): View
    {
        $kullanicilar = User::whereDoesntHave('takimUyesi')->get();
        $roller = TakimUyesi::getRoller();
        $statuslar = TakimUyesi::getDurumlar();

        return view('admin.takim-yonetimi.takim.create', compact(
            'kullanicilar',
            'roller',
            'statuslar'
        ));
    }

    /**
     * Yeni takım üyesi kaydetme
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|unique:takim_uyeleri,user_id',
            'rol' => 'required|in:'.implode(',', TakimUyesi::getRoller()),
            'status' => 'required|in:'.implode(',', TakimUyesi::getDurumlar()),
            'lokasyon' => 'nullable|string|max:255',
            'uzmanlik_alani' => 'nullable|array',
            'calisma_saati' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $takimUyesi = TakimUyesi::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Takım üyesi başarıyla eklendi!',
                'data' => $takimUyesi,
            ]);

        } catch (\Exception $e) {
            Log::error('Takım üyesi ekleme hatası: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Takım üyesi eklenirken hata oluştu: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Takım üyesi düzenleme formu
     */
    public function edit($takimId): View
    {
        $takimUyesi = TakimUyesi::with('user')->findOrFail($takimId);
        $roller = TakimUyesi::getRoller();
        $statuslar = TakimUyesi::getDurumlar();

        return view('admin.takim-yonetimi.takim.edit', compact(
            'takimUyesi',
            'roller',
            'statuslar'
        ));
    }

    /**
     * Takım üyesi güncelleme
     */
    public function update(Request $request, $takimId): JsonResponse
    {
        $takimUyesi = TakimUyesi::findOrFail($takimId);

        $validator = Validator::make($request->all(), [
            'rol' => 'required|in:'.implode(',', TakimUyesi::getRoller()),
            'status' => 'required|in:'.implode(',', TakimUyesi::getDurumlar()),
            'lokasyon' => 'nullable|string|max:255',
            'uzmanlik_alani' => 'nullable|array',
            'calisma_saati' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $takimUyesi->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Takım üyesi başarıyla güncellendi!',
                'data' => $takimUyesi,
            ]);

        } catch (\Exception $e) {
            Log::error('Takım üyesi güncelleme hatası: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Takım üyesi güncellenirken hata oluştu: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Takım üyesi silme
     */
    public function destroy($takimId): JsonResponse
    {
        try {
            $takimUyesi = TakimUyesi::findOrFail($takimId);
            $takimUyesi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Takım üyesi başarıyla silindi!',
            ]);

        } catch (\Exception $e) {
            Log::error('Takım üyesi silme hatası: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Takım üyesi silinirken hata oluştu: '.$e->getMessage(),
            ], 500);
        }
    }
}
