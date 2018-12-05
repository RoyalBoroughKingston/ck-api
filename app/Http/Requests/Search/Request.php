<?php

namespace App\Http\Requests\Search;

use App\Contracts\Search;
use App\Models\Service;
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
            'query' => ['required_without_all:category,persona,wait_time,is_free,location', 'string', 'min:3', 'max:255'],
            'category' => ['required_without_all:query,persona,wait_time,is_free,location', 'string', 'min:1', 'max:255'],
            'persona' => ['required_without_all:query,category,wait_time,is_free,location', 'string', 'min:1', 'max:255'],
            'wait_time' => ['required_without_all:query,category,is_free,persona,location', Rule::in([
                Service::WAIT_TIME_ONE_WEEK,
                Service::WAIT_TIME_TWO_WEEKS,
                Service::WAIT_TIME_THREE_WEEKS,
                Service::WAIT_TIME_MONTH,
                Service::WAIT_TIME_LONGER,
            ])],
            'is_free' => ['required_without_all:query,category,persona,wait_time,location', 'boolean'],
            'order' => [Rule::in([Search::ORDER_RELEVANCE, Search::ORDER_DISTANCE])],
            'location' => ['required_without_all:query,category,persona,wait_time,is_free', 'required_if:order,distance', 'array'],
            'location.lat' => ['required_with:location', 'numeric', 'min:-90', 'max:90'],
            'location.lon' => ['required_with:location', 'numeric', 'min:-180', 'max:180'],
            'distance' => ['integer', 'min:0'],
        ];
    }
}
