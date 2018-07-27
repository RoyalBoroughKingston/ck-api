<?php

namespace App\Models\Relationships;

use App\Models\File;
use App\Models\ReportType;

trait ReportRelationships
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reportType()
    {
        return $this->belongsTo(ReportType::class);
    }

    /**
     * @return mixed
     */
    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
