<?php

namespace App\Observers;

use App\Models\Referral;

class ReferralObserver
{
    /**
     * Handle the organisation "creating" event.
     *
     * @param \App\Models\Referral $referral
     * @throws \Exception
     */
    public function creating(Referral $referral)
    {
        if (empty($referral->reference)) {
            $referral->reference = $referral->generateReference();
        }
    }

    /**
     * Handle the organisation "deleting" event.
     *
     * @param \App\Models\Referral $referral
     */
    public function deleting(Referral $referral)
    {
        $referral->statusUpdates->each->delete();
    }
}
