<?php

namespace App\Http\Controllers\Admin;

use App\Models\Eslesme;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EslesmeController extends AdminController
{
    /**
     * Display a listing of the resource.
     * Context7: Eşleştirme listesi ve filtreleme
     *
     * @return Response|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // ✅ N+1 FIX: Eager loading with select optimization
        $eslesmeler = \App\Models\Eslesme::with([
            'ilan:id,baslik,fiyat,para_birimi,status',
            'kisi:id,ad,soyad,telefon,email',
            'danisman:id,name,email',
        ])
            ->select(['id', 'ilan_id', 'kisi_id', 'danisman_id', 'status', 'one_cikan', 'created_at'])
            ->latest()
            ->paginate(20);

        $istatistikler = [
            'toplam' => \App\Models\Eslesme::count(),
            'aktif' => \App\Models\Eslesme::where('status', 'Aktif')->count(),
            'beklemede' => \App\Models\Eslesme::where('status', 'Beklemede')->count(),
        ];

        return $this->render('admin.eslesmeler.index', compact('eslesmeler', 'istatistikler'));
    }

    /**
     * Show the form for creating a new resource.
     * Context7: Yeni eşleştirme oluşturma formu
     *
     * @return Response|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        // Context7: Provide datasets for form
        // ✅ N+1 FIX: Select optimization
        $kisiler = \App\Models\Kisi::where('status', 'Aktif')
            ->select(['id', 'ad', 'soyad', 'telefon', 'email'])
            ->orderBy('ad')
            ->get();

        // ✅ N+1 FIX: Eager loading with select optimization
        $ilanlar = \App\Models\Ilan::where('status', 'Aktif')
            ->with([
                'kategori:id,name,slug',
                'il:id,il_adi',
                'ilce:id,ilce_adi',
            ])
            ->select(['id', 'baslik', 'fiyat', 'para_birimi', 'kategori_id', 'il_id', 'ilce_id', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->get();

        // ✅ N+1 FIX: Eager loading with select optimization
        $talepler = \App\Models\Talep::where('status', 'Aktif')
            ->with([
                'kisi:id,ad,soyad,telefon',
                'kategori:id,name,slug',
            ])
            ->select(['id', 'baslik', 'kisi_id', 'kategori_id', 'il_id', 'ilce_id', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->get();

        // ✅ N+1 FIX: Select optimization
        $danismanlar = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get();

        $data = [
            'pageTitle' => 'Yeni Eşleştirme Oluştur',
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['name' => 'Eşleştirmeler', 'url' => route('admin.eslesmeler.index')],
                ['name' => 'Yeni Eşleştirme', 'active' => true],
            ],
            'kisiler' => $kisiler,
            'ilanlar' => $ilanlar,
            'talepler' => $talepler,
            'danismanlar' => $danismanlar,
        ];

        return $this->render('admin.eslesmeler.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     * Context7: Yeni eşleştirme kaydetme
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Exception
     */
    public function store(Request $request)
    {
        // Context7 Validation Rules
        $validated = $request->validate([
            'kisi_id' => 'required|exists:kisiler,id',
            'ilan_id' => 'required|exists:ilanlar,id',
            'talep_id' => 'nullable|exists:talepler,id',
            'danisman_id' => 'nullable|exists:users,id',
            'status' => 'required|string|in:Aktif,Beklemede,İptal,Tamamlandı',
            'one_cikan' => 'nullable|boolean',
            'notlar' => 'nullable|string|max:1000',
            'eslesme_tarihi' => 'nullable|date',
        ]);

        try {
            // Context7 uyumlu veri hazırlama
            $eslesmeData = [
                'kisi_id' => $validated['kisi_id'],
                'ilan_id' => $validated['ilan_id'],
                'talep_id' => $validated['talep_id'] ?? null,
                'danisman_id' => $validated['danisman_id'] ?? auth()->id(),
                'status' => $validated['status'],
                'one_cikan' => $validated['one_cikan'] ?? false,
                'notlar' => $validated['notlar'] ?? null,
                'eslesme_tarihi' => $validated['eslesme_tarihi'] ?? now(),
                'son_guncelleme' => now(),
            ];

            $eslesme = \App\Models\Eslesme::create($eslesmeData);

            return redirect()
                ->route('admin.eslesmeler.index')
                ->with('success', 'Eşleştirme başarıyla oluşturuldu! 🎉');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Eşleştirme oluşturulurken hata oluştu: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Context7: Eşleştirme detay sayfası
     *
     * @param  int|string|Eslesme  $eslesme
     * @return Response|\Illuminate\Contracts\View\View
     */
    public function show($eslesme)
    {
        return $this->render('admin.eslesmeler.show', ['eslesme' => $eslesme]);
    }

    /**
     * Show the form for editing the specified resource.
     * Context7: Eşleştirme düzenleme formu
     *
     * @param  int|string|Eslesme  $eslesme
     * @return Response|\Illuminate\Contracts\View\View
     */
    public function edit($eslesme)
    {
        return $this->render('admin.eslesmeler.edit', ['eslesme' => $eslesme]);
    }

    /**
     * Update the specified resource in storage.
     * Context7: Eşleştirme güncelleme
     *
     * @param  int|string|Eslesme  $eslesme
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $eslesme)
    {
        return redirect()->route('admin.eslesmeler.edit', $eslesme);
    }

    /**
     * Remove the specified resource from storage.
     * Context7: Eşleştirme silme
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Exception
     */
    public function destroy(Eslesme $eslesme)
    {
        try {
            // ✅ N+1 FIX: Eager loading ekle
            $eslesme->load('ilan:id,baslik');

            // Eşleşme bilgilerini al
            $eslesmeBilgi = 'Eşleşme #'.$eslesme->id;
            if ($eslesme->ilan) {
                $eslesmeBilgi .= ' ('.$eslesme->ilan->baslik.')';
            }
            $eslesme->delete();

            return redirect()
                ->route('admin.eslesmeler.index')
                ->with('success', $eslesmeBilgi.' başarıyla silindi.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.eslesmeler.index')
                ->with('error', 'Eşleşme silinirken bir hata oluştu: '.$e->getMessage());
        }
    }

    /**
     * Auto match requests with listings
     * Context7: Otomatik eşleştirme
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function autoMatch()
    {
        return response()->json(['created' => 0]);
    }

    /**
     * Bulk create matches
     * Context7: Toplu eşleştirme oluşturma
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkCreate(Request $request)
    {
        return response()->json(['created' => 0]);
    }

    /**
     * Get persons for form dropdown
     * Context7: Form için kişi listesi API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKisiler()
    {
        try {
            $kisiler = \App\Models\Kisi::select(['id', 'ad', 'soyad', 'telefon'])
                ->orderBy('ad')
                ->limit(100)
                ->get()
                ->map(function ($kisi) {
                    return [
                        'id' => $kisi->id,
                        'ad' => $kisi->ad,
                        'soyad' => $kisi->soyad,
                        'telefon' => $kisi->telefon,
                        'display_name' => "{$kisi->ad} {$kisi->soyad}".($kisi->telefon ? " - {$kisi->telefon}" : ''),
                    ];
                });

            return response()->json($kisiler);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Kişiler yüklenemedi'], 500);
        }
    }

    /**
     * Get advisors for form dropdown
     * Context7: Form için danışman listesi API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDanismanlar()
    {
        try {
            $danismanlar = \App\Models\User::select(['id', 'name', 'email'])
                ->where('role', 'danisman')
                ->orWhere('role', 'admin')
                ->orderBy('name')
                ->get();

            return response()->json($danismanlar);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Danışmanlar yüklenemedi'], 500);
        }
    }

    /**
     * Get requests for form dropdown
     * Context7: Form için talep listesi API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTalepler()
    {
        try {
            $talepler = \App\Models\Talep::select(['id', 'baslik', 'il', 'ilce', 'created_at'])
                ->where('status', 'Aktif') // Context7: Database değeri
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get()
                ->map(function ($talep) {
                    return [
                        'id' => $talep->id,
                        'baslik' => $talep->baslik,
                        'il' => $talep->il,
                        'ilce' => $talep->ilce,
                        'display_name' => "{$talep->baslik} - {$talep->il}".($talep->ilce ? "/{$talep->ilce}" : ''),
                    ];
                });

            return response()->json($talepler);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Talepler yüklenemedi'], 500);
        }
    }

    /**
     * Get listings for form dropdown
     * Context7: Form için ilan listesi API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIlanlar()
    {
        try {
            $ilanlar = \App\Models\Ilan::select(['id', 'baslik', 'fiyat', 'para_birimi', 'adres_il', 'adres_ilce'])
                ->where('status', 'Aktif') // Context7: Database değeri
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get()
                ->map(function ($ilan) {
                    $fiyatText = $ilan->fiyat ? number_format($ilan->fiyat).' '.($ilan->para_birimi ?? 'TL') : 'Fiyat Yok';
                    $lokasyonText = $ilan->adres_il.($ilan->adres_ilce ? "/{$ilan->adres_ilce}" : '');

                    return [
                        'id' => $ilan->id,
                        'baslik' => $ilan->baslik,
                        'fiyat' => $ilan->fiyat,
                        'para_birimi' => $ilan->para_birimi,
                        'display_name' => "{$ilan->baslik} - {$fiyatText} ({$lokasyonText})",
                    ];
                });

            return response()->json($ilanlar);
        } catch (\Exception $e) {
            return response()->json(['error' => 'İlanlar yüklenemedi'], 500);
        }
    }

    /**
     * Get AI matching suggestions
     * Context7: AI destekli eşleştirme önerileri
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAIEslesmeOnerileri(Request $request)
    {
        try {
            // Mock AI Suggestions - Gerçek AI entegrasyonu için genişletilebilir
            $suggestions = [
                [
                    'score' => 95,
                    'reason' => 'Lokasyon ve fiyat uyumu mükemmel',
                    'ilan_id' => 1,
                    'confidence' => 'Yüksek',
                ],
                [
                    'score' => 87,
                    'reason' => 'Özellikler büyük oranda eşleşiyor',
                    'ilan_id' => 2,
                    'confidence' => 'Orta',
                ],
            ];

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'AI önerileri alınamadı',
            ], 500);
        }
    }

    /**
     * Render view helper
     * Context7: View render helper
     */
    private function render(string $view, array $data = []): Response|\Illuminate\Contracts\View\View
    {
        if (view()->exists($view)) {
            return response()->view($view, $data);
        }

        return response('Eşleşmeler sayfaları hazır değil', 200);
    }
}
