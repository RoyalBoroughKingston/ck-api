<?php

namespace Tests\Unit\Listeners\Notifications;

use App\Events\EndpointHit;
use App\Listeners\Notifications\UpdateRequestRejected;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UpdateRequestRejectedTest extends TestCase
{
    public function test_emails_sent_out_for_existing()
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
        $event = EndpointHit::onDelete($request, '', $updateRequest);
        $listener = new UpdateRequestRejected();
        $listener->handle($event);

        Queue::assertPushedOn(
            'notifications',
            \App\Emails\UpdateRequestRejected\NotifySubmitterEmail::class
        );
        Queue::assertPushed(
            \App\Emails\UpdateRequestRejected\NotifySubmitterEmail::class,
            function (\App\Emails\UpdateRequestRejected\NotifySubmitterEmail $email) {
                $this->assertArrayHasKey('SUBMITTER_NAME', $email->values);
                $this->assertArrayHasKey('RESOURCE_NAME', $email->values);
                $this->assertArrayHasKey('RESOURCE_TYPE', $email->values);
                $this->assertArrayHasKey('REQUEST_DATE', $email->values);
                return true;
            }
        );
    }

    public function test_emails_sent_out_for_new()
    {
        Queue::fake();

        $updateRequest = UpdateRequest::create([
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

        $request = Request::create('');
        $event = EndpointHit::onDelete($request, '', $updateRequest);
        $listener = new UpdateRequestRejected();
        $listener->handle($event);

        Queue::assertPushedOn(
            'notifications',
            \App\Emails\OrganisationSignUpFormRejected\NotifySubmitterEmail::class
        );
        Queue::assertPushed(
            \App\Emails\OrganisationSignUpFormRejected\NotifySubmitterEmail::class,
            function (\App\Emails\OrganisationSignUpFormRejected\NotifySubmitterEmail $email) {
                $this->assertArrayHasKey('SUBMITTER_NAME', $email->values);
                $this->assertArrayHasKey('ORGANISATION_NAME', $email->values);
                $this->assertArrayHasKey('REQUEST_DATE', $email->values);
                return true;
            }
        );
    }
}
