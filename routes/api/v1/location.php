<?php

use App\Http\Controllers\Api\LocationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Location API Routes (v1)
|--------------------------------------------------------------------------
|
| Context7 Standard Location API Endpoints
| Professional cascading dropdown system
|
*/

Route::prefix('location')->name('api.location.')->group(function () {
    // ✅ Context7: Standard Location Endpoints
    Route::get('/districts/{id}', [LocationController::class, 'getDistrictsByProvince'])
        ->name('districts');

    Route::get('/neighborhoods/{id}', [LocationController::class, 'getNeighborhoodsByDistrict'])
        ->name('neighborhoods');

    Route::get('/neighborhood/{id}/coordinates', [LocationController::class, 'getNeighborhoodCoordinates'])
        ->name('neighborhood.coordinates');

    Route::get('/provinces', [LocationController::class, 'getProvinces'])
        ->name('provinces');

    // 📍 Gelişmiş Lokasyon API (Context7 Kural #75)
    Route::post('/geocode', [LocationController::class, 'geocode'])
        ->name('geocode');

    Route::post('/reverse-geocode', [LocationController::class, 'reverseGeocode'])
        ->name('reverse-geocode');

    Route::get('/nearby/{lat}/{lng}/{radius?}', [LocationController::class, 'findNearby'])
        ->name('nearby');

    Route::post('/validate-address', [LocationController::class, 'validateAddress'])
        ->name('validate-address');

    // TurkiyeAPI + WikiMapia Integration
    Route::get('/all-types/{districtId}', [LocationController::class, 'getAllLocationTypes'])
        ->name('all-types');

    Route::post('/profile', [LocationController::class, 'getLocationProfile'])
        ->name('profile');

    Route::post('/nearest-sites', [LocationController::class, 'getNearestSites'])
        ->name('nearest-sites');

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
                'message' => 'Şehirler yüklenirken hata oluştu',
            ], 500);
        }
    })->name('cities');

    // Get countries
    Route::get('/countries', function () {
        try {
            $countries = \App\Models\Ulke::orderBy('ulke_adi')
                ->get(['id', 'ulke_adi as name']);

            return response()->json([
                'success' => true,
                'countries' => $countries,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ülkeler yüklenirken hata oluştu',
            ], 500);
        }
    })->name('countries');

    // Location search (autocomplete)
    Route::get('/search', function (Illuminate\Http\Request $request) {
        try {
            $query = $request->get('q', '');

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'results' => [],
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
                    'full_path' => $city->name.', '.$city->ulke->ulke_adi,
                    'country' => $city->ulke->ulke_adi,
                ];
            }

            foreach ($districts as $district) {
                $results[] = [
                    'id' => $district->id,
                    'name' => $district->name,
                    'type' => 'district',
                    'full_path' => $district->name.', '.$district->il->il_adi.', '.$district->il->ulke->ulke_adi,
                    'city' => $district->il->il_adi,
                    'country' => $district->il->ulke->ulke_adi,
                ];
            }

            return response()->json([
                'success' => true,
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Arama sırasında hata oluştu',
            ], 500);
        }
    })->name('search');

    // Get location hierarchy for SEO URLs
    Route::get('/hierarchy/{type}/{id}', function ($type, $id) {
        try {
            $hierarchy = [];

            switch ($type) {
                case 'neighborhood':
                    $neighborhood = \App\Models\Mahalle::with([
                        'ilce:id,ilce_adi,il_id',
                        'ilce.il:id,il_adi,ulke_id',
                        'ilce.il.ulke:id,ulke_adi',
                    ])->find($id);

                    if ($neighborhood) {
                        $hierarchy = [
                            'country' => $neighborhood->ilce->il->ulke,
                            'city' => $neighborhood->ilce->il,
                            'district' => $neighborhood->ilce,
                            'neighborhood' => $neighborhood,
                        ];
                    }
                    break;

                case 'district':
                    $district = \App\Models\Ilce::with([
                        'il:id,il_adi,ulke_id',
                        'il.ulke:id,ulke_adi',
                    ])->find($id);

                    if ($district) {
                        $hierarchy = [
                            'country' => $district->il->ulke,
                            'city' => $district->il,
                            'district' => $district,
                        ];
                    }
                    break;

                case 'city':
                    $city = \App\Models\Il::with('ulke:id,ulke_adi')->find($id);

                    if ($city) {
                        $hierarchy = [
                            'country' => $city->ulke,
                            'city' => $city,
                        ];
                    }
                    break;
            }

            return response()->json([
                'success' => true,
                'hierarchy' => $hierarchy,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiyerarşi bilgisi alınırken hata oluştu',
            ], 500);
        }
    })->name('hierarchy');
});
