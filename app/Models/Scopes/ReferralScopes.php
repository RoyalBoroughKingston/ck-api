<?php

namespace App\Models\Scopes;

use App\Models\Referral;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;

trait ReferralScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $workingDays
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnactioned(Builder $query, int $workingDays): Builder
    {
        return $query
            ->where('status', '!=', Referral::STATUS_COMPLETED)
            ->where('status', '!=', Referral::STATUS_INCOMPLETED)
            ->where('created_at', '<=', Date::now()->subWeekdays($workingDays));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDueForDeletion(Builder $query): Builder
    {
        $date = Date::today()->subMonths(Referral::AUTO_DELETE_MONTHS);

        return $query
            ->where('updated_at', '<=', $date)
            ->where('status', '=', Referral::STATUS_COMPLETED);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $alias
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatusLastUpdatedAt(Builder $query, string $alias = 'status_last_updated_at'): Builder
    {
        /*
         * This query will select the latest status update, which has
         * a changed status, or fall back to the referral creation date.
         */
        $sql = <<<'EOT'
IFNULL(
    (
        SELECT `status_updates`.`created_at` 
        FROM `status_updates` 
        WHERE `status_updates`.`referral_id` = `referrals`.`id`
        AND `status_updates`.`from` != `status_updates`.`to`
        ORDER BY `status_updates`.`created_at` DESC 
        LIMIT 1
    ),
    `referrals`.`created_at`
)
EOT;

        return $query->selectSub($sql, $alias);
    }
}
