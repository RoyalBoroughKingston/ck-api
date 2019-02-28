<?php

namespace App\Http\Filters\Referral;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\Filters\Filter;

class ServiceNameFilter implements Filter
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
        $value = implode(',', Arr::wrap($value));

        return $query->whereHas('service', function (Builder $query) use ($value) {
            $query->where('services.name', 'LIKE', "%{$value}%");
        });
    }
}
