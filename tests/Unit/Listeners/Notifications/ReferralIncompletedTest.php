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

class ReferralIncompletedTest extends TestCase
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
        Queue::assertPushed(NotifyRefereeEmail::class, function (NotifyRefereeEmail $email) {
            $this->assertArrayHasKey('REFEREE_NAME', $email->values);
            $this->assertArrayHasKey('SERVICE_NAME', $email->values);
            $this->assertArrayHasKey('REFERRAL_STATUS', $email->values);
            $this->assertArrayHasKey('REFERRAL_ID', $email->values);
            return true;
        });

        Queue::assertPushedOn('notifications', NotifyClientEmail::class);
        Queue::assertPushed(NotifyClientEmail::class, function (NotifyClientEmail $email) {
            $this->assertArrayHasKey('REFERRAL_ID', $email->values);
            $this->assertArrayHasKey('SERVICE_NAME', $email->values);
            $this->assertArrayHasKey('REFERRAL_STATUS', $email->values);
            return true;
        });
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
        Queue::assertPushed(NotifyRefereeSms::class, function (NotifyRefereeSms $sms) {
            $this->assertArrayHasKey('REFEREE_NAME', $sms->values);
            $this->assertArrayHasKey('REFERRAL_ID', $sms->values);
            return true;
        });

        Queue::assertPushedOn('notifications', NotifyClientSms::class);
        Queue::assertPushed(NotifyClientSms::class, function (NotifyClientSms $sms) {
            $this->assertArrayHasKey('CLIENT_INITIALS', $sms->values);
            $this->assertArrayHasKey('REFERRAL_ID', $sms->values);
            return true;
        });
    }
}
