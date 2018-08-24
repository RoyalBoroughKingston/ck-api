<?php

namespace App\Listeners\Notifications;

use App\Emails\UserCreated\NotifyUserEmail;
use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\User;

class UserCreated
{
    /**
     * Handle the event.
     *
     * @param  EndpointHit $event
     * @return void
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
        // Only send an email if email address was provided.
        $user->sendEmail(new NotifyUserEmail($user->email, [
            'NAME' => $user->first_name,
            'PERMISSIONS' => $user->userRoles()->with('role')->get()->toJson(),
        ]));
    }
}
