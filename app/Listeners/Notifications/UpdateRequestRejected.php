<?php

namespace App\Listeners\Notifications;

use App\Emails\UpdateRequestRejected\NotifySubmitterEmail;
use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Location;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\ServiceLocation;
use App\Models\UpdateRequest;

class UpdateRequestRejected
{
    /**
     * Handle the event.
     *
     * @param EndpointHit $event
     */
    public function handle(EndpointHit $event)
    {
        // Only handle specific endpoint events.
        if ($event->isntFor(UpdateRequest::class, Audit::ACTION_DELETE)) {
            return;
        }

        /** @var \App\Models\UpdateRequest $updateRequest */
        $updateRequest = $event->getModel();

        if ($updateRequest->isExisting()) {
            $this->notifySubmitter($updateRequest);
        }
    }

    /**
     * @param \App\Models\UpdateRequest $updateRequest
     */
    protected function notifySubmitter(UpdateRequest $updateRequest)
    {
        $resourceName = null;
        $resourceType = null;
        if ($updateRequest->getUpdateable() instanceof Location) {
            $resourceName = $updateRequest->getUpdateable()->address_line_1;
            $resourceType = 'location';
        } elseif ($updateRequest->getUpdateable() instanceof Service) {
            $resourceName = $updateRequest->getUpdateable()->name;
            $resourceType = 'service';
        } elseif ($updateRequest->getUpdateable() instanceof ServiceLocation) {
            $resourceName = $updateRequest->getUpdateable()->name ?? $updateRequest->getUpdateable()->location->address_line_1;
            $resourceType = 'service location';
        } elseif ($updateRequest->getUpdateable() instanceof Organisation) {
            $resourceName = $updateRequest->getUpdateable()->name;
            $resourceType = 'organisation';
        } else {
            $resourceName = 'N/A';
            $resourceType = 'N/A';
        }

        $updateRequest->user->sendEmail(new NotifySubmitterEmail($updateRequest->user->email, [
            'SUBMITTER_NAME' => $updateRequest->user->first_name,
            'RESOURCE_NAME' => $resourceName,
            'RESOURCE_TYPE' => $resourceType,
            'REQUEST_DATE' => $updateRequest->created_at->format('j/n/Y'),
        ]));
    }
}
