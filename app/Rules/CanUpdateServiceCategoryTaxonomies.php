<?php

namespace App\Rules;

use App\Models\Service;
use App\Models\Taxonomy;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;

class CanUpdateServiceCategoryTaxonomies implements Rule
{
    /**
     * @var \App\Models\User
     */
    protected $user;

    /**
     * @var \App\Models\Service
     */
    protected $service;

    /**
     * Create a new rule instance.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Service $service
     */
    public function __construct(User $user, Service $service)
    {
        $this->user = $user;
        $this->service = $service;
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
        // Immediately fail if the value is not an array of strings.
        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $item) {
            if (!is_string($item)) {
                return false;
            }
        }

        // Allow changing of taxonomies if global admin.
        if ($this->user->isGlobalAdmin()) {
            return true;
        }

        // Only pass if the taxonomies remain unchanged.
        $existingTaxonomyIds = $this->service
            ->taxonomies()
            ->pluck(table(Taxonomy::class, 'id'))
            ->toArray();
        $existingTaxonomies = Arr::sort($existingTaxonomyIds);
        $newTaxonomies = Arr::sort($value);
        $taxonomiesUnchanged = $existingTaxonomies === $newTaxonomies;

        return $taxonomiesUnchanged;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You are not authorised to update this service\'s category taxonomies.';
    }
}
