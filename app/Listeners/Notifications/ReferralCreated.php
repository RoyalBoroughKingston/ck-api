<?php

namespace App\Listeners\Notifications;

use App\Emails\ReferralCreated\NotifyClientEmail;
use App\Emails\ReferralCreated\NotifyRefereeEmail;
use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Referral;
use App\Models\Service;
use App\Sms\ReferralCreated\NotifyClientSms;
use App\Sms\ReferralCreated\NotifyRefereeSms;

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
        $this->notifyService($event->getModel()->service);
    }

    /**
     * @param \App\Models\Referral $referral
     */
    protected function notifyClient(Referral $referral)
    {
        // Only send an email if email address was provided.
        if ($referral->email) {
            $referral->sendEmailToClient(new NotifyClientEmail($referral->email, ['CLIENT_NAME' => $referral->name]));
        }

        // Only send SMS if phone number was provided.
        if ($referral->phone) {
            $referral->sendSmsToClient(new NotifyClientSms($referral->phone, ['CLIENT_NAME' => $referral->name]));
        }
    }

    /**
     * @param \App\Models\Referral $referral
     */
    protected function notifyReferee(Referral $referral)
    {
        // Only send an email if email address was provided.
        if ($referral->referee_email) {
            $referral->sendEmailToReferee(new NotifyRefereeEmail($referral->referee_email, ['REFEREE_NAME' => $referral->referee_name]));
        }

        // Only send SMS if phone number was provided.
        if ($referral->referee_phone) {
            $referral->sendSmsToReferee(new NotifyRefereeSms($referral->referee_phone, ['REFEREE_NAME' => $referral->referee_name]));
        }
    }

    /**
     * @param \App\Models\Service $service
     */
    protected function notifyService(Service $service)
    {
        // TODO: Send and log notifications.
    }
}
