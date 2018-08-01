<?php

namespace App\Events\Organisation;

use App\Events\EndpointHit;
use App\Models\Audit;
use Illuminate\Http\Request;

class OrganisationsListed extends EndpointHit
{
    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->action = Audit::ACTION_READ;
        $this->description = 'Viewed all organisations';
    }
}
