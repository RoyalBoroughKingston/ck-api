<?php

namespace App\Listeners\Notifications;

use App\Emails\ReferralCreated\NotifyClientEmail;
use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Referral;
use App\Sms\ReferralCreated\NotifyClientSms;

class ReferralCreated
{
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

        $this->notifyClient($event->getModel());
        $this->notifyReferee($event->getModel());
        $this->notifyService($event->getModel());
    }

    /**
     * @param \App\Models\Referral $referral
     */
    protected function notifyClient(Referral $referral)
    {
        // Only send an email if email address was provided.
        if ($referral->email) {
            // TODO: Specify the values array.
            $referral->sendEmailToClient(new NotifyClientEmail($referral->email, []));
        }

        // Only send SMS if phone number was provided.
        if ($referral->phone) {
            // TODO: Specify the values array.
            $referral->sendSmsToClient(new NotifyClientSms($referral->phone, []));
        }
    }

    /**
     * @param \App\Models\Referral $referral
     */
    protected function notifyReferee(Referral $referral)
    {
        // TODO: Check if referee details present.

        // TODO: Send and log notifications.
    }

    /**
     * @param \App\Models\Referral $referral
     */
    protected function notifyService(Referral $referral)
    {
        // TODO: Send and log notifications.
    }
}
