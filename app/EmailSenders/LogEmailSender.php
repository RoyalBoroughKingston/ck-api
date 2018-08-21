<?php

namespace App\EmailSenders;

use App\Contracts\EmailSender;
use App\Emails\Email;

class LogEmailSender implements EmailSender
{
    /**
     * @param \App\Emails\Email $email
     * @return string
     */
    public function send(Email $email): string
    {
        logger()->debug('Email sent at ['.now()->toDateTimeString().']', [
            'to' => $email->to,
            'templateId' => $email->templateId,
            'values' => $email->values,
            'reference' => $email->reference,
            'replyTo' => $email->replyTo,
        ]);

        return 'Dummy content';
    }
}
