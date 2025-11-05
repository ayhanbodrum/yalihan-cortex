<?php

namespace App\Http\Controllers\Api;

use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;
use App\Models\Ilan;
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
            // ✅ Context7: Eloquent ile eager loading
            $query = Ilan::with(['ilanSahibi', 'danisman'])
                ->select([
                    'ilanlar.id',
                    'ilanlar.baslik',
                    'ilanlar.slug',
                    'ilanlar.status',
                    'ilanlar.fiyat',
                    'ilanlar.para_birimi',
                    'ilanlar.site_id',
                    'ilanlar.ilan_sahibi_id',
                    'ilanlar.danisman_id',
                    'ilanlar.created_at',
                ]);

            // Site ilişkisi için join (Ilan modelinde site() ilişkisi yok)
            $query->leftJoin('sites as s', 's.id', '=', 'ilanlar.site_id')
                ->addSelect('s.name as site_name');

            // Apply filters by type
            $query->where(function ($w) use ($q, $type) {
                if ($type === 'owner') {
                    $w->whereHas('ilanSahibi', function ($q2) use ($q) {
                        $q2->where('ad', 'like', "%{$q}%")
                           ->orWhere('soyad', 'like', "%{$q}%");
                    });
                } elseif ($type === 'phone') {
                    $digits = preg_replace('/\D+/', '', $q);
                    $w->whereHas('ilanSahibi', function ($q2) use ($digits) {
                        $q2->where('telefon', 'like', "%{$digits}%");
                    });
                } elseif ($type === 'site') {
                    // ✅ Context7: Sadece sites tablosundan ara
                    $w->where('s.name', 'like', "%{$q}%");
                } elseif ($type === 'advisor') {
                    $w->whereHas('danisman', function ($q2) use ($q) {
                        $q2->where('name', 'like', "%{$q}%")
                           ->orWhere('email', 'like', "%{$q}%");
                    });
                } else { // all
                    $digits = preg_replace('/\D+/', '', $q);
                    $w->where(function ($q2) use ($q, $digits) {
                        $q2->whereHas('ilanSahibi', function ($q3) use ($q, $digits) {
                            $q3->where('ad', 'like', "%{$q}%")
                               ->orWhere('soyad', 'like', "%{$q}%")
                               ->orWhere('telefon', 'like', "%{$digits}%");
                        })
                        ->orWhere('s.name', 'like', "%{$q}%")
                        ->orWhereHas('danisman', function ($q3) use ($q) {
                            $q3->where('name', 'like', "%{$q}%")
                               ->orWhere('email', 'like', "%{$q}%");
                        });
                    });
                }
            });

            $results = $query->orderBy('ilanlar.created_at', 'desc')->limit($limit)->get();

            // Format response data
            $formattedResults = $results->map(function ($ilan) {
                return [
                    'id' => $ilan->id,
                    'baslik' => $ilan->baslik,
                    'slug' => $ilan->slug,
                    'status' => $ilan->status,
                    'fiyat' => $ilan->fiyat,
                    'para_birimi' => $ilan->para_birimi,
                    'site_id' => $ilan->site_id,
                    'ilan_sahibi_id' => $ilan->ilan_sahibi_id,
                    'danisman_id' => $ilan->danisman_id,
                    'site_name' => $ilan->site_name ?? null,
                    'sahibi_ad' => $ilan->ilanSahibi->ad ?? null,
                    'sahibi_soyad' => $ilan->ilanSahibi->soyad ?? null,
                    'sahibi_telefon' => $ilan->ilanSahibi->telefon ?? null,
                    'danisman_ad' => $ilan->danisman->name ?? null,
                    'danisman_email' => $ilan->danisman->email ?? null,
                ];
            });

            return response()->json([
                'success' => true,
                'count' => $formattedResults->count(),
                'data' => $formattedResults,
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


