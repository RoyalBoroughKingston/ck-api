<?php

namespace App\Observers;

use App\Emails\UpdateRequestReceived\NotifySubmitterEmail;
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
        $updateRequest->user->sendEmail(new NotifySubmitterEmail($updateRequest->user->email, [
            'SUBMITTER_NAME' => $updateRequest->user->first_name,
        ]));
    }
}
