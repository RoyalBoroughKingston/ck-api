<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    public function test_logged_in_user_can_logout()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user);

        $response = $this->json('POST', '/oauth/logout');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['message' => 'You have successfully logged out.']);
    }
}
