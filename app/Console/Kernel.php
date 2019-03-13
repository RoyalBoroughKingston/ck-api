<?php

namespace App\Console;

use App\Console\Commands\Ck\AutoDeleteAuditsCommand;
use App\Console\Commands\Ck\AutoDeletePageFeedbacksCommand;
use App\Console\Commands\Ck\AutoDeleteReferralsCommand;
use App\Console\Commands\Ck\Notify\StillUnactionedReferralsCommand;
use App\Console\Commands\Ck\Notify\UnactionedReferralsCommand;
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
        $schedule->command(UnactionedReferralsCommand::class)
            ->dailyAt('09:00');

        $schedule->command(StillUnactionedReferralsCommand::class)
            ->dailyAt('09:00');

        $schedule->command(AutoDeleteReferralsCommand::class)
            ->daily();

        $schedule->command(AutoDeleteAuditsCommand::class)
            ->daily();

        $schedule->command(AutoDeletePageFeedbacksCommand::class)
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
