<?php

namespace App\Listeners;

use App\Events\EndpointHit;
use App\Models\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;

class AuditLogger implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  EndpointHit  $event
     * @return void
     */
    public function handle(EndpointHit $event)
    {
        // Filter out any null values.
        $attributes = array_filter([
            'action' => $event->getAction(),
            'description' => $event->getDescription(),
            'ip_address' => $event->getIpAddress(),
            'user_agent' => $event->getUserAgent(),
            'created_at' => $event->getCreatedAt(),
        ]);

        // When an authenticated user makes the request.
        if ($event->getUser()) {
            $event->getUser()->audits()->create($attributes);
            return;
        }

        // When a guest makes the request.
        Audit::create($attributes);
    }
}
