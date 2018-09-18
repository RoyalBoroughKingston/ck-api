<?php

namespace Tests\Unit\Listeners\Notifications;

use App\Emails\UpdateRequestApproved\NotifySubmitterEmail;
use App\Events\EndpointHit;
use App\Listeners\Notifications\UpdateRequestApproved;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UpdateRequestApprovedTest extends TestCase
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
        $event = EndpointHit::onUpdate($request, '', $updateRequest);
        $listener = new UpdateRequestApproved();
        $listener->handle($event);

        Queue::assertPushedOn('notifications', NotifySubmitterEmail::class);
        Queue::assertPushed(NotifySubmitterEmail::class, function (NotifySubmitterEmail $email) {
            $this->assertArrayHasKey('SUBMITTER_NAME', $email->values);
            $this->assertArrayHasKey('RESOURCE_NAME', $email->values);
            $this->assertArrayHasKey('RESOURCE_TYPE', $email->values);
            $this->assertArrayHasKey('REQUEST_DATE', $email->values);
            return true;
        });
    }
}
