<?php

namespace App\Http\Requests\CollectionCategory;

use App\Rules\Slug;
use App\Models\Taxonomy;
use App\Models\Collection;
use App\Rules\RootTaxonomyIs;
use Illuminate\Validation\Rule;
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
        return [
            'slug' => [
                'required',
                'string',
                'min:1',
                'max:255',
                Rule::unique(table(Collection::class), 'slug')->ignoreModel($this->collection),
                new Slug(),
            ],
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'intro' => ['required', 'string', 'min:1', 'max:300'],
            'icon' => ['required', 'string', 'min:1', 'max:255'],
            'order' => ['required', 'integer', 'min:1', 'max:' . Collection::categories()->count()],
            'sideboxes' => ['present', 'array', 'max:3'],
            'sideboxes.*' => ['array'],
            'sideboxes.*.title' => ['required_with:sideboxes.*', 'string'],
            'sideboxes.*.content' => ['required_with:sideboxes.*', 'string'],
            'category_taxonomies' => ['present', 'array'],
            'category_taxonomies.*' => ['string', 'exists:taxonomies,id', new RootTaxonomyIs(Taxonomy::NAME_CATEGORY)],
        ];
    }
}
