<?php

namespace App\Modules\TakimYonetimi\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TakimUyesi;
use Illuminate\Http\Request;

class TakimController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $query = TakimUyesi::with('user')->orderBy('performans_skoru', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $takimUyeleri = $query->paginate(20);

        $istatistikler = [
            'toplam' => TakimUyesi::count(),
            'aktif' => TakimUyesi::where('status', 'aktif')->count(),
            'pasif' => TakimUyesi::where('status', 'pasif')->count(),
            'ortalama_performans' => TakimUyesi::avg('performans_skoru'),
        ];

        $lokasyonlar = TakimUyesi::select('lokasyon')->distinct()->whereNotNull('lokasyon')->pluck('lokasyon');

        return view('admin.takim-yonetimi.takim.index', compact('takimUyeleri', 'istatistikler', 'lokasyonlar', 'status'));
    }

    public function performans()
    {
        $topPerformers = TakimUyesi::with('user')
            ->orderBy('performans_skoru', 'desc')
            ->take(10)
            ->get();

        return view('admin.takim-yonetimi.takim.performans', compact('topPerformers'));
    }
}
