<?php

namespace App\Events\Notification;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationRead extends EndpointHit
{
    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Notification $Notification
     */
    public function __construct(Request $request, Notification $Notification)
    {
        parent::__construct($request);

        $this->action = Audit::ACTION_READ;
        $this->description = "Viewed Notification [{$Notification->id}]";
    }
}
