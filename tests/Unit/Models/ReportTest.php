<?php

namespace Tests\Unit\Models;

use App\Models\Audit;
use App\Models\Location;
use App\Models\Organisation;
use App\Models\PageFeedback;
use App\Models\Referral;
use App\Models\Report;
use App\Models\ReportType;
use App\Models\Service;
use App\Models\ServiceLocation;
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
        $referral = factory(Referral::class)->create(['referral_consented_at' => now()]);

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

        // Assert created referral exported.
        $this->assertEquals([
            $referral->service->organisation->id,
            $referral->service->organisation->name,
            $referral->service->id,
            $referral->service->name,
            $referral->created_at->format(Carbon::ISO8601),
            '',
            'Self',
            '',
            $referral->referral_consented_at->format(Carbon::ISO8601),
        ], $csv[1]);
    }

    public function test_referrals_export_works_when_completed()
    {
        $user = factory(User::class)->create()->makeSuperAdmin();

        // Create a single referral.
        $referral = factory(Referral::class)->create(['referral_consented_at' => now()]);

        // Update the referral.
        Carbon::setTestNow(now()->addHour());
        $statusUpdate = $referral->updateStatus($user, Referral::STATUS_COMPLETED);

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

        // Assert created referral exported.
        $this->assertEquals([
            $referral->service->organisation->id,
            $referral->service->organisation->name,
            $referral->service->id,
            $referral->service->name,
            $referral->created_at->format(Carbon::ISO8601),
            $statusUpdate->created_at->format(Carbon::ISO8601),
            'Self',
            '',
            $referral->referral_consented_at->format(Carbon::ISO8601),
        ], $csv[1]);
    }

    public function test_referrals_export_works_with_date_range()
    {
        // Create an in range referral.
        $referralInRange = factory(Referral::class)->create([
            'referral_consented_at' => now(),
        ]);

        // Create an out of range referral.
        factory(Referral::class)->create([
            'referral_consented_at' => now(),
            'created_at' => today()->subMonths(2),
        ]);

        // Generate the report.
        $report = Report::generate(
            ReportType::referralsExport(),
            today()->startOfMonth(),
            today()->endOfMonth()
        );

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

        // Assert created referral exported.
        $this->assertEquals([
            $referralInRange->service->organisation->id,
            $referralInRange->service->organisation->name,
            $referralInRange->service->id,
            $referralInRange->service->name,
            $referralInRange->created_at->format(Carbon::ISO8601),
            '',
            'Self',
            '',
            $referralInRange->referral_consented_at->format(Carbon::ISO8601),
        ], $csv[1]);
    }

    /*
     * Feedback export.
     */

    public function test_feedback_export_works()
    {
        // Create a single feedback.
        $feedback = factory(PageFeedback::class)->create();

        // Generate the report.
        $report = Report::generate(ReportType::feedbackExport());

        // Test that the data is correct.
        $csv = csv_to_array($report->file->getContent());

        // Assert correct number of records exported.
        $this->assertEquals(2, count($csv));

        // Assert headings are correct.
        $this->assertEquals([
            'Date Submitted',
            'Feedback Content',
            'Page URL',
        ], $csv[0]);

        // Assert created feedback exported.
        $this->assertEquals([
            $feedback->created_at->toDateString(),
            $feedback->feedback,
            $feedback->url,
        ], $csv[1]);
    }

    public function test_feedback_export_works_with_date_range()
    {
        // Create a single feedback.
        $feedbackWithinRange = factory(PageFeedback::class)->create();
        factory(PageFeedback::class)->create(['created_at' => today()->subMonths(2)]);

        // Generate the report.
        $report = Report::generate(
            ReportType::feedbackExport(),
            today()->startOfMonth(),
            today()->endOfMonth()
        );

        // Test that the data is correct.
        $csv = csv_to_array($report->file->getContent());

        // Assert correct number of records exported.
        $this->assertEquals(2, count($csv));

        // Assert headings are correct.
        $this->assertEquals([
            'Date Submitted',
            'Feedback Content',
            'Page URL',
        ], $csv[0]);

        // Assert created feedback exported.
        $this->assertEquals([
            $feedbackWithinRange->created_at->toDateString(),
            $feedbackWithinRange->feedback,
            $feedbackWithinRange->url,
        ], $csv[1]);
    }

    /*
     * Audit logs export.
     */

    public function test_audit_logs_export_works()
    {
        // Create a single audit log.
        $audit = factory(Audit::class)->create();

        // Generate the report.
        $report = Report::generate(ReportType::auditLogsExport());

        // Test that the data is correct.
        $csv = csv_to_array($report->file->getContent());

        // Assert correct number of records exported.
        $this->assertEquals(2, count($csv));

        // Assert headings are correct.
        $this->assertEquals([
            'Action',
            'Description',
            'User',
            'Date/Time',
            'IP Address',
            'User Agent',
        ], $csv[0]);

        // Assert created audit log exported.
        $this->assertEquals([
            $audit->action,
            $audit->description,
            '',
            $audit->created_at->format(Carbon::ISO8601),
            $audit->ip_address,
            $audit->user_agent ?? '',
        ], $csv[1]);
    }

    /*
     * Search histories export.
     */

    /*
     * Thesaurus export.
     */
}
