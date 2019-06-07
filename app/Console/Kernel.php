<?php

namespace App\Console;

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
        $schedule->command(Commands\Ck\Notify\StaleServicesCommand::class)
            ->dailyAt('09:00');

        $schedule->command(Commands\Ck\Notify\UnactionedReferralsCommand::class)
            ->dailyAt('09:00');

        $schedule->command(Commands\Ck\Notify\StillUnactionedReferralsCommand::class)
            ->dailyAt('09:00');

        $schedule->command(Commands\Ck\AutoDelete\AuditsCommand::class)
            ->daily();

        $schedule->command(Commands\Ck\AutoDelete\PageFeedbacksCommand::class)
            ->daily();

        $schedule->command(Commands\Ck\AutoDelete\PendingAssignmentFilesCommand::class)
            ->daily();

        $schedule->command(Commands\Ck\AutoDelete\ReferralsCommand::class)
            ->daily();

        $schedule->command(Commands\Ck\AutoDelete\ServiceRefreshTokensCommand::class)
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
