<?php

namespace App\Contracts;

use App\Sms\Sms;

interface SmsSender
{
    /**
     * @param \App\Sms\Sms $sms
     */
    public function send(Sms $sms);
}
