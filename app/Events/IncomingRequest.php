<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class IncomingRequest extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $request;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
        return ['request' => $this->request];
    }
}
