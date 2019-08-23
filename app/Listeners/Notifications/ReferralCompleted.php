<?php

namespace App\Listeners\Notifications;

use App\Emails\ReferralCompleted\NotifyClientEmail;
use App\Emails\ReferralCompleted\NotifyRefereeEmail;
use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Referral;
use App\Sms\ReferralCompleted\NotifyClientSms;
use App\Sms\ReferralCompleted\NotifyRefereeSms;

class ReferralCompleted
{
    /**
     * Handle the event.
     *
     * @param EndpointHit $event
     */
    public function handle(EndpointHit $event)
    {
        // Only handle specific endpoint events.
        if ($event->isntFor(Referral::class, Audit::ACTION_UPDATE)) {
            return;
        }

        /** @var \App\Models\StatusUpdate $latestStatusUpdate */
        $latestStatusUpdate = $event->getModel()->statusUpdates()->latest()->firstOrFail();
        $isComplete = $latestStatusUpdate->to === Referral::STATUS_COMPLETED;

        // Only handle referrals that have been marked as completed.
        if (!$latestStatusUpdate->statusHasChanged() || !$isComplete) {
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
        // Only send an email if email address was provided.
        if ($referral->email) {
            $referral->sendEmailToClient(new NotifyClientEmail($referral->email, [
                'REFERRAL_ID' => $referral->reference,
                'SERVICE_NAME' => $referral->service->name,
            ]));
        }

        // Only send an SMS if phone number was provided.
        if ($referral->phone) {
            $referral->sendSmsToClient(new NotifyClientSms($referral->phone, [
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
            $referral->sendEmailToReferee(new NotifyRefereeEmail($referral->referee_email, [
                'REFEREE_NAME' => $referral->referee_name,
                'SERVICE_NAME' => $referral->service->name,
                'REFERRAL_ID' => $referral->reference,
                'SERVICE_PHONE' => $referral->service->contact_phone ?? '(not provided)',
                'SERVICE_EMAIL' => $referral->service->contact_email ?? '(not provided)',
            ]));
        } elseif ($referral->referee_phone) {
            // Resort to SMS, but only if phone number address was provided.
            $referral->sendSmsToReferee(new NotifyRefereeSms($referral->referee_phone, [
                'REFEREE_NAME' => $referral->referee_name,
                'SERVICE_NAME' => $referral->service->name,
                'REFERRAL_ID' => $referral->reference,
                'SERVICE_PHONE' => $referral->service->contact_phone ?? '(not provided)',
                'SERVICE_EMAIL' => $referral->service->contact_email ?? '(not provided)',
            ]));
        }
    }
}
