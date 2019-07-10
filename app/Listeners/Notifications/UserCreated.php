<?php

namespace App\Listeners\Notifications;

use App\Emails\UserCreated\NotifyUserEmail;
use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;

class UserCreated
{
    /**
     * Handle the event.
     *
     * @param EndpointHit $event
     */
    public function handle(EndpointHit $event)
    {
        // Only handle specific endpoint events.
        if ($event->isntFor(User::class, Audit::ACTION_CREATE)) {
            return;
        }

        $this->notifyUser($event->getModel());
    }

    /**
     * @param \App\Models\User $user
     */
    protected function notifyUser(User $user)
    {
        $permissions = $user
            ->userRoles()
            ->with('role', 'organisation', 'service')
            ->get()
            ->map(function (UserRole $userRole) {
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
            });
        $permissions = $permissions->implode(', ');

        // Only send an email if email address was provided.
        $user->sendEmail(new NotifyUserEmail($user->email, [
            'NAME' => $user->first_name,
            'PERMISSIONS' => $permissions,
        ]));
    }
}
