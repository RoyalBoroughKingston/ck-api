<?php

namespace App\Listeners\Notifications;

use App\Emails\PageFeedbackReceived\NotifyGlobalAdminEmail;
use App\Emails\PageFeedbackReceived\NotifyUserEmail;
use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\PageFeedback;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PageFeedbackReceived
{
    /**
     * Handle the event.
     *
     * @param  EndpointHit $event
     * @return void
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
        $this->getGlobalAdminsQuery()->chunk(200, function (Collection $users) use ($pageFeedback) {
            $users->each(function (User $user) use ($pageFeedback) {
                $user->sendEmail(new NotifyGlobalAdminEmail($user->email, [
                    'NAME' => $user->first_name,
                    'URL' => $pageFeedback->url,
                    'FEEDBACK' => $pageFeedback->feedback,
                ]));
            });
        });
    }

    /**
     * Get all users with email addresses that are Global Admins.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getGlobalAdminsQuery(): Builder
    {
        return User::query()
            ->whereNotNull('email')
            ->globalAdmins();
    }
}
