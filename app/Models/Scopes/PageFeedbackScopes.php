<?php

namespace App\Models\Scopes;

use App\Models\PageFeedback;
use Illuminate\Database\Eloquent\Builder;

trait PageFeedbackScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDueForDeletion(Builder $query): Builder
    {
        $date = today()->subMonths(PageFeedback::AUTO_DELETE_MONTHS);

        return $query->where('updated_at', '<=', $date);
    }
}
