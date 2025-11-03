<?php

use App\Modules\TalepAnaliz\Controllers\TalepAnalizController;
use Illuminate\Support\Facades\Route;

// Talep Analiz Rotaları - Admin Panel
Route::middleware(['auth', 'role:admin,danisman'])->prefix('admin/talep-analiz')->name('admin.talep-analiz.')->group(function () {
    // Ana analiz sayfası
    Route::get('/', [TalepAnalizController::class, 'index'])->name('index');

    // Test sayfası
    Route::get('/test', [TalepAnalizController::class, 'testSayfasi'])->name('test');

    // Tek bir talebin analiz detayı
    Route::get('/{id}', [TalepAnalizController::class, 'analizEt'])->name('show');

    // Toplu analiz işlemi
    Route::post('/toplu-analiz', [TalepAnalizController::class, 'topluAnalizEt'])->name('toplu');

    // PDF rapor oluşturma
    Route::get('/{id}/rapor', [TalepAnalizController::class, 'raporOlustur'])->name('rapor');
});
