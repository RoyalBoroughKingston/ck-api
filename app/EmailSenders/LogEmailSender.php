<?php

namespace App\EmailSenders;

use App\Contracts\EmailSender;
use App\Emails\Email;
use Illuminate\Support\Facades\Date;

class LogEmailSender implements EmailSender
{
    /**
     * @param \App\Emails\Email $email
     */
    public function send(Email $email)
    {
        logger()->debug('Email sent at [' . Date::now()->toDateTimeString() . ']', [
            'to' => $email->to,
            'templateId' => $email->templateId,
            'values' => $email->values,
            'reference' => $email->reference,
            'replyTo' => $email->replyTo,
        ]);

        $email->notification->update(['message' => 'Sent by log sender - no message content provided']);
    }
}
