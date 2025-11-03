<?php

require 'vendor/autoload.php';

// Laravel uygulamasını başlat
$app = require_once 'bootstrap/app.php';

// Request oluştur ve handle et
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::create('/test-users-check', 'GET')
);

// Test route tanımla
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/test-users-check', function () {
    try {
        $users = DB::table('users')->get();
        echo "Toplam kullanıcı: " . count($users) . "\n";

        if (count($users) > 0) {
            foreach ($users as $user) {
                echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email}\n";
            }
        } else {
            echo "Kullanıcı bulunamadı\n";
        }

        return "Test tamamlandı";
    } catch (Exception $e) {
        echo "Hata: " . $e->getMessage() . "\n";
        return "Hata oluştu";
    }
});
