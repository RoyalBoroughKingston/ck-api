<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Sms\OtpLoginCode\UserSms;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_otp_sms_sent_to_user()
    {
        Config::set('ck.otp_enabled', true);

        Queue::fake();

        $user = factory(User::class)->create(['password' => bcrypt('password')]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        Queue::assertPushedOn('notifications', UserSms::class);
        Queue::assertPushed(UserSms::class, function (UserSms $sms) {
            $this->assertArrayHasKey('OTP_CODE', $sms->values);
            return true;
        });
    }
}
