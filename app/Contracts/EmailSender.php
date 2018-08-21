<?php

namespace App\Contracts;

use App\Emails\Email;

interface EmailSender
{
    /**
     * @param \App\Emails\Email $email
     * @return \App\Contracts\EmailSender
     */
    public function send(Email $email): EmailSender;
}
