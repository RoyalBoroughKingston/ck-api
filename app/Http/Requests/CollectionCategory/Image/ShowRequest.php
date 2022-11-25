<?php

namespace App\Http\Requests\CollectionCategory\Image;

use App\Http\Requests\ImageFormRequest;

class ShowRequest extends ImageFormRequest
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
    public function extraRules(): array
    {
        return [
            //
        ];
    }
}
