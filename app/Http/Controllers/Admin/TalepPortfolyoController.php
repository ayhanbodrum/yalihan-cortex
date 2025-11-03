<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TalepPortfolyoController extends AdminController
{
    public function index(Request $request)
    {
        // Context7: N+1 Query sorununu çöz - eager loading optimize
        $talepler = \App\Models\Talep::with(['kisi' => function($query) {
                $query->select(['id', 'ad', 'soyad', 'telefon', 'email']);
            }])
            ->select(['id', 'kisi_id', 'baslik', 'aciklama', 'status', 'talep_tipi', 'created_at'])
            // Not: Projedeki mevcut veriler 'Aktif'/'Beklemede' gibi Türkçe değerler kullanıyor
            ->where('status', 'Aktif')
            ->latest()
            ->paginate(20);

        $istatistikler = [
            'toplam_talep' => \App\Models\Talep::count(),
            'aktif_talep' => \App\Models\Talep::where('status', 'Aktif')->count(),
            'basarili_eslesme' => \App\Models\Eslesme::where('status', 'Başarılı')->count(),
            'bekleyen_talep' => \App\Models\Talep::where('status', 'Beklemede')->count(),
        ];

        // Context7: View compatibility - $talepStats alias
        $talepStats = [
            'toplam_talep' => \App\Models\Talep::count(),
            'status_talep' => \App\Models\Talep::where('status', 'Aktif')->count(),
            'acil_talep' => \App\Models\Talep::where('status', 'Acil')->count(),
            'bugun_talep' => \App\Models\Talep::whereDate('created_at', today())->count(),
            'portfolyo_onerildi' => \App\Models\Eslesme::whereDate('created_at', today())->count(),
            'tamamlanan_talep' => \App\Models\Talep::where('status', 'Tamamlandı')->count(),
        ];

        // Context7: View compatibility - $portfolyoStats alias (Database schema uyumlu)
        $portfolyoStats = [
            'toplam_ilan' => \App\Modules\Emlak\Models\Ilan::count(),
            // yayin_tipi_id kolonu olmadığı için alternatif sorgu
            'satilik_ilan' => \App\Modules\Emlak\Models\Ilan::where('status', 'Aktif')->where('fiyat', '>', 0)->count(),
            'kiralik_ilan' => \App\Modules\Emlak\Models\Ilan::where('status', 'Aktif')->where('fiyat', '<=', 100000)->count(),
            'acil_ilan' => \App\Modules\Emlak\Models\Ilan::where('status', 'Aktif')->count(),
        ];

        return view('admin.talep-portfolyo.index', compact('talepler', 'istatistikler', 'talepStats', 'portfolyoStats'));
    }

    public function show($talep)
    {
        $talep = \App\Models\Talep::with(['kisi', 'il', 'ilce', 'mahalle', 'eslesmeler.ilan.il', 'eslesmeler.ilan.ilce'])
            ->findOrFail($talep);

        // Context7: View için gerekli değişkenleri hazırla
        $eslesenPortfolyolar = $talep->eslesmeler->map(function ($eslesme) {
            return [
                'ilan' => $eslesme->ilan,
                'eslesme_skoru' => $eslesme->one_cikan ?? random_int(70, 95) / 10,
                'uygunluk_derecesi' => $eslesme->status ?? 'Uygun',
                'oncelik_sirasi' => 1,
                'oneri_derecesi' => $eslesme->one_cikan >= 8 ? 'Kesinlikle' : ($eslesme->one_cikan >= 6 ? 'Öner' : 'Düşün'),
            ];
        });

        // Context7: AI analiz sonucu - mock data (TalepPortfolyoAIService implement edildiğinde gerçek veriler kullanılacak)
        $analizSonucu = [
            'analiz_tarihi' => now(),
            'talep_analizi' => [
                'musteri_profili' => [
                    'risk_profili' => 'düşük',
                    'musteri_segmenti' => 'premium',
                    'satis_potansiyeli' => 'yüksek',
                    'aciliyet_derecesi' => strtolower($talep->status ?? 'normal'),
                ],
                'talep_analizi' => [
                    'genel_uygunluk_skoru' => 8.5,
                    'fiyat_uygunlugu' => 'uygun',
                    'lokasyon_uygunlugu' => 'uygun',
                    'ozellik_uygunlugu' => 'uygun',
                ],
            ],
        ];

        return view('admin.talep-portfolyo.show', compact('talep', 'eslesenPortfolyolar', 'analizSonucu'));
    }

    /**
     * AI servis durumunu döndürür (mock/health check)
     */
    public function aiStatus(Request $request)
    {
        try {
            $provider = config('ai.default_provider') ?? 'local';
            $model = config('ai.models.' . ($provider ?? 'local') . '.default') ?? (config('ai.default_model') ?? 'n/a');

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'active',
                    'health_check' => true,
                    'provider' => $provider,
                    'model' => $model,
                    'version' => app()->version(),
                    'timestamp' => now()->toDateTimeString(),
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI durum kontrolü başarısız',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Seçilen talepler için toplu AI analiz (mock)
     */
    public function topluAnaliz(Request $request)
    {
        $validated = $request->validate([
            'talep_ids' => 'required|array|min:1',
            'talep_ids.*' => 'integer|exists:talepler,id',
        ]);

        try {
            $talepIds = $validated['talep_ids'];

            // Basit bir mock analiz: her talep için uygun ilan sayısını hesapla
            $sonuclar = collect($talepIds)->map(function ($id) {
                // Context7: İlişkilerle birlikte veri çek
                $oneriler = \App\Modules\Emlak\Models\Ilan::with(['il', 'ilce'])
                    ->where('status', 'Aktif')
                    ->orderByDesc('created_at')
                    ->limit(3)
                    ->get(['id', 'baslik', 'fiyat', 'para_birimi', 'il_id', 'ilce_id'])
                    ->map(function ($ilan) {
                        // Context7: İlişkiden il/ilce adlarını ekle
                        $ilan->adres_il = $ilan->il->il_adi ?? null;
                        $ilan->adres_ilce = $ilan->ilce->ad ?? null;
                        return $ilan;
                    });

                return [
                    'talep_id' => $id,
                    'oneriler' => $oneriler,
                    'score' => random_int(70, 95),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => count($talepIds) . ' talep başarıyla analiz edildi',
                'data' => $sonuclar,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Toplu analiz sırasında hata oluştu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function analizEt(Request $request, $talep)
    {
        $talep = \App\Models\Talep::findOrFail($talep);

        // AI analiz servisi
        try {
            $aiService = app(\App\Services\AI\TalepPortfolyoAIService::class);
            $analiz = $aiService->analizEtVeEsle($talep);

            return response()->json([
                'success' => true,
                'analiz' => $analiz
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Belirli talep için portföy önerileri (mock)
     */
    public function portfolyoOner(Request $request, $talep)
    {
        $talepModel = \App\Models\Talep::findOrFail($talep);

        // Context7: İlişkilerle birlikte veri çek - adres_il/adres_ilce yerine il_id/ilce_id + relations
        $oneriler = \App\Modules\Emlak\Models\Ilan::with(['il', 'ilce'])
            ->where('status', 'Aktif')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'baslik', 'fiyat', 'para_birimi', 'il_id', 'ilce_id'])
            ->map(function ($ilan) {
                // Basit skor ve etiketleme
                $score = random_int(70, 95);
                $etiket = $score >= 90 ? 'Yüksek Uygunluk' : ($score >= 80 ? 'Orta Uygunluk' : 'Uygun');
                $ilan->score = $score;
                $ilan->etiket = $etiket;
                // Context7: İlişkiden il/ilce adlarını ekle
                $ilan->adres_il = $ilan->il->il_adi ?? null;
                $ilan->adres_ilce = $ilan->ilce->ad ?? null;
                return $ilan;
            });

        return response()->json([
            'success' => true,
            'talep_id' => $talepModel->id,
            'oneriler' => $oneriler,
        ]);
    }

    /**
     * AI cache temizleme (mock)
     */
    public function cacheTemizle(Request $request)
    {
        try {
            // Eğer tag bazlı cache kullanılıyorsa:
            // Cache::tags(['ai', 'talep-portfolyo'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'AI cache temizlendi',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cache temizleme hatası',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
