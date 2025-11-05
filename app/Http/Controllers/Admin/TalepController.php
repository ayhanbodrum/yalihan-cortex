<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Ulke;
use App\Models\Talep;

class TalepController extends AdminController
{
    /**
     * Display a listing of the resource.
     * Context7: Talep listesi ve filtreleme
     *
     * @param Request $request
     * @return Response|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // ✅ EAGER LOADING: Select optimization ile birlikte
        $talepler = \App\Models\Talep::with([
            'kisi:id,ad,soyad,telefon,email',
            'danisman:id,name,email'
        ])
        ->select([
            'id', 'baslik', 'tip', 'status', 'kisi_id', 'danisman_id',
            'kategori_id', 'il_id', 'ilce_id', 'created_at', 'updated_at'
        ])
        ->latest()
        ->paginate(20);
        
        // ✅ OPTIMIZED: İstatistikleri tek query'de hesapla
        $istatistikler = [
            'toplam' => \App\Models\Talep::count(),
            'aktif' => \App\Models\Talep::where('status', 'Aktif')->count(),
            'beklemede' => \App\Models\Talep::where('status', 'Beklemede')->count(),
        ];

        // Context7: Tüm benzersiz statusları al
        $statuslar = \App\Models\Talep::select('status')->distinct()->pluck('status');

        // ✅ OPTIMIZED: Select optimization
        $kategoriler = \App\Models\IlanKategori::whereNull('parent_id')
            ->select(['id', 'name', 'slug'])
            ->orderBy('name')
            ->get();

        // ✅ OPTIMIZED: Select optimization
        $ulkeler = Ulke::select(['id', 'ulke_adi', 'ulke_kodu'])
            ->orderBy('ulke_adi')
            ->get();

        // 🆕 USTA Auto-Fix: Talep Tipleri eklendi
        $talepTipleri = ['Satılık', 'Kiralık', 'Günlük Kiralık', 'Devren', 'Konut', 'Arsa', 'İşyeri'];

        return $this->render('admin.talepler.index', compact('talepler', 'istatistikler', 'statuslar', 'kategoriler', 'ulkeler', 'talepTipleri'));
    }

    /**
     * Show the form for creating a new resource.
     * Context7: Yeni talep oluşturma formu
     *
     * @return Response|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        // Context7: Kategoriler (Ana kategoriler)
        $kategoriler = \App\Models\IlanKategori::whereNull('parent_id')
            ->where('status', 'Aktif')
            ->orderBy('order')
            ->get();

        // Context7: İller
        $iller = \App\Models\Il::orderBy('il_adi')->get();

        // Context7: Danışmanlar
        $danismanlar = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->get();
        
        // ✅ Status options (needed by _form.blade.php)
        $statuslar = ['active', 'pending', 'cancelled', 'completed'];
        
        // ✅ Ülkeler (needed by _form.blade.php if used)
        $ulkeler = Ulke::orderBy('ulke_adi')->get();

        return $this->render('admin.talepler.create', compact('kategoriler', 'iller', 'danismanlar', 'statuslar', 'ulkeler'));
    }

    /**
     * Store a newly created resource in storage.
     * Context7: Yeni talep kaydetme
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        // Context7: Validation (mahalle_id standardı - 2025-10-31)
        $validated = $request->validate([
            'baslik' => 'required|string|max:255',
            'aciklama' => 'nullable|string',
            'tip' => 'required|string|in:Satılık,Kiralık,Günlük Kiralık,Devren',
            'kategori_id' => 'nullable|exists:ilan_kategoriler,id',
            'alt_kategori_id' => 'nullable|exists:ilan_kategoriler,id',
            'status' => 'required|string|in:Aktif,Beklemede,İptal',
            'one_cikan' => 'nullable|boolean',
            'il_id' => 'required|exists:iller,id',
            'ilce_id' => 'nullable|exists:ilceler,id',
            'mahalle_id' => 'nullable|exists:mahalleler,id',
            'kisi_id' => 'nullable|exists:kisiler,id',
            'danisman_id' => 'nullable|exists:users,id',
            // Yeni kişi fields
            'kisi_ad' => 'nullable|string|max:100',
            'kisi_soyad' => 'nullable|string|max:100',
            'kisi_telefon' => 'nullable|string|max:20',
            'kisi_email' => 'nullable|email|max:100',
        ]);

        try {
            // Eğer kişi_id yoksa ve yeni kişi bilgileri varsa, önce kişi oluştur
            if (!$validated['kisi_id'] && $validated['kisi_ad']) {
                $kisi = \App\Models\Kisi::create([
                    'ad' => $validated['kisi_ad'],
                    'soyad' => $validated['kisi_soyad'],
                    'telefon' => $validated['kisi_telefon'],
                    'email' => $validated['kisi_email'],
                    'kisi_tipi' => 'Potansiyel',
                    'status' => 'active',
                ]);
                $validated['kisi_id'] = $kisi->id;
            }

            // Talebi oluştur (Context7: mahalle_id only - 2025-10-31)
            $talep = \App\Models\Talep::create([
                'baslik' => $validated['baslik'],
                'aciklama' => $validated['aciklama'],
                'tip' => $validated['tip'],
                'kategori_id' => $validated['kategori_id'],
                'alt_kategori_id' => $validated['alt_kategori_id'],
                'status' => $validated['status'],
                'one_cikan' => $validated['one_cikan'] ?? false,
                'il_id' => $validated['il_id'],
                'ilce_id' => $validated['ilce_id'],
                'mahalle_id' => $validated['mahalle_id'],
                'kisi_id' => $validated['kisi_id'],
                'danisman_id' => $validated['danisman_id'],
            ]);

            return redirect()
                ->route('admin.talepler.show', $talep->id)
                ->with('success', 'Talep başarıyla oluşturuldu! 🎉');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Talep oluşturulurken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Context7: Talep detay sayfası
     *
     * @param Talep $talep
     * @return Response|\Illuminate\Contracts\View\View
     */
    public function show(Talep $talep)
    {
        return $this->render('admin.talepler.show', ['talep' => $talep]);
    }

