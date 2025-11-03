<?php

namespace App\Modules\CRMSatis;

use Illuminate\Support\ServiceProvider;

class CRMSatisServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'crmsatis');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register any bindings or services
    }
}
