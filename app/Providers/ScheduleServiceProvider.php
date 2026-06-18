<?php

namespace App\Providers;

use App\Console\Commands\SendRsvpReminders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(Schedule $schedule): void
    {
        $schedule->command(SendRsvpReminders::class)->dailyAt('08:00');
    }
}
