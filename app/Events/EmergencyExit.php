<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Service\Settings;

class EmergencyExit extends Event implements ShouldBroadcast
{
    use SerializesModels;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        Settings::set('emergency_exit', 1);
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['egerep'];
    }

    public function broadcastWith()
    {
        return [];
    }
}
