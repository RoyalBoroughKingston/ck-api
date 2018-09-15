<?php

namespace App\Models\Scopes;

use App\Models\CollectionTaxonomy;
use App\Models\Service;
use App\Models\ServiceTaxonomy;
use Illuminate\Database\Eloquent\Builder;

trait CollectionTaxonomyScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Models\CollectionTaxonomy $collectionTaxonomy
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeServices(Builder $query, CollectionTaxonomy $collectionTaxonomy): Builder
    {
        $serviceIds = ServiceTaxonomy::query()
            ->where('taxonomy_id', $collectionTaxonomy->taxonomy_id)
            ->pluck('service_id');

        return Service::query()->whereIn('id', $serviceIds);
    }
}
