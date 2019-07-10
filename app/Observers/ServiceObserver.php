<?php

namespace App\Observers;

use App\Emails\StaleServiceDisabled\NotifyGlobalAdminEmail;
use App\Exceptions\CannotRevokeRoleException;
use App\Models\Notification;
use App\Models\Role;
use App\Models\Service;
use App\Models\UserRole;

class ServiceObserver
{
    /**
     * Handle the organisation "created" event.
     *
     * @param \App\Models\Service $service
     */
    public function created(Service $service)
    {
        UserRole::query()
            ->with('user')
            ->where('role_id', Role::organisationAdmin()->id)
            ->where('organisation_id', $service->organisation_id)
            ->get()
            ->each(function (UserRole $userRole) use ($service) {
                $userRole->user->makeServiceAdmin($service);
            });
    }

    /**
     * Handle the organisation "updated" event.
     *
     * @param \App\Models\Service $service
     */
    public function updated(Service $service)
    {
        // Check if the organisation_id was updated.
        if ($service->isDirty('organisation_id')) {
            // Remove old service workers and service admins.
            UserRole::query()
                ->with('user')
                ->where('service_id', $service->id)
                ->get()
                ->each(function (UserRole $userRole) use ($service) {
                    try {
                        $userRole->user->revokeServiceAdmin($service);
                    } catch (CannotRevokeRoleException $exception) {
                        // Do nothing.
                    }

                    try {
                        $userRole->user->revokeServiceWorker($service);
                    } catch (CannotRevokeRoleException $exception) {
                        // Do nothing.
                    }
                });

            // Add new service admins.
            UserRole::query()
                ->with('user')
                ->where('role_id', Role::organisationAdmin()->id)
                ->where('organisation_id', $service->organisation_id)
                ->get()
                ->each(function (UserRole $userRole) use ($service) {
                    $userRole->user->makeServiceAdmin($service);
                });
        }

        // Check if the status was updated.
        if ($service->isDirty('status')) {
            // Check if the service was disabled and last modified over a year ago.
            if (
                $service->status === Service::STATUS_INACTIVE
                && $service->getOriginal('last_modified_at')
            ) {
                Notification::sendEmail(
                    new NotifyGlobalAdminEmail(
                        config('ck.global_admin.email'),
                        ['SERVICE_NAME' => $service->name]
                    )
                );
            }
        }
    }

    /**
     * Handle the organisation "deleting" event.
     *
     * @param \App\Models\Service $service
     */
    public function deleting(Service $service)
    {
        $service->updateRequests->each->delete();
        $service->userRoles->each->delete();
        $service->referrals->each->delete();
        $service->serviceLocations->each->delete();
        $service->serviceCriterion->delete();
        $service->socialMedias->each->delete();
        $service->usefulInfos->each->delete();
        $service->serviceGalleryItems->each->delete();
        $service->serviceTaxonomies->each->delete();
        $service->offerings->each->delete();
        $service->serviceRefreshTokens->each->delete();
    }
}
