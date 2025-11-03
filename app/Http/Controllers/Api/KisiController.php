<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kisi;
use App\Models\Ilan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Kişi API Controller
 *
 * Context7 Standardı: C7-KISI-API-CONTROLLER-2025-10-11
 *
 * CRM ve İlan geçmişi API endpoint'leri
 */
class KisiController extends Controller
{
    /**
     * Kişinin ilan geçmişini getir
     *
     * GET /api/kisiler/{id}/ilan-gecmisi
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getIlanGecmisi($id): JsonResponse
    {
        try {
            $kisi = Kisi::findOrFail($id);

            // Son 10 ilanı al (ilan sahibi olarak)
            $ilanlar = Ilan::where('ilan_sahibi_id', $kisi->id)
                ->with(['anaKategori', 'altKategori', 'il', 'ilce', 'mahalle'])
                ->latest()
                ->limit(10)
                ->get();

            // Analiz
            $analysis = [
                'total_listings' => $ilanlar->count(),
                'avg_price' => $ilanlar->avg('fiyat'),
                'total_value' => $ilanlar->sum('fiyat'),
                'preferred_category' => $this->getMostUsedCategory($ilanlar),
                'preferred_location' => $this->getMostUsedLocation($ilanlar),
                'preferred_currency' => $this->getMostUsedCurrency($ilanlar),
                'status_distribution' => $this->getStatusDistribution($ilanlar),
                'crm_score' => $kisi->crm_score,
                'avg_metrekare' => $ilanlar->avg('metrekare'),
                'date_range' => [
                    'first' => $ilanlar->last()?->created_at?->format('d.m.Y'),
                    'last' => $ilanlar->first()?->created_at?->format('d.m.Y')
                ]
            ];

            // İlan listesi (detaylı)
            $listingDetails = $ilanlar->map(function ($ilan) {
                return [
                    'id' => $ilan->id,
                    'referans_no' => $ilan->referans_no ?? 'YE-' . $ilan->id,
                    'baslik' => $ilan->baslik,
                    'kategori' => $ilan->altKategori->name ?? $ilan->anaKategori->name ?? 'Belirtilmemiş',
                    'lokasyon' => $this->formatLocation($ilan),
                    'fiyat' => $ilan->fiyat,
                    'para_birimi' => $ilan->para_birimi,
                    'fiyat_formatted' => number_format($ilan->fiyat, 0, ',', '.') . ' ' . $this->getCurrencySymbol($ilan->para_birimi),
                    'metrekare' => $ilan->metrekare,
                    'status' => $ilan->status,
                    'created_at' => $ilan->created_at->diffForHumans(),
                    'created_at_full' => $ilan->created_at->format('d.m.Y H:i')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'kisi' => [
                        'id' => $kisi->id,
                        'tam_ad' => $kisi->tam_ad,
                        'telefon' => $kisi->telefon,
                        'email' => $kisi->email,
                        'musteri_tipi' => $kisi->musteri_tipi,
                        'crm_score' => $kisi->crm_score
                    ],
                    'analysis' => $analysis,
                    'listings' => $listingDetails
                ],
                'context7_compliant' => true
            ]);
        } catch (\Exception $e) {
            \Log::error('Kişi ilan geçmişi hatası', [
                'kisi_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'İlan geçmişi yüklenemedi',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * En çok kullanılan kategoriyi bul
     */
    protected function getMostUsedCategory($ilanlar): ?string
    {
        if ($ilanlar->isEmpty()) return null;

        $categories = $ilanlar->pluck('altKategori.name')->filter();

        if ($categories->isEmpty()) return null;

        $counts = $categories->countBy();
        $mostUsed = $counts->sortDesc()->keys()->first();

        return $mostUsed;
    }

    /**
     * En çok kullanılan lokasyonu bul
     */
    protected function getMostUsedLocation($ilanlar): ?string
    {
        if ($ilanlar->isEmpty()) return null;

        $locations = $ilanlar->map(function ($ilan) {
            return [
                $ilan->il->il_adi ?? null,
                $ilan->ilce->ilce_adi ?? null
            ];
        })->filter();

        if ($locations->isEmpty()) return null;

        $locationStrings = $locations->map(function ($loc) {
            return implode(', ', array_filter($loc));
        });

        $counts = $locationStrings->countBy();
        $mostUsed = $counts->sortDesc()->keys()->first();

        return $mostUsed;
    }

    /**
     * En çok kullanılan para birimini bul
     */
    protected function getMostUsedCurrency($ilanlar): ?string
    {
        if ($ilanlar->isEmpty()) return null;

        $currencies = $ilanlar->pluck('para_birimi')->filter();

        if ($currencies->isEmpty()) return 'TRY';

        $counts = $currencies->countBy();
        return $counts->sortDesc()->keys()->first();
    }

