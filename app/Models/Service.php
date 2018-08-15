<?php

namespace App\Models;

use App\Http\Requests\Service\UpdateRequest as Request;
use App\IndexConfigurators\ServicesIndexConfigurator;
use App\Models\Mutators\ServiceMutators;
use App\Models\Relationships\ServiceRelationships;
use App\Models\Scopes\ServiceScopes;
use App\UpdateRequest\AppliesUpdateRequests;
use App\UpdateRequest\UpdateRequests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use ScoutElastic\Searchable;

class Service extends Model implements AppliesUpdateRequests
{
    use Searchable;
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
     * The Elasticsearch index configuration class.
     *
     * @var string
     */
    protected $indexConfigurator = ServicesIndexConfigurator::class;

    /**
     * Allows you to set different search algorithms.
     *
     * @var array
     */
    protected $searchRules = [
        //
    ];

    /**
     * The mapping for the fields.
     *
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'id' => ['type' => 'keyword'],
            'name' => [
                'type' => 'text',
                'fields' => [
                    'keyword' => ['type' => 'keyword'],
                ],
            ],
            'description' => ['type' => 'text'],
            'is_free' => ['type' => 'boolean'],
            'status' => ['type' => 'keyword'],
            'organisation_name' => [
                'type' => 'text',
                'fields' => [
                    'keyword' => ['type' => 'keyword'],
                ],
            ],
            'taxonomy_categories' => ['type' => 'keyword'],
            'collection_categories' => ['type' => 'keyword'],
            'collection_personas' => ['type' => 'keyword'],
            'service_locations' => [
                'type' => 'nested',
                'properties' => [
                    'id' => ['type' => 'keyword'],
                    'location' => ['type' => 'geo_point'],
                ],
            ],
        ]
    ];

    /**
     * Overridden to always boot searchable.
     */
    public static function bootSearchable()
    {
        self::bootScoutSearchable();
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'is_free' => $this->is_free,
            'status' => $this->status,
            'organisation_name' => $this->organisation->name,
            'taxonomy_categories' => $this->taxonomies()->pluck('name')->toArray(),
            'collection_categories' => static::collections($this)->where('type', Collection::TYPE_CATEGORY)->pluck('name')->toArray(),
            'collection_personas' => static::collections($this)->where('type', Collection::TYPE_PERSONA)->pluck('name')->toArray(),
            'service_locations' => $this->serviceLocations()
                ->with('location')
                ->get()
                ->map(function (ServiceLocation $serviceLocation) {
                    return [
                        'id' => $serviceLocation->id,
                        'location' => [
                            'lat' => $serviceLocation->location->lat,
                            'lon' => $serviceLocation->location->lon,
                        ],
                    ];
                })->toArray(),
        ];
    }

    /**
     * Check if the update request is valid.
     *
     * @param \App\Models\UpdateRequest $updateRequest
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateUpdateRequest(UpdateRequest $updateRequest): Validator
    {
        $request = new Request();
        $request->merge($updateRequest->data);
        $rules = $request->rules();

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
        // Update the service record.
        $this->update([
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
        // TODO: Add parent taxonomies.
        $this->serviceTaxonomies()->delete();
        foreach ($updateRequest->data['category_taxonomies'] as $taxonomy) {
            $this->serviceTaxonomies()->create(['taxonomy_id' => $taxonomy]);
        }

        return $updateRequest;
    }
}
