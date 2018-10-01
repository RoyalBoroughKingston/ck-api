<?php

namespace App\Models;

use App\Emails\Email;
use App\Http\Requests\Service\UpdateRequest as UpdateServiceRequest;
use App\Models\IndexConfigurators\ServicesIndexConfigurator;
use App\Models\Mutators\ServiceMutators;
use App\Models\Relationships\ServiceRelationships;
use App\Models\Scopes\ServiceScopes;
use App\Notifications\Notifiable;
use App\Notifications\Notifications;
use App\Sms\Sms;
use App\UpdateRequest\AppliesUpdateRequests;
use App\UpdateRequest\UpdateRequests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use ScoutElastic\Searchable;

class Service extends Model implements AppliesUpdateRequests, Notifiable
{
    use DispatchesJobs;
    use Notifications;
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
            'wait_time' => ['type' => 'keyword'],
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
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'wait_time' => $this->wait_time,
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
        $rules = (new UpdateServiceRequest())
            ->merge(['service' => $this])
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
        // Update the service record.
        $this->update([
            'slug' => $updateRequest->data['slug'],
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
            'logo_file_id' => $updateRequest->data['logo_file_id'] ?? $this->logo_file_id,
            'seo_image_file_id' => $updateRequest->data['seo_image_file_id'] ?? $this->seo_image_file_id,
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
        $taxonomies = Taxonomy::whereIn('id', $updateRequest->data['category_taxonomies'])->get();
        $this->syncServiceTaxonomies($taxonomies);

        return $updateRequest;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $taxonomies
     * @return \App\Models\Service
     */
    public function syncServiceTaxonomies(EloquentCollection $taxonomies): Service
    {
        // Delete all existing service taxonomies.
        $this->serviceTaxonomies()->delete();

        // Create a service taxonomy record for each taxonomy and their parents.
        foreach ($taxonomies as $taxonomy) {
            $this->createServiceTaxonomy($taxonomy);
        }

        return $this;
    }

    /**
     * @param \App\Models\Taxonomy $taxonomy
     * @return \App\Models\ServiceTaxonomy
     */
    protected function createServiceTaxonomy(Taxonomy $taxonomy): ServiceTaxonomy
    {
        $hasParent = $taxonomy->parent !== null;
        $parentIsNotTopLevel = $taxonomy->parent->id !== Taxonomy::category()->id;

        if ($hasParent && $parentIsNotTopLevel) {
            $this->createServiceTaxonomy($taxonomy->parent);
        }

        return $this->serviceTaxonomies()->updateOrCreate(['taxonomy_id' => $taxonomy->id]);
    }

    /**
     * @param \App\Emails\Email $email
     */
    public function sendEmailToContact(Email $email)
    {
        Notification::sendEmail($email, $this);
    }

    /**
     * @param \App\Sms\Sms $sms
     */
    public function sendSmsToContact(Sms $sms)
    {
        Notification::sendSms($sms, $this);
    }

    /**
     * @param string $waitTime
     * @return bool
     */
    public static function waitTimeIsValid(string $waitTime): bool
    {
        return in_array($waitTime, [
            static::WAIT_TIME_ONE_WEEK,
            static::WAIT_TIME_TWO_WEEKS,
            static::WAIT_TIME_THREE_WEEKS,
            static::WAIT_TIME_MONTH,
            static::WAIT_TIME_LONGER,
        ]);
    }

    /**
     * @return bool
     */
    public function hasLogo(): bool
    {
        return $this->logo_file_id !== null;
    }
}
