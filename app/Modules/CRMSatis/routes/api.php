<?php

use Illuminate\Support\Facades\Route;

Route::get('/crm-satis/health', function () {
    return response()->json(['success' => true]);
})->name('api.crm_satis.health');


