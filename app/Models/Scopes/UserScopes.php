<?php

namespace App\Models\Scopes;

use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait UserScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGlobalAdmins(Builder $query): Builder
    {
        return $query->whereHas('userRoles', function (Builder $query) {
            return $query->where(table(UserRole::class, 'role_id'), Role::globalAdmin()->id);
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $alias
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithHighestRoleOrder(Builder $query, string $alias = 'highest_role_order'): Builder
    {
        $sql = $this->getHighestRoleOrderSql();

        $subQuery = DB::table('user_roles')
            ->selectRaw($sql['sql'], $sql['bindings'])
            ->whereRaw('`user_roles`.`user_id` = `users`.`id`')
            ->orderByRaw($sql['sql'], $sql['bindings'])
            ->take(1);

        return $query->selectRaw(
            "({$subQuery->toSql()}) AS `{$alias}`",
            $subQuery->getBindings()
        );
    }

    /**
     * This SQL query is placed into its own method as it is referenced
     * in multiple places.
     *
     * @return array
     */
    public function getHighestRoleOrderSql(): array
    {
        $sql = <<<'EOT'
CASE `user_roles`.`role_id`
    WHEN ? THEN 1
    WHEN ? THEN 2
    WHEN ? THEN 3
    WHEN ? THEN 4
    WHEN ? THEN 5
    ELSE 6
END
EOT;

        $bindings = [
            Role::superAdmin()->id,
            Role::globalAdmin()->id,
            Role::organisationAdmin()->id,
            Role::serviceAdmin()->id,
            Role::serviceWorker()->id,
        ];

        return compact('sql', 'bindings');
    }
}
