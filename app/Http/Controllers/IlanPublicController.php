<?php

namespace App\Http\Controllers;

use App\Http\Resources\IlanPublicResource;
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
        
        $query->with(['fotograflar']);
        $ilanlar = $query->orderBy('created_at', 'desc')->paginate(12);

        // API response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => IlanPublicResource::collection($ilanlar->items()),
                'meta' => [
                    'current_page' => $ilanlar->currentPage(),
                    'last_page' => $ilanlar->lastPage(),
                    'per_page' => $ilanlar->perPage(),
                    'total' => $ilanlar->total(),
                ]
            ]);
        }

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
            'il:id,il_adi',
            'ilce:id,ilce_adi',
            'mahalle:id,mahalle_adi',
            'kategori:id,name',
            'parentKategori:id,name',
            'fotograflar' => function($q) {
                $q->select('id', 'ilan_id', 'dosya_yolu', 'sira', 'kapak_fotografi', 'alt_text')
                  ->orderBy('sira');
            }
        ])
        ->where('status', 'Aktif') // Context7 compliant!
        ->findOrFail($id);

        // API response
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => new IlanPublicResource($ilan)
            ]);
        }

        // Benzer ilanlar (ListingNavigationService kullanıyoruz artık)
        $navigationService = app(\App\Services\ListingNavigationService::class);
        $similar = $navigationService->getSimilar($ilan, 4);

        return view('frontend.ilanlar.show', compact('ilan', 'similar'));
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
