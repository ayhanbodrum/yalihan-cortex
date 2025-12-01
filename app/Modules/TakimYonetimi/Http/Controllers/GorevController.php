<?php

namespace App\Modules\TakimYonetimi\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Gorev;
use Illuminate\Http\Request;

class GorevController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $query = Gorev::with(['atananUser', 'olusturanUser'])->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $gorevler = $query->paginate(20);

        // Context7: Danışmanlar listesi (view için gerekli)
        $danismanlar = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->select(['id', 'name', 'email'])->get();

        $istatistikler = [
            'toplam' => Gorev::count(),
            'beklemede' => Gorev::where('status', 'Beklemede')->count(),
            'devam_ediyor' => Gorev::where('status', 'Devam Ediyor')->count(),
            'tamamlandi' => Gorev::where('status', 'Tamamlandı')->count(),
        ];

        return view('admin.takim-yonetimi.gorevler.index', compact('gorevler', 'istatistikler', 'status', 'danismanlar'));
    }
}
