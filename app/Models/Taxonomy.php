<?php

namespace App\Models;

use App\Models\Mutators\TaxonomyMutators;
use App\Models\Relationships\TaxonomyRelationships;
use App\Models\Scopes\TaxonomyScopes;

class Taxonomy extends Model
{
    use TaxonomyMutators;
    use TaxonomyRelationships;
    use TaxonomyScopes;

    const ROOT_CATEGORY = 'Category';
    const ROOT_ORGANISATION = 'Organisation';

    /**
     * @return \App\Models\Taxonomy
     */
    public static function category(): self
    {
        return static::whereNull('parent_id')->where('name', static::ROOT_CATEGORY)->firstOrFail();
    }

    /**
     * @return \App\Models\Taxonomy
     */
    public static function organisation(): self
    {
        return static::whereNull('parent_id')->where('name', static::ROOT_ORGANISATION)->firstOrFail();
    }

    /**
     * @param null|\App\Models\Taxonomy $taxonomy
     * @return \App\Models\Taxonomy
     */
    public function getRootTaxonomy(Taxonomy $taxonomy = null): Taxonomy
    {
        $taxonomy = $taxonomy ?? $this;

        if ($taxonomy->parent_id === null) {
            return $taxonomy;
        }

        return $this->getRootTaxonomy($taxonomy->parent);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function rootIsCalled(string $name): bool
    {
        return $this->getRootTaxonomy()->name === $name;
    }
}
