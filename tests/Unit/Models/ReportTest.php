<?php

namespace Tests\Unit\Models;

use App\Models\Organisation;
use App\Models\Report;
use App\Models\ReportType;
use App\Models\Role;
use App\Models\Service;
use App\Models\ServiceLocation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReportTest extends TestCase
{
    /*
     * Users export.
     */

    public function test_users_export_works()
    {
        $this->markTestIncomplete();
    }

    public function test_users_export_with_date_range_works()
    {
        $this->markTestIncomplete();
    }

    /*
     * Services export.
     */

    public function test_services_export_works()
    {
        // Create a single service.
        $service = factory(Service::class)->create();

        // Generate the report.
        $report = Report::generate(ReportType::servicesExport());

        // Test that the data is correct.
        $csv = csv_to_array($report->file->getContent());

        // Assert correct number of records exported.
        $this->assertEquals(2, count($csv));

        // Assert headings are correct.
        $this->assertEquals([
            'Organisation',
            'Org Reference ID',
            'Org Email',
            'Org Phone',
            'Service Reference ID',
            'Service Name',
            'Service Web Address',
            'Service Contact Name',
            'Last Updated',
            'Referral Type',
            'Referral Contact',
            'Status',
            'Locations Delivered At',
        ], $csv[0]);

        // Assert created service exported.
        $this->assertEquals([
            $service->organisation->name,
            $service->organisation->id,
            $service->organisation->email,
            $service->organisation->phone,
            $service->id,
            $service->name,
            $service->url,
            $service->contact_name,
            $service->updated_at->format(Carbon::ISO8601),
            $service->referral_method,
            $service->referral_email ?? '',
            $service->status,
            $service->serviceLocations->map(function (ServiceLocation $serviceLocation) {
                return $serviceLocation->location->full_address;
            })->implode('|'),
        ], $csv[1]);
    }

    /*
     * Organisations export.
     */

    public function test_organisations_export_works()
    {
        // Create a single organisation.
        $organisation = factory(Organisation::class)->create();

        // Create an admin and non-admin user.
        factory(User::class)->create()->makeSuperAdmin();
        factory(User::class)->create()->makeOrganisationAdmin($organisation);

        // Generate the report.
        $report = Report::generate(ReportType::organisationsExport());

        // Test that the data is correct.
        $csv = csv_to_array($report->file->getContent());

        // Assert correct number of records exported.
        $this->assertEquals(2, count($csv));

        // Assert headings are correct.
        $this->assertEquals([
            'Organisation Reference ID',
            'Organisation Name',
            'Number of Services',
            'Organisation Email',
            'Organisation Phone',
            'Organisation URL',
            'Number of Accounts Attributed',
        ], $csv[0]);

        // Assert created organisation exported.
        $this->assertEquals([
            $organisation->id,
            $organisation->name,
            0,
            $organisation->email,
            $organisation->phone,
            $organisation->url,
            1,
        ], $csv[1]);
    }

    /*
     * Locations export.
     */

    /*
     * Referrals export.
     */

    /*
     * Feedback export.
     */

    /*
     * Audit logs export.
     */

    /*
     * Search histories export.
     */

    /*
     * Thesaurus export.
     */
}
