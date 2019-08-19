<?php

namespace App\Listeners\Notifications;

use App\Emails\UserRolesUpdated\NotifyUserEmail;
use App\Events\UserRolesUpdated as UserRolesUpdatedEvent;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Collection;

class UserRolesUpdated
{
    /**
     * Handle the event.
     *
     * @param \App\Events\UserRolesUpdated $event
     */
    public function handle(UserRolesUpdatedEvent $event)
    {
        $this->notifyUser($event);
    }

    /**
     * @param \App\Events\UserRolesUpdated $event
     */
    protected function notifyUser(UserRolesUpdatedEvent $event)
    {
        // Eager load needed relationships.
        $event->oldRoles->load('role', 'organisation', 'service.organisation');
        $event->newRoles->load('role', 'organisation', 'service.organisation');

        // Get the human readable role name.
        $humanReadableRole = function (UserRole $userRole) {
            switch ($userRole->role_id) {
                case Role::superAdmin()->id:
                    return 'Super admin';
                case Role::globalAdmin()->id:
                    return 'Global admin';
                case Role::organisationAdmin()->id:
                    return "Organisation admin for {$userRole->organisation->name}";
                case Role::serviceAdmin()->id:
                    return "Service admin for {$userRole->service->name}";
                case Role::serviceWorker()->id:
                    return "Service worker for {$userRole->service->name}";
                default:
                    return 'Unknown role';
            }
        };

        $revokedRoles = $this
            ->filterHighestRoles(
                $this->getRevokedRoles($event->oldRoles, $event->newRoles)
            )
            ->map($humanReadableRole)
            ->implode(', ');

        $addedRoles = $this
            ->filterHighestRoles(
                $this->getAddedRoles($event->oldRoles, $event->newRoles)
            )
            ->map($humanReadableRole)
            ->implode(', ');

        $event->user->sendEmail(new NotifyUserEmail($event->user->email, [
            'NAME' => $event->user->first_name,
            'OLD_PERMISSIONS' => $revokedRoles,
            'PERMISSIONS' => $addedRoles,
        ]));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $oldRoles
     * @param \Illuminate\Database\Eloquent\Collection $newRoles
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getRevokedRoles(Collection $oldRoles, Collection $newRoles): Collection
    {
        // Reject roles that also exist in the new roles collection.
        return $oldRoles->reject(function (UserRole $oldUserRole) use ($newRoles) {
            // Find the corresponding role in the new roles collection, if there is one.
            return $newRoles->first(function (UserRole $newUserRole) use ($oldUserRole) {
                // It's a match if the role_id, organisation_id, and service_id are the same.
                return ($oldUserRole->role_id === $newUserRole->role_id)
                    && ($oldUserRole->organisation_id === $newUserRole->organisation_id)
                    && ($oldUserRole->service_id === $newUserRole->service_id);
            });
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $oldRoles
     * @param \Illuminate\Database\Eloquent\Collection $newRoles
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getAddedRoles(Collection $oldRoles, Collection $newRoles): Collection
    {
        // Reject roles that also exist in the old roles collection.
        return $newRoles->reject(function (UserRole $newUserRole) use ($oldRoles) {
            // Find the corresponding role in the old roles collection, if there is one.
            return $oldRoles->first(function (UserRole $oldUserRole) use ($newUserRole) {
                // It's a match if the role_id, organisation_id, and service_id are the same.
                return ($newUserRole->role_id === $oldUserRole->role_id)
                    && ($newUserRole->organisation_id === $oldUserRole->organisation_id)
                    && ($newUserRole->service_id === $oldUserRole->service_id);
            });
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $roles
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function filterHighestRoles(Collection $roles): Collection
    {
        return $roles->filter(function (UserRole $userRole) use ($roles) {
            // Common checks.
            $isSuperAdmin = $roles->first(function (UserRole $userRole) {
                return $userRole->isSuperAdmin();
            }) !== null;
            $isGlobalAdmin = $roles->first(function (UserRole $userRole) {
                return $userRole->isGlobalAdmin();
            }) !== null;
            $isOrganisationAdmin = function (UserRole $userRole) use ($roles) {
                return $roles->first(function (UserRole $userRoleIteration) use ($userRole) {
                    return $userRoleIteration->isOrganisationAdmin($userRole->service->organisation);
                }) !== null;
            };
            $isServiceAdmin = function (UserRole $userRole) use ($roles) {
                return $roles->first(function (UserRole $userRoleIteration) use ($userRole) {
                    return $userRoleIteration->isServiceAdmin($userRole->service);
                }) !== null;
            };

            // Always allow super admin.
            if ($userRole->isSuperAdmin()) {
                return true;
            }

            // Only allow global admin if not also super admin.
            if ($userRole->isGlobalAdmin()) {
                return !$isSuperAdmin;
            }

            // Only allow organisation admin if not also super admin or global admin.
            if ($userRole->isOrganisationAdmin()) {
                return !$isSuperAdmin && !$isGlobalAdmin;
            }

            // Only allow service admin if not also super admin, global admin or organisation admin.
            if ($userRole->isServiceAdmin()) {
                return !$isSuperAdmin
                    && !$isGlobalAdmin
                    && !$isOrganisationAdmin($userRole);
            }

            // Only allow service worker if not also super admin, global admin, organisation admin or service admin.
            if ($userRole->isServiceWorker()) {
                return !$isSuperAdmin
                    && !$isGlobalAdmin
                    && !$isOrganisationAdmin($userRole)
                    && !$isServiceAdmin($userRole);
            }

            throw new \Exception("User role invalid [{$userRole->role->name}]");
        });
    }
}
