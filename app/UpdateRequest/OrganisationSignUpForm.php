<?php

namespace App\UpdateRequest;

use App\Http\Requests\OrganisationSignUpForm\StoreRequest as StoreOrganisationSignUpFormRequest;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\UpdateRequest;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\Str;

class OrganisationSignUpForm implements AppliesUpdateRequests
{
    /**
     * Check if the update request is valid.
     *
     * @param \App\Models\UpdateRequest $updateRequest
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateUpdateRequest(UpdateRequest $updateRequest): Validator
    {
        $rules = (new StoreOrganisationSignUpFormRequest())
            ->merge($updateRequest->data)
            ->rules();

        return ValidatorFacade::make($updateRequest->data, $rules);
    }

    /**
     * Apply the update request.
     *
     * @param \App\Models\UpdateRequest $updateRequest
     * @return \App\Models\UpdateRequest
     */
    public function applyUpdateRequest(UpdateRequest $updateRequest): UpdateRequest
    {
        /** @var \App\Models\User $user */
        $user = User::create([
            'first_name' => $updateRequest->getFromData('user.first_name'),
            'last_name' => $updateRequest->getFromData('user.last_name'),
            'email' => $updateRequest->getFromData('user.email'),
            'phone' => $updateRequest->getFromData('user.phone'),
            'password' => bcrypt(Str::random()),
        ]);

        /** @var \App\Models\Organisation $organisation */
        $organisation = Organisation::create([
            'slug' => $updateRequest->getFromData('organisation.slug'),
            'name' => $updateRequest->getFromData('organisation.name'),
            'description' => sanitize_markdown(
                $updateRequest->getFromData('organisation.description')
            ),
            'url' => $updateRequest->getFromData('organisation.url'),
            'email' => $updateRequest->getFromData('organisation.email'),
            'phone' => $updateRequest->getFromData('organisation.phone'),
        ]);

        /** @var \App\Models\Service $service */
        $service = Service::create([
            'organisation_id' => $organisation->id,
            'slug' => $updateRequest->getFromData('service.slug'),
            'name' => $updateRequest->getFromData('service.name'),
            'type' => $updateRequest->getFromData('service.type'),
            'status' => Service::STATUS_INACTIVE,
            'intro' => $updateRequest->getFromData('service.intro'),
            'description' => sanitize_markdown(
                $updateRequest->getFromData('service.description')
            ),
            'wait_time' => $updateRequest->getFromData('service.wait_time'),
            'is_free' => $updateRequest->getFromData('service.is_free'),
            'fees_text' => $updateRequest->getFromData('service.fees_text'),
            'fees_url' => $updateRequest->getFromData('service.fees_url'),
            'testimonial' => $updateRequest->getFromData('service.testimonial'),
            'video_embed' => $updateRequest->getFromData('service.video_embed'),
            'url' => $updateRequest->getFromData('service.url'),
            'contact_name' => $updateRequest->getFromData('service.contact_name'),
            'contact_phone' => $updateRequest->getFromData('service.contact_phone'),
            'contact_email' => $updateRequest->getFromData('service.contact_email'),
            'show_referral_disclaimer' => false,
            'referral_method' => Service::REFERRAL_METHOD_NONE,
            'referral_button_text' => null,
            'referral_email' => null,
            'referral_url' => null,
            'logo_file_id' => null,
            'last_modified_at' => Date::now(),
        ]);

        // Create the service criterion record.
        $service->serviceCriterion()->create([
            'age_group' => $updateRequest->getFromData('service.criteria.age_group'),
            'disability' => $updateRequest->getFromData('service.criteria.disability'),
            'employment' => $updateRequest->getFromData('service.criteria.employment'),
            'gender' => $updateRequest->getFromData('service.criteria.gender'),
            'housing' => $updateRequest->getFromData('service.criteria.housing'),
            'income' => $updateRequest->getFromData('service.criteria.income'),
            'language' => $updateRequest->getFromData('service.criteria.language'),
            'other' => $updateRequest->getFromData('service.criteria.other'),
        ]);

        // Create the useful info records.
        foreach ($updateRequest->getFromData('service.useful_infos') as $usefulInfo) {
            $service->usefulInfos()->create([
                'title' => $usefulInfo['title'],
                'description' => sanitize_markdown($usefulInfo['description']),
                'order' => $usefulInfo['order'],
            ]);
        }

        // Create the offering records.
        foreach ($updateRequest->getFromData('service.offerings') as $offering) {
            $service->offerings()->create([
                'offering' => $offering['offering'],
                'order' => $offering['order'],
            ]);
        }

        // Create the social media records.
        foreach ($updateRequest->getFromData('service.social_medias') as $socialMedia) {
            $service->socialMedias()->create([
                'type' => $socialMedia['type'],
                'url' => $socialMedia['url'],
            ]);
        }

        $user->makeOrganisationAdmin($organisation->load('services'));

        return $updateRequest;
    }
}
