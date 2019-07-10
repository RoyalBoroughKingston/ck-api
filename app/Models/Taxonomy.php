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

    const NAME_CATEGORY = 'Category';
    const NAME_ORGANISATION = 'Organisation';

    /**
     * @return \App\Models\Taxonomy
     */
    public static function category(): self
    {
        return static::whereNull('parent_id')->where('name', static::NAME_CATEGORY)->firstOrFail();
    }

    /**
     * @return \App\Models\Taxonomy
     */
    public static function organisation(): self
    {
        return static::whereNull('parent_id')->where('name', static::NAME_ORGANISATION)->firstOrFail();
    }

    /**
     * @param \App\Models\Taxonomy|null $taxonomy
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

    /**
     * @return \App\Models\Taxonomy
     */
    public function touchServices(): Taxonomy
    {
        $this->services()->get()->each->save();

        return $this;
    }
}
