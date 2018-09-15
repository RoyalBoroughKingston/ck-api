<?php

namespace App\Http\Requests\StatusUpdate;

use App\Models\Referral;
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
        $referral = Referral::find($this->filter['referral_id'] ?? null);
        $service = $referral->service ?? null;

        if ($service && $this->user()->isServiceWorker($service)) {
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
            'filter' => ['required', 'array'],
            'filter.referral_id' => ['required', 'exists:referrals,id'],
        ];
    }
}
