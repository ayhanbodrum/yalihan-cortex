<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // TestSprite otomatik öğrenme - Her gün 03:00'da
        $schedule->command('testsprite:auto-learn')
            ->dailyAt('03:00')
            ->appendOutputTo(storage_path('logs/testsprite-auto-learn.log'));

        // TestSprite otomatik test - Her 6 saatte bir
        $schedule->exec('cd ' . base_path('testsprite') . ' && ./test-run.sh')
            ->everySixHours()
            ->appendOutputTo(storage_path('logs/testsprite-tests.log'));

        // Context7 compliance check - Haftalık
        $schedule->command('context7:check')
            ->weekly()
            ->sundays()
            ->at('02:00')
            ->appendOutputTo(storage_path('logs/context7-check.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
