<?php

namespace App\Http\Filters\Organisation;

use App\Models\Organisation;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class HasPermissionFilter implements Filter
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @param string $property
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $organisationIds = [];
        $user = request()->user('api');

        if ($user) {
            $userOrganisationIds = $user->organisations()
                ->pluck(table(Organisation::class, 'id'))
                ->toArray();
            $userServiceOrganisationIds = $user->services()
                ->pluck(table(Service::class, 'organisation_id'))
                ->toArray();

            $organisationIds = array_merge($userOrganisationIds, $userServiceOrganisationIds);
        }

        return $query->whereIn(table(Organisation::class, 'id'), $organisationIds);
    }
}
