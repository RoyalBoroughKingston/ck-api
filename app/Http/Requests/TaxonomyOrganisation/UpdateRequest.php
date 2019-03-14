<?php

namespace App\Http\Requests\TaxonomyOrganisation;

use App\Models\Taxonomy;
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
        if ($this->user()->isGlobalAdmin()) {
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
        $count = Taxonomy::organisation()->children()->count();

        return [
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'order' => ['required', 'integer', 'min:1', "max:$count"],
        ];
    }
}
