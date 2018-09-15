<?php

namespace App\Rules;

use App\Models\Taxonomy;
use Illuminate\Contracts\Validation\Rule;

class RootTaxonomyIs implements Rule
{
    /**
     * @var string
     */
    protected $rootTaxonomyName;

    /**
     * Create a new rule instance.
     *
     * @param string $rootTaxonomyName
     */
    public function __construct(string $rootTaxonomyName)
    {
        $this->rootTaxonomyName = $rootTaxonomyName;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Taxonomy::findOrFail($value)->rootIsCalled($this->rootTaxonomyName);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The root taxonomy must be called [{$this->rootTaxonomyName}].";
    }
}
