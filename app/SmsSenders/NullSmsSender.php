<?php

namespace App\SmsSenders;

use App\Contracts\SmsSender;
use App\Sms\Sms;

class NullSmsSender implements SmsSender
{
    /**
     * @param \App\Sms\Sms $sms
     */
    public function send(Sms $sms)
    {
        $sms->notification->update(['message' => 'Sent by null sender - no message content provided']);
    }
}
