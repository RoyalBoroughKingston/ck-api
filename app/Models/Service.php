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
use App\Rules\FileIsMimeType;
use App\Sms\Sms;
use App\UpdateRequest\AppliesUpdateRequests;
use App\UpdateRequest\UpdateRequests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
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

    const TYPE_SERVICE = 'service';
    const TYPE_ACTIVITY = 'activity';
    const TYPE_CLUB = 'club';
    const TYPE_GROUP = 'group';

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
            'intro' => ['type' => 'text'],
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
            'taxonomy_categories' => [
                'type' => 'text',
                'fields' => [
                    'keyword' => ['type' => 'keyword'],
                ],
            ],
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
            'intro' => $this->intro,
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
            ->setUserResolver(function () use ($updateRequest) {
                return $updateRequest->user;
            })
            ->merge(['service' => $this])
            ->merge($updateRequest->data)
            ->rules();

        // Remove the pending assignment rule since the file is now uploaded.
        $rules['gallery_items.*.file_id'] = [
            'required_with:gallery_items.*',
            'exists:files,id',
            new FileIsMimeType(File::MIME_TYPE_PNG),
        ];
        $rules['logo_file_id'] = [
            'nullable',
            'exists:files,id',
            new FileIsMimeType(File::MIME_TYPE_PNG),
        ];

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
        $data = $updateRequest->data;

        // Update the service record.
        $this->update([
            'organisation_id' => $data['organisation_id'] ?? $this->organisation_id,
            'slug' => $data['slug'] ?? $this->slug,
            'name' => $data['name'] ?? $this->name,
            'type' => $data['type'] ?? $this->type,
            'status' => $data['status'] ?? $this->status,
            'intro' => $data['intro'] ?? $this->intro,
            'description' => sanitize_markdown($data['description'] ?? $this->description),
            'wait_time' => $data['wait_time'] ?? $this->wait_time,
            'is_free' => $data['is_free'] ?? $this->is_free,
            'fees_text' => $data['fees_text'] ?? $this->fees_text,
            'fees_url' => $data['fees_url'] ?? $this->fees_url,
            'testimonial' => $data['testimonial'] ?? $this->testimonial,
            'video_embed' => $data['video_embed'] ?? $this->video_embed,
            'url' => $data['url'] ?? $this->url,
            'contact_name' => $data['contact_name'] ?? $this->contact_name,
            'contact_phone' => $data['contact_phone'] ?? $this->contact_phone,
            'contact_email' => $data['contact_email'] ?? $this->contact_email,
            'show_referral_disclaimer' => $data['show_referral_disclaimer'] ?? $this->show_referral_disclaimer,
            'referral_method' => $data['referral_method'] ?? $this->referral_method,
            'referral_button_text' => $data['referral_button_text'] ?? $this->referral_button_text,
            'referral_email' => $data['referral_email'] ?? $this->referral_email,
            'referral_url' => $data['referral_url'] ?? $this->referral_url,
            'logo_file_id' => array_key_exists('logo_file_id', $data)
                ? $data['logo_file_id']
                : $this->logo_file_id,
        ]);

        // Update the service criterion record.
        if (array_key_exists('criteria.age_group', Arr::dot($data))) {
            $this->serviceCriterion->age_group = Arr::get($data, 'criteria.age_group');
        }
        if (array_key_exists('criteria.disability', Arr::dot($data))) {
            $this->serviceCriterion->disability = Arr::get($data, 'criteria.disability');
        }
        if (array_key_exists('criteria.employment', Arr::dot($data))) {
            $this->serviceCriterion->employment = Arr::get($data, 'criteria.employment');
        }
        if (array_key_exists('criteria.gender', Arr::dot($data))) {
            $this->serviceCriterion->gender = Arr::get($data, 'criteria.gender');
        }
        if (array_key_exists('criteria.housing', Arr::dot($data))) {
            $this->serviceCriterion->housing = Arr::get($data, 'criteria.housing');
        }
        if (array_key_exists('criteria.income', Arr::dot($data))) {
            $this->serviceCriterion->income = Arr::get($data, 'criteria.income');
        }
        if (array_key_exists('criteria.language', Arr::dot($data))) {
            $this->serviceCriterion->language = Arr::get($data, 'criteria.language');
        }
        if (array_key_exists('criteria.other', Arr::dot($data))) {
            $this->serviceCriterion->other = Arr::get($data, 'criteria.other');
        }
        $this->serviceCriterion->save();

        // Update the useful info records.
        if (array_key_exists('useful_infos', $data)) {
            $this->usefulInfos()->delete();
            foreach ($data['useful_infos'] as $usefulInfo) {
                $this->usefulInfos()->create([
                    'title' => $usefulInfo['title'],
                    'description' => sanitize_markdown($usefulInfo['description']),
                    'order' => $usefulInfo['order'],
                ]);
            }
        }

        // Update the offering records.
        if (array_key_exists('offerings', $data)) {
            $this->offerings()->delete();
            foreach ($data['offerings'] as $offering) {
                $this->offerings()->create([
                    'offering' => $offering['offering'],
                    'order' => $offering['order'],
                ]);
            }
        }

        // Update the social media records.
        if (array_key_exists('social_medias', $updateRequest->data)) {
            $this->socialMedias()->delete();
            foreach ($data['social_medias'] as $socialMedia) {
                $this->socialMedias()->create([
                    'type' => $socialMedia['type'],
                    'url' => $socialMedia['url'],
                ]);
            }
        }

        // Update the gallery item records.
        if (array_key_exists('gallery_items', $updateRequest->data)) {
            $this->serviceGalleryItems()->delete();
            foreach ($data['gallery_items'] as $galleryItem) {
                $this->serviceGalleryItems()->create([
                    'file_id' => $galleryItem['file_id'],
                ]);
            }
        }

        // Update the category taxonomy records.
        if (array_key_exists('category_taxonomies', $data)) {
            $taxonomies = Taxonomy::whereIn('id', $data['category_taxonomies'])->get();
            $this->syncServiceTaxonomies($taxonomies);
        }

        // Ensure conditional fields are reset if needed.
        $this->resetConditionalFields();

        return $updateRequest;
    }

    /**
     * Ensures conditional fields are reset to expected values.
     *
     * @return \App\Models\Service
     */
    public function resetConditionalFields(): Service
    {
        if ($this->is_free) {
            $this->update([
                'fees_text' => null,
                'fees_url' => null,
            ]);
        }

        if ($this->referral_method === static::REFERRAL_METHOD_NONE) {
            $this->update([
                'referral_button_text' => null,
                'referral_email' => null,
                'referral_url' => null,
                'show_referral_disclaimer' => false,
            ]);
        }

        if ($this->referral_method === static::REFERRAL_METHOD_INTERNAL) {
            $this->update(['referral_url' => null]);
        }

        if ($this->referral_method === static::REFERRAL_METHOD_EXTERNAL) {
            $this->update(['referral_email' => null]);
        }

        return $this;
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

    /**
     * @param int|null $maxDimension
     * @return \App\Models\File|\Illuminate\Http\Response|\Illuminate\Contracts\Support\Responsable
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException|\InvalidArgumentException
     */
    public static function placeholderLogo(int $maxDimension = null)
    {
        if ($maxDimension !== null) {
            return File::resizedPlaceholder($maxDimension, File::META_PLACEHOLDER_FOR_SERVICE);
        }

        return response()->make(
            Storage::disk('local')->get('/placeholders/service.png'),
            Response::HTTP_OK,
            ['Content-Type' => File::MIME_TYPE_PNG]
        );
    }
}
