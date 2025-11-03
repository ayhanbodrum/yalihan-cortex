<?php

use Illuminate\Support\Facades\Route;

Route::get('/finans/health', function () {
    return response()->json(['success' => true]);
})->name('api.finans.health');


