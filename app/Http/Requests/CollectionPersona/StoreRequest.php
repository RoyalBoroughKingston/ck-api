<?php

namespace App\Http\Requests\CollectionPersona;

use App\Models\Collection;
use App\Models\File;
use App\Models\Taxonomy;
use App\Rules\FileIsMimeType;
use App\Rules\FileIsPendingAssignment;
use App\Rules\RootTaxonomyIs;
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
        return [
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'intro' => ['required', 'string', 'min:1', 'max:500'],
            'subtitle' => ['required', 'string', 'min:1', 'max:255'],
            'order' => ['required', 'integer', 'min:1', 'max:'.(Collection::personas()->count() + 1)],
            'sidebox_title' => ['present', 'required_with:sidebox_content', 'nullable', 'string'],
            'sidebox_content' => ['present', 'required_with:sidebox_title', 'nullable', 'string'],
            'category_taxonomies' => ['present', 'array'],
            'category_taxonomies.*' => ['string', 'exists:taxonomies,id', new RootTaxonomyIs(Taxonomy::NAME_CATEGORY)],
            'image_file_id' => [
                'exists:files,id',
                new FileIsMimeType(File::MIME_TYPE_PNG),
                new FileIsPendingAssignment(),
            ],
        ];
    }
}
