<?php

namespace Tests\Unit\Observers;

use App\Emails\OrganisationSignUpFormReceived\NotifyGlobalAdminEmail as NotifyOrganisationSignUpFormGlobalAdminEmailAlias;
use App\Emails\OrganisationSignUpFormReceived\NotifySubmitterEmail as NotifyOrganisationSignUpFormSubmitterEmail;
use App\Emails\UpdateRequestReceived\NotifyGlobalAdminEmail;
use App\Emails\UpdateRequestReceived\NotifySubmitterEmail;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\UpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UpdateRequestObserverTest extends TestCase
{
    public function test_emails_sent_for_existing_organisation()
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
        Queue::assertPushed(NotifySubmitterEmail::class, function (NotifySubmitterEmail $email) {
            $this->assertArrayHasKey('SUBMITTER_NAME', $email->values);
            $this->assertArrayHasKey('RESOURCE_NAME', $email->values);
            $this->assertArrayHasKey('RESOURCE_TYPE', $email->values);
            return true;
        });

        Queue::assertPushedOn('notifications', NotifyGlobalAdminEmail::class);
        Queue::assertPushed(NotifyGlobalAdminEmail::class, function (NotifyGlobalAdminEmail $email) {
            $this->assertArrayHasKey('RESOURCE_NAME', $email->values);
            $this->assertArrayHasKey('RESOURCE_TYPE', $email->values);
            $this->assertArrayHasKey('RESOURCE_ID', $email->values);
            $this->assertArrayHasKey('REQUEST_URL', $email->values);
            return true;
        });
    }

    public function test_emails_sent_for_new_organisation()
    {
        Queue::fake();

        UpdateRequest::create([
            'updateable_type' => UpdateRequest::NEW_TYPE_ORGANISATION_SIGN_UP_FORM,
            'data' => [
                'user' => [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'email' => $this->faker->safeEmail,
                    'phone' => random_uk_phone(),
                ],
                'organisation' => [
                    'slug' => 'test-org',
                    'name' => 'Test Org',
                    'description' => 'Test description',
                    'url' => 'http://test-org.example.com',
                    'email' => 'info@test-org.example.com',
                    'phone' => '07700000000',
                ],
                'service' => [
                    'slug' => 'test-service',
                    'name' => 'Test Service',
                    'type' => Service::TYPE_SERVICE,
                    'intro' => 'This is a test intro',
                    'description' => 'Lorem ipsum',
                    'wait_time' => null,
                    'is_free' => true,
                    'fees_text' => null,
                    'fees_url' => null,
                    'testimonial' => null,
                    'video_embed' => null,
                    'url' => $this->faker->url,
                    'contact_name' => $this->faker->name,
                    'contact_phone' => random_uk_phone(),
                    'contact_email' => $this->faker->safeEmail,
                    'criteria' => [
                        'age_group' => '18+',
                        'disability' => null,
                        'employment' => null,
                        'gender' => null,
                        'housing' => null,
                        'income' => null,
                        'language' => null,
                        'other' => null,
                    ],
                    'useful_infos' => [],
                    'offerings' => [],
                    'social_medias' => [],
                ],
            ],
        ]);

        Queue::assertPushedOn(
            'notifications',
            NotifyOrganisationSignUpFormSubmitterEmail::class
        );
        Queue::assertPushed(
            NotifyOrganisationSignUpFormSubmitterEmail::class,
            function (NotifyOrganisationSignUpFormSubmitterEmail $email) {
                $this->assertArrayHasKey('SUBMITTER_NAME', $email->values);
                $this->assertArrayHasKey('ORGANISATION_NAME', $email->values);
                return true;
            }
        );

        Queue::assertPushedOn(
            'notifications',
            NotifyOrganisationSignUpFormGlobalAdminEmailAlias::class
        );
        Queue::assertPushed(
            NotifyOrganisationSignUpFormGlobalAdminEmailAlias::class,
            function (NotifyOrganisationSignUpFormGlobalAdminEmailAlias $email) {
                $this->assertArrayHasKey('ORGANISATION_NAME', $email->values);
                $this->assertArrayHasKey('REQUEST_URL', $email->values);
                return true;
            }
        );
    }
}
