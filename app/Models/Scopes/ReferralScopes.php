<?php

namespace App\Models\Scopes;

use App\Models\Referral;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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
     * @param string $alias
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithServiceName(Builder $query, string $alias = 'service_name'): Builder
    {
        $subQuery = DB::table('services')
            ->select('services.name')
            ->whereRaw('`referrals`.`service_id` = `services`.`id`')
            ->take(1);

        return $query->selectSub($subQuery, $alias);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $alias
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithOrganisationName(Builder $query, string $alias = 'organisation_name'): Builder
    {
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

        return $query->selectSub($subQuery, $alias);
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
