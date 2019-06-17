<?php

namespace Tests\Unit\Console\Commands\Ck\Notify;

use App\Console\Commands\Ck\Notify\StaleServicesCommand;
use App\Emails\ServiceUpdatePrompt\NotifyGlobalAdminEmail;
use App\Emails\ServiceUpdatePrompt\NotifyServiceAdminEmail;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class StaleServicesCommandTest extends TestCase
{
    /*
     * 6 to 12 months.
     */
    public function test_6_to_12_months_emails_not_sent_after_5_months()
    {
        Queue::fake();

        $service = factory(Service::class)->create([
            'last_modified_at' => now()->subMonths(5),
        ]);

        factory(User::class)->create()->makeServiceAdmin($service);

        Artisan::call(StaleServicesCommand::class);

        Queue::assertNotPushed(NotifyServiceAdminEmail::class);
    }

    public function test_6_to_12_months_emails_not_sent_after_13_months()
    {
        Queue::fake();

        $service = factory(Service::class)->create([
            'last_modified_at' => now()->subMonths(13),
        ]);

        factory(User::class)->create()->makeServiceAdmin($service);

        Artisan::call(StaleServicesCommand::class);

        Queue::assertNotPushed(NotifyServiceAdminEmail::class);
    }

    public function test_6_to_12_months_emails_sent_after_6_months()
    {
        Queue::fake();

        $service = factory(Service::class)->create([
            'last_modified_at' => now()->subMonths(6),
        ]);

        factory(User::class)->create()->makeServiceAdmin($service);

        Artisan::call(StaleServicesCommand::class);

        Queue::assertPushedOn('notifications', NotifyServiceAdminEmail::class);
        Queue::assertPushed(NotifyServiceAdminEmail::class, function (NotifyServiceAdminEmail $email): bool {
            $this->assertArrayHasKey('SERVICE_NAME', $email->values);
            $this->assertArrayHasKey('SERVICE_URL', $email->values);
            $this->assertArrayHasKey('SERVICE_STILL_UP_TO_DATE_URL', $email->values);
            return true;
        });
    }

    public function test_6_to_12_months_emails_sent_after_12_months()
    {
        Queue::fake();

        $service = factory(Service::class)->create([
            'last_modified_at' => now()->subMonths(12),
        ]);

        factory(User::class)->create()->makeServiceAdmin($service);

        Artisan::call(StaleServicesCommand::class);

        Queue::assertPushedOn('notifications', NotifyServiceAdminEmail::class);
        Queue::assertPushed(NotifyServiceAdminEmail::class, function (NotifyServiceAdminEmail $email): bool {
            $this->assertArrayHasKey('SERVICE_NAME', $email->values);
            $this->assertArrayHasKey('SERVICE_URL', $email->values);
            $this->assertArrayHasKey('SERVICE_STILL_UP_TO_DATE_URL', $email->values);
            return true;
        });
    }

    public function test_6_to_12_months_emails_sent_after_9_months()
    {
        Queue::fake();

        $service = factory(Service::class)->create([
            'last_modified_at' => now()->subMonths(9),
        ]);

        factory(User::class)->create()->makeServiceAdmin($service);

        Artisan::call(StaleServicesCommand::class);

        Queue::assertPushedOn('notifications', NotifyServiceAdminEmail::class);
        Queue::assertPushed(NotifyServiceAdminEmail::class, function (NotifyServiceAdminEmail $email): bool {
            $this->assertArrayHasKey('SERVICE_NAME', $email->values);
            $this->assertArrayHasKey('SERVICE_URL', $email->values);
            $this->assertArrayHasKey('SERVICE_STILL_UP_TO_DATE_URL', $email->values);
            return true;
        });
    }

    public function test_6_to_12_months_emails_not_sent_to_service_workers()
    {
        Queue::fake();

        $service = factory(Service::class)->create([
            'last_modified_at' => now()->subMonths(9),
        ]);

        factory(User::class)->create()->makeServiceWorker($service);

        Artisan::call(StaleServicesCommand::class);

        Queue::assertNotPushed(NotifyServiceAdminEmail::class);
    }

    public function test_6_to_12_months_emails_not_sent_to_global_admins()
    {
        Queue::fake();

        factory(Service::class)->create([
            'last_modified_at' => now()->subMonths(9),
        ]);

        factory(User::class)->create()->makeGlobalAdmin();

        Artisan::call(StaleServicesCommand::class);

        Queue::assertNotPushed(NotifyServiceAdminEmail::class);
    }

    /*
     * After 12 months.
     */

    public function test_after_12_months_emails_not_sent_after_11_months()
    {
        Queue::fake();

        factory(Service::class)->create([
            'last_modified_at' => now()->subMonths(11),
        ]);

        factory(User::class)->create()->makeSuperAdmin();

        Artisan::call(StaleServicesCommand::class);

        Queue::assertNotPushed(NotifyGlobalAdminEmail::class);
    }

    public function test_after_12_months_emails_not_sent_after_13_months()
    {
        Queue::fake();

        factory(Service::class)->create([
            'last_modified_at' => now()->subMonths(13),
        ]);

        factory(User::class)->create()->makeSuperAdmin();

        Artisan::call(StaleServicesCommand::class);

        Queue::assertNotPushed(NotifyGlobalAdminEmail::class);
    }

    public function test_after_12_months_emails_sent_after_12_months()
    {
        Queue::fake();

        factory(Service::class)->create([
            'last_modified_at' => now()->subMonths(12),
        ]);

        factory(User::class)->create()->makeSuperAdmin();

        Artisan::call(StaleServicesCommand::class);

        Queue::assertPushedOn('notifications', NotifyGlobalAdminEmail::class);
        Queue::assertPushed(NotifyGlobalAdminEmail::class, function (NotifyGlobalAdminEmail $email): bool {
            $this->assertArrayHasKey('SERVICE_NAME', $email->values);
            $this->assertArrayHasKey('SERVICE_URL', $email->values);
            $this->assertArrayHasKey('SERVICE_ADMIN_NAMES', $email->values);
            $this->assertArrayHasKey('SERVICE_STILL_UP_TO_DATE_URL', $email->values);
            return true;
        });
    }
}
