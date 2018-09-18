<?php

namespace Tests\Unit\Listeners\Notifications;

use App\Emails\UserCreated\NotifyUserEmail;
use App\Events\EndpointHit;
use App\Listeners\Notifications\UserCreated;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UserCreatedTest extends TestCase
{
    public function test_emails_sent_out()
    {
        Queue::fake();

        $user = factory(User::class)->create();

        $request = Request::create('')->setUserResolver(function () {
            return factory(User::class)->create();
        });
        $event = EndpointHit::onCreate($request, '', $user);
        $listener = new UserCreated();
        $listener->handle($event);

        Queue::assertPushedOn('notifications', NotifyUserEmail::class);
        Queue::assertPushed(NotifyUserEmail::class, function (NotifyUserEmail $email) {
            $this->assertArrayHasKey('NAME', $email->values);
            $this->assertArrayHasKey('PERMISSIONS', $email->values);
            return true;
        });
    }
}
