<?php

namespace Tests\Feature;

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
        $this->markTestIncomplete();
    }

    /*
     * Service Worker Invoked.
     */
    public function test_service_worker_cannot_create_one()
    {
        $this->markTestIncomplete();
    }

    /*
     * Service Admin Invoked.
     */
    public function test_service_admin_cannot_create_service_worker_for_another_service()
    {
        $this->markTestIncomplete();
    }

    public function test_service_admin_can_create_service_worker_for_their_service()
    {
        $this->markTestIncomplete();
    }

    public function test_service_admin_cannot_create_service_admin_for_another_service()
    {
        $this->markTestIncomplete();
    }

    public function test_service_admin_can_create_service_admin_for_their_service()
    {
        $this->markTestIncomplete();
    }

    public function test_service_admin_cannot_create_organisation_admin()
    {
        $this->markTestIncomplete();
    }

    /*
     * Organisation Admin Invoked.
     */
    public function test_organisation_admin_cannot_create_service_worker_for_another_service()
    {
        $this->markTestIncomplete();
    }

    public function test_organisation_admin_can_create_service_worker_for_their_service()
    {
        $this->markTestIncomplete();
    }

    public function test_organisation_admin_cannot_create_service_admin_for_another_service()
    {
        $this->markTestIncomplete();
    }

    public function test_organisation_admin_can_create_service_admin_for_their_service()
    {
        $this->markTestIncomplete();
    }

    public function test_organisation_admin_cannot_create_organisation_admin_for_another_organisation()
    {
        $this->markTestIncomplete();
    }

    public function test_organisation_admin_can_create_organisation_admin_for_their_organisation()
    {
        $this->markTestIncomplete();
    }

    public function test_organisation_admin_cannot_create_global_admin()
    {
        $this->markTestIncomplete();
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
}
