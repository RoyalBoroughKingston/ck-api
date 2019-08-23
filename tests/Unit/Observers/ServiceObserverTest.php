<?php

namespace Tests\Unit\Observers;

use App\Emails\StaleServiceDisabled\NotifyGlobalAdminEmail;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Queue;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ServiceObserverTest extends TestCase
{
    public function test_service_disabled_email_sent_when_over_12_month_stale_service_updated_to_become_disabled()
    {
        Queue::fake();

        $oldNow = Date::now()->subMonths(13);

        $user = factory(User::class)->create()->makeGlobalAdmin();
        Passport::actingAs($user);

        /** @var \App\Models\Service $service */
        $service = factory(Service::class)->create([
            'status' => Service::STATUS_ACTIVE,
            'last_modified_at' => $oldNow,
            'created_at' => $oldNow,
            'updated_at' => $oldNow,
        ]);

        /** @var \App\Models\UpdateRequest $updateRequest */
        $updateRequest = $service->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => [
                'status' => Service::STATUS_INACTIVE,
            ],
        ]);

        $service->applyUpdateRequest($updateRequest);

        Queue::assertPushedOn('notifications', NotifyGlobalAdminEmail::class);
        Queue::assertPushed(NotifyGlobalAdminEmail::class, function (NotifyGlobalAdminEmail $email): bool {
            $this->assertArrayHasKey('SERVICE_NAME', $email->values);
            return true;
        });
    }
}
