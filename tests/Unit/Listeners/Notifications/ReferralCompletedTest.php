<?php

namespace Tests\Unit\Listeners\Notifications;

use App\Emails\ReferralCompleted\NotifyRefereeEmail;
use App\Events\EndpointHit;
use App\Listeners\Notifications\ReferralCompleted;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ReferralCompletedTest extends TestCase
{
    public function test_email_sent_to_referee()
    {
        Queue::fake();

        $referral = factory(Referral::class)->create([
            'referee_email' => 'test@example.com',
            'status' => Referral::STATUS_COMPLETED,
        ]);
        $referral->statusUpdates()->create([
            'user_id' => factory(User::class)->create()->id,
            'from' => Referral::STATUS_NEW,
            'to' => Referral::STATUS_COMPLETED,
        ]);

        $request = Request::create('')->setUserResolver(function () {
            return factory(User::class)->create();
        });
        $event = EndpointHit::onUpdate($request, '', $referral);
        $listener = new ReferralCompleted();
        $listener->handle($event);

        Queue::assertPushedOn('notifications', NotifyRefereeEmail::class);
    }
}
