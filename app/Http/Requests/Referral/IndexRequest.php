<?php

namespace App\Http\Requests\Referral;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $service = Service::find($this->filter['service_id']);

        if ($this->user()->isServiceWorker($service)) {
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
            'filter.service_id' => ['required', 'exists:services,id'],
        ];
    }
}
