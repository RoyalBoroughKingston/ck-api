<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait AuditScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $alias
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithUserFullName(Builder $query, string $alias = 'user_full_name'): Builder
    {
        $subQuery = DB::table('users')
            ->selectRaw('CONCAT(`users`.`first_name`, " ", `users`.`last_name`)')
            ->whereRaw('`audits`.`user_id` = `users`.`id`')
            ->take(1);

        return $query->selectSub($subQuery, $alias);
    }
}
