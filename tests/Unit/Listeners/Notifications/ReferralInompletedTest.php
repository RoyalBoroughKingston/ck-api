<?php

namespace Tests\Unit\Listeners\Notifications;

use App\Emails\ReferralIncompleted\NotifyClientEmail;
use App\Emails\ReferralIncompleted\NotifyRefereeEmail;
use App\Events\EndpointHit;
use App\Listeners\Notifications\ReferralIncompleted;
use App\Models\Referral;
use App\Models\User;
use App\Sms\ReferralIncompleted\NotifyClientSms;
use App\Sms\ReferralIncompleted\NotifyRefereeSms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ReferralInompletedTest extends TestCase
{
    public function test_emails_sent_out()
    {
        Queue::fake();

        $referral = factory(Referral::class)->create([
            'email' => 'test@example.com',
            'referee_email' => 'test@example.com',
            'status' => Referral::STATUS_INCOMPLETED,
        ]);
        $referral->statusUpdates()->create([
            'user_id' => factory(User::class)->create()->id,
            'from' => Referral::STATUS_NEW,
            'to' => Referral::STATUS_INCOMPLETED,
        ]);

        $request = Request::create('')->setUserResolver(function () {
            return factory(User::class)->create();
        });
        $event = EndpointHit::onUpdate($request, '', $referral);
        $listener = new ReferralIncompleted();
        $listener->handle($event);

        Queue::assertPushedOn('notifications', NotifyRefereeEmail::class);
        Queue::assertPushedOn('notifications', NotifyClientEmail::class);
    }

    public function test_sms_sent_out()
    {
        Queue::fake();

        $referral = factory(Referral::class)->create([
            'phone' => 'test@example.com',
            'referee_phone' => '07700000000',
            'status' => Referral::STATUS_INCOMPLETED,
        ]);
        $referral->statusUpdates()->create([
            'user_id' => factory(User::class)->create()->id,
            'from' => Referral::STATUS_NEW,
            'to' => Referral::STATUS_INCOMPLETED,
        ]);

        $request = Request::create('')->setUserResolver(function () {
            return factory(User::class)->create();
        });
        $event = EndpointHit::onUpdate($request, '', $referral);
        $listener = new ReferralIncompleted();
        $listener->handle($event);

        Queue::assertPushedOn('notifications', NotifyRefereeSms::class);
        Queue::assertPushedOn('notifications', NotifyClientSms::class);
    }
}
