<?php

namespace App\Models\Relationships;

use App\Models\CollectionTaxonomy;
use App\Models\Taxonomy;

trait CollectionRelationships
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function collectionTaxonomies()
    {
        return $this->hasMany(CollectionTaxonomy::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function taxonomies()
    {
        return $this->hasManyThrough(Taxonomy::class, CollectionTaxonomy::class);
    }
}
