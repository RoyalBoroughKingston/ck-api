<?php

namespace App\Observers;

use App\Models\Location;

class LocationObserver
{
    /**
     * Handle to the location "saving" event.
     *
     * @param  \App\Models\Location  $location
     * @return void
     */
    public function saving(Location $location)
    {
        $location->updateCoordinate();
    }
}
