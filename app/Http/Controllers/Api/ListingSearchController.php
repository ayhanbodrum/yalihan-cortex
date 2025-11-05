<?php

namespace App\Http\Controllers\Api;

use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ListingSearchController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1',
            'type' => 'nullable|in:owner,phone,site,advisor,all',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $q = trim($request->input('q'));
        $type = $request->input('type', 'all');
        $limit = (int) ($request->input('limit', 20));

        try {
            // ✅ Context7 uyumlu sorgu
            $base = DB::table('ilanlar as i')
                ->select(
                    'i.id',
                    'i.baslik',
                    'i.slug',
                    'i.status',
                    'i.fiyat',
                    'i.para_birimi',
                    'i.site_id',
                    'i.ilan_sahibi_id',
                    'i.danisman_id',
                    's.name as site_name', // ✅ Context7: site_adi → site_name
                    'k.ad as sahibi_ad',
                    'k.soyad as sahibi_soyad',
                    'k.telefon as sahibi_telefon',
                    'u.name as danisman_ad',
                    'u.email as danisman_email'
                )
                ->leftJoin('sites as s', 's.id', '=', 'i.site_id')
                ->leftJoin('kisiler as k', 'k.id', '=', 'i.ilan_sahibi_id')
                ->leftJoin('users as u', 'u.id', '=', 'i.danisman_id');

            // Apply filters by type
            $base->where(function ($w) use ($q, $type) {
                if ($type === 'owner') {
                    $w->where(function ($q2) use ($q) {
                        $q2->where('k.ad', 'like', "%{$q}%")
                           ->orWhere('k.soyad', 'like', "%{$q}%");
                    });
                } elseif ($type === 'phone') {
                    $digits = preg_replace('/\D+/', '', $q);
                    $w->where('k.telefon', 'like', "%{$digits}%");
                } elseif ($type === 'site') {
                    // ✅ Context7: Sadece sites tablosundan ara
                    $w->where('s.name', 'like', "%{$q}%");
                } elseif ($type === 'advisor') {
                    $w->where(function ($q2) use ($q) {
                        $q2->where('u.name', 'like', "%{$q}%")
                           ->orWhere('u.email', 'like', "%{$q}%");
                    });
                } else { // all
                    $digits = preg_replace('/\D+/', '', $q);
                    $w->where(function ($q2) use ($q, $digits) {
                        $q2->where('k.ad', 'like', "%{$q}%")
                           ->orWhere('k.soyad', 'like', "%{$q}%")
                           ->orWhere('k.telefon', 'like', "%{$digits}%")
                           ->orWhere('s.name', 'like', "%{$q}%") // ✅ Context7 uyumlu
                           ->orWhere('u.name', 'like', "%{$q}%")
                           ->orWhere('u.email', 'like', "%{$q}%");
                    });
                }
            });

            $results = $base->orderBy('i.created_at', 'desc')->limit($limit)->get();

            return response()->json([
                'success' => true,
                'count' => $results->count(),
                'data' => $results,
            ]);
        } catch (\Throwable $e) {
            Log::error('ListingSearchController@search error: ' . $e->getMessage(), [
                'q' => $q,
                'type' => $type,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'İlan arama sırasında hata oluştu.'
            ], 500);
        }
    }

    // Lokasyon metodları (API endpoint'leri için)

    public function getProvinces()
    {
        try {
            $provinces = Il::select('id', 'il_adi as il')
                ->orderBy('il_adi')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $provinces
            ]);
        } catch (\Throwable $e) {
            Log::error('ListingSearchController@getProvinces error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'İller yüklenirken hata oluştu.'
            ], 500);
        }
    }

    public function getDistricts($provinceId)
    {
        try {
            $districts = Ilce::select('id', 'ilce_adi as ilce')
                ->where('il_id', $provinceId)
                ->orderBy('ilce_adi')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $districts
            ]);
        } catch (\Throwable $e) {
            Log::error('ListingSearchController@getDistricts error: ' . $e->getMessage(), [
                'province_id' => $provinceId
            ]);
            return response()->json([
                'success' => false,
                'message' => 'İlçeler yüklenirken hata oluştu.'
            ], 500);
        }
    }

    public function getNeighborhoods($districtId)
    {
        try {
            $neighborhoods = Mahalle::select('id', 'mahalle_adi as mahalle')
                ->where('ilce_id', $districtId)
                ->orderBy('mahalle_adi')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $neighborhoods
            ]);
        } catch (\Throwable $e) {
            Log::error('ListingSearchController@getNeighborhoods error: ' . $e->getMessage(), [
                'district_id' => $districtId
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Mahalleler yüklenirken hata oluştu.'
            ], 500);
        }
    }
}


