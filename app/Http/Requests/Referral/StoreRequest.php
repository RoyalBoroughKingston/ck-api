<?php

namespace App\Http\Requests\Referral;

use App\Models\Taxonomy;
use App\Rules\RootTaxonomyIs;
use App\Rules\UkPhoneNumber;
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
            'service_id' => ['required', 'exists:services,id'],
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'email' => ['required_without_all:phone,other_contact', 'nullable', 'email', 'max:255'],
            'phone' => ['required_without_all:email,other_contact', 'nullable', 'string', 'min:1', 'max:255', new UkPhoneNumber()],
            'other_contact' => ['required_without_all:phone,email', 'nullable', 'string', 'min:1', 'max:255'],
            'postcode_outward_code' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'comments' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'referral_consented' => ['required', 'boolean', 'accepted'],
            'feedback_consented' => ['required', 'boolean'],
            'referee_name' => ['required', 'string', 'min:1', 'max:255'],
            'referee_email' => ['required', 'email', 'max:255'],
            'referee_phone' => ['required', 'string', 'min:1', 'max:255', new UkPhoneNumber()],
            'organisation_taxonomy_id' => [
                'required_without:organisation',
                'nullable',
                'exists:taxonomies,id',
                new RootTaxonomyIs(Taxonomy::NAME_ORGANISATION),
            ],
            'organisation' => ['required_without:organisation_taxonomy_id', 'nullable', 'string', 'min:1', 'max:255'],
        ];
    }
}
