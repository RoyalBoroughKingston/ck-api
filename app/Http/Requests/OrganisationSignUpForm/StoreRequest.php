<?php

namespace App\Http\Requests\OrganisationSignUpForm;

use App\Models\Organisation;
use App\Models\Service;
use App\Models\SocialMedia;
use App\Rules\InOrder;
use App\Rules\MarkdownMaxLength;
use App\Rules\MarkdownMinLength;
use App\Rules\Slug;
use App\Rules\UkPhoneNumber;
use App\Rules\UserEmailNotTaken;
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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user' => ['required', 'array'],
            'user.first_name' => ['required', 'string', 'min:1', 'max:255'],
            'user.last_name' => ['required', 'string', 'min:1', 'max:255'],
            'user.email' => ['required', 'email', 'max:255', new UserEmailNotTaken()],
            'user.phone' => ['required', 'string', 'min:1', 'max:255', new UkPhoneNumber()],

            'organisation' => ['required', 'array'],
            'organisation.slug' => [
                'required',
                'string',
                'min:1',
                'max:255',
                'unique:' . table(Organisation::class) . ',slug',
                new Slug(),
            ],
            'organisation.name' => ['required', 'string', 'min:1', 'max:255'],
            'organisation.description' => ['required', 'string', 'min:1', 'max:10000'],
            'organisation.url' => ['required', 'url', 'max:255'],
            'organisation.email' => ['required', 'email', 'max:255'],
            'organisation.phone' => ['required', 'string', 'min:1', 'max:255'],

            'service' => ['required', 'array'],
            'service.slug' => [
                'required',
                'string',
                'min:1',
                'max:255',
                'unique:' . table(Service::class) . ',slug',
                new Slug(),
            ],
            'service.name' => ['required', 'string', 'min:1', 'max:255'],
            'service.type' => [
                'required',
                Rule::in([
                    Service::TYPE_SERVICE,
                    Service::TYPE_ACTIVITY,
                    Service::TYPE_CLUB,
                    Service::TYPE_GROUP,
                ]),
            ],
            'service.intro' => ['required', 'string', 'min:1', 'max:300'],
            'service.description' => [
                'required',
                'string',
                new MarkdownMinLength(1),
                new MarkdownMaxLength(1600),
            ],
            'service.wait_time' => ['present', 'nullable', Rule::in([
                Service::WAIT_TIME_ONE_WEEK,
                Service::WAIT_TIME_TWO_WEEKS,
                Service::WAIT_TIME_THREE_WEEKS,
                Service::WAIT_TIME_MONTH,
                Service::WAIT_TIME_LONGER,
            ])],
            'service.is_free' => ['required', 'boolean'],
            'service.fees_text' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'service.fees_url' => ['present', 'nullable', 'url', 'max:255'],
            'service.testimonial' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'service.video_embed' => ['present', 'nullable', 'url', 'max:255', new VideoEmbed()],
            'service.url' => ['required', 'url', 'max:255'],
            'service.contact_name' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'service.contact_phone' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'service.contact_email' => ['present', 'nullable', 'email', 'max:255'],
            'service.criteria' => ['required', 'array'],
            'service.criteria.age_group' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'service.criteria.disability' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'service.criteria.employment' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'service.criteria.gender' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'service.criteria.housing' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'service.criteria.income' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'service.criteria.language' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'service.criteria.other' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'service.useful_infos' => ['present', 'array'],
            'service.useful_infos.*' => ['array'],
            'service.useful_infos.*.title' => [
                'required_with:service.useful_infos.*',
                'string',
                'min:1',
                'max:255',
            ],
            'service.useful_infos.*.description' => [
                'required_with:service.useful_infos.*',
                'string',
                new MarkdownMinLength(1),
                new MarkdownMaxLength(10000),
            ],
            'service.useful_infos.*.order' => [
                'required_with:service.useful_infos.*',
                'integer',
                'min:1',
                new InOrder(array_pluck_multi($this->input('service.useful_infos'), 'order')),
            ],
            'service.offerings' => ['present', 'array'],
            'service.offerings.*' => ['array'],
            'service.offerings.*.offering' => [
                'required_with:service.offerings.*',
                'string',
                'min:1',
                'max:255',
            ],
            'service.offerings.*.order' => [
                'required_with:service.offerings.*',
                'integer',
                'min:1',
                new InOrder(array_pluck_multi($this->input('service.offerings'), 'order')),
            ],
            'service.social_medias' => ['present', 'array'],
            'service.social_medias.*' => ['array'],
            'service.social_medias.*.type' => ['required_with:service.social_medias.*', Rule::in([
                SocialMedia::TYPE_TWITTER,
                SocialMedia::TYPE_FACEBOOK,
                SocialMedia::TYPE_INSTAGRAM,
                SocialMedia::TYPE_YOUTUBE,
                SocialMedia::TYPE_OTHER,
            ])],
            'service.social_medias.*.url' => [
                'required_with:service.social_medias.*',
                'url',
                'max:255',
            ],
        ];
    }
}
