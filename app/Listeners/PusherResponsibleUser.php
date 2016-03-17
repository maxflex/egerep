<?php

namespace App\Listeners;

use App\Events\ResponsibleUserChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PusherResponsibleUser
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ResponsibleUserChanged  $event
     * @return void
     */
    public function handle(ResponsibleUserChanged $event)
    {
        
    }
}
