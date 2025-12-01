<?php

namespace App\Http\Controllers\Admin;

use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;
use App\Models\SiteApartman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiteApartmanController extends AdminController
{
    /**
     * Site/Apartman listesi
     */
    public function index()
    {
        $sites = SiteApartman::with(['il', 'ilce', 'mahalle', 'creator'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.site-apartman.index', compact('sites'));
    }

    /**
     * Yeni site/apartman oluşturma formu
     */
    public function create()
    {
        $iller = Il::orderBy('il_adi')->get();
        $ilceler = collect();
        $mahalleler = collect();

        return view('admin.site-apartman.create', compact('iller', 'ilceler', 'mahalleler'));
    }

    /**
     * Site/Apartman kaydetme
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'toplam_daire_sayisi' => 'nullable|integer|min:1',
            'adres' => 'nullable|string|max:500',
            'il_id' => 'nullable|exists:iller,id',
            'ilce_id' => 'nullable|exists:ilceler,id',
            'mahalle_id' => 'nullable|exists:mahalleler,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'site_ozellikleri' => 'nullable|array',
            'status' => 'required|in:active,inactive,pending',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $site = SiteApartman::create([
            'name' => $request->name,
            'toplam_daire_sayisi' => $request->toplam_daire_sayisi,
            'adres' => $request->adres,
            'il_id' => $request->il_id,
            'ilce_id' => $request->ilce_id,
            'mahalle_id' => $request->mahalle_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'site_ozellikleri' => $request->site_ozellikleri,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.site-apartman.index')
            ->with('success', 'Site/Apartman başarıyla oluşturuldu.');
    }

    /**
     * Site/Apartman düzenleme formu
     */
    public function edit(SiteApartman $siteApartman)
    {
        $iller = Il::orderBy('il_adi')->get();
        $ilceler = $siteApartman->il_id ? Ilce::where('il_id', $siteApartman->il_id)->orderBy('ilce_adi')->get() : collect();
        $mahalleler = $siteApartman->ilce_id ? Mahalle::where('ilce_id', $siteApartman->ilce_id)->orderBy('mahalle_adi')->get() : collect();

        return view('admin.site-apartman.edit', compact('siteApartman', 'iller', 'ilceler', 'mahalleler'));
    }

    /**
     * Site/Apartman güncelleme
     */
    public function update(Request $request, SiteApartman $siteApartman)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'toplam_daire_sayisi' => 'nullable|integer|min:1',
            'adres' => 'nullable|string|max:500',
            'il_id' => 'nullable|exists:iller,id',
            'ilce_id' => 'nullable|exists:ilceler,id',
            'mahalle_id' => 'nullable|exists:mahalleler,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'site_ozellikleri' => 'nullable|array',
            'status' => 'required|in:active,inactive,pending',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $siteApartman->update([
            'name' => $request->name,
            'toplam_daire_sayisi' => $request->toplam_daire_sayisi,
            'adres' => $request->adres,
            'il_id' => $request->il_id,
            'ilce_id' => $request->ilce_id,
            'mahalle_id' => $request->mahalle_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'site_ozellikleri' => $request->site_ozellikleri,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.site-apartman.index')
            ->with('success', 'Site/Apartman başarıyla güncellendi.');
    }

    /**
     * Site/Apartman silme
     */
    public function destroy(SiteApartman $siteApartman)
    {
        // İlan sayısını kontrol et
        if ($siteApartman->ilanlar()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Bu site/apartmana ait ilanlar bulunduğu için silinemez.');
        }

        $siteApartman->delete();

        return redirect()->route('admin.site-apartman.index')
            ->with('success', 'Site/Apartman başarıyla silindi.');
    }

    /**
     * AJAX: İlçeleri getir
     */
    public function getIlceler(Request $request)
    {
        $ilId = $request->input('il_id');

        if (! $ilId) {
            return response()->json(['success' => false, 'message' => 'İl ID gerekli']);
        }

        $ilceler = Ilce::where('il_id', $ilId)
            ->orderBy('ilce_adi')
            ->select(['id', 'ilce_adi as name'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $ilceler,
        ]);
    }

    /**
     * AJAX: Mahalleleri getir
     */
    public function getMahalleler(Request $request)
    {
        $ilceId = $request->input('ilce_id');

        if (! $ilceId) {
            return response()->json(['success' => false, 'message' => 'İlçe ID gerekli']);
        }

        $mahalleler = Mahalle::where('ilce_id', $ilceId)
            ->orderBy('mahalle_adi')
            ->select(['id', 'mahalle_adi as name'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $mahalleler,
        ]);
    }

    /**
     * AJAX: Site/Apartman arama
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        $limit = $request->input('limit', 20);

        if (! $query) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $sites = SiteApartman::where('name', 'like', "%{$query}%")
            ->orWhere('adres', 'like', "%{$query}%")
            ->with(['il', 'ilce'])
            ->limit($limit)
            ->get()
            ->map(function ($site) {
                return [
                    'id' => $site->id,
                    'name' => $site->name,
                    'full_address' => $site->full_address,
                    'toplam_daire_sayisi' => $site->toplam_daire_sayisi,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $sites,
        ]);
    }
}
