<?php

namespace App\Events\Location;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationDeleted extends EndpointHit
{
    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Location $location
     */
    public function __construct(Request $request, Location $location)
    {
        parent::__construct($request);

        $this->action = Audit::ACTION_DELETE;
        $this->description = "Deleted location [{$location->id}]";
    }
}
