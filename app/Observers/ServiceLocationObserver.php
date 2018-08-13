<?php

namespace App\Observers;

use App\Models\ServiceLocation;

class ServiceLocationObserver
{
    /**
     * Handle the service location "created" event.
     *
     * @param  \App\Models\ServiceLocation $serviceLocation
     * @return void
     */
    public function created(ServiceLocation $serviceLocation)
    {
        $serviceLocation->touchService();
    }

    /**
     * Handle the service location "deleting" event.
     *
     * @param  \App\Models\ServiceLocation $serviceLocation
     * @return void
     */
    public function deleting(ServiceLocation $serviceLocation)
    {
        $serviceLocation->updateRequests()->delete();
        $serviceLocation->regularOpeningHours()->delete();
        $serviceLocation->holidayOpeningHours()->delete();
    }

    /**
     * Handle the service location "deleted" event.
     *
     * @param  \App\Models\ServiceLocation $serviceLocation
     * @return void
     */
    public function deleted(ServiceLocation $serviceLocation)
    {
        $serviceLocation->touchService();
    }
}
