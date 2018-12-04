<?php

namespace App\Models;

use App\Models\Mutators\ReportMutators;
use App\Models\Relationships\ReportRelationships;
use App\Models\Scopes\ReportScopes;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class Report extends Model
{
    use ReportMutators;
    use ReportRelationships;
    use ReportScopes;

    /**
     * Created a report record and a file record.
     * Then delegates the physical file creation to a `generateReportName` method.
     *
     * @param \App\Models\ReportType $type
     * @param \Illuminate\Support\Carbon|null $startsAt
     * @param \Illuminate\Support\Carbon|null $endsAt
     * @return \App\Models\Report
     * @throws \Exception
     */
    public static function generate(ReportType $type, Carbon $startsAt = null, Carbon $endsAt = null): self
    {
        // Create the file record.
        $file = File::create([
            'filename' => 'temp.csv',
            'mime_type' => 'text/csv',
            'is_private' => true,
        ]);

        // Create the report record.
        $report = static::create([
            'report_type_id' => $type->id,
            'file_id' => $file->id,
        ]);

        // Get the name for the report generation method.
        $methodName = 'generate' . ucfirst(camel_case($type->name));

        // Throw exception if the report type does not have a generate method.
        if (!method_exists($report, $methodName)) {
            throw new Exception("The report type [{$type->name}] does not have a corresponding generate method");
        }

        return $report->$methodName($startsAt, $endsAt);
    }

    /**
     * @return \App\Models\Report
     */
    public function generateUsersExport(): self
    {
        // TODO: Add report generation logic here.
        $this->file->upload('This is a dummy report');

        return $this;
    }

    /**
     * @return \App\Models\Report
     */
    public function generateServicesExport(): self
    {
        $headings = [
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
        ];

        $data = [$headings];

        Service::query()
            ->with('organisation', 'serviceLocations.location')
            ->chunk(200, function (Collection $services) use (&$data) {
                // Loop through each service in the chunk.
                $services->each(function (Service $service) use (&$data) {
                    // Append a row to the data array.
                    $data[] = [
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
                        $service->referral_email,
                        $service->status,
                        $service->serviceLocations->map(function (ServiceLocation $serviceLocation) {
                            return $serviceLocation->location->full_address;
                        })->implode('|'),
                    ];
                });
            });

        // Upload the report.
        $this->file->upload(array_to_csv($data));

        return $this;
    }

    /**
     * @return \App\Models\Report
     */
    public function generateOrganisationsExport(): self
    {
        $headings = [
            'Organisation Reference ID',
            'Organisation Name',
            'Number of Services',
            'Organisation Email',
            'Organisation Phone',
            'Organisation URL',
            'Number of Accounts Attributed',
        ];

        $data = [$headings];

        Organisation::query()
            ->withCount('services', 'nonAdminUsers')
            ->chunk(200, function (Collection $organisations) use (&$data) {
                // Loop through each service in the chunk.
                $organisations->each(function (Organisation $organisation) use (&$data) {
                    // Append a row to the data array.
                    $data[] = [
                        $organisation->id,
                        $organisation->name,
                        $organisation->services_count,
                        $organisation->email,
                        $organisation->phone,
                        $organisation->url,
                        $organisation->non_admin_users_count,
                    ];
                });
            });

        // Upload the report.
        $this->file->upload(array_to_csv($data));

        return $this;
    }

    /**
     * @return \App\Models\Report
     */
    public function generateLocationsExport(): self
    {
        $headings = [
            'Address Line 1',
            'Address Line 2',
            'Address Line 3',
            'City',
            'County',
            'Postcode',
            'Country',
            'Number of Services Delivered at The Location',
        ];

        $data = [$headings];

        Location::query()
            ->withCount('services')
            ->chunk(200, function (Collection $locations) use (&$data) {
                // Loop through each location in the chunk.
                $locations->each(function (Location $location) use (&$data) {
                    // Append a row to the data array.
                    $data[] = [
                        $location->address_line_1,
                        $location->address_line_2,
                        $location->address_line_3,
                        $location->city,
                        $location->county,
                        $location->postcode,
                        $location->country,
                        $location->services_count,
                    ];
                });
            });

        // Upload the report.
        $this->file->upload(array_to_csv($data));

        return $this;
    }

    /**
     * @param \Illuminate\Support\Carbon|null $startsAt
     * @param \Illuminate\Support\Carbon|null $endsAt
     * @return \App\Models\Report
     */
    public function generateReferralsExport(Carbon $startsAt = null, Carbon $endsAt = null): self
    {
        // Update the date range fields if passed.
        if ($startsAt && $endsAt) {
            $this->update([
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);
        }

        $headings = [
            'Referred to Organisation ID',
            'Referred to Organisation',
            'Referred to Service ID',
            'Referred to Service Name',
            'Date Made',
            'Date Complete',
            'Self/Champion',
            'Refer from organisation',
            'Date Consent Provided',
        ];

        $data = [$headings];

        Referral::query()
            ->with('service.organisation', 'latestCompletedStatusUpdate', 'organisationTaxonomy')
            ->when($startsAt && $endsAt, function (Builder $query) use ($startsAt, $endsAt) {
                // When date range provided, filter referrals which were created between the date range.
                $query->whereBetween(table(Referral::class, 'created_at'), [$startsAt, $endsAt]);
            })
            ->chunk(200, function (Collection $referrals) use (&$data) {
                // Loop through each referral in the chunk.
                $referrals->each(function (Referral $referral) use (&$data) {
                    // Append a row to the data array.
                    $data[] = [
                        $referral->service->organisation->id,
                        $referral->service->organisation->name,
                        $referral->service->id,
                        $referral->service->name,
                        optional($referral->created_at)->format(Carbon::ISO8601) ?? '',
                        $referral->isCompleted()
                            ? $referral->latestCompletedStatusUpdate->created_at->format(Carbon::ISO8601)
                            : '',
                        $referral->isSelfReferral() ? 'Self' : 'Champion',
                        $referral->isSelfReferral() ? '' : $referral->organisationTaxonomy->name,
                        optional($referral->referral_consented_at)->format(Carbon::ISO8601) ?? '',
                    ];
                });
            });

        // Upload the report.
        $this->file->upload(array_to_csv($data));

        return $this;
    }

    /**
     * @param \Illuminate\Support\Carbon|null $startsAt
     * @param \Illuminate\Support\Carbon|null $endsAt
     * @return \App\Models\Report
     */
    public function generateFeedbackExport(Carbon $startsAt = null, Carbon $endsAt = null): self
    {
        // TODO: Add report generation logic here.
        $this->file->upload('This is a dummy report');

        return $this;
    }

    /**
     * @param \Illuminate\Support\Carbon|null $startsAt
     * @param \Illuminate\Support\Carbon|null $endsAt
     * @return \App\Models\Report
     */
    public function generateAuditLogsExport(Carbon $startsAt = null, Carbon $endsAt = null): self
    {
        // TODO: Add report generation logic here.
        $this->file->upload('This is a dummy report');

        return $this;
    }

    /**
     * @param \Illuminate\Support\Carbon|null $startsAt
     * @param \Illuminate\Support\Carbon|null $endsAt
     * @return \App\Models\Report
     */
    public function generateSearchHistoriesExport(Carbon $startsAt = null, Carbon $endsAt = null): self
    {
        // TODO: Add report generation logic here.
        $this->file->upload('This is a dummy report');

        return $this;
    }

    /**
     * @return \App\Models\Report
     */
    public function generateThesaurusExport(): self
    {
        // TODO: Add report generation logic here.
        $this->file->upload('This is a dummy report');

        return $this;
    }
}
