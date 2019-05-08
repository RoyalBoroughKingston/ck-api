<?php

namespace App\Http\Filters\User;

use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class AtServiceFilter implements Filter
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
        $serviceIds = explode(',', $value);

        return $query
            ->whereHas('userRoles', function (Builder $query) use ($serviceIds) {
                // Only get users with roles at the services.
                $query->whereIn('user_roles.service_id', $serviceIds);
            })
            ->whereDoesntHave('userRoles', function (Builder $query) {
                // Don't get users who are a global admin.
                $query->where('user_roles.role_id', '=', Role::globalAdmin()->id);
            });
    }
}
