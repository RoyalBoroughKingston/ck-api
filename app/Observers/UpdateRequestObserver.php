<?php

namespace App\Observers;

use App\Emails\UpdateRequestReceived\NotifyGlobalAdminEmail;
use App\Emails\UpdateRequestReceived\NotifySubmitterEmail;
use App\Models\Notification;
use App\Models\UpdateRequest;

class UpdateRequestObserver
{
    /**
     * Handle to the update request "created" event.
     *
     * @param  \App\Models\UpdateRequest  $updateRequest
     * @return void
     */
    public function created(UpdateRequest $updateRequest)
    {
        // Send notification to the submitter.
        $updateRequest->user->sendEmail(new NotifySubmitterEmail($updateRequest->user->email, [
            'SUBMITTER_NAME' => $updateRequest->user->first_name,
        ]));

        // Send notification to the global admins.
        Notification::sendEmail(
            new NotifyGlobalAdminEmail(config('ck.global_admin.email'), [
                'RESOURCE_TYPE' => $updateRequest->updateable_type,
                'RESOURCE_ID' => $updateRequest->updateable_id,
                'REQUEST_URL' => backend_uri("/update-requests/{$updateRequest->id}"),
            ])
        );
    }
}
