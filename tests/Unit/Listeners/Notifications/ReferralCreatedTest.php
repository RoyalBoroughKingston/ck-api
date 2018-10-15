<?php

namespace Tests\Unit\Listeners\Notifications;

use App\Emails\ReferralCreated\NotifyClientEmail;
use App\Emails\ReferralCreated\NotifyRefereeEmail;
use App\Emails\ReferralCreated\NotifyServiceEmail;
use App\Events\EndpointHit;
use App\Listeners\Notifications\ReferralCreated;
use App\Models\Referral;
use App\Models\Service;
use App\Models\User;
use App\Sms\ReferralCreated\NotifyClientSms;
use App\Sms\ReferralCreated\NotifyRefereeSms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ReferralCreatedTest extends TestCase
{
    public function test_emails_sent_out()
    {
        Queue::fake();

        $service = factory(Service::class)->create([
            'referral_method' => Service::REFERRAL_METHOD_INTERNAL,
            'referral_email' => $this->faker->safeEmail,
        ]);
        $referral = factory(Referral::class)->create([
            'service_id' => $service->id,
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
        Queue::assertPushed(NotifyRefereeEmail::class, function (NotifyRefereeEmail $email) {
            $this->assertArrayHasKey('REFEREE_NAME', $email->values);
            $this->assertArrayHasKey('REFERRAL_SERVICE_NAME', $email->values);
            $this->assertArrayHasKey('REFERRAL_CONTACT_METHOD', $email->values);
            $this->assertArrayHasKey('REFERRAL_ID', $email->values);
            return true;
        });

        Queue::assertPushedOn('notifications', NotifyClientEmail::class);
        Queue::assertPushed(NotifyClientEmail::class, function (NotifyClientEmail $email) {
            $this->assertArrayHasKey('REFERRAL_SERVICE_NAME', $email->values);
            $this->assertArrayHasKey('REFERRAL_CONTACT_METHOD', $email->values);
            $this->assertArrayHasKey('REFERRAL_ID', $email->values);
            return true;
        });

        Queue::assertPushedOn('notifications', NotifyServiceEmail::class);
        Queue::assertPushed(NotifyServiceEmail::class, function (NotifyServiceEmail $email) {
            $this->assertArrayHasKey('REFERRAL_ID', $email->values);
            $this->assertArrayHasKey('REFERRAL_SERVICE_NAME', $email->values);
            $this->assertArrayHasKey('REFERRAL_INITIALS', $email->values);
            $this->assertArrayHasKey('CONTACT_INFO', $email->values);
            $this->assertArrayHasKey('REFERRAL_TYPE', $email->values);
            $this->assertArrayHasKey('REFERRAL_CONTACT_METHOD', $email->values);
            return true;
        });
    }

    public function test_sms_sent_out()
    {
        Queue::fake();

        $service = factory(Service::class)->create([
            'referral_method' => Service::REFERRAL_METHOD_INTERNAL,
            'referral_email' => $this->faker->safeEmail,
        ]);
        $referral = factory(Referral::class)->create([
            'service_id' => $service->id,
            'phone' => 'test@example.com',
            'referee_phone' => '07700000000',
            'status' => Referral::STATUS_NEW,
        ]);

        $request = Request::create('')->setUserResolver(function () {
            return factory(User::class)->create();
        });
        $event = EndpointHit::onCreate($request, '', $referral);
        $listener = new ReferralCreated();
        $listener->handle($event);

        Queue::assertPushedOn('notifications', NotifyRefereeSms::class);
        Queue::assertPushed(NotifyRefereeSms::class, function (NotifyRefereeSms $sms) {
            $this->assertArrayHasKey('REFERRAL_ID', $sms->values);
            return true;
        });

        Queue::assertPushedOn('notifications', NotifyClientSms::class);
        Queue::assertPushed(NotifyClientSms::class, function (NotifyClientSms $sms) {
            $this->assertArrayHasKey('REFERRAL_ID', $sms->values);
            return true;
        });
    }
}
