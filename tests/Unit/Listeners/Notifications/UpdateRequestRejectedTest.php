<?php

namespace Tests\Unit\Listeners\Notifications;

use App\Emails\UpdateRequestRejected\NotifySubmitterEmail;
use App\Events\EndpointHit;
use App\Listeners\Notifications\UpdateRequestRejected;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UpdateRequestRejectedTest extends TestCase
{
    public function test_emails_sent_out()
    {
        Queue::fake();

        $organisation = factory(Organisation::class)->create();
        $updateRequest = $organisation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => [
                'slug' => 'test-org',
                'name' => 'Test Org',
                'description' => 'Lorem ipsum',
                'url' => 'http://example.com',
                'email' => 'info@example.com',
                'phone' => '07700000000',
            ],
        ]);

        $request = Request::create('')->setUserResolver(function () {
            return factory(User::class)->create();
        });
        $event = EndpointHit::onDelete($request, '', $updateRequest);
        $listener = new UpdateRequestRejected();
        $listener->handle($event);

        Queue::assertPushedOn('notifications', NotifySubmitterEmail::class);
    }
}
