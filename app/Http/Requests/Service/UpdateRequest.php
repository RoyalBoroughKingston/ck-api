<?php

namespace App\Http\Requests\Service;

use App\Http\Requests\HasMissingValues;
use App\Models\Role;
use App\Models\Service;
use App\Models\SocialMedia;
use App\Models\Taxonomy;
use App\Models\UserRole;
use App\Rules\Base64EncodedPng;
use App\Rules\CanUpdateServiceCategoryTaxonomies;
use App\Rules\InOrder;
use App\Rules\RootTaxonomyIs;
use App\Rules\Slug;
use App\Rules\UserHasRole;
use App\Rules\VideoEmbed;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    use HasMissingValues;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->user()->isServiceAdmin($this->service)) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'slug' => [
                'string',
                'min:1',
                'max:255',
                Rule::unique(table(Service::class), 'slug')->ignoreModel($this->service),
                new Slug(),
                new UserHasRole(
                    $this->user(),
                    new UserRole([
                        'user_id' => $this->user()->id,
                        'role_id' => Role::globalAdmin()->id,
                    ]),
                    $this->service->slug
                )
            ],
            'name' => ['string', 'min:1', 'max:255'],
            'status' => [
                Rule::in([
                    Service::STATUS_ACTIVE,
                    Service::STATUS_INACTIVE,
                ]),
                new UserHasRole(
                    $this->user(),
                    new UserRole([
                        'user_id' => $this->user()->id,
                        'role_id' => Role::globalAdmin()->id,
                    ]),
                    $this->service->status
                )
            ],
            'intro' => ['string', 'min:1', 'max:255'],
            'description' => ['string', 'min:1', 'max:10000'],
            'wait_time' => ['nullable', Rule::in([
                Service::WAIT_TIME_ONE_WEEK,
                Service::WAIT_TIME_TWO_WEEKS,
                Service::WAIT_TIME_THREE_WEEKS,
                Service::WAIT_TIME_MONTH,
                Service::WAIT_TIME_LONGER,
            ])],
            'is_free' => ['boolean'],
            'fees_text' => ['nullable', 'string', 'min:1', 'max:255'],
            'fees_url' => ['nullable', 'url', 'max:255'],
            'testimonial' => ['nullable', 'string', 'min:1', 'max:255'],
            'video_embed' => ['nullable', 'string', 'url', 'max:255', new VideoEmbed()],
            'url' => ['url', 'max:255'],
            'contact_name' => ['nullable', 'string', 'min:1', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'min:1', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'show_referral_disclaimer' => [
                'boolean',
                new UserHasRole(
                    $this->user(),
                    new UserRole([
                        'user_id' => $this->user()->id,
                        'role_id' => Role::globalAdmin()->id,
                    ]),
                    $this->service->show_referral_disclaimer
                ),
            ],
            'referral_method' => [
                Rule::in([
                    Service::REFERRAL_METHOD_INTERNAL,
                    Service::REFERRAL_METHOD_EXTERNAL,
                    Service::REFERRAL_METHOD_NONE,
                ]),
                new UserHasRole(
                    $this->user(),
                    new UserRole([
                        'user_id' => $this->user()->id,
                        'role_id' => Role::globalAdmin()->id,
                    ]),
                    $this->service->referral_method
                ),
            ],
            'referral_button_text' => [
                'nullable',
                'string',
                'min:1',
                'max:255',
                new UserHasRole(
                    $this->user(),
                    new UserRole([
                        'user_id' => $this->user()->id,
                        'role_id' => Role::globalAdmin()->id,
                    ]),
                    $this->service->referral_button_text
                ),
            ],
            'referral_email' => [
                'required_if:referral_method,' . Service::REFERRAL_METHOD_INTERNAL,
                'nullable',
                'email',
                'max:255',
                new UserHasRole(
                    $this->user(),
                    new UserRole([
                        'user_id' => $this->user()->id,
                        'role_id' => Role::globalAdmin()->id,
                    ]),
                    $this->service->referral_email
                ),
            ],
            'referral_url' => [
                'required_if:referral_method,' . Service::REFERRAL_METHOD_EXTERNAL,
                'nullable',
                'url',
                'max:255',
                new UserHasRole(
                    $this->user(),
                    new UserRole([
                        'user_id' => $this->user()->id,
                        'role_id' => Role::globalAdmin()->id,
                    ]),
                    $this->service->referral_url
                ),
            ],
            'criteria' => ['array'],
            'criteria.age_group' => ['nullable', 'string', 'min:1', 'max:255'],
            'criteria.disability' => ['nullable', 'string', 'min:1', 'max:255'],
            'criteria.employment' => ['nullable', 'string', 'min:1', 'max:255'],
            'criteria.gender' => ['nullable', 'string', 'min:1', 'max:255'],
            'criteria.housing' => ['nullable', 'string', 'min:1', 'max:255'],
            'criteria.income' => ['nullable', 'string', 'min:1', 'max:255'],
            'criteria.language' => ['nullable', 'string', 'min:1', 'max:255'],
            'criteria.other' => ['nullable', 'string', 'min:1', 'max:255'],

            'useful_infos' => ['array'],
            'useful_infos.*' => ['array'],
            'useful_infos.*.title' => ['required_with:useful_infos.*', 'string', 'min:1', 'max:255'],
            'useful_infos.*.description' => ['required_with:useful_infos.*', 'string', 'min:1', 'max:10000'],
            'useful_infos.*.order' => [
                'required_with:useful_infos.*',
                'integer',
                'min:1',
                new InOrder(array_pluck_multi(
                    $this->input('useful_infos', []),
                    'order'
                )),
            ],

            'social_medias' => ['array'],
            'social_medias.*' => ['array'],
            'social_medias.*.type' => [
                'required_with:social_medias.*',
                Rule::in([
                    SocialMedia::TYPE_TWITTER,
                    SocialMedia::TYPE_FACEBOOK,
                    SocialMedia::TYPE_INSTAGRAM,
                    SocialMedia::TYPE_YOUTUBE,
                    SocialMedia::TYPE_OTHER,
                ]),
            ],
            'social_medias.*.url' => ['required_with:social_medias.*', 'url', 'max:255'],

            'category_taxonomies' => $this->categoryTaxonomiesRules(),
            'category_taxonomies.*' => [
                'exists:taxonomies,id',
                new RootTaxonomyIs(Taxonomy::NAME_CATEGORY),
            ],

            'logo' => ['nullable', 'string', new Base64EncodedPng()],
        ];
    }

    /**
     * @return array
     */
    protected function categoryTaxonomiesRules(): array
    {
        // If global admin and above.
        if ($this->user()->isGlobalAdmin()) {
            return [
                Rule::requiredIf(function () {
                    // Only required if the service currently has no taxonomies.
                    return $this->service->serviceTaxonomies()->doesntExist();
                }),
                'array',
                new CanUpdateServiceCategoryTaxonomies($this->user(), $this->service),
            ];
        }

        // If not a global admin.
        return [
            'array',
            new CanUpdateServiceCategoryTaxonomies($this->user(), $this->service),
        ];
    }
}
