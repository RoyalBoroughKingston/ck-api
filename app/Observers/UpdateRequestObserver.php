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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

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
        $this->sendCreatedNotifications($updateRequest);
        $this->removeSameFieldsForPending($updateRequest);
        $this->deleteEmptyPending($updateRequest);
    }

    /**
     * Removes the field present in the new update request from any
     * pending ones, for the same resource.
     *
     * @param \App\Models\UpdateRequest $updateRequest
     */
    protected function removeSameFieldsForPending(UpdateRequest $updateRequest)
    {
        // Skip if there is no data in the update request.
        if (count($updateRequest->data) === 0) {
            return;
        }

        $data = Arr::dot($updateRequest->data);
        $dataKeys = array_keys($data);
        foreach ($dataKeys as &$dataKey) {
            // Delete entire arrays if provided.
            $dataKey = preg_replace('/\.([0-9]+)(.*)$/', '', $dataKey);

            // Format for MySQL.
            $dataKey = "\"$.{$dataKey}\"";
        }
        $dataKeys = array_unique($dataKeys);
        $implodedDataKeys = implode(', ', $dataKeys);

        UpdateRequest::query()
            ->where('updateable_type', '=', $updateRequest->updateable_type)
            ->where('updateable_id', '=', $updateRequest->updateable_id)
            ->where('id', '!=', $updateRequest->id)
            ->pending()
            ->update(['data' => DB::raw("JSON_REMOVE(`update_requests`.`data`, {$implodedDataKeys})")]);
    }

    /**
     * Soft deletes / rejects pending update requests that have empty
     * data objects. This is called after removing the same fields
     * for new update requests.
     *
     * @param \App\Models\UpdateRequest $updateRequest
     */
    protected function deleteEmptyPending(UpdateRequest $updateRequest)
    {
        // Uses JSON_DEPTH to determine if the data object is empty (depth of 1).
        UpdateRequest::query()
            ->where('updateable_type', '=', $updateRequest->updateable_type)
            ->where('updateable_id', '=', $updateRequest->updateable_id)
            ->whereRaw('JSON_DEPTH(`update_requests`.`data`) = ?', [1])
            ->pending()
            ->delete();
    }

    /**
     * @param \App\Models\UpdateRequest $updateRequest
     */
    protected function sendCreatedNotifications(UpdateRequest $updateRequest)
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
            $resourceName = $updateRequest->updateable->name ?? $updateRequest->updateable->location->address_line_1;
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
