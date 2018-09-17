<?php

namespace Tests\Unit\Console\Commands\Ck;

use App\Console\Commands\Ck\SendNotificationsForUnactionedReferrals;
use App\Emails\ReferralUnactioned\NotifyServiceEmail;
use App\Models\Referral;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SendNotificationsForUnactionedReferralsTest extends TestCase
{
    public function test_emails_sent()
    {
        Queue::fake();

        factory(Referral::class)->create([
            'email' => 'test@example.com',
            'referee_email' => 'test@example.com',
            'status' => Referral::STATUS_NEW,
            'created_at' => now()->subWeekdays(6),
        ]);

        Artisan::call(SendNotificationsForUnactionedReferrals::class);

        Queue::assertPushedOn('notifications', NotifyServiceEmail::class);
    }
}
