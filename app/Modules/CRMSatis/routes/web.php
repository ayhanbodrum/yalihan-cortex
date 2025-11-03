<?php

use Illuminate\Support\Facades\Route;

Route::get('/admin/crm-satis/health', function () {
    return response('ok', 200);
})->name('admin.crm_satis.health');


