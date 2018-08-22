<?php

namespace App\Contracts;

use App\Emails\Email;

interface EmailSender
{
    /**
     * @param \App\Emails\Email $email
     */
    public function send(Email $email);
}
