<?php

namespace App\Listeners\Notifications;

use App\Emails\UserRolesUpdated\NotifyUserEmail;
use App\Events\UserRolesUpdated as UserRolesUpdatedEvent;

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
        $event->user->sendEmail(new NotifyUserEmail($event->user->email, [
            'NAME' => $event->user->first_name,
            'OLD_PERMISSIONS' => $event->oldRoles->toJson(),
            'NEW_PERMISSIONS' => $event->newRoles->toJson(),
        ]));
    }
}
