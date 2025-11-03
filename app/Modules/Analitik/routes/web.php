<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Analitik\Controllers\Admin\DashboardController;
use App\Modules\Analitik\Controllers\Admin\RaporController;
use App\Modules\Analitik\Controllers\Admin\IstatistikController;

Route::prefix('admin/analitik')->name('admin.analitik.')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard.data');

    // Raporlar
    Route::prefix('raporlar')->name('raporlar.')->group(function () {
        Route::get('/', [RaporController::class, 'index'])->name('index');
        Route::get('/ilan-raporu', [RaporController::class, 'ilanRaporu'])->name('ilan');
        Route::get('/satis-raporu', [RaporController::class, 'satisRaporu'])->name('satis');
        Route::get('/finans-raporu', [RaporController::class, 'finansRaporu'])->name('finans');
        Route::get('/musteri-raporu', [RaporController::class, 'musteriRaporu'])->name('musteri');
        Route::get('/performans-raporu', [RaporController::class, 'performansRaporu'])->name('performans');
        Route::post('/export', [RaporController::class, 'export'])->name('export');
    });

    // İstatistikler
    Route::prefix('istatistikler')->name('istatistikler.')->group(function () {
        Route::get('/', [IstatistikController::class, 'index'])->name('index');
        Route::get('/genel', [IstatistikController::class, 'genel'])->name('genel');
        Route::get('/ilan', [IstatistikController::class, 'ilan'])->name('ilan');
        Route::get('/satis', [IstatistikController::class, 'satis'])->name('satis');
        Route::get('/finans', [IstatistikController::class, 'finans'])->name('finans');
        Route::get('/musteri', [IstatistikController::class, 'musteri'])->name('musteri');
    });
});
