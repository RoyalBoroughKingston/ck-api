<?php

namespace App\Events\CollectionCategory;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Collection;
use Illuminate\Http\Request;

class CollectionCategoryDeleted extends EndpointHit
{
    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Collection $collection
     */
    public function __construct(Request $request, Collection $collection)
    {
        parent::__construct($request);

        $this->action = Audit::ACTION_DELETE;
        $this->description = "Deleted collection category [{$collection->id}]";
    }
}
