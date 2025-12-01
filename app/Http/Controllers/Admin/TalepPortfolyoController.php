<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ilan;
use App\Models\Talep;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

/**
 * Talep Portfolyo Controller
 *
 * Context7 Standardı: C7-TALEP-PORTFOLYO-2025-11-26
 * AI-Powered Talep-Portföy Eşleştirme Sistemi
 */
class TalepPortfolyoController extends Controller
{
    /**
     * Talep Portfolyo ana sayfası.
     * Context7: N+1 Query önleme - eager loading
     */
    public function index(Request $request)
    {
        // Context7: Eager loading ile N+1 query önleme
        $talepler = Talep::with(['kisi:id,ad,soyad,telefon', 'il:id,il_adi', 'ilce:id,ilce_adi'])
            ->latest()
            ->paginate(20);

        // Context7: İstatistikleri cache ile hesapla
        $talepStats = Cache::remember('talep_portfolyo_stats', 300, function () {
            return [
                'toplam_talep' => Talep::count(),
                'status_talep' => Talep::where('status', 'Aktif')->orWhere('status', 1)->count(),
                'acil_talep' => Talep::where('status', 'Acil')->orWhere('status', 'acil')->count(),
            ];
        });

        $portfolyoStats = Cache::remember('portfolyo_stats', 300, function () {
            return [
                'toplam_ilan' => Ilan::where('status', 'Aktif')->orWhere('status', 1)->count(),
            ];
        });

        return view('admin.talep-portfolyo.index', compact('talepler', 'talepStats', 'portfolyoStats'));
    }
}
