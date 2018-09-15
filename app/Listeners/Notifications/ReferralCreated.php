<?php

namespace App\Listeners\Notifications;

use App\Emails\ReferralCreated\NotifyClientEmail;
use App\Emails\ReferralCreated\NotifyRefereeEmail;
use App\Emails\ReferralCreated\NotifyServiceEmail;
use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Referral;
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
        $this->notifyService($event->getModel());
    }

    /**
     * @param \App\Models\Referral $referral
     */
    protected function notifyClient(Referral $referral)
    {
        if ($referral->email) {
            // Only send an email if email address was provided.
            $referral->sendEmailToClient(new NotifyClientEmail($referral->email, [
                'CLIENT_NAME' => $referral->name,
            ]));
        } elseif ($referral->phone) {
            // Resort to SMS, but only if phone number address was provided.
            $referral->sendSmsToClient(new NotifyClientSms($referral->phone, [
                'CLIENT_NAME' => $referral->name,
            ]));
        }
    }

    /**
     * @param \App\Models\Referral $referral
     */
    protected function notifyReferee(Referral $referral)
    {
        if ($referral->referee_email) {
            // Only send an email if email address was provided.
            $referral->sendEmailToReferee(new NotifyRefereeEmail($referral->referee_email, [
                'REFEREE_NAME' => $referral->referee_name,
            ]));
        } elseif ($referral->referee_phone) {
            // Resort to SMS, but only if phone number address was provided.
            $referral->sendSmsToReferee(new NotifyRefereeSms($referral->referee_phone, [
                'REFEREE_NAME' => $referral->referee_name,
            ]));
        }
    }

    /**
     * @param \App\Models\Referral $referral
     */
    protected function notifyService(Referral $referral)
    {
        $referral->service->sendEmailToContact(new NotifyServiceEmail($referral->service->contact_email, [
            'CONTACT_NAME' => $referral->service->contact_name,
            'SERVICE_NAME' => $referral->service->name,
            'REFERRAL_ID' => $referral->id,
        ]));
    }
}
