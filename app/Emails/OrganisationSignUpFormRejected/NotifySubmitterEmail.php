<?php

namespace App\Emails\OrganisationSignUpFormRejected;

use App\Emails\Email;

class NotifySubmitterEmail extends Email
{
    /**
     * @return string
     */
    protected function getTemplateId(): string
    {
        return config('ck.notifications_template_ids.organisation_sign_up_form_rejected.notify_submitter.email');
    }

    /**
     * @return string|null
     */
    protected function getReference(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    protected function getReplyTo(): ?string
    {
        return null;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return 'Pending to be sent. Content will be filled once sent.';
    }
}
