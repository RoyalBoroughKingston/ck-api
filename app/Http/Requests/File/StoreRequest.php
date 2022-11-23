<?php

namespace App\Http\Requests\File;

use App\Models\File;
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
            'is_private' => ['required', 'boolean'],
            'mime_type' => ['required', Rule::in([File::MIME_TYPE_PNG, File::MIME_TYPE_JPG, File::MIME_TYPE_SVG])],
            'file' => ['required', 'string'],
        ];
    }
}
