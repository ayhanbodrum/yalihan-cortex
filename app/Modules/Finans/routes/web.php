<?php

use Illuminate\Support\Facades\Route;

Route::get('/admin/finans/health', function () {
    return response('ok', 200);
})->name('admin.finans.health');
