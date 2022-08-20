<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('command:sync-flex-tasks')->everyThirtyMinutes();
        $schedule->command('command:sync-flex-calls')->everyThirtyMinutes();
        $schedule->command('command:sync-flex-conferences')->everyThirtyMinutes();
        $schedule->command('command:sync-flex-recordings')->everyThirtyMinutes();
        $schedule->command('command:sync-customers')->everyFifteenMinutes();
        $schedule->command('command:sync-customers-latlng')->everyFifteenMinutes();
        $schedule->command('command:sync-jobs')->everyFifteenMinutes();
        $schedule->command('command:send-job-number-alerts')->everyFifteenMinutes();
        $schedule->command('command:jobsCategoryEmail')->everyMinute();
        $schedule->command('command:watch-gmail')->daily();
        $schedule->command('command:sync-flex-agents')->daily();
        $schedule->command('command:update-vehicle-status')->everyTenMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