    /**
     * Status dağılımını hesapla
     */
    protected function getStatusDistribution($ilanlar): array
    {
        $distribution = [
            'Aktif' => 0,
            'Pasif' => 0,
            'Taslak' => 0,
            'Beklemede' => 0
        ];

        foreach ($ilanlar as $ilan) {
            if (isset($distribution[$ilan->status])) {
                $distribution[$ilan->status]++;
            }
        }

        return $distribution;
    }

    /**
     * Lokasyon formatla
     */
    protected function formatLocation($ilan): string
    {
        $parts = array_filter([
            $ilan->il->il_adi ?? null,
            $ilan->ilce->ilce_adi ?? null,
            $ilan->mahalle->mahalle_adi ?? null
        ]);

        return implode(', ', $parts) ?: 'Belirtilmemiş';
    }

    /**
     * Para birimi sembolü
     */
    protected function getCurrencySymbol($currency): string
    {
        return match ($currency) {
            'TRY' => '₺',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $currency
        };
    }

    /**
     * Kişi profil özeti
     *
     * GET /api/kisiler/{id}/profil
     */
    public function getProfil($id): JsonResponse
    {
        try {
            $kisi = Kisi::with(['il', 'ilce', 'danisman'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $kisi->id,
                    'tam_ad' => $kisi->tam_ad,
                    'telefon' => $kisi->telefon,
                    'email' => $kisi->email,
                    'musteri_tipi' => $kisi->musteri_tipi,
                    'status' => $kisi->status,
                    'crm_score' => $kisi->crm_score,
                    'lokasyon' => [
                        'il' => $kisi->il->il_adi ?? null,
                        'ilce' => $kisi->ilce->ilce_adi ?? null
                    ],
                    'danisman' => [
                        'id' => $kisi->danisman->id ?? null,
                        'name' => $kisi->danisman->name ?? null
                    ],
                    'istatistikler' => [
                        'toplam_ilan' => $kisi->ilanlarAsSahibi()->count(),
                        'aktif_ilan' => $kisi->ilanlarAsSahibi()->where('status', 'Aktif')->count(),
                        'ortalama_fiyat' => $kisi->ilanlarAsSahibi()->avg('fiyat'),
                    ]
                ],
                'context7_compliant' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Profil yüklenemedi'
            ], 500);
        }
    }

    /**
     * Kişi oluştur (Modal'dan)
     *
     * POST /api/kisiler
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ad' => 'required|string|max:255',
                'soyad' => 'required|string|max:255',
                'telefon' => 'required|string|max:50',
                'email' => 'nullable|email|max:255',
                'musteri_tipi' => 'required|in:ev_sahibi,satici,alici,kiraci,yatirimci',
                'status' => 'nullable|in:Aktif,Pasif',
                'notlar' => 'nullable|string'
            ]);

            $kisi = Kisi::create([
                'ad' => $validated['ad'],
                'soyad' => $validated['soyad'],
                'telefon' => $validated['telefon'],
                'email' => $validated['email'] ?? null,
                'musteri_tipi' => $validated['musteri_tipi'],
                'status' => $validated['status'] ?? 'Aktif',
                'notlar' => $validated['notlar'] ?? null,
                'danisman_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kişi başarıyla oluşturuldu',
                'data' => [
                    'id' => $kisi->id,
                    'ad' => $kisi->ad,
                    'soyad' => $kisi->soyad,
                    'tam_ad' => $kisi->tam_ad,
                    'telefon' => $kisi->telefon,
                    'email' => $kisi->email
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Kişi oluşturma hatası', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Kişi oluşturulamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI İlan Geçmişi Analizi
     *
     * GET /api/kisiler/{id}/ai-gecmis-analiz
     *
     * Context7 Kural #69: AI Geçmiş Analizi
     */
    public function getAIGecmisAnaliz($id): JsonResponse
    {
        try {
            $aiService = app(\App\Services\AI\IlanGecmisAIService::class);
            $analiz = $aiService->analyzeKisiHistory($id);

            if (!$analiz['success']) {
                return response()->json($analiz, $analiz['has_history'] === false ? 200 : 404);
            }

            return response()->json([
                'success' => true,
                'data' => $analiz,
                'context7_compliant' => true
            ]);
        } catch (\Exception $e) {
            \Log::error('AI Geçmiş Analizi hatası', [
                'kisi_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Analiz yapılamadı: ' . $e->getMessage()
            ], 500);
        }
    }
}
