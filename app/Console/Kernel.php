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

        // Process vendor payouts daily (checks configured day in settings)
        $schedule->command('vendors:process-payouts')
            ->daily()
            ->at('01:00')
            ->runInBackground()
            ->onSuccess(function () {
                \Illuminate\Support\Facades\Log::info('Vendor payouts processed successfully');
            })
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::error('Vendor payout processing failed');
            });

        // Queue job processing: use --once for shared-host friendly cron execution
        $schedule->command('queue:work --once')
            ->everyMinute()
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
