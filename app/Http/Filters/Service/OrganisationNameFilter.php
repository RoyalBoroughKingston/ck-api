<?php

namespace App\Http\Filters\Service;

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
        // Don't treat comma's as an array separator.
        $value = implode(',', array_wrap($value));

        return $query->whereHas('organisation', function (Builder $query) use ($value) {
            $query->where('organisations.name', 'LIKE', "%{$value}%");
        });
    }
}
