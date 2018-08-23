<?php

namespace App\Sms\ReferralIncompleted;

use App\Sms\Sms;

class NotifyClientSms extends Sms
{
    /**
     * @return string
     */
    protected function getTemplateId(): string
    {
        return config('ck.notifications_template_ids.referral_incompleted.notify_client.sms');
    }

    /**
     * @return string|null
     */
    protected function getReference(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    protected function getSenderId(): ?string
    {
        return null;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        // TODO: Return actual content.
        return 'Lorem ipsum';
    }
}
