<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SmsStatusUpdate extends Event implements ShouldBroadcast
{
    use SerializesModels;
    public $id_smsru; # id сообщения
    public $id_status; // статус сообщения

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id_smsru, $id_status)
    {
        $this->id_smsru = $id_smsru;
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
            'id_smsru' => $this->id_smsru,
            'id_status'     => $this->id_status,
        ];
    }
}
