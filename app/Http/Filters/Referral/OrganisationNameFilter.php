<?php

namespace App\Http\Filters\Referral;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class OrganisationNameFilter implements Filter
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @param string $property
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->whereHas('service.organisation', function (Builder $query) use ($value) {
            $query->where('organisations.name', 'LIKE', "%{$value}%");
        });
    }
}
