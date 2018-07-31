<?php

namespace App\Events\Audit;

use App\Events\EndpointHit;
use App\Models\Audit;
use Illuminate\Http\Request;

class AuditRead extends EndpointHit
{
    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Audit $audit
     */
    public function __construct(Request $request, Audit $audit)
    {
        parent::__construct($request);

        $this->action = Audit::ACTION_READ;
        $this->description = "Viewed audit [{$audit->id}]";
    }
}
