<?php

namespace App\Http\Requests\Service;

use App\Http\Requests\HasMissingValues;
use App\Models\File;
use App\Models\Role;
use App\Models\Service;
use App\Models\SocialMedia;
use App\Models\Taxonomy;
use App\Models\UserRole;
use App\Rules\CanUpdateServiceCategoryTaxonomies;
use App\Rules\FileIsMimeType;
use App\Rules\FileIsPendingAssignment;
use App\Rules\InOrder;
use App\Rules\MarkdownMaxLength;
use App\Rules\MarkdownMinLength;
use App\Rules\NullableIf;
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
            'organisation_id' => [
                'exists:organisations,id',
                new UserHasRole(
                    $this->user('api'),
                    new UserRole([
                        'user_id' => $this->user('api')->id,
                        'role_id' => Role::globalAdmin()->id,
                    ]),
                    $this->service->organisation_id
                ),
            ],
            'slug' => [
                'string',
                'min:1',
                'max:255',
                Rule::unique(table(Service::class), 'slug')->ignoreModel($this->service),
                new Slug(),
                new UserHasRole(
                    $this->user('api'),
                    new UserRole([
                        'user_id' => $this->user('api')->id,
                        'role_id' => Role::globalAdmin()->id,
                    ]),
                    $this->service->slug
                ),
            ],
            'name' => ['string', 'min:1', 'max:255'],
            'status' => [
                Rule::in([
                    Service::STATUS_ACTIVE,
                    Service::STATUS_INACTIVE,
                ]),
                new UserHasRole(
                    $this->user('api'),
                    new UserRole([
                        'user_id' => $this->user('api')->id,
                        'role_id' => Role::globalAdmin()->id,
                    ]),
                    $this->service->status
                ),
            ],
            'intro' => ['string', 'min:1', 'max:300'],
            'description' => ['string', new MarkdownMinLength(1), new MarkdownMaxLength(1600)],
            'wait_time' => [
                'nullable',
                Rule::in([
                    Service::WAIT_TIME_ONE_WEEK,
                    Service::WAIT_TIME_TWO_WEEKS,
                    Service::WAIT_TIME_THREE_WEEKS,
                    Service::WAIT_TIME_MONTH,
                    Service::WAIT_TIME_LONGER,
                ]),
            ],
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
                    $this->user('api'),
                    new UserRole([
                        'user_id' => $this->user('api')->id,
                        'role_id' => Role::superAdmin()->id,
                    ]),
                    $this->showReferralDisclaimerOriginalValue()
                ),
            ],
            'referral_method' => [
                Rule::in([
                    Service::REFERRAL_METHOD_INTERNAL,
                    Service::REFERRAL_METHOD_EXTERNAL,
                    Service::REFERRAL_METHOD_NONE,
                ]),
                new UserHasRole(
                    $this->user('api'),
                    new UserRole([
                        'user_id' => $this->user('api')->id,
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
                    $this->user('api'),
                    new UserRole([
                        'user_id' => $this->user('api')->id,
                        'role_id' => Role::globalAdmin()->id,
                    ]),
                    $this->service->referral_button_text
                ),
            ],
            'referral_email' => [
                Rule::requiredIf(function () {
                    $referralMethod = $this->input('referral_method', $this->service->referral_method);

                    return $referralMethod === Service::REFERRAL_METHOD_INTERNAL
                        && $this->service->referral_email === null;
                }),
                new NullableIf(function () {
                    $referralMethod = $this->input('referral_method', $this->service->referral_method);

                    return $referralMethod !== Service::REFERRAL_METHOD_INTERNAL;
                }),
                'email',
                'max:255',
                new UserHasRole(
                    $this->user('api'),
                    new UserRole([
                        'user_id' => $this->user('api')->id,
                        'role_id' => Role::globalAdmin()->id,
                    ]),
                    $this->service->referral_email
                ),
            ],
            'referral_url' => [
                Rule::requiredIf(function () {
                    $referralMethod = $this->input('referral_method', $this->service->referral_method);

                    return $referralMethod === Service::REFERRAL_METHOD_EXTERNAL
                        && $this->service->referral_url === null;
                }),
                new NullableIf(function () {
                    $referralMethod = $this->input('referral_method', $this->service->referral_method);

                    return $referralMethod !== Service::REFERRAL_METHOD_EXTERNAL;
                }),
                'url',
                'max:255',
                new UserHasRole(
                    $this->user('api'),
                    new UserRole([
                        'user_id' => $this->user('api')->id,
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
            'useful_infos.*.description' => ['required_with:useful_infos.*', 'string', new MarkdownMinLength(1), new MarkdownMaxLength(10000)],
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

            'gallery_items' => ['array'],
            'gallery_items.*' => ['array'],
            'gallery_items.*.file_id' => [
                'required_with:gallery_items.*',
                'exists:files,id',
                new FileIsMimeType(File::MIME_TYPE_PNG),
                new FileIsPendingAssignment(function (File $file) {
                    return $this->service
                        ->serviceGalleryItems()
                        ->where('file_id', '=', $file->id)
                        ->exists();
                }),
            ],

            'category_taxonomies' => $this->categoryTaxonomiesRules(),
            'category_taxonomies.*' => [
                'exists:taxonomies,id',
                new RootTaxonomyIs(Taxonomy::NAME_CATEGORY),
            ],

            'logo_file_id' => [
                'nullable',
                'exists:files,id',
                new FileIsMimeType(File::MIME_TYPE_PNG),
                new FileIsPendingAssignment(),
            ],
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

    /**
     * @return bool
     */
    protected function showReferralDisclaimerOriginalValue(): bool
    {
        // If the new referral method is none, then always require false.
        if ($this->referral_method === Service::REFERRAL_METHOD_NONE) {
            return false;
        }

        /*
         * If the original referral method was not none, and the referral disclaimer was hidden,
         * then continue hiding the disclaimer.
         */
        if (
            $this->service->referral_method !== Service::REFERRAL_METHOD_NONE
            && $this->service->show_referral_disclaimer === false
        ) {
            return false;
        }

        return true;
    }
}
