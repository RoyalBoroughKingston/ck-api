<?php

namespace App\Listeners\Notifications;

use App\Emails\ServiceCreated\NotifyGlobalAdminEmail;
use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Notification;
use App\Models\Service;
use App\Models\User;

class ServiceCreated
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
        if ($event->isntFor(Service::class, Audit::ACTION_CREATE)) {
            return;
        }

        $this->notifyGlobalAdmins($event->getModel(), $event->getUser());
    }

    /**
     * @param \App\Models\Service $service
     * @param \App\Models\User $user
     */
    protected function notifyGlobalAdmins(Service $service, User $user)
    {
        Notification::sendEmail(
            new NotifyGlobalAdminEmail(config('ck.global_admin.email'), [
                'SERVICE_NAME' => $service->name,
                'ORGANISATION_ADMIN_NAME' => $user->full_name,
                'ORGANISATION_NAME' => $service->organisation->name,
                'ORGANISATION_ADMIN_EMAIL' => $user->email,
                'SERVICE_URL' => backend_uri("/services/{$service->id}"),
            ])
        );
    }
}
