<?php

namespace App\Models\Scopes;

use App\Models\ServiceRefreshToken;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;

trait ServiceRefreshTokenScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDueForDeletion(Builder $query): Builder
    {
        $date = Date::today()->subMonths(ServiceRefreshToken::AUTO_DELETE_MONTHS);

        return $query->where('created_at', '<=', $date);
    }
}
