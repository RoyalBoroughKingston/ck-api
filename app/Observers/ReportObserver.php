<?php

namespace App\Observers;

use App\Models\Report;

class ReportObserver
{
    /**
     * Handle the organisation "deleted" event.
     *
     * @param  \App\Models\Report $report
     * @return void
     */
    public function deleted(Report $report)
    {
        $report->file->delete();
    }
}
