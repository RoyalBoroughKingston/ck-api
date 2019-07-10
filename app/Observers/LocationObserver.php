<?php

namespace App\Observers;

use App\Models\Location;

class LocationObserver
{
    /**
     * Handle the location "updated" event.
     *
     * @param \App\Models\Location $location
     */
    public function updated(Location $location)
    {
        $location->touchServices();
    }

    /**
     * Handle the organisation "deleting" event.
     *
     * @param \App\Models\Location $location
     */
    public function deleting(Location $location)
    {
        $location->updateRequests->each->delete();
        $location->serviceLocations->each->delete();
    }

    /**
     * Handle the organisation "deleted" event.
     *
     * @param \App\Models\Location $location
     */
    public function deleted(Location $location)
    {
        $location->touchServices();
    }
}
