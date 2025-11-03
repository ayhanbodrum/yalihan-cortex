<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kisi;
use App\Models\Talep;
use App\Models\Eslesme;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CRMController extends AdminController
{
    public function index(Request $request)
    {
        $stats = [
            'toplam_kisi' => Kisi::count(),
            'active_kisi' => Kisi::where('status', 'Aktif')->count(),
            'toplam_talep' => Talep::count(),
            'active_talep' => Talep::where('status', 'Aktif')->count(),
            'toplam_eslesme' => Eslesme::count(),
            'basarili_eslesme' => Eslesme::where('status', 'Başarılı')->count(),
        ];

        $aiOnerileri = [
            'mukerrer_kisiler' => Kisi::select('email')
                ->whereNotNull('email')
                ->groupBy('email')
                ->havingRaw('COUNT(*) > 1')
                ->get()
                ->map(function($item) {
                    return [
                        'email' => $item->email,
                        'sayi' => Kisi::where('email', $item->email)->count(),
                        'kisiler' => Kisi::where('email', $item->email)->get(['id', 'ad', 'soyad', 'telefon'])
                    ];
                }),
            'eksik_bilgiler' => Kisi::where(function($query) {
                $query->whereNull('email')
                      ->orWhereNull('telefon')
                      ->orWhereNull('ad')
                      ->orWhereNull('soyad');
            })->count(),
            'eslesmeyen_talepler' => Talep::whereDoesntHave('eslesmeler')->count(),
            'yuksek_skorlu_eslesmeler' => Eslesme::where('skor', '>=', 8)->count(),
        ];
        
        // ✅ Etiketler (needed by customers/index filter)
        $etiketler = \App\Models\Etiket::orderBy('name')->get();

        return view('admin.crm.index', compact('stats', 'aiOnerileri', 'etiketler'));
    }

    public function aiAnalyze(Request $request)
    {
        $type = $request->get('type', 'all');

        $analysis = [];

        if ($type === 'all' || $type === 'duplicates') {
            $analysis['duplicates'] = Kisi::select('email')
                ->whereNotNull('email')
                ->groupBy('email')
                ->havingRaw('COUNT(*) > 1')
                ->get()
                ->map(function($item) {
                    return [
                        'email' => $item->email,
                        'count' => Kisi::where('email', $item->email)->count(),
                        'records' => Kisi::where('email', $item->email)->get(['id', 'ad', 'soyad', 'telefon', 'created_at'])
                    ];
                });
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
            $analysis['matching_issues'] = [
                'unmatched_requests' => Talep::whereDoesntHave('eslesmeler')->count(),
                'low_score_matches' => Eslesme::where('skor', '<', 5)->count(),
                'high_score_matches' => Eslesme::where('skor', '>=', 8)->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'timestamp' => now()->toISOString()
        ]);
    }

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

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} mükerrer kayıt silindi",
            'deleted_count' => $deletedCount
        ]);
    }

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
