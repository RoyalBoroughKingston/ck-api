<?php

namespace App\Http\Sorts\Referral;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Sorts\Sort;

class OrganisationNameSort implements Sort
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $descending
     * @param string $property
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Builder $query, $descending, string $property): Builder
    {
        $descending = $descending ? 'DESC' : 'ASC';

        $subQuery = DB::table('services')
            ->select('organisations.name')
            ->whereRaw('`referrals`.`service_id` = `services`.`id`')
            ->leftJoin(
                'organisations',
                'services.organisation_id',
                '=',
                'organisations.id'
            )
            ->take(1);

        return $query->orderByRaw("({$subQuery->toSql()}) $descending", $subQuery->getBindings());
    }
}
