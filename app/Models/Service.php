<?php

namespace App\Models;

use App\Models\Mutators\ServiceMutators;
use App\Models\Relationships\ServiceRelationships;
use App\Models\Scopes\ServiceScopes;
use App\Rules\InOrder;
use App\Rules\RootTaxonomyIs;
use App\UpdateRequest\AppliesUpdateRequests;
use App\UpdateRequest\UpdateRequests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Service extends Model implements AppliesUpdateRequests
{
    use ServiceMutators;
    use ServiceRelationships;
    use ServiceScopes;
    use UpdateRequests;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const WAIT_TIME_ONE_WEEK = 'one_week';
    const WAIT_TIME_TWO_WEEKS = 'two_weeks';
    const WAIT_TIME_THREE_WEEKS = 'three_weeks';
    const WAIT_TIME_MONTH = 'month';
    const WAIT_TIME_LONGER = 'longer';

    const REFERRAL_METHOD_INTERNAL = 'internal';
    const REFERRAL_METHOD_EXTERNAL = 'external';
    const REFERRAL_METHOD_NONE = 'none';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_free' => 'boolean',
        'show_referral_disclaimer' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Check if the update request is valid.
     *
     * @param \App\Models\UpdateRequest $updateRequest
     * @return bool
     */
    public function validateUpdateRequest(UpdateRequest $updateRequest): bool
    {
        $rules = [
            'organisation_id' => ['required', 'exists:organisations,id'],
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'status' => ['required', Rule::in([
                Service::STATUS_ACTIVE,
                Service::STATUS_INACTIVE,
            ])],
            'intro' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['required', 'string', 'min:1', 'max:10000'],
            'wait_time' => ['present', 'nullable', Rule::in([
                Service::WAIT_TIME_ONE_WEEK,
                Service::WAIT_TIME_TWO_WEEKS,
                Service::WAIT_TIME_THREE_WEEKS,
                Service::WAIT_TIME_MONTH,
                Service::WAIT_TIME_LONGER,
            ])],
            'is_free' => ['required', 'boolean'],
            'fees_text' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'fees_url' => ['present', 'nullable', 'url', 'max:255'],
            'testimonial' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'video_embed' => ['present', 'nullable', 'string', 'min:1', 'max:10000'],
            'url' => ['required', 'url', 'max:255'],
            'contact_name' => ['required', 'string', 'min:1', 'max:255'],
            'contact_phone' => ['required', 'string', 'min:1', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'show_referral_disclaimer' => ['required', 'boolean'],
            'referral_method' => ['required', Rule::in([
                Service::REFERRAL_METHOD_INTERNAL,
                Service::REFERRAL_METHOD_EXTERNAL,
                Service::REFERRAL_METHOD_NONE,
            ])],
            'referral_button_text' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'referral_email' => ['present', 'nullable', 'email', 'max:255'],
            'referral_url' => ['present', 'nullable', 'url', 'max:255'],
            'criteria' => ['required', 'array'],
            'criteria.age_group' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'criteria.disability' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'criteria.employment' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'criteria.gender' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'criteria.housing' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'criteria.income' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'criteria.language' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'criteria.other' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'seo_title' => ['required', 'string', 'min:1', 'max:255'],
            'seo_description' => ['required', 'string', 'min:1', 'max:255'],

            'useful_infos' => ['present', 'array'],
            'useful_infos.*' => ['array'],
            'useful_infos.*.title' => ['required_with:useful_infos.*', 'string', 'min:1', 'max:255'],
            'useful_infos.*.description' => ['required_with:useful_infos.*', 'string', 'min:1', 'max:10000'],
            'useful_infos.*.order' => ['required_with:useful_infos.*', 'integer', 'min:1', new InOrder(array_pluck_multi($this->useful_infos, 'order'))],

            'social_medias' => ['present', 'array'],
            'social_medias.*' => ['array'],
            'social_medias.*.type' => ['required_with:social_medias.*', Rule::in([
                SocialMedia::TYPE_TWITTER,
                SocialMedia::TYPE_FACEBOOK,
                SocialMedia::TYPE_INSTAGRAM,
                SocialMedia::TYPE_YOUTUBE,
                SocialMedia::TYPE_OTHER,
            ])],
            'social_medias.*.url' => ['required_with:social_medias.*', 'url', 'max:255'],

            'category_taxonomies' => ['required', 'array'],
            'category_taxonomies.*' => ['required', 'exists:taxonomies,id', new RootTaxonomyIs(Taxonomy::NAME_CATEGORY)],
        ];

        return Validator::make($updateRequest->data, $rules)->fails() === false;
    }

    /**
     * Apply the update request.
     *
     * @param \App\Models\UpdateRequest $updateRequest
     * @return \App\Models\UpdateRequest
     */
    public function applyUpdateRequest(UpdateRequest $updateRequest): UpdateRequest
    {
        // Update the service record.
        $this->update([
            'organisation_id' => $updateRequest->data['organisation_id'],
            'name' => $updateRequest->data['name'],
            'status' => $updateRequest->data['status'],
            'intro' => $updateRequest->data['intro'],
            'description' => $updateRequest->data['description'],
            'wait_time' => $updateRequest->data['wait_time'],
            'is_free' => $updateRequest->data['is_free'],
            'fees_text' => $updateRequest->data['fees_text'],
            'fees_url' => $updateRequest->data['fees_url'],
            'testimonial' => $updateRequest->data['testimonial'],
            'video_embed' => $updateRequest->data['video_embed'],
            'url' => $updateRequest->data['url'],
            'contact_name' => $updateRequest->data['contact_name'],
            'contact_phone' => $updateRequest->data['contact_phone'],
            'contact_email' => $updateRequest->data['contact_email'],
            'show_referral_disclaimer' => $updateRequest->data['show_referral_disclaimer'],
            'referral_method' => $updateRequest->data['referral_method'],
            'referral_button_text' => $updateRequest->data['referral_button_text'],
            'referral_email' => $updateRequest->data['referral_email'],
            'referral_url' => $updateRequest->data['referral_url'],
            'seo_title' => $updateRequest->data['seo_title'],
            'seo_description' => $updateRequest->data['seo_description'],
        ]);

        // Update the service criterion record.
        $this->serviceCriterion()->update([
            'age_group' => $updateRequest->data['criteria']['age_group'],
            'disability' => $updateRequest->data['criteria']['disability'],
            'employment' => $updateRequest->data['criteria']['employment'],
            'gender' => $updateRequest->data['criteria']['gender'],
            'housing' => $updateRequest->data['criteria']['housing'],
            'income' => $updateRequest->data['criteria']['income'],
            'language' => $updateRequest->data['criteria']['language'],
            'other' => $updateRequest->data['criteria']['other'],
        ]);

        // Update the useful info records.
        $this->usefulInfos()->delete();
        foreach ($updateRequest->data['useful_infos'] as $usefulInfo) {
            $this->usefulInfos()->create([
                'title' => $usefulInfo['title'],
                'description' => $usefulInfo['description'],
                'order' => $usefulInfo['order'],
            ]);
        }

        // Update the social media records.
        $this->socialMedias()->delete();
        foreach ($updateRequest->data['social_medias'] as $socialMedia) {
            $this->socialMedias()->create([
                'type' => $socialMedia['type'],
                'url' => $socialMedia['url'],
            ]);
        }

        // Update the category taxonomy records.
        $this->serviceTaxonomies()->delete();
        foreach ($updateRequest->data['category_taxonomies'] as $taxonomy) {
            $this->serviceTaxonomies()->create(['taxonomy_id' => $taxonomy]);
        }

        return $updateRequest;
    }
}
