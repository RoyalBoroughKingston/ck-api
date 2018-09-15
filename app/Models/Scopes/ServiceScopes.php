<?php

namespace App\Models\Scopes;

use App\Models\Collection;
use App\Models\CollectionTaxonomy;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;

trait ServiceScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Models\Service $service
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCollectionTaxonomies(Builder $query, Service $service): Builder
    {
        $taxonomyIds = $service->serviceTaxonomies()->pluck('taxonomy_id')->toArray();

        return CollectionTaxonomy::query()->whereIn('taxonomy_id', $taxonomyIds);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Models\Service $service
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCollections(Builder $query, Service $service): Builder
    {
        $taxonomyIds = $service->serviceTaxonomies()
            ->pluck('taxonomy_id')
            ->toArray();
        $collectionIds = CollectionTaxonomy::query()
            ->whereIn('taxonomy_id', $taxonomyIds)
            ->pluck('collection_id')
            ->toArray();

        return Collection::query()->whereIn('id', $collectionIds);
    }
}
