<?php

namespace App\EmailSenders;

use App\Contracts\EmailSender;
use App\Emails\Email;

class LogEmailSender implements EmailSender
{
    /**
     * @param \App\Emails\Email $email
     * @return \App\Contracts\EmailSender
     */
    public function send(Email $email): EmailSender
    {
        logger()->debug('Email sent at ['.now()->toDateTimeString().']', [
            'to' => $email->to,
            'templateId' => $email->templateId,
            'values' => $email->values,
            'reference' => $email->reference,
            'replyTo' => $email->replyTo,
        ]);

        return $this;
    }
}
