<?php

namespace Tests\Unit\Listeners\Notifications;

use App\Emails\ReferralCreated\NotifyClientEmail;
use App\Emails\ReferralCreated\NotifyRefereeEmail;
use App\Emails\ReferralCreated\NotifyServiceEmail;
use App\Events\EndpointHit;
use App\Listeners\Notifications\ReferralCreated;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ReferralCreatedTest extends TestCase
{
    public function test_emails_sent_out()
    {
        Queue::fake();

        $referral = factory(Referral::class)->create([
            'email' => 'test@example.com',
            'referee_email' => 'test@example.com',
            'status' => Referral::STATUS_NEW,
        ]);

        $request = Request::create('')->setUserResolver(function () {
            return factory(User::class)->create();
        });
        $event = EndpointHit::onCreate($request, '', $referral);
        $listener = new ReferralCreated();
        $listener->handle($event);

        Queue::assertPushedOn('notifications', NotifyRefereeEmail::class);
        Queue::assertPushedOn('notifications', NotifyClientEmail::class);
        Queue::assertPushedOn('notifications', NotifyServiceEmail::class);
    }
}
