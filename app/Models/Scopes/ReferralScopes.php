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
}
