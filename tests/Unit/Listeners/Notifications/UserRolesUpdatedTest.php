<?php

namespace Tests\Unit\Listeners\Notifications;

use App\Emails\UserRolesUpdated\NotifyUserEmail;
use App\Events\UserRolesUpdated as UserRolesUpdatedEvent;
use App\Listeners\Notifications\UserRolesUpdated as UserRolesUpdatedListener;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UserRolesUpdatedTest extends TestCase
{
    public function test_emails_sent_out()
    {
        Queue::fake();

        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);

        Request::create('')->setUserResolver(function () {
            return factory(User::class)->create();
        });
        $event = new UserRolesUpdatedEvent($user, new Collection(), $user->userRoles);
        $listener = new UserRolesUpdatedListener();
        $listener->handle($event);

        Queue::assertPushedOn('notifications', NotifyUserEmail::class);
        Queue::assertPushed(NotifyUserEmail::class, function (NotifyUserEmail $email) {
            $this->assertArrayHasKey('NAME', $email->values);
            $this->assertArrayHasKey('OLD_PERMISSIONS', $email->values);
            $this->assertArrayHasKey('PERMISSIONS', $email->values);
            return true;
        });
    }
}
