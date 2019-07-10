<?php

namespace App\Listeners\Notifications;

use App\Emails\PageFeedbackReceived\NotifyGlobalAdminEmail;
use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Notification;
use App\Models\PageFeedback;

class PageFeedbackReceived
{
    /**
     * Handle the event.
     *
     * @param EndpointHit $event
     */
    public function handle(EndpointHit $event)
    {
        // Only handle specific endpoint events.
        if ($event->isntFor(PageFeedback::class, Audit::ACTION_CREATE)) {
            return;
        }

        $this->notifyGlobalAdmins($event->getModel());
    }

    /**
     * @param \App\Models\PageFeedback $pageFeedback
     */
    protected function notifyGlobalAdmins(PageFeedback $pageFeedback)
    {
        Notification::sendEmail(
            new NotifyGlobalAdminEmail(config('ck.global_admin.email'), [
                'FEEDBACK_URL' => $pageFeedback->url,
                'FEEDBACK_CONTENT' => $pageFeedback->feedback,
                'CONTACT_DETAILS_PROVIDED' => $pageFeedback->userDetailsProvided(),
            ])
        );
    }
}
