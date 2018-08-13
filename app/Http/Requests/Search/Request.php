<?php

namespace App\Http\Requests\Search;

use App\Rules\LatLong;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Request extends FormRequest
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
            'query' => ['required_without_all:category,persona', 'string', 'min:3', 'max:255'],
            'category' => ['required_without_all:query,persona', 'string', 'min:1', 'max:255'],
            'persona' => ['required_without_all:query,category', 'string', 'min:1', 'max:255'],
            'order' => [Rule::in(['relevance', 'distance'])],
            'location' => ['required_if:order,distance', new LatLong()],
        ];
    }
}
