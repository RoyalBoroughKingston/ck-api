<?php

namespace App\Models;

use App\Models\Mutators\ReportMutators;
use App\Models\Relationships\ReportRelationships;
use App\Models\Scopes\ReportScopes;
use Exception;
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
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
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
     * @param \Illuminate\Support\Carbon|null $startsAt
     * @param \Illuminate\Support\Carbon|null $endsAt
     * @return \App\Models\Report
     */
    public function generateUsersExport(Carbon $startsAt = null, Carbon $endsAt = null): self
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
    public function generateServicesExport(Carbon $startsAt = null, Carbon $endsAt = null): self
    {
        // Parameters are unused for this report.
        unset($startsAt, $endsAt);

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

        $this->file->upload(array_to_csv($data));

        return $this;
    }

    /**
     * @param \Illuminate\Support\Carbon|null $startsAt
     * @param \Illuminate\Support\Carbon|null $endsAt
     * @return \App\Models\Report
     */
    public function generateOrganisationsExport(Carbon $startsAt = null, Carbon $endsAt = null): self
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
    public function generateLocationsExport(Carbon $startsAt = null, Carbon $endsAt = null): self
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
    public function generateReferralsExport(Carbon $startsAt = null, Carbon $endsAt = null): self
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
     * @param \Illuminate\Support\Carbon|null $startsAt
     * @param \Illuminate\Support\Carbon|null $endsAt
     * @return \App\Models\Report
     */
    public function generateThesaurusExport(Carbon $startsAt = null, Carbon $endsAt = null): self
    {
        // TODO: Add report generation logic here.
        $this->file->upload('This is a dummy report');

        return $this;
    }
}
