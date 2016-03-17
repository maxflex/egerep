<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Tutor;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ResponsibleUserChanged extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $tutor;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Tutor $tutor)
    {
        $this->tutor = $tutor;
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
            'tutor_id'              => $this->tutor->id,
            'responsible_user_id'   => $this->tutor->responsible_user_id,
        ];
    }
}
