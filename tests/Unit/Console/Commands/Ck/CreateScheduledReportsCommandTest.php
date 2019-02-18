<?php

namespace Tests\Unit\Console\Commands\Ck;

use App\Console\Commands\Ck\CreateScheduledReportsCommand;
use App\Emails\ScheduledReportGenerated\NotifyGlobalAdminEmail;
use App\Models\Report;
use App\Models\ReportSchedule;
use App\Models\ReportType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CreateScheduledReportsCommandTest extends TestCase
{
    public function test_weekly_report_generated()
    {
        Queue::fake();

        $reportSchedule = factory(ReportSchedule::class)->create([
            'report_type_id' => ReportType::usersExport()->id,
            'repeat_type' => ReportSchedule::REPEAT_TYPE_WEEKLY,
        ]);

        Carbon::setTestNow(now()->startOfWeek());

        Artisan::call(CreateScheduledReportsCommand::class);

        $this->assertDatabaseHas(table(Report::class), [
            'report_type_id' => $reportSchedule->reportType->id,
            'starts_at' => now()->subWeek()->startOfWeek()->toDateString(),
            'ends_at' => now()->subWeek()->endOfWeek()->toDateString(),
        ]);

        Queue::assertPushedOn('notifications', NotifyGlobalAdminEmail::class);
        Queue::assertPushed(NotifyGlobalAdminEmail::class, function (NotifyGlobalAdminEmail $email) {
            $this->assertArrayHasKey('REPORT_FREQUENCY', $email->values);
            $this->assertArrayHasKey('REPORT_TYPE', $email->values);
            return true;
        });
    }

    public function test_monthly_report_generated()
    {
        Queue::fake();

        $reportSchedule = factory(ReportSchedule::class)->create([
            'report_type_id' => ReportType::servicesExport()->id,
            'repeat_type' => ReportSchedule::REPEAT_TYPE_MONTHLY,
        ]);

        Carbon::setTestNow(now()->startOfMonth());

        Artisan::call(CreateScheduledReportsCommand::class);

        $this->assertDatabaseHas(table(Report::class), [
            'report_type_id' => $reportSchedule->reportType->id,
            'starts_at' => now()->subMonth()->startOfMonth()->toDateString(),
            'ends_at' => now()->subMonth()->endOfMonth()->toDateString(),
        ]);

        Queue::assertPushedOn('notifications', NotifyGlobalAdminEmail::class);
        Queue::assertPushed(NotifyGlobalAdminEmail::class, function (NotifyGlobalAdminEmail $email) {
            $this->assertArrayHasKey('REPORT_FREQUENCY', $email->values);
            $this->assertArrayHasKey('REPORT_TYPE', $email->values);
            return true;
        });
    }
}
