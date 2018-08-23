<?php

namespace App\Observers;

use App\Emails\UpdateRequestReceived\NotifyGlobalAdminEmail;
use App\Emails\UpdateRequestReceived\NotifySubmitterEmail;
use App\Models\UpdateRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

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
        User::query()
            ->whereNotNull('email')
            ->globalAdmins()
            ->chunk(200, function (Collection $users) use ($updateRequest) {
                $users->each(function (User $user) use ($updateRequest) {
                    $user->sendEmail(new NotifyGlobalAdminEmail($user->email, [
                        'NAME' => $user->first_name,
                        'RESOURCE_TYPE' => $updateRequest->updateable_type,
                        'RESOURCE_ID' => $updateRequest->updateable_id,
                        'REQUEST' => json_encode($updateRequest->data),
                    ]));
                });
            });
    }
}
