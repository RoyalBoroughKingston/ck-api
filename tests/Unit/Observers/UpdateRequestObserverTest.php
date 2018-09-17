<?php

namespace Tests\Unit\Observers;

use App\Emails\UpdateRequestReceived\NotifyGlobalAdminEmail;
use App\Emails\UpdateRequestReceived\NotifySubmitterEmail;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UpdateRequestObserverTest extends TestCase
{
    public function test_emails_sent()
    {
        Queue::fake();

        $user = factory(User::class)->create()->makeSuperAdmin();
        $organisation = factory(Organisation::class)->create();
        $organisation->updateRequests()->create([
            'user_id' => $user->id,
            'data' => [
                'slug' => 'test-org',
                'name' => 'Test Org',
                'description' => 'Lorem ipsum',
                'url' => 'https://example.com',
                'email' => 'info@example.com',
                'phone' => '07700000000',
            ],
        ]);

        Queue::assertPushedOn('notifications', NotifySubmitterEmail::class);
        Queue::assertPushedOn('notifications', NotifyGlobalAdminEmail::class);
    }
}
