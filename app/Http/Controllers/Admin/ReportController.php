<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController;
use App\Models\Ilan;
use App\Models\IlanViewDaily;
use Illuminate\Http\Request;

class ReportController extends AdminController
{
    public function visits(Request $request)
    {
        $days = (int) ($request->get('days', 7));
        if ($days < 1) $days = 7; if ($days > 30) $days = 30;
        $start = now()->subDays($days - 1)->startOfDay()->toDateString();

        $daily = IlanViewDaily::where('tarih', '>=', $start)
            ->selectRaw('tarih, SUM(adet) as total')
            ->groupBy('tarih')
            ->orderBy('tarih')
            ->get();

        $device = IlanViewDaily::where('tarih', '>=', $start)
            ->selectRaw('cihaz, SUM(adet) as total')
            ->groupBy('cihaz')
            ->get();

        $topListings = IlanViewDaily::with(['ilan:id,baslik,fiyat,para_birimi,slug'])
            ->where('tarih', '>=', $start)
            ->selectRaw('ilan_id, SUM(adet) as views')
            ->groupBy('ilan_id')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        $totalViews = (int) ($daily->sum('total'));
        $publicListings = Ilan::public()->count();

        return view('admin.raporlar.ziyaret', compact(
            'days', 'daily', 'device', 'topListings', 'totalViews', 'publicListings'
        ));
    }
}