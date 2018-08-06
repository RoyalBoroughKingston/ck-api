<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\Report;
use App\Models\ReportSchedule;
use App\Models\ReportType;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ReportSchedulesTest extends TestCase
{
    /*
     * List all the report schedules.
     */

    public function test_guest_cannot_list_them()
    {
        $response = $this->json('GET', '/core/v1/report-schedules');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_list_them()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/report-schedules');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_list_them()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/report-schedules');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_list_them()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create();
        $user->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/report-schedules');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_Global_admin_can_list_them()
    {
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $reportSchedule = ReportSchedule::create([
            'report_type_id' => ReportType::commissionersReport()->id,
            'repeat_type' => ReportSchedule::REPEAT_TYPE_WEEKLY,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/report-schedules');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $reportSchedule->id,
            'report_type' => ReportType::commissionersReport()->name,
            'repeat_type' => ReportSchedule::REPEAT_TYPE_WEEKLY,
            'created_at' => $reportSchedule->created_at->format(Carbon::ISO8601),
        ]);
    }

    /*
     * Create a report schedule.
     */

    /*
     * Get a specific report schedule.
     */

    /*
     * Update a specific report schedule.
     */

    /*
     * Delete a specific report schdule.
     */
}
