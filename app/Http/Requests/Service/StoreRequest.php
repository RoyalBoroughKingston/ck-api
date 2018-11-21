<?php

namespace App\Http\Requests\Service;

use App\Models\Service;
use App\Models\SocialMedia;
use App\Models\Taxonomy;
use App\Rules\Base64EncodedPng;
use App\Rules\InOrder;
use App\Rules\Is;
use App\Rules\IsOrganisationAdmin;
use App\Rules\RootTaxonomyIs;
use App\Rules\Slug;
use App\Rules\VideoEmbed;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->user()->isOrganisationAdmin()) {
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
            'organisation_id' => ['required', 'exists:organisations,id', new IsOrganisationAdmin($this->user())],
            'slug' => ['required', 'string', 'min:1', 'max:255', 'unique:'.table(Service::class).',slug', new Slug()],
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'status' => $this->statusRules(),
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
            'video_embed' => ['present', 'nullable', 'url', 'max:255', new VideoEmbed()],
            'url' => ['required', 'url', 'max:255'],
            'contact_name' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'contact_phone' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'contact_email' => ['present', 'nullable', 'email', 'max:255'],
            'show_referral_disclaimer' => $this->showReferralDisclaimerRules(),
            'referral_method' => $this->referralMethodRules(),
            'referral_button_text' => $this->referralButtonTextRules(),
            'referral_email' => $this->referralEmailRules(),
            'referral_url' => $this->referralUrlRules(),
            'criteria' => ['required', 'array'],
            'criteria.age_group' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'criteria.disability' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'criteria.employment' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'criteria.gender' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'criteria.housing' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'criteria.income' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'criteria.language' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'criteria.other' => ['present', 'nullable', 'string', 'min:1', 'max:255'],

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

            'category_taxonomies' => $this->categoryTaxonomiesRules(),
            'category_taxonomies.*' => ['exists:taxonomies,id', new RootTaxonomyIs(Taxonomy::NAME_CATEGORY)],
            'logo' => ['nullable', 'string', new Base64EncodedPng()],
        ];
    }

    /**
     * @return array
     */
    protected function statusRules(): array
    {
        // If global admin and above.
        if ($this->user()->isGlobalAdmin()) {
            return [
                'required',
                Rule::in([
                    Service::STATUS_ACTIVE,
                    Service::STATUS_INACTIVE,
                ]),
            ];
        }

        // If not a global admin.
        return [
            'required',
            new Is(Service::STATUS_INACTIVE),
        ];
    }

    /**
     * @return array
     */
    protected function showReferralDisclaimerRules(): array
    {
        // If global admin and above.
        if ($this->user()->isGlobalAdmin()) {
            return ['required', 'boolean'];
        }

        // If not a global admin.
        return ['required', 'boolean', new Is(false)];
    }

    /**
     * @return array
     */
    protected function referralMethodRules(): array
    {
        // If global admin and above.
        if ($this->user()->isGlobalAdmin()) {
            return [
                'required',
                Rule::in([
                    Service::REFERRAL_METHOD_INTERNAL,
                    Service::REFERRAL_METHOD_EXTERNAL,
                    Service::REFERRAL_METHOD_NONE,
                ]),
            ];
        }

        // If not a global admin.
        return [
            'required',
           new Is(Service::REFERRAL_METHOD_NONE),
        ];
    }

    /**
     * @return array
     */
    protected function referralButtonTextRules(): array
    {
        // If global admin and above.
        if ($this->user()->isGlobalAdmin()) {
            return ['present', 'nullable', 'string', 'min:1', 'max:255'];
        }

        // If not a global admin.
        return ['present', 'nullable', new Is(null)];
    }

    /**
     * @return array
     */
    protected function referralEmailRules(): array
    {
        // If global admin and above.
        if ($this->user()->isGlobalAdmin()) {
            return [
                'required_if:referral_method,' . Service::REFERRAL_METHOD_INTERNAL,
                'present',
                'nullable',
                'email',
                'max:255',
            ];
        }

        // If not a global admin.
        return ['present', new Is(null)];
    }

    /**
     * @return array
     */
    protected function referralUrlRules(): array
    {
        // If global admin and above.
        if ($this->user()->isGlobalAdmin()) {
            return [
                'required_if:referral_method,' . Service::REFERRAL_METHOD_EXTERNAL,
                'present',
                'nullable',
                'url',
                'max:255',
            ];
        }

        // If not a global admin.
        return ['present', new Is(null)];
    }

    /**
     * @return array
     */
    protected function categoryTaxonomiesRules(): array
    {
        // If global admin and above.
        if ($this->user()->isGlobalAdmin()) {
            return ['required', 'array'];
        }

        // If not a global admin.
        return ['present', 'array', 'size:0'];
    }
}
