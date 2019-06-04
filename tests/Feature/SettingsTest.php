<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    /*
     * List all the settings.
     */

    public function test_guest_can_list_them()
    {
        $response = $this->getJson('/settings');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_structure_correct_when_listed()
    {
        $response = $this->getJson('/settings');

        $response->assertJsonStructure([
            'data' => [
                'cms' => [
                    'frontend' => [
                        'global' => [
                            'footer_title',
                            'footer_content',
                            'contact_phone',
                            'contact_email',
                            'facebook_handle',
                            'twitter_handle',
                        ],
                        'home' => [
                            'search_title',
                            'categories_title',
                            'personas_title',
                            'personas_content',
                        ],
                        'terms_and_conditions' => [
                            'title',
                            'content',
                        ],
                        'privacy_policy' => [
                            'title',
                            'content',
                        ],
                        'about' => [
                            'title',
                            'content',
                            'video_url',
                        ],
                        'contact' => [
                            'title',
                            'content',
                        ],
                        'get_involved' => [
                            'title',
                            'content',
                        ],
                        'favourites' => [
                            'title',
                            'content',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_values_correct_when_listed()
    {
        $response = $this->getJson('/settings');

        $response->assertJson([
            'data' => [
                'cms' => [
                    'frontend' => [
                        'global' => [
                            'footer_title' => 'Footer title',
                            'footer_content' => 'Footer content',
                            'contact_phone' => 'Contact phone',
                            'contact_email' => 'Contact email',
                            'facebook_handle' => 'Facebook handle',
                            'twitter_handle' => 'Twitter handle',
                        ],
                        'home' => [
                            'search_title' => 'Search title',
                            'categories_title' => 'Categories title',
                            'personas_title' => 'Personas title',
                            'personas_content' => 'Personas content',
                        ],
                        'terms_and_conditions' => [
                            'title' => 'Title',
                            'content' => 'Content',
                        ],
                        'privacy_policy' => [
                            'title' => 'Title',
                            'content' => 'Content',
                        ],
                        'about' => [
                            'title' => 'Title',
                            'content' => 'Content',
                            'video_url' => 'Video URL',
                        ],
                        'contact' => [
                            'title' => 'Title',
                            'content' => 'Content',
                        ],
                        'get_involved' => [
                            'title' => 'Title',
                            'content' => 'Content',
                        ],
                        'favourites' => [
                            'title' => 'Title',
                            'content' => 'Content',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /*
     * Update the settings.
     */

    public function test_guest_cannot_update_them()
    {
        $response = $this->putJson('/settings');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_update_them()
    {
        Passport::actingAs(
            factory(User::class)->makeServiceWorker(
                factory(Service::class)->create()
            )
        );

        $response = $this->putJson('/settings');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_update_them()
    {
        Passport::actingAs(
            factory(User::class)->makeServiceAdmin(
                factory(Service::class)->create()
            )
        );

        $response = $this->putJson('/settings');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_update_them()
    {
        Passport::actingAs(
            factory(User::class)->makeOrganisationAdmin(
                factory(Organisation::class)->create()
            )
        );

        $response = $this->putJson('/settings');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_update_them()
    {
        Passport::actingAs(
            factory(User::class)->makeGlobalAdmin()
        );

        $response = $this->putJson('/settings');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_structure_correct_when_updated()
    {
        Passport::actingAs(
            factory(User::class)->makeGlobalAdmin()
        );

        $response = $this->putJson('/settings');

        $response->assertJsonStructure([
            'data' => [
                'cms' => [
                    'frontend' => [
                        'global' => [
                            'footer_title',
                            'footer_content',
                            'contact_phone',
                            'contact_email',
                            'facebook_handle',
                            'twitter_handle',
                        ],
                        'home' => [
                            'search_title',
                            'categories_title',
                            'personas_title',
                            'personas_content',
                        ],
                        'terms_and_conditions' => [
                            'title',
                            'content',
                        ],
                        'privacy_policy' => [
                            'title',
                            'content',
                        ],
                        'about' => [
                            'title',
                            'content',
                            'video_url',
                        ],
                        'contact' => [
                            'title',
                            'content',
                        ],
                        'get_involved' => [
                            'title',
                            'content',
                        ],
                        'favourites' => [
                            'title',
                            'content',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_values_correct_when_updated()
    {
        Passport::actingAs(
            factory(User::class)->makeGlobalAdmin()
        );

        $response = $this->putJson('/settings', [
            'cms' => [
                'frontend' => [
                    'global' => [
                        'footer_title' => 'data/cms/frontend/global/footer_title',
                        'footer_content' => 'data/cms/frontend/global/footer_content',
                        'contact_phone' => 'data/cms/frontend/global/contact_phone',
                        'contact_email' => 'data/cms/frontend/global/contact_email',
                        'facebook_handle' => 'data/cms/frontend/global/facebook_handle',
                        'twitter_handle' => 'data/cms/frontend/global/twitter_handle',
                    ],
                    'home' => [
                        'search_title' => 'data/cms/frontend/home/search_title',
                        'categories_title' => 'data/cms/frontend/home/categories_title',
                        'personas_title' => 'data/cms/frontend/home/personas_title',
                        'personas_content' => 'data/cms/frontend/home/personas_content',
                    ],
                    'terms_and_conditions' => [
                        'title' => 'data/cms/frontend/terms_and_conditions/title',
                        'content' => 'data/cms/frontend/terms_and_conditions/content',
                    ],
                    'privacy_policy' => [
                        'title' => 'data/cms/frontend/privacy_policy/title',
                        'content' => 'data/cms/frontend/privacy_policy/content',
                    ],
                    'about' => [
                        'title' => 'data/cms/frontend/about/title',
                        'content' => 'data/cms/frontend/about/content',
                        'video_url' => 'data/cms/frontend/about/video_url',
                    ],
                    'contact' => [
                        'title' => 'data/cms/frontend/contact/title',
                        'content' => 'data/cms/frontend/contact/content',
                    ],
                    'get_involved' => [
                        'title' => 'data/cms/frontend/get_involved/title',
                        'content' => 'data/cms/frontend/get_involved/content',
                    ],
                    'favourites' => [
                        'title' => 'data/cms/frontend/favourites/title',
                        'content' => 'data/cms/frontend/favourites/content',
                    ],
                ],
            ],
        ]);

        $response->assertJson([
            'data' => [
                'cms' => [
                    'frontend' => [
                        'global' => [
                            'footer_title' => 'data/cms/frontend/global/footer_title',
                            'footer_content' => 'data/cms/frontend/global/footer_content',
                            'contact_phone' => 'data/cms/frontend/global/contact_phone',
                            'contact_email' => 'data/cms/frontend/global/contact_email',
                            'facebook_handle' => 'data/cms/frontend/global/facebook_handle',
                            'twitter_handle' => 'data/cms/frontend/global/twitter_handle',
                        ],
                        'home' => [
                            'search_title' => 'data/cms/frontend/home/search_title',
                            'categories_title' => 'data/cms/frontend/home/categories_title',
                            'personas_title' => 'data/cms/frontend/home/personas_title',
                            'personas_content' => 'data/cms/frontend/home/personas_content',
                        ],
                        'terms_and_conditions' => [
                            'title' => 'data/cms/frontend/terms_and_conditions/title',
                            'content' => 'data/cms/frontend/terms_and_conditions/content',
                        ],
                        'privacy_policy' => [
                            'title' => 'data/cms/frontend/privacy_policy/title',
                            'content' => 'data/cms/frontend/privacy_policy/content',
                        ],
                        'about' => [
                            'title' => 'data/cms/frontend/about/title',
                            'content' => 'data/cms/frontend/about/content',
                            'video_url' => 'data/cms/frontend/about/video_url',
                        ],
                        'contact' => [
                            'title' => 'data/cms/frontend/contact/title',
                            'content' => 'data/cms/frontend/contact/content',
                        ],
                        'get_involved' => [
                            'title' => 'data/cms/frontend/get_involved/title',
                            'content' => 'data/cms/frontend/get_involved/content',
                        ],
                        'favourites' => [
                            'title' => 'data/cms/frontend/favourites/title',
                            'content' => 'data/cms/frontend/favourites/content',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_only_changed_values_can_be_sent_when_updated()
    {
        Passport::actingAs(
            factory(User::class)->makeGlobalAdmin()
        );

        $response = $this->putJson('/settings', [
            'cms' => [
                'frontend' => [
                    'global' => [
                        'footer_title' => 'data/cms/frontend/global/footer_title',
                    ],
                ],
            ],
        ]);

        $response->assertJsonFragment([
            'footer_title' => 'data/cms/frontend/global/footer_title',
        ]);
    }
}
