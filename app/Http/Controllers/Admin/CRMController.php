<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kisi;
use App\Models\Talep;
use App\Models\Eslesme;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class CRMController extends AdminController
{
    /**
     * Display CRM dashboard
     * Context7: CRM ana sayfası ve istatistikler
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // ✅ CACHE: İstatistikleri cache ile optimize et (1800s = 30 dakika)
        $stats = Cache::remember('crm_dashboard_stats', 1800, function () {
            return [
                'toplam_kisi' => Kisi::count(),
                'active_kisi' => Kisi::where('status', 'Aktif')->count(),
                'toplam_talep' => Talep::count(),
                'active_talep' => Talep::where('status', 'Aktif')->count(),
                'toplam_eslesme' => Eslesme::count(),
                'basarili_eslesme' => Eslesme::where('status', 'Başarılı')->count(),
            ];
        });

        // ✅ CACHE: Mükerrer email analizi cache ile optimize et (3600s = 1 saat)
        $mukerrerKisiler = Cache::remember('crm_mukerrer_kisiler', 3600, function () {
            // ✅ OPTIMIZED: N+1 query önlendi - Tüm mükerrer email'leri tek query'de al
            $mukerrerEmails = Kisi::select('email')
                ->whereNotNull('email')
                ->groupBy('email')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('email');

            // ✅ EAGER LOADING: Tüm mükerrer kişileri tek query'de yükle
            return Kisi::whereIn('email', $mukerrerEmails)
                ->select(['id', 'ad', 'soyad', 'telefon', 'email'])
                ->get()
                ->groupBy('email')
                ->map(function($kisiler, $email) {
                    return [
                        'email' => $email,
                        'sayi' => $kisiler->count(),
                        'kisiler' => $kisiler->map(function($kisi) {
                            return [
                                'id' => $kisi->id,
                                'ad' => $kisi->ad,
                                'soyad' => $kisi->soyad,
                                'telefon' => $kisi->telefon
                            ];
                        })->values()
                    ];
                })
                ->values();
        });

        // ✅ CACHE: AI önerileri cache ile optimize et (1800s = 30 dakika)
        $aiOnerileri = Cache::remember('crm_ai_onerileri', 1800, function () {
            // Check if skor column exists before querying
            $hasSkorColumn = Schema::hasColumn('eslesmeler', 'skor');

            // Safely get high score matches count with fallback
            $yuksekSkorluEslesmeler = 0;
            if ($hasSkorColumn) {
                try {
                    $yuksekSkorluEslesmeler = Eslesme::where('skor', '>=', 8)->count();
                } catch (\Exception $e) {
                    // Column might not exist yet, return 0
                    $yuksekSkorluEslesmeler = 0;
                }
            }

            return [
                'mukerrer_kisiler' => [], // Yukarıda cache'lenmiş olarak alınacak
                'eksik_bilgiler' => Kisi::where(function($query) {
                    $query->whereNull('email')
                          ->orWhereNull('telefon')
                          ->orWhereNull('ad')
                          ->orWhereNull('soyad');
                })->count(),
                'eslesmeyen_talepler' => Talep::whereDoesntHave('eslesmeler')->count(),
                'yuksek_skorlu_eslesmeler' => $yuksekSkorluEslesmeler,
            ];
        });

        // Mükerrer kişileri ekle
        $aiOnerileri['mukerrer_kisiler'] = $mukerrerKisiler;

        // ✅ CACHE: Etiketler cache ile optimize et (7200s = 2 saat)
        $etiketler = Cache::remember('crm_etiketler', 7200, function () {
            return \App\Models\Etiket::select(['id', 'name', 'color', 'status'])
                ->orderBy('name')
                ->get();
        });

        return view('admin.crm.index', compact('stats', 'aiOnerileri', 'etiketler'));
    }

    /**
     * AI analysis for CRM data
     * Context7: AI destekli CRM analizi
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function aiAnalyze(Request $request)
    {
        $type = $request->get('type', 'all');

        $analysis = [];

        if ($type === 'all' || $type === 'duplicates') {
            // ✅ OPTIMIZED: N+1 query önlendi
            $duplicateEmails = Kisi::select('email')
                ->whereNotNull('email')
                ->groupBy('email')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('email');

            // ✅ EAGER LOADING: Tüm duplicate kayıtları tek query'de yükle
            $duplicateRecords = Kisi::whereIn('email', $duplicateEmails)
                ->select(['id', 'ad', 'soyad', 'telefon', 'email', 'created_at'])
                ->get()
                ->groupBy('email')
                ->map(function($records, $email) {
                    return [
                        'email' => $email,
                        'count' => $records->count(),
                        'records' => $records->map(function($record) {
                            return [
                                'id' => $record->id,
                                'ad' => $record->ad,
                                'soyad' => $record->soyad,
                                'telefon' => $record->telefon,
                                'created_at' => $record->created_at
                            ];
                        })->values()
                    ];
                })
                ->values();

            $analysis['duplicates'] = $duplicateRecords;
        }

        if ($type === 'all' || $type === 'missing') {
            $analysis['missing_data'] = [
                'no_email' => Kisi::whereNull('email')->count(),
                'no_phone' => Kisi::whereNull('telefon')->count(),
                'no_name' => Kisi::where(function($q) {
                    $q->whereNull('ad')->orWhereNull('soyad');
                })->count(),
            ];
        }

        if ($type === 'all' || $type === 'matching') {
            // Check if skor column exists before querying
            $hasSkorColumn = Schema::hasColumn('eslesmeler', 'skor');

            // Safely get match counts with fallback
            $lowScoreMatches = 0;
            $highScoreMatches = 0;

            if ($hasSkorColumn) {
                try {
                    $lowScoreMatches = Eslesme::where('skor', '<', 5)->count();
                    $highScoreMatches = Eslesme::where('skor', '>=', 8)->count();
                } catch (\Exception $e) {
                    // Column might not exist yet, use defaults (0)
                }
            }

            $analysis['matching_issues'] = [
                'unmatched_requests' => Talep::whereDoesntHave('eslesmeler')->count(),
                'low_score_matches' => $lowScoreMatches,
                'high_score_matches' => $highScoreMatches,
            ];
        }

        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Fix duplicate customer records
     * Context7: Mükerrer kayıt düzeltme
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fixDuplicates(Request $request)
    {
        $email = $request->get('email');
        $keepId = $request->get('keep_id');

        if (!$email || !$keepId) {
            return response()->json(['success' => false, 'message' => 'Geçersiz parametreler']);
        }

        $duplicates = Kisi::where('email', $email)->where('id', '!=', $keepId);
        $deletedCount = $duplicates->count();
        $duplicates->delete();

        // ✅ CACHE INVALIDATION: İlgili cache'leri temizle
        Cache::forget('crm_dashboard_stats');
        Cache::forget('crm_mukerrer_kisiler');
        Cache::forget('crm_ai_onerileri');

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} mükerrer kayıt silindi",
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Generate CRM report
     * Context7: CRM raporu oluşturma
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateReport(Request $request)
    {
        $report = [
            'generated_at' => now()->format('d.m.Y H:i'),
            'summary' => [
                'total_customers' => Kisi::count(),
                'active_customers' => Kisi::where('status', 'Aktif')->count(),
                'total_requests' => Talep::count(),
                'total_matches' => Eslesme::count(),
                'success_rate' => Eslesme::count() > 0 ? round((Eslesme::where('status', 'Başarılı')->count() / Eslesme::count()) * 100, 2) : 0,
            ],
            'ai_insights' => [
                'duplicate_emails' => Kisi::select('email')
                    ->whereNotNull('email')
                    ->groupBy('email')
                    ->havingRaw('COUNT(*) > 1')
                    ->count(),
                'missing_data_percentage' => round((Kisi::where(function($q) {
                    $q->whereNull('email')->orWhereNull('telefon')->orWhereNull('ad')->orWhereNull('soyad');
                })->count() / Kisi::count()) * 100, 2),
                'unmatched_requests' => Talep::whereDoesntHave('eslesmeler')->count(),
            ],
            'recommendations' => [
                'Clean up duplicate customer records',
                'Complete missing customer information',
                'Review unmatched requests for better matching',
                'Optimize matching algorithm parameters'
            ]
        ];

        return response()->json([
            'success' => true,
            'report' => $report
        ]);
    }
}
