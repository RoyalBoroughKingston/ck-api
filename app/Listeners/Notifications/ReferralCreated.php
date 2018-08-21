<?php

namespace App\Listeners\Notifications;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Referral;

class ReferralCreated
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EndpointHit $event
     * @return void
     */
    public function handle(EndpointHit $event)
    {
        // Only handle specific endpoint events.
        if ($event->isntFor(Referral::class, Audit::ACTION_CREATE)) {
            return;
        }

        logger()->debug('Referral', $event->getModel()->toArray());
    }
}
