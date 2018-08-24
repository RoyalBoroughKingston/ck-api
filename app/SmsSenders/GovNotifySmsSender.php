<?php

namespace App\SmsSenders;

use Alphagov\Notifications\Client;
use App\Contracts\SmsSender;
use App\Sms\Sms;

class GovNotifySmsSender implements SmsSender
{
    /**
     * @param \App\Sms\Sms $sms
     */
    public function send(Sms $sms)
    {
        /** @var \Alphagov\Notifications\Client $client */
        $client = resolve(Client::class);

        $response = $client->sendSms(
            $sms->to,
            $sms->templateId,
            $sms->values,
            $sms->reference,
            $sms->senderId
        );

        $sms->notification->update(['message' => $response['content']['body']]);

        if (config('app.debug')) {
            logger()->debug('SMS sent', $response);
        }
    }
}
