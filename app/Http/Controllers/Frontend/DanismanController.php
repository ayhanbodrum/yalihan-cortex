<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DanismanController extends Controller
{
    /**
     * Danışmanlar listesi sayfası
     * Context7 Compliance: Gerçek verilerle danışman listesi
     */
    public function index(Request $request)
    {
        // Danışman rolüne sahip kullanıcıları çek
        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->with(['roles']);

        // Filtreleme
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        // Departman filtresi
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Pozisyon filtresi
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        // Status filtresi
        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->where(function ($q) {
                    $q->where('status', 1)
                        ->orWhere('status_text', 'aktif');
                });
            } elseif ($request->status === 'pasif') {
                $query->where(function ($q) {
                    $q->where('status', 0)
                        ->orWhere('status_text', 'pasif');
                });
            }
        }

        // Sıralama
        $sort = $request->get('sort', 'name_asc');
        switch ($sort) {
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'created_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('name', 'asc');
        }

        $danismanlar = $query->paginate(12)->withQueryString();

        // İstatistikler
        $danismanIds = User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->pluck('id')->toArray();

        $stats = [
            'total' => count($danismanIds),
            'status' => User::whereHas('roles', function ($q) {
                $q->where('name', 'danisman');
            })->where(function ($q) {
                $q->where('status', 1)->orWhere('status_text', 'aktif');
            })->count(), // Context7: Response key olarak 'status' kullan
            'toplam_ilan' => \App\Models\Ilan::whereIn('danisman_id', $danismanIds)
                ->where('status', 'Aktif')
                ->count(),
        ];

        // Departman ve pozisyon seçenekleri
        $departments = config('danisman.departments', []);
        $positions = config('danisman.positions', []);

        return view('frontend.danismanlar.index', compact('danismanlar', 'stats', 'departments', 'positions'));
    }

    /**
     * Danışman detay sayfası
     */
    public function show($id)
    {
        $danisman = User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->with(['roles', 'onayliDanismanYorumlari' => function ($query) {
            $query->with('kisi')->latest()->limit(10);
        }])->findOrFail($id);

        // Danışmanın aktif ilanları
        $ilanlar = \App\Models\Ilan::where('danisman_id', $danisman->id)
            ->where('status', 'Aktif')
            ->with(['il', 'ilce', 'anaKategori', 'fotograflar'])
            ->latest()
            ->limit(6)
            ->get();

        // Performans istatistikleri
        $performans = [
            'toplam_ilan' => \App\Models\Ilan::where('danisman_id', $danisman->id)->count(),
            'aktif_ilan' => \App\Models\Ilan::where('danisman_id', $danisman->id)->where('status', 'Aktif')->count(),
            'toplam_yorum' => $danisman->danismanYorumlari()->count(),
            'onayli_yorum' => $danisman->onayliDanismanYorumlari()->count(),
            'ortalama_rating' => $danisman->onayliDanismanYorumlari()->avg('rating') ?? 0,
        ];

        return view('frontend.danismanlar.show', compact('danisman', 'ilanlar', 'performans'));
    }
}
