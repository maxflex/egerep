<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class IncomingRequest extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $request_id;
    public $delete; // заявка удаляется? по умолчанию добавляется

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request_id, $delete = false)
    {
        $this->request_id = $request_id;
        $this->delete     = $delete;
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
        return [
            'request_id' => $this->request_id,
            'delete'     => $this->delete,
        ];
    }
}