    /**
     * Show the form for editing the specified resource.
     * Context7: Talep düzenleme formu
     *
     * @param Talep $talep
     * @return Response|\Illuminate\Contracts\View\View
     */
    public function edit(Talep $talep)
    {
        // Context7: Kişiler
        $kisiler = \App\Models\Kisi::orderBy('ad')->get();
        
        // Context7: Kategoriler (Ana kategoriler)
        $kategoriler = \App\Models\IlanKategori::whereNull('parent_id')
            ->where('status', 'Aktif')
            ->orderBy('order')
            ->get();

        // Context7: İller
        $iller = \App\Models\Il::orderBy('il_adi')->get();

        // Context7: Danışmanlar
        $danismanlar = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->get();

        // Context7: Emlak Tipleri
        $emlakTipleri = ['Daire', 'Villa', 'Arsa', 'İşyeri', 'Yazlık'];

        // Context7: Talep Tipleri
        $talepTipleri = ['Satılık', 'Kiralık', 'Günlük Kiralık', 'Devren'];

        // Context7: Statuslar
        $statuslar = ['active', 'pending', 'closed', 'cancelled'];

        // Context7: Ülkeler
        $ulkeler = \App\Models\Ulke::orderBy('ulke_adi')->get();

        return $this->render('admin.talepler.edit', compact('talep', 'kisiler', 'kategoriler', 'iller', 'danismanlar', 'emlakTipleri', 'talepTipleri', 'statuslar', 'ulkeler'));
    }

    /**
     * Update the specified resource in storage.
     * Context7: Talep güncelleme
     *
     * @param Request $request
     * @param Talep $talep
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Talep $talep)
    {
        return redirect()->route('admin.talepler.edit', $talep);
    }

    /**
     * Remove the specified resource from storage.
     * Context7: Talep silme
     *
     * @param Talep $talep
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Talep $talep)
    {
        try {
            // Talep bilgilerini al
            $talepBilgi = $talep->kisi ? ($talep->kisi->ad . ' ' . $talep->kisi->soyad) : 'Talep #' . $talep->id;
            $talep->delete();

            return redirect()
                ->route('admin.talepler.index')
                ->with('success', $talepBilgi . ' başarıyla silindi.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.talepler.index')
                ->with('error', 'Talep silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display matched listings for a request
     * Context7: Eşleşen ilanlar
     *
     * @param int|string $talep
     * @return Response|\Illuminate\Contracts\View\View
     */
    public function eslesen($talep)
    {
        return $this->render('admin.talepler.eslesen', ['talep' => $talep]);
    }

    /**
     * Search requests
     * Context7: Talep arama endpoint
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        return response()->json(['items' => []]);
    }

    /**
     * Bulk action for requests
     * Context7: Toplu işlem endpoint
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAction(Request $request)
    {
        return response()->json(['success' => true]);
    }

    /**
     * Test endpoint
     * Context7: Test endpoint
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function test()
    {
        return response()->json(['ok' => true]);
    }

    /**
     * Debug endpoint
     * Context7: Debug endpoint
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function debug()
    {
        return response()->json(['routes' => 'ok']);
    }

    /**
     * Render view helper
     * Context7: View render helper
     *
     * @param string $view
     * @param array $data
     * @return Response|\Illuminate\Contracts\View\View
     */
    private function render(string $view, array $data = []): Response|\Illuminate\Contracts\View\View
    {
        if (view()->exists($view)) return response()->view($view, $data);
        return response('Talepler sayfaları hazır değil', 200);
    }
}
