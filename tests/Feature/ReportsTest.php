<?php

namespace Tests\Feature;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\File;
use App\Models\Organisation;
use App\Models\Report;
use App\Models\ReportType;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    /*
     * List all the reports.
     */

    public function test_guest_cannot_list_them()
    {
        $response = $this->json('GET', '/core/v1/reports');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_list_them()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/reports');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_list_them()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/reports');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_list_them()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/reports');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_list_them()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        $report = factory(Report::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/reports');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $report->id,
            'report_type' => $report->reportType->name,
        ]);
    }

    public function test_audit_created_when_listed()
    {
        $this->fakeEvents();

        $user = factory(User::class)->create()->makeGlobalAdmin();

        Passport::actingAs($user);

        $this->json('GET', '/core/v1/reports');

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user) {
            return ($event->getAction() === Audit::ACTION_READ) &&
                ($event->getUser()->id === $user->id);
        });
    }

    /*
     * Create a report.
     */

    public function test_guest_cannot_create_one()
    {
        $response = $this->json('POST', '/core/v1/reports');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_create_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/reports');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_create_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/reports');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_create_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/reports');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_create_one()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/reports', [
            'report_type' => ReportType::usersExport()->name,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'report_type' => ReportType::usersExport()->name,
        ]);
        $this->assertDatabaseHas((new Report())->getTable(), [
            'report_type_id' => ReportType::usersExport()->id,
        ]);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'report_type',
                'starts_at',
                'ends_at',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_audit_created_when_created()
    {
        $this->fakeEvents();

        $user = factory(User::class)->create()->makeGlobalAdmin();

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/reports', [
            'report_type' => ReportType::usersExport()->name,
        ]);

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $response) {
            return ($event->getAction() === Audit::ACTION_CREATE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $this->getResponseContent($response)['data']['id']);
        });
    }

    public function test_global_admin_can_create_one_with_date_range()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/reports', [
            'report_type' => ReportType::referralsExport()->name,
            'starts_at' => Date::today()->startOfMonth()->toDateString(),
            'ends_at' => Date::today()->endOfMonth()->toDateString(),
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'report_type' => ReportType::referralsExport()->name,
            'starts_at' => Date::today()->startOfMonth()->toDateString(),
            'ends_at' => Date::today()->endOfMonth()->toDateString(),
        ]);
        $this->assertDatabaseHas((new Report())->getTable(), [
            'report_type_id' => ReportType::referralsExport()->id,
            'starts_at' => Date::today()->startOfMonth()->toDateString(),
            'ends_at' => Date::today()->endOfMonth()->toDateString(),
        ]);
    }

    /*
     * Get a specific report.
     */

    public function test_guest_cannot_view_one()
    {
        $report = factory(Report::class)->create();

        $response = $this->json('GET', "/core/v1/reports/{$report->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_view_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);
        $report = factory(Report::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/reports/{$report->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_view_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        $report = factory(Report::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/reports/{$report->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_view_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);
        $report = factory(Report::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/reports/{$report->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_view_one()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        $report = factory(Report::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/reports/{$report->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $report->id,
            'report_type' => $report->reportType->name,
        ]);
    }

    public function test_audit_created_when_viewed()
    {
        $this->fakeEvents();

        $user = factory(User::class)->create()->makeGlobalAdmin();
        $report = factory(Report::class)->create();

        Passport::actingAs($user);

        $this->json('GET', "/core/v1/reports/{$report->id}");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $report) {
            return ($event->getAction() === Audit::ACTION_READ) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $report->id);
        });
    }

    /*
     * Delete a specific report.
     */

    public function test_guest_cannot_delete_one()
    {
        $report = factory(Report::class)->create();

        $response = $this->json('DELETE', "/core/v1/reports/{$report->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);
        $report = factory(Report::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/reports/{$report->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        $report = factory(Report::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/reports/{$report->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_delete_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);
        $report = factory(Report::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/reports/{$report->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_delete_one()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        $report = factory(Report::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/reports/{$report->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new Report())->getTable(), ['id' => $report->id]);
        $this->assertDatabaseMissing((new File())->getTable(), ['id' => $report->file_id]);
    }

    public function test_audit_created_when_deleted()
    {
        $this->fakeEvents();

        $user = factory(User::class)->create()->makeGlobalAdmin();
        $report = factory(Report::class)->create();

        Passport::actingAs($user);

        $this->json('DELETE', "/core/v1/reports/{$report->id}");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $report) {
            return ($event->getAction() === Audit::ACTION_DELETE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $report->id);
        });
    }

    /*
     * Download a specific report.
     */

    public function test_guest_cannot_download_file()
    {
        $report = factory(Report::class)->create();

        $response = $this->json('GET', "/core/v1/reports/{$report->id}/download");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_download_file()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);
        $report = factory(Report::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/reports/{$report->id}/download");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_download_file()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        $report = factory(Report::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/reports/{$report->id}/download");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_download_file()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);
        $report = factory(Report::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/reports/{$report->id}/download");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_download_file()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        $report = Report::generate(ReportType::usersExport());

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/reports/{$report->id}/download");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_audit_created_when_file_viewed()
    {
        $this->fakeEvents();

        $user = factory(User::class)->create()->makeGlobalAdmin();
        $report = Report::generate(ReportType::usersExport());

        Passport::actingAs($user);

        $this->json('GET', "/core/v1/reports/{$report->id}/download");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $report) {
            return ($event->getAction() === Audit::ACTION_READ) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $report->id);
        });
    }
}
