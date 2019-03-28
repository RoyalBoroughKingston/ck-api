<?php

namespace App\Http\Requests\Organisation;

use App\Http\Requests\HasMissingValues;
use App\Models\File;
use App\Models\Organisation;
use App\Rules\FileIsMimeType;
use App\Rules\FileIsPendingAssignment;
use App\Rules\Slug;
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
        if ($this->user()->isOrganisationAdmin($this->organisation)) {
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
                Rule::unique(table(Organisation::class), 'slug')
                    ->ignoreModel($this->organisation),
                new Slug(),
            ],
            'name' => ['string', 'min:1', 'max:255'],
            'description' => ['string', 'min:1', 'max:10000'],
            'url' => ['url', 'max:255'],
            'email' => ['email', 'max:255'],
            'phone' => ['string', 'min:1', 'max:255'],
            'logo_file_id' => [
                'nullable',
                'exists:files,id',
                new FileIsMimeType(File::MIME_TYPE_PNG),
                new FileIsPendingAssignment(),
            ],
        ];
    }
}
