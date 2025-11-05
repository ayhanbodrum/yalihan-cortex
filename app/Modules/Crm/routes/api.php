<?php

use App\Modules\Crm\Controllers\AktiviteApiController;
use App\Modules\Crm\Controllers\EtiketApiController;
use App\Modules\Crm\Controllers\KisiApiController;
use Illuminate\Support\Facades\Route;

// ✅ API route - CSRF koruması gerekmez (token-based auth)
// Not: web middleware yerine api middleware kullanılmalı, ancak auth için web gerekli ise bu şekilde bırakılabilir
Route::prefix('api/crm')->middleware(['api', 'auth:sanctum'])->name('api.crm.')->group(function () {
    // Kişi Rotaları
    Route::apiResource('kisiler', KisiApiController::class);
    Route::get('kisi/search', [KisiApiController::class, 'search'])->name('kisi.search');
    Route::post('kisi/create', [KisiApiController::class, 'store'])->name('kisi.create');
    Route::post('kisiler/{kisi}/etiketler', [KisiApiController::class, 'attachEtiket'])->name('kisiler.etiketler.attach');
    Route::delete('kisiler/{kisi}/etiketler/{etiket}', [KisiApiController::class, 'detachEtiket'])->name('kisiler.etiketler.detach');
    Route::put('kisiler/{kisi}/etiketler', [KisiApiController::class, 'syncEtiketler'])->name('kisiler.etiketler.sync');

    // Etiket Rotaları
    Route::apiResource('etiketler', EtiketApiController::class);

    // Aktivite Rotaları
    Route::apiResource('aktiviteler', AktiviteApiController::class);
    Route::get('kisiler/{kisi}/aktiviteler', [AktiviteApiController::class, 'kisiAktiviteleri'])->name('kisiler.aktiviteler');
    // Route::get('danismanlar/{danisman}/aktiviteler', [AktiviteApiController::class, 'getAktivitelerByDanisman'])->name('danismanlar.aktiviteler'); // Danışman modeli User olduğu için bu rota Auth modülünde olabilir veya UserApiController gibi bir yerde tanımlanabilir.
});
