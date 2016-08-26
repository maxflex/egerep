<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RequestUserChanged extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $request_id;
    public $new_user_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request_id, $new_user_id)
    {
        $this->request_id  = $request_id;
        $this->new_user_id = $new_user_id;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

    public function broadcastWith()
    {
        return [
            'request_id'  => $this->request_id,
            'new_user_id' => $this->new_user_id,
        ];
    }
}
