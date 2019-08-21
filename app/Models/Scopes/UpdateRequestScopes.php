<?php

namespace App\Models\Scopes;

use App\Models\UpdateRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait UpdateRequestScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNew(Builder $query): Builder
    {
        return $query->whereNull('updateable_id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExisting(Builder $query): Builder
    {
        return $query->whereNotNull('updateable_id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeServiceId(Builder $query, $id): Builder
    {
        $ids = explode(',', $id);

        return $query
            ->where('updateable_type', UpdateRequest::EXISTING_TYPE_SERVICE)
            ->whereIn('updateable_id', $ids);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeServiceLocationId(Builder $query, $id): Builder
    {
        $ids = explode(',', $id);

        return $query
            ->where('updateable_type', UpdateRequest::EXISTING_TYPE_SERVICE_LOCATION)
            ->whereIn('updateable_id', $ids);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLocationId(Builder $query, $id): Builder
    {
        $ids = explode(',', $id);

        return $query
            ->where('updateable_type', UpdateRequest::EXISTING_TYPE_LOCATION)
            ->whereIn('updateable_id', $ids);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrganisationId(Builder $query, $id): Builder
    {
        $ids = explode(',', $id);

        return $query
            ->where('updateable_type', UpdateRequest::EXISTING_TYPE_ORGANISATION)
            ->whereIn('updateable_id', $ids);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $alias
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithEntry(Builder $query, string $alias = 'entry'): Builder
    {
        return $query->addSelect(DB::raw("({$this->getEntrySql()}) AS `{$alias}`"));
    }

    /**
     * This SQL query is placed into its own method as it is referenced
     * in multiple places.
     *
     * @return string
     */
    public function getEntrySql(): string
    {
        $services = UpdateRequest::EXISTING_TYPE_SERVICE;
        $locations = UpdateRequest::EXISTING_TYPE_LOCATION;
        $serviceLocations = UpdateRequest::EXISTING_TYPE_SERVICE_LOCATION;
        $organisations = UpdateRequest::EXISTING_TYPE_ORGANISATION;
        $organisationSignUpForm = UpdateRequest::NEW_TYPE_ORGANISATION_SIGN_UP_FORM;

        return <<<EOT
CASE `update_requests`.`updateable_type`
    WHEN "{$services}" THEN (
        SELECT `services`.`name`
        FROM `services`
        WHERE `update_requests`.`updateable_id` = `services`.`id`
        LIMIT 1
    )
    WHEN "{$locations}" THEN (
        SELECT `locations`.`address_line_1`
        FROM `locations`
        WHERE `update_requests`.`updateable_id` = `locations`.`id`
        LIMIT 1
    )
    WHEN "{$serviceLocations}" THEN (
        SELECT IFNULL(`service_locations`.`name`, `locations`.`address_line_1`)
        FROM `service_locations`
        LEFT JOIN `locations` ON `service_locations`.`location_id` = `locations`.`id`
        WHERE `update_requests`.`updateable_id` = `service_locations`.`id`
        LIMIT 1
    )
    WHEN "{$organisations}" THEN (
        SELECT `organisations`.`name`
        FROM `organisations`
        WHERE `update_requests`.`updateable_id` = `organisations`.`id`
        LIMIT 1
    )
    WHEN "{$organisationSignUpForm}" THEN (
        `update_requests`.`data`->>"$.organisation.name"
    )
END
EOT;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query
            ->whereNull('approved_at')
            ->whereNull('deleted_at');
    }
}
