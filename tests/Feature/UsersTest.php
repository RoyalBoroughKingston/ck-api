<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UsersTest extends TestCase
{
    /*
     * ==================================================
     * List all the users.
     * ==================================================
     */

    public function test_guest_cannot_list_them()
    {
        $response = $this->json('GET', '/core/v1/users');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_can_list_them()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);
        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/users');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'roles' => [
                [
                    'role' => Role::NAME_SERVICE_WORKER,
                    'service_id' => $service->id,
                ]
            ],
        ]);
    }

    /*
     * ==================================================
     * Create a user.
     * ==================================================
     */

    /*
     * Guest Invoked.
     */
    public function test_guest_cannot_create_one()
    {
        $response = $this->json('POST', '/core/v1/users', $this->getCreateUserPayload([
            [
                'role' => Role::NAME_SUPER_ADMIN,
            ]
        ]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /*
     * Service Worker Invoked.
     */
    public function test_service_worker_cannot_create_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);
        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/users', $this->getCreateUserPayload([
            [
                'role' => Role::NAME_SUPER_ADMIN,
            ]
        ]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /*
     * Service Admin Invoked.
     */
    public function test_service_admin_cannot_create_service_worker_for_another_service()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/users', $this->getCreateUserPayload([
            [
                'role' => Role::NAME_SERVICE_WORKER,
                'service_id' => factory(Service::class)->create()->id,
            ]
        ]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_can_create_service_worker_for_their_service()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/users', $this->getCreateUserPayload([
            [
                'role' => Role::NAME_SERVICE_WORKER,
                'service_id' => $service->id,
            ]
        ]));

        $response->assertStatus(Response::HTTP_CREATED);
        $createdUserId = json_decode($response->getContent(), true)['data']['id'];
        $createdUser = User::findOrFail($createdUserId);
        $this->assertTrue($createdUser->isServiceWorker($service));
        $this->assertEquals(1, $createdUser->roles()->count());
    }

    public function test_service_admin_cannot_create_service_admin_for_another_service()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/users', $this->getCreateUserPayload([
            [
                'role' => Role::NAME_SERVICE_ADMIN,
                'service_id' => factory(Service::class)->create()->id,
            ]
        ]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_can_create_service_admin_for_their_service()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/users', $this->getCreateUserPayload([
            [
                'role' => Role::NAME_SERVICE_ADMIN,
                'service_id' => $service->id,
            ]
        ]));

        $response->assertStatus(Response::HTTP_CREATED);
        $createdUserId = json_decode($response->getContent(), true)['data']['id'];
        $createdUser = User::findOrFail($createdUserId);
        $this->assertTrue($createdUser->isServiceWorker($service));
        $this->assertTrue($createdUser->isServiceAdmin($service));
        $this->assertEquals(2, $createdUser->roles()->count());
    }

    public function test_service_admin_cannot_create_organisation_admin()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/users', $this->getCreateUserPayload([
            [
                'role' => Role::NAME_ORGANISATION_ADMIN,
                'organisation_id' => $service->organisation->id,
            ]
        ]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /*
     * Organisation Admin Invoked.
     */
    public function test_organisation_admin_cannot_create_service_worker_for_another_service()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($service->organisation);
        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/users', $this->getCreateUserPayload([
            [
                'role' => Role::NAME_SERVICE_WORKER,
                'service_id' => factory(Service::class)->create()->id,
            ]
        ]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_can_create_service_worker_for_their_service()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($service->organisation);
        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/users', $this->getCreateUserPayload([
            [
                'role' => Role::NAME_SERVICE_WORKER,
                'service_id' => $service->id,
            ]
        ]));

        $response->assertStatus(Response::HTTP_CREATED);
        $createdUserId = json_decode($response->getContent(), true)['data']['id'];
        $createdUser = User::findOrFail($createdUserId);
        $this->assertTrue($createdUser->isServiceWorker($service));
        $this->assertEquals(1, $createdUser->roles()->count());
    }

    public function test_organisation_admin_cannot_create_service_admin_for_another_service()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($service->organisation);
        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/users', $this->getCreateUserPayload([
            [
                'role' => Role::NAME_SERVICE_ADMIN,
                'service_id' => factory(Service::class)->create()->id,
            ]
        ]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_can_create_service_admin_for_their_service()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($service->organisation);
        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/users', $this->getCreateUserPayload([
            [
                'role' => Role::NAME_SERVICE_ADMIN,
                'service_id' => $service->id,
            ]
        ]));

        $response->assertStatus(Response::HTTP_CREATED);
        $createdUserId = json_decode($response->getContent(), true)['data']['id'];
        $createdUser = User::findOrFail($createdUserId);
        $this->assertTrue($createdUser->isServiceWorker($service));
        $this->assertTrue($createdUser->isServiceAdmin($service));
        $this->assertEquals(2, $createdUser->roles()->count());
    }

    public function test_organisation_admin_cannot_create_organisation_admin_for_another_organisation()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($service->organisation);
        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/users', $this->getCreateUserPayload([
            [
                'role' => Role::NAME_ORGANISATION_ADMIN,
                'organisation_id' => factory(Organisation::class)->create()->id,
            ]
        ]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_can_create_organisation_admin_for_their_organisation()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($service->organisation);
        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/users', $this->getCreateUserPayload([
            [
                'role' => Role::NAME_ORGANISATION_ADMIN,
                'organisation_id' => $service->organisation->id,
            ]
        ]));

        $response->assertStatus(Response::HTTP_CREATED);
        $createdUserId = json_decode($response->getContent(), true)['data']['id'];
        $createdUser = User::findOrFail($createdUserId);
        $this->assertTrue($createdUser->isServiceWorker($service));
        $this->assertTrue($createdUser->isServiceAdmin($service));
        $this->assertTrue($createdUser->isOrganisationAdmin($service->organisation));
        $this->assertEquals(3, $createdUser->roles()->count());
    }

    public function test_organisation_admin_cannot_create_global_admin()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($service->organisation);
        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/users', $this->getCreateUserPayload([
            ['role' => Role::NAME_GLOBAL_ADMIN]
        ]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /*
     * Global Admin Invoked.
     */
    public function test_global_admin_can_create_service_worker()
    {
        $this->markTestIncomplete();
    }

    public function test_global_admin_can_create_service_admin()
    {
        $this->markTestIncomplete();
    }

    public function test_global_admin_can_create_organisation_admin()
    {
        $this->markTestIncomplete();
    }

    public function test_global_admin_can_create_global_admin()
    {
        $this->markTestIncomplete();
    }

    public function test_global_admin_cannot_create_super_admin()
    {
        $this->markTestIncomplete();
    }

    /*
     * Super Admin Invoked.
     */

    public function test_super_admin_can_create_service_worker()
    {
        $this->markTestIncomplete();
    }

    public function test_super_admin_can_create_service_admin()
    {
        $this->markTestIncomplete();
    }

    public function test_super_admin_can_create_organisation_admin()
    {
        $this->markTestIncomplete();
    }

    public function test_super_admin_can_create_global_admin()
    {
        $this->markTestIncomplete();
    }

    public function test_super_admin_can_create_super_admin()
    {
        $this->markTestIncomplete();
    }

    /*
     * ==================================================
     * Get a specific user.
     * ==================================================
     */

    /*
     * ==================================================
     * Update a specific user.
     * ==================================================
     */

    /*
     * ==================================================
     * Delete a specific user.
     * ==================================================
     */

    /*
     * ==================================================
     * Helpers.
     * ==================================================
     */

    /**
     * @param array $roles
     * @return array
     */
    protected function getCreateUserPayload(array $roles): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'password' => 'password',
            'roles' => $roles,
        ];
    }
}
