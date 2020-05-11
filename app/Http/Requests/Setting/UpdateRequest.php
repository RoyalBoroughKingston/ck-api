<?php

namespace App\Http\Requests\Setting;

use App\Models\File;
use App\Rules\FileIsMimeType;
use App\Rules\FileIsPendingAssignment;
use App\Rules\VideoEmbed;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isGlobalAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cms' => ['required', 'array'],

            'cms.frontend' => ['required', 'array'],

            'cms.frontend.global' => ['required', 'array'],
            'cms.frontend.global.footer_title' => ['required', 'string'],
            'cms.frontend.global.footer_content' => ['required', 'string'],
            'cms.frontend.global.contact_phone' => ['required', 'string'],
            'cms.frontend.global.contact_email' => ['required', 'string', 'email'],
            'cms.frontend.global.facebook_handle' => ['required', 'string'],
            'cms.frontend.global.twitter_handle' => ['required', 'string'],

            'cms.frontend.home' => ['required', 'array'],
            'cms.frontend.home.search_title' => ['required', 'string'],
            'cms.frontend.home.categories_title' => ['required', 'string'],
            'cms.frontend.home.personas_title' => ['required', 'string'],
            'cms.frontend.home.personas_content' => ['required', 'string'],

            'cms.frontend.terms_and_conditions' => ['required', 'array'],
            'cms.frontend.terms_and_conditions.title' => ['required', 'string'],
            'cms.frontend.terms_and_conditions.content' => ['required', 'string'],

            'cms.frontend.privacy_policy' => ['required', 'array'],
            'cms.frontend.privacy_policy.title' => ['required', 'string'],
            'cms.frontend.privacy_policy.content' => ['required', 'string'],

            'cms.frontend.about' => ['required', 'array'],
            'cms.frontend.about.title' => ['required', 'string'],
            'cms.frontend.about.content' => ['required', 'string'],
            'cms.frontend.about.video_url' => ['required', 'string', 'url', new VideoEmbed()],

            'cms.frontend.contact' => ['required', 'array'],
            'cms.frontend.contact.title' => ['required', 'string'],
            'cms.frontend.contact.content' => ['required', 'string'],

            'cms.frontend.get_involved' => ['required', 'array'],
            'cms.frontend.get_involved.title' => ['required', 'string'],
            'cms.frontend.get_involved.content' => ['required', 'string'],

            'cms.frontend.favourites' => ['required', 'array'],
            'cms.frontend.favourites.title' => ['required', 'string'],
            'cms.frontend.favourites.content' => ['required', 'string'],

            'cms.frontend.banner' => ['required', 'array'],
            'cms.frontend.banner.title' => [
                'required_with:' . implode(',', [
                    'cms.frontend.banner.content',
                    'cms.frontend.banner.button_text',
                    'cms.frontend.banner.button_url',
                    'cms.frontend.banner.image_file_id',
                ]),
                'present',
                'nullable',
                'string',
            ],
            'cms.frontend.banner.content' => [
                'required_with:' . implode(',', [
                    'cms.frontend.banner.title',
                    'cms.frontend.banner.button_text',
                    'cms.frontend.banner.button_url',
                    'cms.frontend.banner.image_file_id',
                ]),
                'present',
                'nullable',
                'string',
            ],
            'cms.frontend.banner.button_text' => [
                'required_with:' . implode(',', [
                    'cms.frontend.banner.title',
                    'cms.frontend.banner.content',
                    'cms.frontend.banner.button_url',
                    'cms.frontend.banner.image_file_id',
                ]),
                'present',
                'nullable',
                'string',
                'max:30',
            ],
            'cms.frontend.banner.button_url' => [
                'required_with:' . implode(',', [
                    'cms.frontend.banner.title',
                    'cms.frontend.banner.content',
                    'cms.frontend.banner.button_text',
                    'cms.frontend.banner.image_file_id',
                ]),
                'present',
                'nullable',
                'string',
            ],
            'cms.frontend.banner.image_file_id' => [
                'nullable',
                'exists:files,id',
                new FileIsMimeType(File::MIME_TYPE_PNG),
                new FileIsPendingAssignment(),
            ],
        ];
    }
}
