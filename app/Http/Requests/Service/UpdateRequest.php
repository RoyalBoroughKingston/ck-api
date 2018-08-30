<?php

namespace App\Http\Requests\Service;

use App\Models\Service;
use App\Models\SocialMedia;
use App\Models\Taxonomy;
use App\Rules\InOrder;
use App\Rules\RootTaxonomyIs;
use App\Rules\Slug;
use App\Rules\UkPhoneNumber;
use App\Rules\VideoEmbed;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
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
                'required',
                'string',
                'min:1',
                'max:255',
                Rule::unique(table(Service::class), 'slug')
                    ->ignoreModel($this->service),
                new Slug(),
            ],
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
            'video_embed' => ['present', 'nullable', 'string', 'url', 'max:255', new VideoEmbed()],
            'url' => ['required', 'url', 'max:255'],
            'contact_name' => ['required', 'string', 'min:1', 'max:255'],
            'contact_phone' => ['required', 'string', 'min:1', 'max:255', new UkPhoneNumber()],
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
    }
}
