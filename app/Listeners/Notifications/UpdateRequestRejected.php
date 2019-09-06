<?php

namespace App\Listeners\Notifications;

use App\Emails\UpdateRequestRejected\NotifySubmitterEmail;
use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\ServiceLocation;
use App\Models\UpdateRequest;
use App\UpdateRequest\OrganisationSignUpForm;
use Illuminate\Support\Arr;

class UpdateRequestRejected
{
    /**
     * Handle the event.
     *
     * @param EndpointHit $event
     * @throws \Exception
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
            $this->notifySubmitterForExisting($updateRequest);
        }

        if ($updateRequest->isNew()) {
            $this->notifySubmitterForNew($updateRequest);
        }
    }

    /**
     * @param \App\Models\UpdateRequest $updateRequest
     * @throws \Exception
     */
    protected function notifySubmitterForExisting(UpdateRequest $updateRequest)
    {
        $resourceName = 'N/A';
        $resourceType = 'N/A';
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
        }

        $updateRequest->user->sendEmail(new NotifySubmitterEmail($updateRequest->user->email, [
            'SUBMITTER_NAME' => $updateRequest->user->first_name,
            'RESOURCE_NAME' => $resourceName,
            'RESOURCE_TYPE' => $resourceType,
            'REQUEST_DATE' => $updateRequest->created_at->format('j/n/Y'),
        ]));
    }

    /**
     * @param \App\Models\UpdateRequest $updateRequest
     * @throws \Exception
     */
    protected function notifySubmitterForNew(UpdateRequest $updateRequest)
    {
        if ($updateRequest->getUpdateable() instanceof OrganisationSignUpForm) {
            Notification::sendEmail(
                new \App\Emails\OrganisationSignUpFormRejected\NotifySubmitterEmail(
                    Arr::get($updateRequest->data, 'user.email'),
                    [
                        'SUBMITTER_NAME' => Arr::get($updateRequest->data, 'user.first_name'),
                        'ORGANISATION_NAME' => Arr::get($updateRequest->data, 'organisation.name'),
                        'REQUEST_DATE' => $updateRequest->created_at->format('j/n/Y'),
                    ]
                )
            );
        }
    }
}
