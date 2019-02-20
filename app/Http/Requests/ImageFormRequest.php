<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int|null $max_dimension
 */
abstract class ImageFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public final function rules()
    {
        $rules = [
            'max_dimension' => ['integer', 'min:1', 'max:1000'],
        ];

        return array_merge($rules, $this->extraRules());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract protected function extraRules(): array;
}
