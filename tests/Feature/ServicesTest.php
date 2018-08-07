<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ServicesTest extends TestCase
{
    /*
     * List all the services.
     */

    public function test_guest_can_list_them()
    {
        $service = factory(Service::class)->create();

        $response = $this->json('GET', '/core/v1/services');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $service->id,
            'organisation_id' => $service->organisation_id,
            'name' => $service->name,
            'status' => $service->status,
            'intro' => $service->intro,
            'description' => $service->description,
            'wait_time' => $service->wait_time,
            'is_free' => $service->is_free,
            'fees_text' => $service->fees_text,
            'fees_url' => $service->fees_url,
            'testimonial' => $service->testimonial,
            'video_embed' => $service->video_embed,
            'url' => $service->url,
            'contact_name' => $service->contact_name,
            'contact_phone' => $service->contact_phone,
            'contact_email' => $service->contact_email,
            'show_referral_disclaimer' => $service->show_referral_disclaimer,
            'referral_method' => $service->referral_method,
            'referral_button_text' => $service->referral_button_text,
            'referral_email' => $service->referral_email,
            'referral_url' => $service->referral_url,
            'criteria' => [
                'age_group' => $service->serviceCriterion->age_group,
                'disability' => $service->serviceCriterion->disability,
                'employment' => $service->serviceCriterion->employment,
                'gender' => $service->serviceCriterion->gender,
                'housing' => $service->serviceCriterion->housing,
                'income' => $service->serviceCriterion->income,
                'language' => $service->serviceCriterion->language,
                'other' => $service->serviceCriterion->other,
            ],
            'seo_title' => $service->seo_title,
            'seo_description' => $service->seo_description,
            'useful_infos' => [],
            'social_medias' => [],
            'category_taxonomies' => [],
            'created_at' => $service->created_at->format(Carbon::ISO8601)
        ]);
    }

    /*
     * Create a service.
     */

    /*
     * Get a specific service.
     */

    /*
     * Update a specific service.
     */

    /*
     * Delete a specific service.
     */

    /*
     * Get a specific service's logo.
     */

    /*
     * Upload a specific service's logo.
     */

    /*
     * Delete a specific service's logo.
     */

    /*
     * Get a specific service's SEO image.
     */

    /*
     * Upload a specific service's SEO image.
     */

    /*
     * Delete a specific service's SEO image.
     */
}
