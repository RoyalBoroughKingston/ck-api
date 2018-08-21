<?php

namespace App\Providers;

use App\Events\EndpointHit;
use App\Listeners\AuditLogger;
use App\Listeners\Notifications\ReferralMade;
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
            ReferralMade::class,
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
