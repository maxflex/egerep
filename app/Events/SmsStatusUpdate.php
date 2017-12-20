<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SmsStatusUpdate extends Event implements ShouldBroadcast
{
    use SerializesModels;
    public $external_id; # id сообщения
    public $id_status; // статус сообщения

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($external_id, $id_status)
    {
        $this->external_id = $external_id;
        $this->id_status = $id_status;
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
            'external_id' => $this->external_id,
            'id_status'     => $this->id_status,
        ];
    }
}
