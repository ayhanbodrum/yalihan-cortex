<?php

namespace App\Providers;

use App\Models\Ilan;
use App\Modules\ModuleServiceProvider;
use App\Observers\IlanObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

/**
 * @property \\Illuminate\\Contracts\\Foundation\\Application $app
 *
 * @extends ServiceProvider
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Modül servisini kaydediyoruz
        $this->app->register(ModuleServiceProvider::class);

        // AI Service'i singleton olarak kaydet
        $this->app->singleton(\App\Services\AIService::class, function ($app) {
            return new \App\Services\AIService;
        });

        // Location Service'i singleton olarak kaydet
        $this->app->singleton(\App\Services\LocationService::class, function ($app) {
            return new \App\Services\LocationService;
        });

        // Plan Notları AI Service'i singleton olarak kaydet
        $this->app->singleton(\App\Services\PlanNotlariAIService::class, function ($app) {
            return new \App\Services\PlanNotlariAIService($app->make(\App\Services\AIService::class));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Blade bileşenlerini kaydet
        Blade::componentNamespace('App\View\Components', 'app');

        // Address module component'ini kaydet
        Blade::component('address-selector', \App\Modules\Address\Components\AddressSelector::class);

        // Str sınıfını global olarak kullanılabilir hale getir
        if (! class_exists('Str')) {
            class_alias(\Illuminate\Support\Str::class, 'Str');
        }

        Ilan::observe(IlanObserver::class);

        // Google Drive Storage (for Laravel Backup)
        try {
            Storage::extend('google', function ($app, $config) {
                $client = new \Google\Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);
                
                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folder'] ?? '/');
                
                return new \League\Flysystem\Filesystem($adapter, ['case_sensitive' => false]);
            });
        } catch (\Exception $e) {
            // Google Drive not configured yet, skip silently
        }
    }
}
