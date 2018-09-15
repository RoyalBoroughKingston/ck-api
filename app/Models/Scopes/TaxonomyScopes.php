<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;

trait TaxonomyScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTopLevelCategories(Builder $query): Builder
    {
        return $query->where('parent_id', static::category()->id);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrganisations(Builder $query): Builder
    {
        return $query->where('parent_id', static::organisation()->id);
    }
}
