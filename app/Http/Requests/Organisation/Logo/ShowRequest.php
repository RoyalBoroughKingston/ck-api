<?php

namespace App\Http\Requests\Organisation\Logo;

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
    protected function extraRules(): array
    {
        return [
            'update_request_id' => ['exists:update_requests,id'],
        ];
    }
}
