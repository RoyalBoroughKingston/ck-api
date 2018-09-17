<?php

namespace App\Listeners\Notifications;

use App\Emails\ReferralIncompleted\NotifyClientEmail;
use App\Emails\ReferralIncompleted\NotifyRefereeEmail;
use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Referral;
use App\Sms\ReferralIncompleted\NotifyClientSms;
use App\Sms\ReferralIncompleted\NotifyRefereeSms;

class ReferralIncompleted
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
        if ($event->isntFor(Referral::class, Audit::ACTION_UPDATE)) {
            return;
        }

        /** @var \App\Models\StatusUpdate $latestStatusUpdate */
        $latestStatusUpdate = $event->getModel()->statusUpdates()->latest()->firstOrFail();
        $isIncomplete = $latestStatusUpdate->to === Referral::STATUS_INCOMPLETED;

        // Only handle referrals that have been marked as completed.
        if (!$latestStatusUpdate->statusHasChanged() || !$isIncomplete) {
            return;
        }

        $this->notifyClient($event->getModel());
        $this->notifyReferee($event->getModel());
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
                'SERVICE_NAME' => $referral->service->contact_name,
            ]));
        } elseif ($referral->phone) {
            // Resort to SMS, but only if phone number address was provided.
            $referral->sendSmsToClient(new NotifyClientSms($referral->phone, [
                'CLIENT_INITIALS' => $referral->initials(),
                'REFERRAL_ID' => $referral->reference,
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
            $referral->sendEmailToClient(new NotifyRefereeEmail($referral->referee_email, [
                'REFEREE_NAME' => $referral->referee_name,
                'SERVICE_NAME' => $referral->service->contact_name,
            ]));
        } elseif ($referral->referee_phone) {
            // Resort to SMS, but only if phone number address was provided.
            $referral->sendSmsToClient(new NotifyRefereeSms($referral->referee_phone, [
                'REFEREE_NAME' => $referral->referee_name,
                'REFERRAL_ID' => $referral->reference,
            ]));
        }
    }
}
