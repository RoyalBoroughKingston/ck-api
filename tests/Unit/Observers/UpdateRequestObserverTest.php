<?php

namespace Tests\Unit\Observers;

use App\Emails\UpdateRequestReceived\NotifyGlobalAdminEmail;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UpdateRequestObserverTest extends TestCase
{
    public function test_email_sent_to_global_admin_when_update_request_created()
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

        Queue::assertPushedOn('notifications', NotifyGlobalAdminEmail::class);
        Queue::assertPushed(NotifyGlobalAdminEmail::class, function (NotifyGlobalAdminEmail $email) {
            if ($email->to !== config('ck.global_admin.email')){
                return false;
            }

            return true;
        });
    }
}
