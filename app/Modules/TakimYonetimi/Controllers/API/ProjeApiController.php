<?php

namespace App\Modules\TakimYonetimi\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Proje;
use App\Models\Gorev;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjeApiController extends Controller
{
    /**
     * Proje listesi
     */
    public function index(Request $request): JsonResponse
    {
        $query = Proje::with(['user', 'takim']);

        // Filtreleme
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('oncelik')) {
            $query->where('oncelik', $request->oncelik);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('active') && $request->active) {
            $query->where('status', true);
        }

        if ($request->has('arama')) {
            $arama = $request->arama;
            $query->where(function($q) use ($arama) {
                $q->where('proje_adi', 'like', "%{$arama}%")
                  ->orWhere('aciklama', 'like', "%{$arama}%");
            });
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $projeler = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $projeler,
            'message' => 'Proje listesi getirildi'
        ]);
    }

    /**
     * Proje detayı
     */
    public function show(Proje $proje): JsonResponse
    {
        $proje->load(['user', 'takim', 'gorevler.user', 'dosyalar']);

        // Proje istatistikleri
        $istatistikler = [
            'toplam_gorev' => $proje->gorevler->count(),
            'tamamlanan_gorev' => $proje->gorevler->where('status', 'tamamlandi')->count(),
            'devam_eden_gorev' => $proje->gorevler->where('status', 'devam_ediyor')->count(),
            'gecikmis_gorev' => $proje->gorevler->filter(function($gorev) {
                return $gorev->isGecikmis();
            })->count(),
            'progress_yuzde' => $proje->progress,
            'kalan_gun' => $proje->kalan_gun,
            'toplam_budget' => $proje->budget,
            'gerceklesen_maliyet' => $proje->gerceklesen_maliyet,
        ];

        $proje->istatistikler = $istatistikler;

        return response()->json([
            'success' => true,
            'data' => $proje,
            'message' => 'Proje detayı getirildi'
        ]);
    }

    /**
     * Yeni proje oluştur
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'proje_adi' => 'required|string|max:255',
            'aciklama' => 'nullable|string',
            'baslangic_tarihi' => 'required|date',
            'bitis_tarihi' => 'nullable|date|after:baslangic_tarihi',
            'status' => 'required|in:planlama,devam_ediyor,tamamlandi,iptal,beklemede',
            'oncelik' => 'required|in:dusuk,orta,yuksek,kritik',
            'user_id' => 'required|exists:users,id',
            'takim_id' => 'nullable|exists:users,id',
            'budget' => 'nullable|numeric|min:0',
            'notlar' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        $proje = Proje::create($validated);
        $proje->load(['user', 'takim']);

        return response()->json([
            'success' => true,
            'data' => $proje,
            'message' => 'Proje başarıyla oluşturuldu'
        ], 201);
    }

    /**
     * Proje güncelle
     */
    public function update(Request $request, Proje $proje): JsonResponse
    {
        $validated = $request->validate([
            'proje_adi' => 'sometimes|required|string|max:255',
            'aciklama' => 'nullable|string',
            'baslangic_tarihi' => 'sometimes|required|date',
            'bitis_tarihi' => 'nullable|date|after:baslangic_tarihi',
            'status' => 'sometimes|required|in:planlama,devam_ediyor,tamamlandi,iptal,beklemede',
            'oncelik' => 'sometimes|required|in:dusuk,orta,yuksek,kritik',
            'user_id' => 'sometimes|required|exists:users,id',
            'takim_id' => 'nullable|exists:users,id',
            'budget' => 'nullable|numeric|min:0',
            'gerceklesen_maliyet' => 'nullable|numeric|min:0',
            'notlar' => 'nullable|string',
            'tags' => 'nullable|array',
            'status' => 'boolean',
        ]);

        $proje->update($validated);
        $proje->load(['user', 'takim']);

        return response()->json([
            'success' => true,
            'data' => $proje,
            'message' => 'Proje başarıyla güncellendi'
        ]);
    }

    /**
     * Proje sil
     */
    public function destroy(Proje $proje): JsonResponse
    {
        // Aktif görevleri kontrol et
        $statusGorevler = $proje->gorevler()
            ->whereIn('status', ['beklemede', 'devam_ediyor'])
            ->count();

        if ($statusGorevler > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Bu projede status görevler var. Önce görevleri tamamlayın veya silin.'
            ], 400);
        }

        $proje->delete();

        return response()->json([
            'success' => true,
            'message' => 'Proje başarıyla silindi'
        ]);
    }

    /**
     * Projeye görev ekle
     */
    public function gorevEkle(Request $request, Proje $proje): JsonResponse
    {
        $validated = $request->validate([
            'gorev_adi' => 'required|string|max:255',
            'aciklama' => 'nullable|string',
            'baslangic_tarihi' => 'nullable|date',
            'bitis_tarihi' => 'nullable|date|after:baslangic_tarihi',
            'status' => 'required|in:beklemede,devam_ediyor,tamamlandi,iptal,askida',
            'oncelik' => 'required|in:dusuk,orta,yuksek,kritik',
            'user_id' => 'required|exists:users,id',
            'atayan_id' => 'required|exists:users,id',
            'tahmini_sure' => 'nullable|integer|min:0',
            'notlar' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        $validated['proje_id'] = $proje->id;
        $gorev = Gorev::create($validated);
        $gorev->load(['user', 'atayan']);

        // Proje progress'ini güncelle
        $proje->updateProgress();

        return response()->json([
            'success' => true,
            'data' => $gorev,
            'message' => 'Görev projeye başarıyla eklendi'
        ], 201);
    }

    /**
     * Proje raporu
     */
    public function rapor(Proje $proje): JsonResponse
    {
        $proje->load(['user', 'takim', 'gorevler.user', 'gorevler.atayan']);

        $rapor = [
            'proje' => $proje,
            'genel_istatistikler' => [
                'toplam_gorev' => $proje->gorevler->count(),
                'tamamlanan_gorev' => $proje->gorevler->where('status', 'tamamlandi')->count(),
                'devam_eden_gorev' => $proje->gorevler->where('status', 'devam_ediyor')->count(),
                'gecikmis_gorev' => $proje->gorevler->filter(function($gorev) {
                    return $gorev->isGecikmis();
                })->count(),
                'progress_yuzde' => $proje->progress,
                'kalan_gun' => $proje->kalan_gun,
            ],
            'zaman_analizi' => [
                'baslangic_tarihi' => $proje->baslangic_tarihi,
                'bitis_tarihi' => $proje->bitis_tarihi,
                'toplam_sure' => $proje->baslangic_tarihi && $proje->bitis_tarihi ?
                    $proje->baslangic_tarihi->diffInDays($proje->bitis_tarihi) : null,
                'gecen_sure' => $proje->baslangic_tarihi ?
                    $proje->baslangic_tarihi->diffInDays(now()) : null,
            ],
            'maliyet_analizi' => [
                'budget' => $proje->budget,
                'gerceklesen_maliyet' => $proje->gerceklesen_maliyet,
                'kalan_budget' => $proje->budget ? $proje->budget - ($proje->gerceklesen_maliyet ?? 0) : null,
                'maliyet_orani' => $proje->budget && $proje->gerceklesen_maliyet ?
                    round(($proje->gerceklesen_maliyet / $proje->budget) * 100, 2) : null,
            ],
            'gorev_dagilimi' => [
                'status_dagilimi' => $proje->gorevler->groupBy('status')->map->count(),
                'oncelik_dagilimi' => $proje->gorevler->groupBy('oncelik')->map->count(),
                'kullanici_dagilimi' => $proje->gorevler->groupBy('user_id')->map(function($gorevler) {
                    return [
                        'kullanici' => $gorevler->first()->user->name ?? 'Bilinmiyor',
                        'gorev_sayisi' => $gorevler->count(),
                        'tamamlanan' => $gorevler->where('status', 'tamamlandi')->count(),
                    ];
                }),
            ],
            'son_aktiviteler' => $proje->gorevler()
                ->with(['user', 'atayan'])
                ->latest()
                ->limit(10)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $rapor,
            'message' => 'Proje raporu oluşturuldu'
        ]);
    }

    /**
     * Proje görevleri
     */
    public function gorevler(Proje $proje): JsonResponse
    {
        $gorevler = $proje->gorevler()
            ->with(['user', 'atayan'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $gorevler,
            'message' => 'Proje görevleri getirildi'
        ]);
    }

    /**
     * Proje progress güncelle
     */
    public function progressGuncelle(Proje $proje): JsonResponse
    {
        $proje->updateProgress();
        $proje->load(['user', 'takim']);

        return response()->json([
            'success' => true,
            'data' => $proje,
            'message' => 'Proje progress\'i güncellendi'
        ]);
    }

    /**
     * Proje statusu güncelle
     */
    public function statusGuncelle(Request $request, Proje $proje): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:planlama,devam_ediyor,tamamlandi,iptal,beklemede',
            'aciklama' => 'nullable|string',
        ]);

        $eskiDurum = $proje->status;
        $proje->update(['status' => $validated['status']]);

        // Durum değişikliğine göre özel işlemler
        if ($validated['status'] === 'devam_ediyor' && !$proje->baslangic_tarihi) {
            $proje->update(['baslangic_tarihi' => now()]);
        }

        if ($validated['status'] === 'tamamlandi') {
            $proje->update(['bitis_tarihi' => now()]);
            $proje->updateProgress();
        }

        return response()->json([
            'success' => true,
            'data' => $proje->load(['user', 'takim']),
            'message' => 'Proje statusu başarıyla güncellendi'
        ]);
    }

    /**
     * Proje istatistikleri
     */
    public function istatistikler(): JsonResponse
    {
        $istatistikler = [
            'genel' => [
                'toplam_proje' => Proje::count(),
                'status_proje' => Proje::where('status', true)->count(),
                'tamamlanan_proje' => Proje::where('status', 'tamamlandi')->count(),
                'devam_eden_proje' => Proje::where('status', 'devam_ediyor')->count(),
                'planlama_proje' => Proje::where('status', 'planlama')->count(),
            ],
            'status_dagilimi' => Proje::selectRaw('status, COUNT(*) as sayi')
                ->groupBy('status')
                ->get(),
            'oncelik_dagilimi' => Proje::selectRaw('oncelik, COUNT(*) as sayi')
                ->groupBy('oncelik')
                ->get(),
            'zaman_dagilimi' => [
                'bu_hafta' => Proje::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'bu_ay' => Proje::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
                'bu_yil' => Proje::whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])->count(),
            ],
            'maliyet_analizi' => [
                'toplam_budget' => Proje::sum('budget'),
                'toplam_gerceklesen' => Proje::sum('gerceklesen_maliyet'),
                'ortalama_budget' => Proje::avg('budget'),
            ],
            'performans' => [
                'ortalama_progress' => Proje::avg('progress'),
                'yuksek_progress' => Proje::where('progress', '>=', 80)->count(),
                'dusuk_progress' => Proje::where('progress', '<=', 20)->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $istatistikler,
            'message' => 'Proje istatistikleri getirildi'
        ]);
    }
}
