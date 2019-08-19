<?php

namespace App\Observers;

use App\Models\ServiceLocation;

class ServiceLocationObserver
{
    /**
     * Handle the service location "created" event.
     *
     * @param \App\Models\ServiceLocation $serviceLocation
     */
    public function created(ServiceLocation $serviceLocation)
    {
        $serviceLocation->touchService();
    }

    /**
     * Handle the service location "deleting" event.
     *
     * @param \App\Models\ServiceLocation $serviceLocation
     */
    public function deleting(ServiceLocation $serviceLocation)
    {
        $serviceLocation->updateRequests->each->delete();
        $serviceLocation->regularOpeningHours->each->delete();
        $serviceLocation->holidayOpeningHours->each->delete();
    }

    /**
     * Handle the service location "deleted" event.
     *
     * @param \App\Models\ServiceLocation $serviceLocation
     */
    public function deleted(ServiceLocation $serviceLocation)
    {
        $serviceLocation->touchService();
    }
}
