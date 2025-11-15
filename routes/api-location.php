<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Location API Routes
|--------------------------------------------------------------------------
|
| Flynax style location filtering API endpoints
| Professional cascading dropdown system
|
*/

// Location Filter API Routes
Route::prefix('location')->group(function () {

    // Get cities by country
    Route::get('/cities/{countryId}', function ($countryId) {
        try {
            $cities = \App\Models\Il::where('ulke_id', $countryId)
                ->orderBy('il_adi')
                ->get(['id', 'il_adi', 'ulke_id']);

            return response()->json($cities);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Şehirler yüklenirken hata oluştu'
            ], 500);
        }
    });

    // Get districts by city - Context7 Standard Format
    Route::get('/districts/{cityId}', function ($cityId) {
        try {
            $districts = \App\Models\Ilce::where('il_id', $cityId)
                ->orderBy('ilce_adi')
                ->get(['id', 'ilce_adi', 'il_id'])
                ->map(function ($ilce) {
                    return [
                        'id' => $ilce->id,
                        'name' => $ilce->ilce_adi,
                        'il_id' => $ilce->il_id
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $districts,
                'ilceler' => $districts // Context7: Backward compatibility
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İlçeler yüklenirken hata oluştu'
            ], 500);
        }
    });

    // Get neighborhoods by district - Context7 Standard Format
    Route::get('/neighborhoods/{districtId}', function ($districtId) {
        try {
            $neighborhoods = \App\Models\Mahalle::where('ilce_id', $districtId)
                ->orderBy('mahalle_adi')
                ->get(['id', 'mahalle_adi', 'ilce_id'])
                ->map(function ($mahalle) {
                    return [
                        'id' => $mahalle->id,
                        'name' => $mahalle->mahalle_adi,
                        'ilce_id' => $mahalle->ilce_id
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $neighborhoods,
                'mahalleler' => $neighborhoods // Context7: Backward compatibility
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mahalleler yüklenirken hata oluştu'
            ], 500);
        }
    });

    // Get countries
    Route::get('/countries', function () {
        try {
            $countries = \App\Models\Ulke::orderBy('ulke_adi')
                ->get(['id', 'ulke_adi as name']);

            return response()->json([
                'success' => true,
                'countries' => $countries
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ülkeler yüklenirken hata oluştu'
            ], 500);
        }
    });

    // Location search (autocomplete)
    Route::get('/search', function (Illuminate\Http\Request $request) {
        try {
            $query = $request->get('q', '');

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'results' => []
                ]);
            }

            // Search in cities
            $cities = \App\Models\Il::where('il_adi', 'LIKE', "%{$query}%")
                ->where('status', 1)
                ->with('ulke:id,ulke_adi')
                ->limit(5)
                ->get(['id', 'il_adi as name', 'ulke_id']);

            // Search in districts
            $districts = \App\Models\Ilce::where('ilce_adi', 'LIKE', "%{$query}%")
                ->where('status', 1)
                ->with(['il:id,il_adi', 'il.ulke:id,ulke_adi'])
                ->limit(5)
                ->get(['id', 'ilce_adi as name', 'il_id']);

            $results = [];

            foreach ($cities as $city) {
                $results[] = [
                    'id' => $city->id,
                    'name' => $city->name,
                    'type' => 'city',
                    'full_path' => $city->name . ', ' . $city->ulke->ulke_adi,
                    'country' => $city->ulke->ulke_adi
                ];
            }

            foreach ($districts as $district) {
                $results[] = [
                    'id' => $district->id,
                    'name' => $district->name,
                    'type' => 'district',
                    'full_path' => $district->name . ', ' . $district->il->il_adi . ', ' . $district->il->ulke->ulke_adi,
                    'city' => $district->il->il_adi,
                    'country' => $district->il->ulke->ulke_adi
                ];
            }

            return response()->json([
                'success' => true,
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Arama sırasında hata oluştu'
            ], 500);
        }
    });

    // ========================================
    // TurkiyeAPI + WikiMapia Integration
    // Context7: Enhanced location with towns & villages
    // ========================================

    // Get all location types (Mahalle + Belde + Köy)
    Route::get('/all-types/{districtId}', [\App\Http\Controllers\Api\LocationController::class, 'getAllLocationTypes']);

    // Get comprehensive location profile (TurkiyeAPI + WikiMapia)
    Route::post('/profile', [\App\Http\Controllers\Api\LocationController::class, 'getLocationProfile']);

    // Get nearest residential complexes (WikiMapia)
    Route::post('/nearest-sites', [\App\Http\Controllers\Api\LocationController::class, 'getNearestSites']);

    // Get location hierarchy for SEO URLs
    Route::get('/hierarchy/{type}/{id}', function ($type, $id) {
        try {
            $hierarchy = [];

            switch ($type) {
                case 'neighborhood':
                    $neighborhood = \App\Models\Mahalle::with([
                        'ilce:id,ilce_adi,il_id',
                        'ilce.il:id,il_adi,ulke_id',
                        'ilce.il.ulke:id,ulke_adi'
                    ])->find($id);

                    if ($neighborhood) {
                        $hierarchy = [
                            'country' => $neighborhood->ilce->il->ulke,
                            'city' => $neighborhood->ilce->il,
                            'district' => $neighborhood->ilce,
                            'neighborhood' => $neighborhood
                        ];
                    }
                    break;

                case 'district':
                    $district = \App\Models\Ilce::with([
                        'il:id,il_adi,ulke_id',
                        'il.ulke:id,ulke_adi'
                    ])->find($id);

                    if ($district) {
                        $hierarchy = [
                            'country' => $district->il->ulke,
                            'city' => $district->il,
                            'district' => $district
                        ];
                    }
                    break;

                case 'city':
                    $city = \App\Models\Il::with('ulke:id,ulke_adi')->find($id);

                    if ($city) {
                        $hierarchy = [
                            'country' => $city->ulke,
                            'city' => $city
                        ];
                    }
                    break;
            }

            return response()->json([
                'success' => true,
                'hierarchy' => $hierarchy
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiyerarşi bilgisi alınırken hata oluştu'
            ], 500);
        }
    });
});
