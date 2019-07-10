<?php

namespace App\Observers;

use App\Models\Report;

class ReportObserver
{
    /**
     * Handle the organisation "deleted" event.
     *
     * @param \App\Models\Report $report
     */
    public function deleted(Report $report)
    {
        $report->file->delete();
    }
}
