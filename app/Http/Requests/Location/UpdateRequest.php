<?php

namespace App\Http\Requests\Location;

use App\Http\Requests\HasMissingValues;
use App\Models\File;
use App\Rules\FileIsMimeType;
use App\Rules\FileIsPendingAssignment;
use App\Rules\Postcode;
use Illuminate\Foundation\Http\FormRequest;

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
        if ($this->user()->isServiceAdmin()) {
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
            'address_line_1' => ['string', 'min:1', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'min:1', 'max:255'],
            'address_line_3' => ['nullable', 'string', 'min:1', 'max:255'],
            'city' => ['string', 'min:1', 'max:255'],
            'county' => ['string', 'min:1', 'max:255'],
            'postcode' => ['string', 'min:1', 'max:255', new Postcode()],
            'country' => ['string', 'min:1', 'max:255'],
            'accessibility_info' => ['nullable', 'string', 'min:1', 'max:10000'],
            'has_wheelchair_access' => ['boolean'],
            'has_induction_loop' => ['boolean'],
            'image_file_id' => [
                'nullable',
                'exists:files,id',
                new FileIsMimeType(File::MIME_TYPE_PNG),
                new FileIsPendingAssignment(),
            ],
        ];
    }
}
