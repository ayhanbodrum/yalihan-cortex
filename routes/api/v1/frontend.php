<?php

use App\Http\Controllers\Api\Frontend\PropertyFeedController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend API Routes (v1)
|--------------------------------------------------------------------------
|
| Public-facing API endpoints for frontend
|
*/

Route::prefix('frontend')->name('api.frontend.')->group(function () {
    // Property Feed API
    Route::prefix('properties')->name('properties.')->group(function () {
        Route::get('/', [PropertyFeedController::class, 'index'])->name('index');
        Route::get('/featured', [PropertyFeedController::class, 'featured'])->name('featured');
        Route::get('/{propertyId}', [PropertyFeedController::class, 'show'])->name('show');
    });
});
