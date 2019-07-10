<?php

namespace App\Observers;

use App\Models\Organisation;
use App\Models\Role;
use App\Models\User;

class OrganisationObserver
{
    /**
     * Handle the organisation "created" event.
     *
     * @param \App\Models\Organisation $organisation
     */
    public function created(Organisation $organisation)
    {
        Role::globalAdmin()->users()->get()->each(function (User $user) use ($organisation) {
            $user->makeOrganisationAdmin($organisation);
        });
    }

    /**
     * Handle the organisation "updated" event.
     *
     * @param \App\Models\Organisation $organisation
     */
    public function updated(Organisation $organisation)
    {
        $organisation->touchServices();
    }

    /**
     * Handle the organisation "deleting" event.
     *
     * @param \App\Models\Organisation $organisation
     */
    public function deleting(Organisation $organisation)
    {
        $organisation->userRoles->each->delete();
        $organisation->updateRequests->each->delete();
        $organisation->services->each->delete();
    }
}
