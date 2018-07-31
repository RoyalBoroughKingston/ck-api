<?php

namespace App\Models\Scopes;

use App\Models\Collection;
use Illuminate\Database\Eloquent\Builder;

trait CollectionScopes
{
    /**
     * Get only category collections.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategories(Builder $query): Builder
    {
        return $query->where('type', Collection::TYPE_CATEGORY);
    }

    /**
     * Get only persona collections.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePersonas(Builder $query): Builder
    {
        return $query->where('type', Collection::TYPE_PERSONA);
    }
}
