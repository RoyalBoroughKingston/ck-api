<?php

namespace App\EmailSenders;

use Alphagov\Notifications\Client;
use App\Contracts\EmailSender;
use App\Emails\Email;

class GovNotifyEmailSender implements EmailSender
{
    /**
     * @param \App\Emails\Email $email
     */
    public function send(Email $email)
    {
        /** @var \Alphagov\Notifications\Client $client */
        $client = resolve(Client::class);

        $response = $client->sendEmail(
            $email->to,
            $email->templateId,
            $email->values,
            $email->reference,
            $email->replyTo
        );

        if (config('app.debug')) {
            logger()->debug('Email sent', $response);
        }
    }
}
