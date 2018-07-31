<?php

namespace App\Events\CollectionPersona\Image;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Collection;
use Illuminate\Http\Request;

class ImageRead extends EndpointHit
{
    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Collection $persona
     */
    public function __construct(Request $request, Collection $persona)
    {
        parent::__construct($request);

        $this->action = Audit::ACTION_READ;
        $this->description = "Viewed collection persona image [{$persona->id}]";
    }
}
