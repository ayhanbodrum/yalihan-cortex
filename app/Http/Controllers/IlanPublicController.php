<?php

namespace App\Http\Controllers;

use App\Models\Ilan;
use App\Models\IlanKategori;
use App\Models\Il;
use Illuminate\Http\Request;

class IlanPublicController extends Controller
{
    /**
     * Frontend İlan Listesi
     */
    public function index(Request $request)
    {
        $query = Ilan::where('status', 'Aktif'); // Context7 compliant!

        // Kategori filtresi
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // İl filtresi
        if ($request->filled('il')) {
            $query->where('il_id', $request->il);
        }

        // Fiyat filtresi
        if ($request->filled('min_fiyat')) {
            $query->where('fiyat', '>=', $request->min_fiyat);
        }
        if ($request->filled('max_fiyat')) {
            $query->where('fiyat', '<=', $request->max_fiyat);
        }

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('baslik', 'like', "%{$search}%")
                  ->orWhere('aciklama', 'like', "%{$search}%");
            });
        }

        // ✅ EAGER LOADING: Select optimization ile birlikte
        $query->select([
            'id', 'baslik', 'fiyat', 'para_birimi', 'status',
            'kategori_id', 'il_id', 'ilce_id', 'slug',
            'created_at', 'updated_at'
        ]);
        
        $query->with([
            'il:id,il_adi',
            'ilce:id,ilce_adi',
            'kategori:id,name'
        ]);
        
        $ilanlar = $query->orderBy('created_at', 'desc')->paginate(12);

        // Filtreler için veriler
        $kategoriler = IlanKategori::where('parent_id', null)->get();
        $iller = Il::orderBy('il_adi')->get();

        return view('frontend.ilanlar.index', compact('ilanlar', 'kategoriler', 'iller'));
    }

    /**
     * Frontend İlan Detayı
     */
    public function show($id)
    {
        $ilan = Ilan::with([
            'il', 'ilce', 'mahalle',
            'ilanSahibi', 'danisman',
            'kategori', 'parentKategori',
            'ilanFotograflari'
        ])
        ->where('status', 'Aktif') // Context7 compliant!
        ->findOrFail($id);

        // Benzer ilanlar
        $benzerIlanlar = Ilan::with(['il', 'ilce', 'kategori'])
            ->where('status', 'Aktif') // Context7 compliant!
            ->where('kategori_id', $ilan->kategori_id)
            ->where('id', '!=', $ilan->id)
            ->limit(4)
            ->get();

        return view('frontend.ilanlar.show', compact('ilan', 'benzerIlanlar'));
    }

    /**
     * Danışman İlanları
     */
    public function danismanIlanlari($id)
    {
        $ilanlar = Ilan::with(['il', 'ilce', 'kategori'])
            ->where('status', 'Aktif') // Context7 compliant!
            ->where('danisman_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('frontend.ilanlar.danisman', compact('ilanlar'));
    }

    /**
     * Kategori İlanları
     */
    public function kategoriIlanlari($kategoriId)
    {
        $kategori = IlanKategori::findOrFail($kategoriId);

        $ilanlar = Ilan::with(['il', 'ilce', 'kategori'])
            ->where('status', 'Aktif') // Context7 compliant!
            ->where('kategori_id', $kategoriId)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('frontend.ilanlar.kategori', compact('ilanlar', 'kategori'));
    }
}
