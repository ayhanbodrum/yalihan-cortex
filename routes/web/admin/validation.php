<?php

use App\Http\Controllers\Admin\ValidationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Validation Routes
|--------------------------------------------------------------------------
|
| Routes for real-time validation of form fields
|
*/

// ✅ REMOVED: Duplicate validation routes - Already defined in routes/admin.php at line 72
// Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
//     Route::post('/validate-field', [ValidationController::class, 'validateField'])
//         ->name('validate.field');
//     Route::post('/validate-step', [ValidationController::class, 'validateStep'])
//         ->name('validate.step');
// });
