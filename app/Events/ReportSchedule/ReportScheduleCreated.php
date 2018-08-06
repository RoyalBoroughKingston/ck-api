<?php

namespace App\Events\ReportSchedule;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\ReportSchedule;
use Illuminate\Http\Request;

class ReportScheduleCreated extends EndpointHit
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

        $this->action = Audit::ACTION_CREATE;
        $this->description = "Created report schedule [{$reportSchedule->id}]";
    }
}
