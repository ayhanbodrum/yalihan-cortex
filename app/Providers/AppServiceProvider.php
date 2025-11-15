<?php

namespace App\Providers;

use App\Models\Ilan;
use App\Modules\ModuleServiceProvider;
use App\Observers\IlanObserver;
use App\Services\AIService;
use App\Models\Setting;
use App\Services\CurrencyConversionService;
use App\Services\PlanNotlariAIService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
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
        $this->app->singleton(AIService::class, function ($app) {
            return new AIService;
        });

        // Location Service'i singleton olarak kaydet (eğer class varsa)
        try {
            if (class_exists(\App\Services\LocationService::class)) {
                $this->app->singleton(\App\Services\LocationService::class, function ($app) {
                    return new \App\Services\LocationService;
                });
            }
        } catch (\Throwable $e) {
            // LocationService not available, skip silently
        }

        // Plan Notları AI Service'i singleton olarak kaydet
        $this->app->singleton(PlanNotlariAIService::class, function ($app) {
            return new PlanNotlariAIService($app->make(AIService::class));
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

        // Address module component'ini kaydet (eğer class mevcutsa)
        try {
            if (class_exists(\App\Modules\Address\Components\AddressSelector::class)) {
                Blade::component('address-selector', \App\Modules\Address\Components\AddressSelector::class);
            }
        } catch (\Throwable $e) {
            // AddressSelector not available, skip silently
        }

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

        $appLocale = Setting::where('key', 'app_locale')->value('value');
        if ($appLocale) { app()->setLocale($appLocale); }
        $defaultCurrency = Setting::where('key', 'currency_default')->value('value');
        if ($defaultCurrency) { session(['currency' => $defaultCurrency]); }

        View::composer('components.frontend.global.topbar', function ($view) {
            $locales = config('localization.supported_locales', []);
            $currentLocale = app()->getLocale();

            /** @var CurrencyConversionService $currencyService */
            $currencyService = app(CurrencyConversionService::class);
            $currencies = $currencyService->getSupported();
            $currentCurrency = session('currency', $currencyService->getDefault());

            $view->with([
                'locales' => $locales,
                'currentLocale' => $currentLocale,
                'currencies' => $currencies,
                'currentCurrency' => $currentCurrency,
            ]);
        });
    }
}
