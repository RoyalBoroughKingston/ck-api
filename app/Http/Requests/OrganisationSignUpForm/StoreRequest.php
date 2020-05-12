<?php

namespace App\Http\Requests\OrganisationSignUpForm;

use App\Models\Organisation;
use App\Models\Service;
use App\Models\SocialMedia;
use App\Rules\InOrder;
use App\Rules\MarkdownMaxLength;
use App\Rules\MarkdownMinLength;
use App\Rules\Password;
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
            'user.email' => [
                'required',
                'email',
                'max:255',
                new UserEmailNotTaken(null, '2. User account - The email entered is already in use. Please enter a different email address or log into your existing account.'),
            ],
            'user.phone' => [
                'required',
                'string', 
                'min:1', 
                'max:255', 
                new UkPhoneNumber('2. User account - Please enter a valid UK telephone number.')
            ],
            'user.password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                new Password('2. User account - Please create a password that is at least eight characters long, contain one uppercase letter, one lowercase letter, one number and one special character (!"#$%&\'()*+,-./:;<=>?@[]^_`{|}~)'),
            ],

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
            'organisation.email' => [
                'nullable',
                'required_without:organisation.phone',
                'email',
                'max:255',
            ],
            'organisation.phone' => [
                'nullable',
                'required_without:organisation.email',
                'string',
                'min:1',
                'max:255',
            ],

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
                new MarkdownMaxLength(3000),
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

    /**
     * @inheritDoc
     */
    public function messages()
    {
        $type = $this->get('service.type', Service::TYPE_SERVICE);

        return [
            'user.first_name.required' => '2. User account - Please enter your first name.',
            'user.last_name.required' => '2. User account - Please enter your last name.',
            'user.email.required' => '2. User account - Please enter your email address.',
            'user.email.email' => '2. User account - Please enter an email address in the correct format (eg. name@example.com).',
            'user.phone.required' => '2. User account - Please enter your phone number.',
            'user.password.required' => '2. User account - Please enter a password.',
            'user.password.min' => '2. User account - Please create a password that is at least eight characters long.',

            'organisation.slug.required' => '3. Organisation - Please enter the organisation slug.',
            'organisation.slug.unique' => '3. Organisation - The organisation is already listed. Please contact us for help logging in info@connectedkingston.uk.',
            'organisation.name.required' => '3. Organisation - Please enter the organisation name.',
            'organisation.description.required' => '3. Organisation - Please enter a one-line summary of the organisation.',
            'organisation.url.required' => '3. Organisation - Please enter a valid web address in the correct format (starting with https:// or http://).',
            'organisation.url.url' => '3. Organisation - Please enter a valid web address in the correct format (starting with https:// or http://).',
            'organisation.email.required_without' => '3. Organisation - Please enter a public email address and/or a public phone number.',
            'organisation.phone.required_without' => '3. Organisation - Please enter a public phone number and/or public email address.',
            'organisation.email.email' => '3. Organisation - Please enter the email for your organisation (eg. name@example.com).',

            'service.slug.required' => "4. Service, Details tab - Please enter the name of your {$type}.",
            'service.name.required' => "4. Service, Details tab - Please enter the name of your {$type}.",
            'service.video_embed.url' => '4. Service, Additional info tab - Please enter a valid video link (eg. https://www.youtube.com/watch?v=JyHR_qQLsLM).',
            'service.intro.required' => "4. Service, Description tab - Please enter a brief description of the {$type}.",
            'service.description.required' => "4. Service, Description tab - Please enter all the information someone should know about your {$type}.",
            'service.url.required' => "4. Service, Details tab - Please provide the web address for your {$type}.",
            'service.url.url' => '4. Service, Details tab - Please enter a valid web address in the correct format (starting with https:// or http://).',
            'service.contact_email.email' => "4. Service, Additional Info tab - Please enter an email address users can use to contact your {$type} (eg. name@example.com).",
        ];
    }
}
