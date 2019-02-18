<?php

namespace App\Console\Commands\Ck;

use App\Emails\ScheduledReportGenerated\NotifyGlobalAdminEmail;
use App\Models\Report;
use App\Models\ReportSchedule;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Collection;
use Throwable;

class CreateScheduledReportsCommand extends Command
{
    use DispatchesJobs;

    const MONDAY = 1;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:create-scheduled-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates and sends scheduled reports';

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @var int
     */
    protected $successful = 0;

    /**
     * @var int
     */
    protected $failed = 0;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->count = ReportSchedule::query()->count();

        // Output the number of report schedules.
        $this->line("Generating reports for {$this->count} report schedules...");

        ReportSchedule::query()
            ->with('reportType')
            ->chunk(200, function (Collection $reportSchedules) {
                $reportSchedules->each(function (ReportSchedule $reportSchedule) {
                    // Output creating message.
                    $this->line("Generating report for report schedule [$reportSchedule->id]...");

                    switch ($reportSchedule->repeat_type) {
                        case ReportSchedule::REPEAT_TYPE_WEEKLY:
                            $this->handleWeekly($reportSchedule);
                            break;
                        case ReportSchedule::REPEAT_TYPE_MONTHLY:
                            $this->handleMonthly($reportSchedule);
                            break;
                    }
                });
            });

        if ($this->failed > 0) {
            $this->error("Generated reports for $this->successful report schedules. Failed generating reports for $this->failed report schedules.");
        } else {
            $this->info("Generated reports for $this->successful report schedules.");
        }
    }

    /**
     * @param \App\Models\ReportSchedule $reportSchedule
     */
    protected function handleWeekly(ReportSchedule $reportSchedule)
    {
        // Skip if not a Monday.
        if (now()->dayOfWeekIso !== static::MONDAY) {
            // Output skipped message.
            $this->info("Report not due for report schedule [$reportSchedule->id]");

            return;
        }

        try {
            // Attempt to create.
            $report = Report::generate(
                $reportSchedule->reportType,
                now()->subWeek()->startOfWeek(),
                now()->subWeek()->endOfWeek()
            );

            // Send a notification.
            $this->dispatch(new NotifyGlobalAdminEmail(config('ck.global_admin.email'), [
                'REPORT_FREQUENCY' => 'weekly',
                'REPORT_TYPE' => $report->reportType->name,
            ]));

            // Output success message.
            $this->info("Generated report for report schedule [$reportSchedule->id]");

            // Increment successful.
            $this->successful++;
        } catch (Throwable $exception) {
            // Output error message.
            $this->error("Failed to generate report for report schedule [$reportSchedule->id]");

            // Increment failed.
            $this->failed++;
        }
    }

    /**
     * @param \App\Models\ReportSchedule $reportSchedule
     */
    protected function handleMonthly(ReportSchedule $reportSchedule)
    {
        // Skip if not the first day of the month.
        if (now()->day !== 1) {
            // Output skipped message.
            $this->info("Report not due for report schedule [$reportSchedule->id]");

            return;
        }

        try {
            // Attempt to create.
            $report = Report::generate(
                $reportSchedule->reportType,
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            );

            // Send a notification.
            $this->dispatch(new NotifyGlobalAdminEmail(config('ck.global_admin.email'), [
                'REPORT_FREQUENCY' => 'monthly',
                'REPORT_TYPE' => $report->reportType->name,
            ]));

            // Output success message.
            $this->info("Generated report for report schedule [$reportSchedule->id]");

            // Increment successful.
            $this->successful++;
        } catch (\Throwable $exception) {
            // Output error message.
            $this->error("Failed to generate report for report schedule [$reportSchedule->id]");

            // Increment failed.
            $this->failed++;
        }
    }
}
