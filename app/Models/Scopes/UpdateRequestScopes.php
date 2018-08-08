<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;

trait UpdateRequestScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeServiceId(Builder $query, $id): Builder
    {
        return $query
            ->where('updateable_type', 'services')
            ->whereIn('updateable_id', (array)$id);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeServiceLocationId(Builder $query, $id): Builder
    {
        return $query
            ->where('updateable_type', 'service_locations')
            ->whereIn('updateable_id', (array)$id);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLocationId(Builder $query, $id): Builder
    {
        return $query
            ->where('updateable_type', 'locations')
            ->whereIn('updateable_id', (array)$id);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrganisationId(Builder $query, $id): Builder
    {
        return $query
            ->where('updateable_type', 'organisations')
            ->whereIn('updateable_id', (array)$id);
    }
}
