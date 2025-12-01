<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ilan;
use Illuminate\Http\Request;

class IlanSearchController extends Controller
{
    public function findByPortalId(Request $request)
    {
        $request->validate([
            'portal' => 'required|string|in:sahibinden,emlakjet,hepsiemlak,zingat,hurriyetemlak',
            'id' => 'required|string|min:2',
        ]);
        $portal = $request->input('portal');
        $id = trim((string) $request->input('id'));
        $map = [
            'sahibinden' => 'sahibinden_id',
            'emlakjet' => 'emlakjet_id',
            'hepsiemlak' => 'hepsiemlak_id',
            'zingat' => 'zingat_id',
            'hurriyetemlak' => 'hurriyetemlak_id',
        ];
        $col = $map[$portal] ?? null;
        if (! $col) {
            return response()->json(['success' => false, 'message' => 'Portal desteklenmiyor'], 422);
        }
        $normalizer = new \App\Services\Portal\PortalIdNormalizer;
        $nid = $normalizer->normalizeProviderId($portal, $id);
        $ilan = Ilan::where($col, $nid)->first();
        if (! $ilan) {
            return response()->json(['success' => false, 'message' => 'Bulunamadı'], 404);
        }
        $data = [
            'id' => $ilan->id,
            'baslik' => $ilan->baslik,
            'kategori' => $ilan->anaKategori->name ?? null,
            'status' => $ilan->status ?? null,
            'fiyat' => $ilan->fiyat ?? null,
            'para_birimi' => $ilan->para_birimi ?? null,
            'created_at' => (string) $ilan->created_at,
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function findByReferans(string $referansNo)
    {
        $ilan = Ilan::where('referans_no', $referansNo)->first();
        if (! $ilan) {
            return response()->json(['success' => false, 'message' => 'Bulunamadı'], 404);
        }
        $data = [
            'id' => $ilan->id,
            'baslik' => $ilan->baslik,
            'kategori' => $ilan->anaKategori->name ?? null,
            'status' => $ilan->status ?? null,
            'fiyat' => $ilan->fiyat ?? null,
            'para_birimi' => $ilan->para_birimi ?? null,
            'created_at' => (string) $ilan->created_at,
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }
}
