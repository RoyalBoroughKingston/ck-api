<?php

namespace Tests\Feature;

use App\Models\Organisation;
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

    public function test_guest_cannot_create_one()
    {
        $response = $this->json('POST', '/core/v1/report-schedules');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_create_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/report-schedules');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_create_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/report-schedules');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_create_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create();
        $user->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/report-schedules');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_create_one()
    {
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/report-schedules', [
            'report_type' => ReportType::commissionersReport()->name,
            'repeat_type' => ReportSchedule::REPEAT_TYPE_WEEKLY,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'report_type' => ReportType::commissionersReport()->name,
            'repeat_type' => ReportSchedule::REPEAT_TYPE_WEEKLY,
        ]);
        $this->assertDatabaseHas((new ReportSchedule())->getTable(), [
            'report_type_id' => ReportType::commissionersReport()->id,
            'repeat_type' => ReportSchedule::REPEAT_TYPE_WEEKLY,
        ]);
    }

    /*
     * Get a specific report schedule.
     */

    public function test_guest_cannot_view_one()
    {
        $reportSchedule = factory(ReportSchedule::class)->create();

        $response = $this->json('GET', "/core/v1/report-schedules/{$reportSchedule->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_view_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);
        $reportSchedule = factory(ReportSchedule::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/report-schedules/{$reportSchedule->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_view_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        $reportSchedule = factory(ReportSchedule::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/report-schedules/{$reportSchedule->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_view_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);
        $reportSchedule = factory(ReportSchedule::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/report-schedules/{$reportSchedule->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_view_one()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        $reportSchedule = factory(ReportSchedule::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/report-schedules/{$reportSchedule->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $reportSchedule->id,
                'report_type' => $reportSchedule->reportType->name,
                'repeat_type' => $reportSchedule->repeat_type,
                'created_at' => $reportSchedule->created_at->format(Carbon::ISO8601),
            ]
        ]);
    }

    /*
     * Update a specific report schedule.
     */

    public function test_guest_cannot_update_one()
    {
        $reportSchedule = factory(ReportSchedule::class)->create();

        $response = $this->json('PUT', "/core/v1/report-schedules/{$reportSchedule->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_update_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);
        $reportSchedule = factory(ReportSchedule::class)->create();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/report-schedules/{$reportSchedule->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_update_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        $reportSchedule = factory(ReportSchedule::class)->create();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/report-schedules/{$reportSchedule->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_update_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);
        $reportSchedule = factory(ReportSchedule::class)->create();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/report-schedules/{$reportSchedule->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_update_one()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        $reportSchedule = factory(ReportSchedule::class)->create([
            'repeat_type' => ReportSchedule::REPEAT_TYPE_WEEKLY,
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/report-schedules/{$reportSchedule->id}", [
            'report_type' => $reportSchedule->reportType->name,
            'repeat_type' => ReportSchedule::REPEAT_TYPE_MONTHLY,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $reportSchedule->id,
                'report_type' => $reportSchedule->reportType->name,
                'repeat_type' => ReportSchedule::REPEAT_TYPE_MONTHLY,
                'created_at' => $reportSchedule->created_at->format(Carbon::ISO8601),
            ]
        ]);
    }

    /*
     * Delete a specific report schedule.
     */

    public function test_guest_cannot_delete_one()
    {
        $reportSchedule = factory(ReportSchedule::class)->create();

        $response = $this->json('DELETE', "/core/v1/report-schedules/{$reportSchedule->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);
        $reportSchedule = factory(ReportSchedule::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/report-schedules/{$reportSchedule->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        $reportSchedule = factory(ReportSchedule::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/report-schedules/{$reportSchedule->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_delete_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);
        $reportSchedule = factory(ReportSchedule::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/report-schedules/{$reportSchedule->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_delete_one()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        $reportSchedule = factory(ReportSchedule::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/report-schedules/{$reportSchedule->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new ReportSchedule())->getTable(), ['id' => $reportSchedule->id]);
    }
}