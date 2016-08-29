<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AttachmentCountChanged extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $delete; // удаляется? по умолчанию добавляется

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($delete = false)
    {
        $this->delete = $delete;
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
            'delete'     => $this->delete,
        ];
    }
}
