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
}
