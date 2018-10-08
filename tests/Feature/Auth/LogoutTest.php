<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Laravel\Passport\Passport;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    public function test_logged_in_user_can_logout()
    {
        Config::set('ck.otp_enabled', false);

        $user = factory(User::class)->create(['password' => bcrypt('secret')]);

        $this->post('/login', [
            '_token' => csrf_token(),
            'email' => $user->email,
            'password' => 'secret',
        ]);
        $this->assertDatabaseHas('sessions', ['user_id' => $user->id]);

        Passport::actingAs($user);
        $response = $this->json('DELETE', '/core/v1/users/user/sessions');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['message' => 'All your sessions have been cleared.']);
        $this->assertDatabaseMissing('sessions', ['user_id' => $user->id]);
    }
}
