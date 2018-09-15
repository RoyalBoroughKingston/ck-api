<?php

namespace App\Observers;

use App\Models\Role;
use App\Models\Service;
use App\Models\UserRole;

class ServiceObserver
{
    /**
     * Handle the organisation "created" event.
     *
     * @param  \App\Models\Service $service
     * @return void
     */
    public function created(Service $service)
    {
        UserRole::query()
            ->where('role_id', Role::organisationAdmin()->id)
            ->where('organisation_id', $service->organisation_id)
            ->get()
            ->each(function (UserRole $userRole) use ($service) {
                $userRole->user->makeServiceAdmin($service);
            });
    }

    /**
     * Handle the organisation "deleting" event.
     *
     * @param  \App\Models\Service $service
     * @return void
     */
    public function deleting(Service $service)
    {
        $service->updateRequests()->delete();
        $service->userRoles()->delete();
        $service->referrals()->delete();
        $service->serviceLocations()->delete();
        $service->serviceCriterion()->delete();
        $service->socialMedias()->delete();
        $service->usefulInfos()->delete();
        $service->serviceTaxonomies()->delete();
    }
}
