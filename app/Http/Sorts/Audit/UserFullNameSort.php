<?php

namespace App\Http\Sorts\Audit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Sorts\Sort;

class UserFullNameSort implements Sort
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $descending
     * @param string $property
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Builder $query, $descending, string $property): Builder
    {
        $subQuery = DB::table('users')
            ->selectRaw('CONCAT(`users`.`first_name`, " ", `users`.`last_name`)')
            ->whereRaw('`audits`.`user_id` = `users`.`id`')
            ->take(1);

        return $query
            ->selectSub($subQuery, $property)
            ->orderBy($property, $descending ? 'DESC' : 'ASC');
    }
}
