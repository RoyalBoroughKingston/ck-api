<?php

namespace Tests\Feature;

use App\Models\Audit;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuditsTest extends TestCase
{
    /*
     * List all the audits.
     */

    public function test_guest_cannot_list_them()
    {
        $response = $this->json('GET', '/core/v1/audits');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_list_them()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/audits');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_list_them()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/audits');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_list_them()
    {
        /**
         * @var \App\Models\Organisation $organisation
         * @var \App\Models\User $user
         */
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create();
        $user->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/audits');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_list_them()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $audit = Audit::create([
            'action' => Audit::ACTION_READ,
            'description' => 'Someone viewed a resource',
            'ip_address' => '127.0.0.1',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/audits');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            [
                'id' => $audit->id,
                'user_id' => null,
                'action' => Audit::ACTION_READ,
                'description' => 'Someone viewed a resource',
                'ip_address' => '127.0.0.1',
                'user_agent' => null,
                'created_at' => $this->now->format(Carbon::ISO8601),
                'updated_at' => $this->now->format(Carbon::ISO8601),
            ]
        ]);
    }

    public function test_global_admin_can_list_them_for_a_specific_user()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $audit = Audit::create([
            'user_id' => $user->id,
            'action' => Audit::ACTION_READ,
            'description' => 'Someone viewed a resource',
            'ip_address' => '127.0.0.1',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);
        $anotherAudit = factory(Audit::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/audits?filter[user_id]={$user->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            [
                'id' => $audit->id,
                'user_id' => $user->id,
                'action' => Audit::ACTION_READ,
                'description' => 'Someone viewed a resource',
                'ip_address' => '127.0.0.1',
                'user_agent' => null,
                'created_at' => $this->now->format(Carbon::ISO8601),
                'updated_at' => $this->now->format(Carbon::ISO8601),
            ]
        ]);
        $response->assertJsonMissing([
            [
                'id' => $anotherAudit->id,
                'user_id' => $anotherAudit->user_id,
                'action' => $anotherAudit->action,
                'description' => $anotherAudit->description,
                'ip_address' => $anotherAudit->ip_address,
                'user_agent' => $anotherAudit->user_agent,
                'created_at' => $anotherAudit->created_at->format(Carbon::ISO8601),
                'updated_at' => $anotherAudit->updated_at->format(Carbon::ISO8601),
            ]
        ]);
    }
}
