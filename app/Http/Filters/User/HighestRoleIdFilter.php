<?php

namespace App\Http\Filters\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filters\Filter;

class HighestRoleIdFilter implements Filter
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

        $subQuery = <<< EOT
SELECT `user_roles`.`role_id`
FROM `user_roles`
WHERE `user_roles`.`user_id` = `users`.`id`
ORDER BY {$sql['sql']}
LIMIT 1
EOT;

        $subQuery = "({$subQuery}) IN (?)";

        $bindings = array_merge(
            $sql['bindings'],
            [$this->quoteRoleIds($value)]
        );

        return $query->whereRaw($subQuery, $bindings);
    }

    /**
     * @param string $ids
     * @return string
     */
    protected function quoteRoleIds(string $ids): string
    {
        $roleIdsArray = explode(',', $ids);

        return implode("','", $roleIdsArray);
    }
}
