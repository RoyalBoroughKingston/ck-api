<?php

namespace App\Http\Requests\TaxonomyCategory;

use App\Models\Taxonomy;
use App\Rules\RootTaxonomyIs;
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
        if ($this->user()->isSuperAdmin()) {
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
        // Get the new parent ID.
        $parentId = $this->parent_id ?? Taxonomy::category()->id;

        // Check if the parent taxonomy remained the same.
        $hasSameParent = $this->taxonomy->parent_id === $parentId;

        // Get the sibling count for the same parent.
        $siblingTaxonomiesCount = Taxonomy::where('parent_id', $parentId)->count();

        // Increment the sibling count if the parent is new.
        $siblingTaxonomiesCount = $hasSameParent ? $siblingTaxonomiesCount : $siblingTaxonomiesCount + 1;

        return [
            'parent_id' => ['present', 'nullable', 'exists:taxonomies,id', new RootTaxonomyIs(Taxonomy::NAME_CATEGORY)],
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'order' => ['required', 'integer', 'min:1', "max:$siblingTaxonomiesCount"],
        ];
    }
}
