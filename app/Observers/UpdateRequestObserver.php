<?php

namespace App\Observers;

use App\Emails\UpdateRequestReceived\NotifyGlobalAdminEmail;
use App\Emails\UpdateRequestReceived\NotifySubmitterEmail;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\ServiceLocation;
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
        $resourceName = null;
        $resourceType = null;
        if ($updateRequest->updateable instanceof Location) {
            $resourceName = $updateRequest->updateable->address_line_1;
            $resourceType = 'location';
        } elseif ($updateRequest->updateable instanceof Service) {
            $resourceName = $updateRequest->updateable->name;
            $resourceType = 'service';
        } elseif ($updateRequest->updateable instanceof ServiceLocation) {
            $resourceName = $updateRequest->updateable->name || $updateRequest->updateable->id;
            $resourceType = 'service location';
        } elseif ($updateRequest->updateable instanceof Organisation) {
            $resourceName = $updateRequest->updateable->name;
            $resourceType = 'organisation';
        } else {
            $resourceName = 'N/A';
            $resourceType = 'N/A';
        }

        // Send notification to the submitter.
        $updateRequest->user->sendEmail(new NotifySubmitterEmail($updateRequest->user->email, [
            'SUBMITTER_NAME' => $updateRequest->user->first_name,
            'RESOURCE_NAME' => $resourceName,
            'RESOURCE_TYPE' => $resourceType,
        ]));

        // Send notification to the global admins.
        Notification::sendEmail(
            new NotifyGlobalAdminEmail(config('ck.global_admin.email'), [
                'RESOURCE_NAME' => $resourceName,
                'RESOURCE_TYPE' => $resourceType,
                'RESOURCE_ID' => $updateRequest->updateable_id,
                'REQUEST_URL' => backend_uri("/update-requests/{$updateRequest->id}"),
            ])
        );
    }
}
