<?php

namespace App\Models\Scopes;

use App\Models\PageFeedback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;

trait PageFeedbackScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDueForDeletion(Builder $query): Builder
    {
        $date = Date::today()->subMonths(PageFeedback::AUTO_DELETE_MONTHS);

        return $query->where('updated_at', '<=', $date);
    }
}
