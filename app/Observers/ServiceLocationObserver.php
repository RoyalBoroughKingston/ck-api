<?php

namespace App\Observers;

use App\Models\ServiceLocation;

class ServiceLocationObserver
{
    /**
     * Handle the organisation "deleting" event.
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
}
