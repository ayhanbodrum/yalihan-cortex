<?php

namespace App\Modules\Talep;

use Illuminate\Support\ServiceProvider;

class TalepServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // TEMPORARILY DISABLED - TalepController does not exist
        /*
        // Route tanımları
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');

        // View tanımları
        $this->loadViewsFrom(__DIR__ . '/Views', 'talep');

        // Migration tanımları
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        // Seeder tanımları
        // Laravel doesn't have a built-in loadSeedersFrom method
        // Instead, seeders are typically registered in DatabaseSeeder.php
        // or run manually via artisan db:seed command
        */

        // Çeviri tanımları
        $this->loadTranslationsFrom(__DIR__.'/Lang', 'talep');

        // Config dosyalarının yayınlanması
        $this->publishes([
            __DIR__.'/Config' => config_path('talep'),
        ], 'talep-config');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // ✅ FIXED: Deprecated service yerine TalepAnaliz service'ini kullan
        // Talep modülü için servis sınıflarını bağla
        $this->app->bind('talep.analiz', function ($app) {
            // Deprecated: \App\Modules\Talep\Services\AIAnalizService
            // Yeni: TalepAnaliz modülündeki service'i kullan
            return new \App\Modules\TalepAnaliz\Services\AIAnalizService;
        });

        // Config dosyalarının birleştirilmesi
        $this->mergeConfigFrom(
            __DIR__.'/Config/talep.php',
            'talep'
        );
    }
}
