<?php

namespace App\Sms\OtpLoginCode;

use App\Sms\Sms;

class UserSms extends Sms
{
    /**
     * @return string
     */
    protected function getTemplateId(): string
    {
        return config('ck.notifications_template_ids.otp_login_code.sms');
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
        return '';
    }
}
