<?php

namespace App\Http\Filters\User;

use App\Models\Organisation;
use App\Models\Role;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class AtOrganisationFilter implements Filter
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @param string $property
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Get the organisation IDs from the query string.
        $organisationIds = explode(',', $value);

        // Get the services IDs associated to the organisation IDs.
        $serviceIds = Service::query()
            ->whereIn('organisation_id', $organisationIds)
            ->pluck('id')
            ->toArray();

        return $query
            ->whereHas('userRoles', function (Builder $query) use ($organisationIds, $serviceIds) {
                // Only get users with roles at the organisation or any associated services.
                $query
                    ->whereIn('user_roles.organisation_id', $organisationIds)
                    ->orWhereIn('user_roles.service_id', $serviceIds);
            })
            ->whereDoesntHave('userRoles', function (Builder $query) {
                // Don't get users who are a global admin.
                $query->where('user_roles.role_id', '=', Role::globalAdmin()->id);
            });
    }
}
