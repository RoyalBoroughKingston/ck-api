<?php

namespace App\Models\Relationships;

use App\Models\ReportType;

trait ReportScheduleRelationships
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reportType()
    {
        return $this->belongsTo(ReportType::class);
    }
}
