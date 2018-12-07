<?php

namespace App\Http\Filters\User;

use App\Models\Organisation;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Models\UserRole;
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
        /** @var \App\Models\User $user */
        $user = request()->user('api');

        // If super admin or global admin then apply no filter.
        if ($user->isSuperAdmin() || $user->isGlobalAdmin()) {
            return $query;
        }

        // Get the ID's of the organisations the user is an admin of.
        $organisationIds = $this->getAdministeredOrganisationIds($user);

        // Get the ID's of the services that belong to the organisations.
        $serviceIdsForOrganisations = $this->getServiceIdsForOrganisations($organisationIds);

        // Get the ID's of the users that work in that organisation.
        $userIdsForOrganisations = $this->getUserIdsForServicesInOrganisations($serviceIdsForOrganisations);

        // Get the ID's of the services the user is an admin of.
        $serviceIdsForServices = $this->getAdministeredServiceIds($user);

        // Get the ID's of the users that work in that service.
        $userIdsForServices = $this->getUserIdsForServices($serviceIdsForServices);

        // Merge the user ID's into a single array.
        $userIds = array_merge($userIdsForOrganisations, $userIdsForServices);

        return $query->whereIn(table(User::class, 'id'), $userIds);
    }

    /**
     * Gets the ID's of the organisations the user is an organisation admin for.
     *
     * @param \App\Models\User $user
     * @return array
     */
    protected function getAdministeredOrganisationIds(User $user): array
    {
        return $user->organisations()
            ->pluck(table(Organisation::class, 'id'))
            ->toArray();
    }

    /**
     * Gets the ID's of the services that belong to the organisations.
     *
     * @param array $organisationIds
     * @return array
     */
    protected function getServiceIdsForOrganisations(array $organisationIds): array
    {
        return Service::query()
            ->whereIn(table(Service::class, 'organisation_id'), $organisationIds)
            ->pluck(table(Service::class, 'id'))
            ->toArray();
    }

    /**
     * Gets the ID's of the users that belong to the services within organisations.
     *
     * @param array $serviceIds
     * @return array
     */
    protected function getUserIdsForServicesInOrganisations(array $serviceIds): array
    {
        return User::query()
            ->whereHas('userRoles', function (Builder $query) use ($serviceIds) {
                $query->whereIn(table(UserRole::class, 'service_id'), $serviceIds);
            })
            ->whereDoesntHave('userRoles', function (Builder $query) {
                // Where the user is not a global or super admin.
                $query->whereIn(table(UserRole::class, 'role_id'), [
                    Role::globalAdmin()->id,
                    Role::superAdmin()->id,
                ]);
            })
            ->pluck(table(User::class, 'id'))
            ->toArray();
    }

    /**
     * Gets the ID's of the service the user is a service admin for.
     *
     * @param \App\Models\User $user
     * @return array
     */
    protected function getAdministeredServiceIds(User $user): array
    {
        return $user->services()
            ->wherePivot('role_id', '=', Role::serviceAdmin()->id)
            ->pluck(table(Service::class, 'id'))
            ->toArray();
    }

    /**
     * Get the ID's for the users.
     *
     * @param array $serviceIds
     * @return array
     */
    protected function getUserIdsForServices(array $serviceIds): array
    {
        return User::query()
            ->whereHas('userRoles', function (Builder $query) use ($serviceIds) {
                // Where the user has a permission for the service.
                $query->whereIn(table(UserRole::class, 'service_id'), $serviceIds);
            })
            ->whereDoesntHave('userRoles', function (Builder $query) use ($serviceIds) {
                // Where the user is not an organisation, global or super admin.
                $query->whereIn(table(UserRole::class, 'role_id'), [
                    Role::organisationAdmin()->id,
                    Role::globalAdmin()->id,
                    Role::superAdmin()->id,
                ]);
            })
            ->pluck(table(User::class, 'id'))
            ->toArray();
    }
}
