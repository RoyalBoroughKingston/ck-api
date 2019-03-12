<?php

namespace App\Models\Scopes;

use App\Models\Referral;
use Illuminate\Database\Eloquent\Builder;

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
            ->where('created_at', '<=', now()->subWeekdays($workingDays));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDueForDeletion(Builder $query): Builder
    {
        $date = today()->subMonths(Referral::AUTO_DELETE_MONTHS);

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
         * 1. If no status updates, then use the referral created_at
         * 2. Or, get created_at for the latest status update with a changed status
         * 3. Or, get created_at for the first status update
         */

        $sql = <<< EOT
IF(
    (
        SELECT COUNT(*) 
        FROM `status_updates` 
        WHERE `status_updates`.`referral_id` = `referrals`.`id`
    ) = 0, 
    `referrals`.`created_at`,
    IFNULL(
        (
            SELECT `status_updates`.`created_at` 
            FROM `status_updates` 
            WHERE `status_updates`.`referral_id` = `referrals`.`id`
            AND `status_updates`.`from` != `status_updates`.`to`
            ORDER BY `status_updates`.`created_at` DESC 
            LIMIT 1
        ),
        (
            SELECT `status_updates`.`created_at` 
            FROM `status_updates` 
            WHERE `status_updates`.`referral_id` = `referrals`.`id`
            ORDER BY `status_updates`.`created_at` ASC 
            LIMIT 1
        )
    )
)
EOT;

        return $query->selectSub($sql, $alias);
    }
}
