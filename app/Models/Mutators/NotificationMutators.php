<?php

namespace App\Models\Mutators;

trait NotificationMutators
{
    /**
     * @param string $recipient
     * @return string
     */
    public function getRecipientAttribute(string $recipient): string
    {
        return decrypt($recipient);
    }

    /**
     * @param string $recipient
     */
    public function setRecipientAttribute(string $recipient)
    {
        $this->attributes['recipient'] = encrypt($recipient);
    }

    /**
     * @param string $message
     * @return string
     */
    public function getMessageAttribute(string $message): string
    {
        return decrypt($message);
    }

    /**
     * @param string $message
     */
    public function setMessageAttribute(string $message)
    {
        $this->attributes['message'] = encrypt($message);
    }
}
