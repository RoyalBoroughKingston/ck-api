<?php

namespace App\Events\ReportSchedule;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\ReportSchedule;
use Illuminate\Http\Request;

class ReportScheduleDeleted extends EndpointHit
{
    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ReportSchedule $reportSchedule
     */
    public function __construct(Request $request, ReportSchedule $reportSchedule)
    {
        parent::__construct($request);

        $this->action = Audit::ACTION_DELETE;
        $this->description = "Deleted report schedule [{$reportSchedule->id}]";
    }
}
