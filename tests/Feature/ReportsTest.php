<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Organisation;
use App\Models\Report;
use App\Models\ReportType;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;
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
            'report_type' => ReportType::commissionersReport()->name,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'report_type' => ReportType::commissionersReport()->name,
        ]);
        $this->assertDatabaseHas((new Report())->getTable(), [
            'report_type_id' => ReportType::commissionersReport()->id,
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
        $report = Report::generate(ReportType::commissionersReport());

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/reports/{$report->id}/download");

        $response->assertStatus(Response::HTTP_OK);
    }
}
