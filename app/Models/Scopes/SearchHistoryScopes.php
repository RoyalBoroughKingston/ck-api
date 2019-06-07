<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait SearchHistoryScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithFilledQuery(Builder $query): Builder
    {
        return $query->whereNotNull(
            DB::raw('JSON_EXTRACT(`query`, "$.query.bool.must.bool.should[0].match.name.query")')
        );
    }
}
