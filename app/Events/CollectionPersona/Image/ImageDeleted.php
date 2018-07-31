<?php

namespace App\Events\CollectionPersona\Image;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Collection;
use Illuminate\Http\Request;

class ImageDeleted extends EndpointHit
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

        $this->action = Audit::ACTION_DELETE;
        $this->description = "Deleted collection persona image [{$persona->id}]";
    }
}
