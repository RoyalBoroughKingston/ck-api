<?php

namespace App\Events\ReportSchedule;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\ReportSchedule;
use Illuminate\Http\Request;

class ReportScheduleRead extends EndpointHit
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

        $this->action = Audit::ACTION_READ;
        $this->description = "Viewed report schedule [{$reportSchedule->id}]";
    }
}
