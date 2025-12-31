<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\DeleteExpiredSeatLocks;
use App\Console\Commands\AutoCancelBookings;
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('trips:renew-daily')->everyMinute();
        $schedule->command(DeleteExpiredSeatLocks::class)->everyMinute();
        $schedule->command(AutoCancelBookings::class)->everyMinute();

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
