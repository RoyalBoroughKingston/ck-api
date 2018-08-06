<?php

namespace App\Events\Referral;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Referral;
use Illuminate\Http\Request;

class ReferralRead extends EndpointHit
{
    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Referral $referral
     */
    public function __construct(Request $request, Referral $referral)
    {
        parent::__construct($request);

        $this->action = Audit::ACTION_READ;
        $this->description = "Viewed referral [{$referral->id}]";
    }
}
