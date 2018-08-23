<?php

namespace App\Listeners\Notifications;

use App\Emails\ReferralIncompleted\NotifyClientEmail;
use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Referral;
use App\Sms\ReferralIncompleted\NotifyClientSms;

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
    }

    /**
     * @param \App\Models\Referral $referral
     */
    protected function notifyClient(Referral $referral)
    {
        // Only send an email if email address was provided.
        if ($referral->email) {
            $referral->sendEmailToClient(new NotifyClientEmail($referral->email, [
                'CLIENT_NAME' => $referral->name,
                'SERVICE_NAME' => $referral->service->contact_name,
            ]));
        }

        // Only send SMS if phone number was provided.
        if ($referral->phone) {
            $referral->sendSmsToClient(new NotifyClientSms($referral->phone, [
                'CLIENT_NAME' => $referral->name,
                'SERVICE_NAME' => $referral->service->contact_name,
            ]));
        }
    }
}