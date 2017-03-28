<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\ResponsibleUserChanged' => [],
        'App\Events\RequestUserChanged' => [],
        'App\Events\IncomingRequest' => [],
        'App\Events\AttachmentCountChanged' => [],
        'App\Events\PhoneChanged' => [],
        'App\Events\LogAction' => [],
        'App\Events\RecalcTutorDebt' => [],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
