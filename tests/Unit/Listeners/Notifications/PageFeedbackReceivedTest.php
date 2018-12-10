<?php

namespace Tests\Unit\Listeners\Notifications;

use App\Emails\PageFeedbackReceived\NotifyGlobalAdminEmail;
use App\Events\EndpointHit;
use App\Listeners\Notifications\PageFeedbackReceived;
use App\Models\PageFeedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PageFeedbackReceivedTest extends TestCase
{
    public function test_email_sent_to_global_admin_email()
    {
        Queue::fake();

        $request = Request::create('')->setUserResolver(function () {
            return factory(User::class)->create();
        });
        $pageFeedback = PageFeedback::create([
            'url' => 'https://example.com',
            'feedback' => 'This page does not work',
        ]);

        $event = EndpointHit::onCreate($request, "Created page feedback [{$pageFeedback->id}]", $pageFeedback);
        $listener = new PageFeedbackReceived();
        $listener->handle($event);

        Queue::assertPushedOn('notifications', NotifyGlobalAdminEmail::class);
        Queue::assertPushed(NotifyGlobalAdminEmail::class, function (NotifyGlobalAdminEmail $email) use ($pageFeedback) {
            $this->assertEquals(config('ck.global_admin.email'), $email->to);
            $this->assertEquals(config('ck.notifications_template_ids.page_feedback_received.notify_global_admin.email'), $email->templateId);
            $this->assertArrayHasKey('FEEDBACK_URL', $email->values);
            $this->assertArrayHasKey('FEEDBACK_CONTENT', $email->values);
            $this->assertArrayHasKey('CONTACT_DETAILS_PROVIDED', $email->values);
            return true;
        });
    }
}
