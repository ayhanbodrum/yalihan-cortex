<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Eslesme;

class EslesmeController extends AdminController
{
    public function index(Request $request)
    {
        $eslesmeler = \App\Models\Eslesme::with(['ilan', 'kisi', 'danisman'])->latest()->paginate(20);
        $istatistikler = [
            'toplam' => \App\Models\Eslesme::count(),
            'aktif' => \App\Models\Eslesme::where('status', 'Aktif')->count(),
            'beklemede' => \App\Models\Eslesme::where('status', 'Beklemede')->count(),
        ];

        return $this->render('admin.eslesmeler.index', compact('eslesmeler', 'istatistikler'));
    }

    public function create()
    {
        // Context7: Provide datasets for form
        $kisiler = \App\Models\Kisi::where('status', 'Aktif')
            ->orderBy('ad')
            ->get();

        $ilanlar = \App\Models\Ilan::where('status', 'Aktif')
            ->with(['kategori', 'il', 'ilce'])
            ->orderBy('created_at', 'desc')
            ->get();

        $talepler = \App\Models\Talep::where('status', 'Aktif')
            ->with(['kisi', 'kategori'])
            ->orderBy('created_at', 'desc')
            ->get();

        $danismanlar = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->orderBy('name')->get();

        $data = [
            'pageTitle' => 'Yeni Eşleştirme Oluştur',
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['name' => 'Eşleştirmeler', 'url' => route('admin.eslesmeler.index')],
                ['name' => 'Yeni Eşleştirme', 'active' => true]
            ],
            'kisiler' => $kisiler,
            'ilanlar' => $ilanlar,
            'talepler' => $talepler,
            'danismanlar' => $danismanlar,
        ];

        return $this->render('admin.eslesmeler.create', $data);
    }

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
            'eslesme_tarihi' => 'nullable|date'
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
                'son_guncelleme' => now()
            ];

            $eslesme = \App\Models\Eslesme::create($eslesmeData);

            return redirect()
                ->route('admin.eslesmeler.index')
                ->with('success', 'Eşleştirme başarıyla oluşturuldu! 🎉');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Eşleştirme oluşturulurken hata oluştu: ' . $e->getMessage());
        }
    }

    public function show($eslesme)
    {
        return $this->render('admin.eslesmeler.show', ['eslesme' => $eslesme]);
    }

    public function edit($eslesme)
    {
        return $this->render('admin.eslesmeler.edit', ['eslesme' => $eslesme]);
    }

    public function update(Request $request, $eslesme)
    {
        return redirect()->route('admin.eslesmeler.edit', $eslesme);
    }

    public function destroy(Eslesme $eslesme)
    {
        try {
            // Eşleşme bilgilerini al
            $eslesmeBilgi = 'Eşleşme #' . $eslesme->id;
            if ($eslesme->ilan) {
                $eslesmeBilgi .= ' (' . $eslesme->ilan->baslik . ')';
            }
            $eslesme->delete();

            return redirect()
                ->route('admin.eslesmeler.index')
                ->with('success', $eslesmeBilgi . ' başarıyla silindi.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.eslesmeler.index')
                ->with('error', 'Eşleşme silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function autoMatch()
    {
        return response()->json(['created' => 0]);
    }

    public function bulkCreate(Request $request)
    {
        return response()->json(['created' => 0]);
    }

    // API Endpoints for Form Data
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
                        'display_name' => "{$kisi->ad} {$kisi->soyad}" . ($kisi->telefon ? " - {$kisi->telefon}" : '')
                    ];
                });

            return response()->json($kisiler);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Kişiler yüklenemedi'], 500);
        }
    }

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

    public function getTalepler()
    {
        try {
            $talepler = \App\Models\Talep::select(['id', 'baslik', 'il', 'ilce', 'created_at'])
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get()
                ->map(function ($talep) {
                    return [
                        'id' => $talep->id,
                        'baslik' => $talep->baslik,
                        'il' => $talep->il,
                        'ilce' => $talep->ilce,
                        'display_name' => "{$talep->baslik} - {$talep->il}" . ($talep->ilce ? "/{$talep->ilce}" : '')
                    ];
                });

            return response()->json($talepler);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Talepler yüklenemedi'], 500);
        }
    }

    public function getIlanlar()
    {
        try {
            $ilanlar = \App\Models\Ilan::select(['id', 'baslik', 'fiyat', 'para_birimi', 'adres_il', 'adres_ilce'])
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get()
                ->map(function ($ilan) {
                    $fiyatText = $ilan->fiyat ? number_format($ilan->fiyat) . ' ' . ($ilan->para_birimi ?? 'TL') : 'Fiyat Yok';
                    $lokasyonText = $ilan->adres_il . ($ilan->adres_ilce ? "/{$ilan->adres_ilce}" : '');

                    return [
                        'id' => $ilan->id,
                        'baslik' => $ilan->baslik,
                        'fiyat' => $ilan->fiyat,
                        'para_birimi' => $ilan->para_birimi,
                        'display_name' => "{$ilan->baslik} - {$fiyatText} ({$lokasyonText})"
                    ];
                });

            return response()->json($ilanlar);
        } catch (\Exception $e) {
            return response()->json(['error' => 'İlanlar yüklenemedi'], 500);
        }
    }

    public function getAIEslesmeOnerileri(Request $request)
    {
        try {
            // Mock AI Suggestions - Gerçek AI entegrasyonu için genişletilebilir
            $suggestions = [
                [
                    'score' => 95,
                    'reason' => 'Lokasyon ve fiyat uyumu mükemmel',
                    'ilan_id' => 1,
                    'confidence' => 'Yüksek'
                ],
                [
                    'score' => 87,
                    'reason' => 'Özellikler büyük oranda eşleşiyor',
                    'ilan_id' => 2,
                    'confidence' => 'Orta'
                ]
            ];

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'AI önerileri alınamadı'
            ], 500);
        }
    }

    private function render(string $view, array $data = []): Response|\Illuminate\Contracts\View\View
    {
        if (view()->exists($view)) return response()->view($view, $data);
        return response('Eşleşmeler sayfaları hazır değil', 200);
    }
}


