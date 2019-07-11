<?php

namespace App\Models\Scopes;

use App\Models\File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;

trait FileScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePendingAssignmentDueForDeletion(Builder $query): Builder
    {
        $date = Date::today()->subDays(File::PEDNING_ASSIGNMENT_AUTO_DELETE_DAYS);

        return $query
            ->whereRaw('`meta`->>"$.type" = ?', [File::META_TYPE_PENDING_ASSIGNMENT])
            ->where('updated_at', '<=', $date);
    }
}
