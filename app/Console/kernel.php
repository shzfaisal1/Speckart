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
        // NPS email sender
        $schedule->command('send:nps-emails')->everyMinute();

        // Daily Gift Voucher expiry sweep — runs at 01:00 every night
        // Transitions all status='active' vouchers whose expires_at < now() to 'expired'
        // Required for: My Account EXPIRED state display & refund policy enforcement
        $schedule->command('vouchers:expire')->dailyAt('01:00')->withoutOverlapping();
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