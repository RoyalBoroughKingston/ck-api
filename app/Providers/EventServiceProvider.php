<?php

namespace App\Providers;

use App\Events\EndpointHit;
use App\Listeners\AuditLogger;
use App\Listeners\Notifications\PageFeedbackReceived;
use App\Listeners\Notifications\ReferralCompleted;
use App\Listeners\Notifications\ReferralCreated;
use App\Listeners\Notifications\ReferralIncompleted;
use App\Listeners\Notifications\UpdateRequestApproved;
use App\Listeners\Notifications\UpdateRequestRejected;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        EndpointHit::class => [
            AuditLogger::class,
            ReferralCreated::class,
            ReferralCompleted::class,
            ReferralIncompleted::class,
            PageFeedbackReceived::class,
            UpdateRequestApproved::class,
            UpdateRequestRejected::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
