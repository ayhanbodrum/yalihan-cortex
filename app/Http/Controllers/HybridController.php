<?php

namespace App\Http\Controllers;

use App\Models\Ilan;
use App\Models\IlanViewDaily;
use Illuminate\Http\Request;

class HybridController extends Controller
{
    public function listings(Request $request)
    {
        $days = (int) ($request->get('days', 7));
        if ($days < 1) $days = 7; if ($days > 30) $days = 30;
        $start = now()->subDays($days - 1)->startOfDay()->toDateString();

        $publicQuery = Ilan::public();
        $crmOnlyQuery = Ilan::query()->where('crm_only', true);

        $publicCount = $publicQuery->count();
        $crmOnlyCount = $crmOnlyQuery->count();

        $topPublic = IlanViewDaily::with(['ilan:id,baslik,fiyat,para_birimi,slug'])
            ->where('tarih', '>=', $start)
            ->selectRaw('ilan_id, SUM(adet) as views')
            ->groupBy('ilan_id')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        $recentCrmOnly = $crmOnlyQuery->latest('updated_at')->limit(10)->get(['id','baslik','status','updated_at']);

        return response()->json([
            'success' => true,
            'days' => $days,
            'public' => [
                'count' => $publicCount,
                'top' => $topPublic->map(fn($i) => [
                    'ilan_id' => $i->ilan_id,
                    'baslik' => $i->ilan?->baslik,
                    'fiyat' => $i->ilan?->fiyat,
                    'para_birimi' => $i->ilan?->para_birimi,
                    'views' => (int) $i->views,
                ]),
            ],
            'crm_only' => [
                'count' => $crmOnlyCount,
                'recent' => $recentCrmOnly->map(fn($r) => [
                    'id' => $r->id,
                    'baslik' => $r->baslik,
                    'status' => $r->status,
                    'updated_at' => optional($r->updated_at)->toDateTimeString(),
                ]),
            ],
        ]);
    }
}