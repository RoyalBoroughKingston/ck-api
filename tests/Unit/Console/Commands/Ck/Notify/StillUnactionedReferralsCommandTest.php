<?php

namespace Tests\Unit\Console\Commands\Ck\Notify;

use App\Console\Commands\Ck\Notify\StillUnactionedReferralsCommand;
use App\Emails\ReferralStillUnactioned\NotifyGlobalAdminEmail;
use App\Models\Referral;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class StillUnactionedReferralsCommandTest extends TestCase
{
    public function test_emails_sent()
    {
        Queue::fake();

        factory(Referral::class)->create([
            'email' => 'test@example.com',
            'referee_email' => 'test@example.com',
            'status' => Referral::STATUS_NEW,
            'created_at' => Date::now()->subWeekdays(9),
        ]);

        Artisan::call(StillUnactionedReferralsCommand::class);

        Queue::assertPushedOn('notifications', NotifyGlobalAdminEmail::class);
        Queue::assertPushed(NotifyGlobalAdminEmail::class, function (NotifyGlobalAdminEmail $email) {
            $this->assertArrayHasKey('REFERRAL_SERVICE_NAME', $email->values);
            $this->assertArrayHasKey('REFERRAL_CREATED_AT', $email->values);
            $this->assertArrayHasKey('REFERRAL_TYPE', $email->values);
            $this->assertArrayHasKey('REFERRAL_INITIALS', $email->values);
            $this->assertArrayHasKey('REFERRAL_ID', $email->values);
            $this->assertArrayHasKey('SERVICE_REFERRAL_EMAIL', $email->values);
            $this->assertArrayHasKey('SERVICE_WORKERS', $email->values);
            $this->assertArrayHasKey('SERVICE_ADMINS', $email->values);
            $this->assertArrayHasKey('ORGANISATION_ADMINS', $email->values);
            return true;
        });
    }
}
