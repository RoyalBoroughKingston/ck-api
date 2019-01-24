<?php

namespace App\Console;

use App\Console\Commands\Ck\AutoDeleteReferralsCommand;
use App\Console\Commands\Ck\SendNotificationsForUnactionedReferralsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(SendNotificationsForUnactionedReferralsCommand::class)
            ->dailyAt('09:00');

        $schedule->command(AutoDeleteReferralsCommand::class)
            ->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
