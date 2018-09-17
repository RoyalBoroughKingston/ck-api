<?php

namespace App\Listeners\Notifications;

use App\Emails\UserRolesUpdated\NotifyUserEmail;
use App\Events\UserRolesUpdated as UserRolesUpdatedEvent;
use App\Models\Role;
use App\Models\UserRole;

class UserRolesUpdated
{
    /**
     * Handle the event.
     *
     * @param \App\Events\UserRolesUpdated $event
     * @return void
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

        $oldRoles = $event->oldRoles
            ->load('role', 'organisation', 'service')
            ->map($humanReadableRole)
            ->implode(', ');

        $newRoles = $event->newRoles
            ->load('role', 'organisation', 'service')
            ->map($humanReadableRole)
            ->implode(', ');

        $event->user->sendEmail(new NotifyUserEmail($event->user->email, [
            'NAME' => $event->user->first_name,
            'OLD_PERMISSIONS' => $oldRoles,
            'PERMISSIONS' => $newRoles,
        ]));
    }
}
