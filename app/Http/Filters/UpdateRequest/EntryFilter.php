<?php

namespace App\Http\Filters\UpdateRequest;

use App\Models\UpdateRequest;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class EntryFilter implements Filter
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @param string $property
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $sql = (new UpdateRequest())->getEntrySql();

        // Don't treat comma's as an array separator.
        $value = implode(',', array_wrap($value));

        return $query->whereRaw("({$sql}) LIKE ?", "%{$value}%");
    }
}
