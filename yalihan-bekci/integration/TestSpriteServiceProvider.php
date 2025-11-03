<?php

namespace App\Services\MCP;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\RunTestSpriteCommand;

class TestSpriteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('testsprite', function ($app) {
            return new TestSpriteService();
        });

        $this->mergeConfigFrom(
            __DIR__.'/config/testsprite.php', 'testsprite'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RunTestSpriteCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/config/testsprite.php' => config_path('testsprite.php'),
            ], 'testsprite-config');
        }

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('testsprite:run')->dailyAt('03:00');
        });
    }
}