<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Response;
use App\Sms\OtpLoginCode\UserSms;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Config;

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

    /**
    * @test
    */
    public function loginWhenApplicationisDownForMaintenance503()
    {
        $this->artisan('down');

        $user = factory(User::class)->create(['password' => bcrypt('password')]);
        factory(\App\Models\Service::class)->create();

        // Login is prevented
        $this->get(route('login'))->assertStatus(Response::HTTP_SERVICE_UNAVAILABLE);
        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertStatus(Response::HTTP_SERVICE_UNAVAILABLE);

        // Other endpoints are available
        $this->json('GET', '/core/v1/services')->assertStatus(Response::HTTP_OK);

        $this->artisan('up');
    }
}
