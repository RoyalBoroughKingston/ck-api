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
        // If the user passed in a filter for the services, then loop through each one and check if authorised.
        $serviceIdsString = $this->input('filter', [])['service_id'] ?? '';
        $serviceIds = array_filter(explode(',', $serviceIdsString));

        $services = Service::query()->whereIn('id', $serviceIds);

        foreach ($services as $service) {
            if (!$this->user()->isServiceWorker($service)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
