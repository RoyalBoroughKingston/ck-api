<?php

namespace App\Http\Requests\Thesaurus;

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
            'synonyms' => ['present', 'array'],
            'synonyms.*' => ['present', 'array'],
            'synonyms.*.*' => ['string'],
        ];
    }
}
