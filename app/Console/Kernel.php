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
        // Mark abandoned carts daily
        $schedule->command('carts:mark-abandoned')
            ->daily()
            ->runInBackground();

        // Process vendor payouts monthly
        $schedule->command('vendors:process-payouts')
            ->monthlyOn(1, '00:00')
            ->runInBackground();

        // Queue job processing
        $schedule->command('queue:work --max-time=3600 --stop-when-empty')
            ->everyFiveMinutes()
            ->withoutOverlapping();

        // Clean up expired sessions daily
        $schedule->command('session:prune-stale-files')
            ->daily();

        // Backup key operations
        $schedule->command('backup:run')
            ->daily()
            ->at('02:00')
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
