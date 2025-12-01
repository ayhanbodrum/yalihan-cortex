<?php

namespace App\Services\MCP;

use App\Console\Commands\RunTestSpriteCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

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
            return new TestSpriteService;
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
