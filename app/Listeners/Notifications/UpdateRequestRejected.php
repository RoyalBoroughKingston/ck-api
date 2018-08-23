<?php

namespace App\Listeners\Notifications;

use App\Emails\UpdateRequestRejected\NotifySubmitterEmail;
use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\UpdateRequest;

class UpdateRequestRejected
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
        if ($event->isntFor(UpdateRequest::class, Audit::ACTION_DELETE)) {
            return;
        }

        $this->notifySubmitter($event->getModel());
    }

    /**
     * @param \App\Models\UpdateRequest $updateRequest
     */
    protected function notifySubmitter(UpdateRequest $updateRequest)
    {
        $updateRequest->user->sendEmail(new NotifySubmitterEmail($updateRequest->user->email, [
            'SUBMITTER_NAME' => $updateRequest->user->first_name,
        ]));
    }
}