<?php

namespace Tests\Unit\Models;

use App\Models\Location;
use App\Models\Organisation;
use App\Models\Referral;
use App\Models\Report;
use App\Models\ReportType;
use App\Models\Service;
use App\Models\ServiceLocation;
use App\Models\StatusUpdate;
use App\Models\User;
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

    public function test_locations_export_works()
    {
        // Create a single location.
        $location = factory(Location::class)->create();

        // Generate the report.
        $report = Report::generate(ReportType::locationsExport());

        // Test that the data is correct.
        $csv = csv_to_array($report->file->getContent());

        // Assert correct number of records exported.
        $this->assertEquals(2, count($csv));

        // Assert headings are correct.
        $this->assertEquals([
            'Address Line 1',
            'Address Line 2',
            'Address Line 3',
            'City',
            'County',
            'Postcode',
            'Country',
            'Number of Services Delivered at The Location',
        ], $csv[0]);

        // Assert created location exported.
        $this->assertEquals([
            $location->address_line_1,
            $location->address_line_2 ?? '',
            $location->address_line_3 ?? '',
            $location->city,
            $location->county,
            $location->postcode,
            $location->country,
            0,
        ], $csv[1]);
    }

    /*
     * Referrals export.
     */

    public function test_referrals_export_works()
    {
        // Create a single referral.
        $referral = factory(Referral::class)->create();

        // Generate the report.
        $report = Report::generate(ReportType::referralsExport());

        // Test that the data is correct.
        $csv = csv_to_array($report->file->getContent());

        // Assert correct number of records exported.
        $this->assertEquals(2, count($csv));

        // Assert headings are correct.
        $this->assertEquals([
            'Referred to Organisation ID',
            'Referred to Organisation',
            'Referred to Service ID',
            'Referred to Service Name',
            'Date Made',
            'Date Complete',
            'Self/Champion',
            'Refer from organisation',
            'Date Consent Provided',
        ], $csv[0]);

        // Assert created referral   exported.
        $this->assertEquals([
            $referral->service->organisation->id,
            $referral->service->organisation->name,
            $referral->service->id,
            $referral->service->name,
            optional($referral->created_at)->format(Carbon::ISO8601) ?? '',
            $referral->status === Referral::STATUS_COMPLETED
                ? $referral->statusUpdates()
                ->orderByDesc('created_at')
                ->where('to', '=', StatusUpdate::TO_COMPLETED)
                ->first()
                ->created_at
                ->format(Carbon::ISO8601)
                : '',
            $referral->isSelfReferral() ? 'Self' : 'Champion',
            $referral->isSelfReferral() ? '' : $referral->organisationTaxonomy->name,
            optional($referral->referral_consented_at)->format(Carbon::ISO8601) ?? '',
        ], $csv[1]);
    }

    public function test_referrals_export_works_when_completed()
    {
        $this->markTestIncomplete();
    }

    public function test_referrals_export_works_with_date_range()
    {
        $this->markTestIncomplete();
    }

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
