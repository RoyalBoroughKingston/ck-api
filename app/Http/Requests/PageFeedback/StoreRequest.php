<?php

namespace App\Http\Requests\PageFeedback;

use Illuminate\Foundation\Http\FormRequest;

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
            'url' => ['required', 'url', 'max:255'],
            'feedback' => ['required', 'string', 'min:1', 'max:10000'],
            'name' => ['present', 'nullable', 'string', 'max:255'],
            'email' => ['present', 'nullable', 'email', 'max:255'],
            'phone' => ['present', 'nullable', 'string', 'max:255'],
        ];
    }
}
