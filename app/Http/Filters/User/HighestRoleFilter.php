<?php

namespace App\Http\Filters\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class HighestRoleFilter implements Filter
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

        $subQuery = <<<EOT
SELECT `user_roles`.`role_id`
FROM `user_roles`
WHERE `user_roles`.`user_id` = `users`.`id`
ORDER BY {$sql['sql']}
LIMIT 1
EOT;

        $subQuery = "({$subQuery}) IN (?)";

        $bindings = array_merge(
            $sql['bindings'],
            [$this->getQuotedRoleIds($value)]
        );

        return $query->whereRaw($subQuery, $bindings);
    }

    /**
     * @param string $roles
     * @return string
     */
    protected function getQuotedRoleIds(string $roles): string
    {
        // Split the role names string into an array of names.
        $roleNames = explode(',', $roles);

        // Convert the role names into the corresponding role IDs.
        $roleIds = array_map(function (string $roleName): string {
            switch ($roleName) {
                case Role::NAME_SUPER_ADMIN:
                    return Role::superAdmin()->id;
                case Role::NAME_GLOBAL_ADMIN:
                    return Role::globalAdmin()->id;
                case Role::NAME_ORGANISATION_ADMIN:
                    return Role::organisationAdmin()->id;
                case Role::NAME_SERVICE_ADMIN:
                    return Role::serviceAdmin()->id;
                case Role::NAME_SERVICE_WORKER:
                    return Role::serviceWorker()->id;
                default:
                    return null;
            }
        }, $roleNames);

        // Filter out any invalid roles.
        $roleIds = array_filter_null($roleIds);

        return implode("','", $roleIds);
    }
}
