<?php

namespace App\Events;

use App\Jobs\UpdateDebtsTable;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RecalcTutorDebt extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($tutor_id)
    {
        dispatch(new UpdateDebtsTable(compact('tutor_id')));
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
}
