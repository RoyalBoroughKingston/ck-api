<?php

namespace App\Http\Filters\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filters\Filter;

class RoleIdFilter implements Filter
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @param string $property
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $sql = (new User())->getHighestRoleOrderSql();

        $subQuery = DB::table('user_roles')
            ->select('user_roles.role_id')
            ->whereRaw('`user_roles`.`user_id` = `users`.`id`')
            ->orderByRaw($sql['sql'], $sql['bindings'])
            ->take(1);

        return $query->whereIn($subQuery, explode(',', $value));
    }
}
