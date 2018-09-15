<?php

namespace App\Observers;

use App\Models\Location;

class LocationObserver
{
    /**
     * Handle the location "updated" event.
     *
     * @param  \App\Models\Location $location
     * @return void
     */
    public function updated(Location $location)
    {
        $location->touchServices();
    }

    /**
     * Handle the organisation "deleting" event.
     *
     * @param  \App\Models\Location $location
     * @return void
     */
    public function deleting(Location $location)
    {
        $location->updateRequests()->delete();
        $location->serviceLocations()->delete();
    }

    /**
     * Handle the organisation "deleted" event.
     *
     * @param  \App\Models\Location $location
     * @return void
     */
    public function deleted(Location $location)
    {
        $location->touchServices();
    }
}
