<?php

namespace App\Models\Scopes;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait AuditScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDueForDeletion(Builder $query): Builder
    {
        $date = today()->subMonths(Audit::AUTO_DELETE_MONTHS);

        return $query->where('updated_at', '<=', $date);
    }
}
