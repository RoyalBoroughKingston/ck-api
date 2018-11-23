<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\EndpointHit::class => [
            \App\Listeners\AuditLogger::class,
            \App\Listeners\Notifications\ReferralCreated::class,
            \App\Listeners\Notifications\ReferralCompleted::class,
            \App\Listeners\Notifications\ReferralIncompleted::class,
            \App\Listeners\Notifications\ServiceCreated::class,
            \App\Listeners\Notifications\PageFeedbackReceived::class,
            \App\Listeners\Notifications\UpdateRequestApproved::class,
            \App\Listeners\Notifications\UpdateRequestRejected::class,
            \App\Listeners\Notifications\UserCreated::class,
        ],
        \App\Events\UserRolesUpdated::class => [
            \App\Listeners\Notifications\UserRolesUpdated::class,
        ],
        \Laravel\Passport\Events\AccessTokenCreated::class => [
            \App\Listeners\RevokeOldTokens::class,
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
