<?php

namespace App\Observers;

use App\Models\Referral;

class ReferralObserver
{
    /**
     * Handle the organisation "deleting" event.
     *
     * @param  \App\Models\Referral $referral
     * @return void
     */
    public function deleting(Referral $referral)
    {
        $referral->statusUpdates()->delete();
    }
}
