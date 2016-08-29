<?php

namespace App\Events;

use Storage;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Задача #1239
 */
class AttachmentCountChanged extends Event
{
    use SerializesModels;

    // удаление/добавление стыковки
    public $type;

    const COUNT_PLUS  = 'attachment_count_plus';
    const COUNT_MINUS = 'attachment_count_minus';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($type)
    {
        $attachment_count_plus  = Storage::exists(self::COUNT_PLUS)  ? Storage::get(self::COUNT_PLUS)  : 0;
        $attachment_count_minus = Storage::exists(self::COUNT_MINUS) ? Storage::get(self::COUNT_MINUS) : 0;

        switch ($type) {
            case 'created':
                $attachment_count_plus++;
                Storage::put(self::COUNT_PLUS, $attachment_count_plus);
                break;
            case 'deleted':
                $attachment_count_minus++;
                Storage::put(self::COUNT_MINUS, $attachment_count_minus);
                break;
        }
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
