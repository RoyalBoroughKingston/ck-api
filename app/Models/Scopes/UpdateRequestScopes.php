<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait UpdateRequestScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeServiceId(Builder $query, $id): Builder
    {
        $ids = explode(',', $id);

        return $query
            ->where('updateable_type', 'services')
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
            ->where('updateable_type', 'service_locations')
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
            ->where('updateable_type', 'locations')
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
            ->where('updateable_type', 'organisations')
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
        return <<< EOT
CASE `update_requests`.`updateable_type`
    WHEN "services" THEN (
        SELECT `services`.`name`
        FROM `services`
        WHERE `update_requests`.`updateable_id` = `services`.`id`
        LIMIT 1
    )
    WHEN "locations" THEN (
        SELECT `locations`.`address_line_1`
        FROM `locations`
        WHERE `update_requests`.`updateable_id` = `locations`.`id`
        LIMIT 1
    )
    WHEN "service_locations" THEN (
        SELECT IFNULL(`service_locations`.`name`, `locations`.`address_line_1`)
        FROM `service_locations`
        LEFT JOIN `locations` ON `service_locations`.`location_id` = `locations`.`id`
        WHERE `update_requests`.`updateable_id` = `service_locations`.`id`
        LIMIT 1
    )
    WHEN "organisations" THEN (
        SELECT `organisations`.`name`
        FROM `organisations`
        WHERE `update_requests`.`updateable_id` = `organisations`.`id`
        LIMIT 1
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
