<?php

namespace App\Models;

use App\Models\Mutators\ReportTypeMutators;
use App\Models\Relationships\ReportTypeRelationships;
use App\Models\Scopes\ReportTypeScopes;

class ReportType extends Model
{
    use ReportTypeMutators;
    use ReportTypeRelationships;
    use ReportTypeScopes;

    /**
     * @return \App\Models\ReportType
     */
    public static function usersExport(): self
    {
        return static::where('name', 'Users Export')->firstOrFail();
    }

    /**
     * @return \App\Models\ReportType
     */
    public static function servicesExport(): self
    {
        return static::where('name', 'Services Export')->firstOrFail();
    }

    /**
     * @return \App\Models\ReportType
     */
    public static function organisationsExport(): self
    {
        return static::where('name', 'Organisations Export')->firstOrFail();
    }

    /**
     * @return \App\Models\ReportType
     */
    public static function locationsExport(): self
    {
        return static::where('name', 'Locations Export')->firstOrFail();
    }

    /**
     * @return \App\Models\ReportType
     */
    public static function referralsExport(): self
    {
        return static::where('name', 'Referrals Export')->firstOrFail();
    }

    /**
     * @return \App\Models\ReportType
     */
    public static function feedbackExport(): self
    {
        return static::where('name', 'Feedback Export')->firstOrFail();
    }

    /**
     * @return \App\Models\ReportType
     */
    public static function auditLogsExport(): self
    {
        return static::where('name', 'Audit Logs Export')->firstOrFail();
    }

    /**
     * @return \App\Models\ReportType
     */
    public static function searchHistoriesExport(): self
    {
        return static::where('name', 'Search Histories Export')->firstOrFail();
    }
}
