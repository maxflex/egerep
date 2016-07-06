<?php

namespace App\Events;

use App\Events\Event;
use DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LogAction extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($model, $type = 'update')
    {
        try {
            if ($model->getDirty()) {
                DB::table('logs')->insert([
                    'user_id'   => userIdOrSystem(),
                    'data'      => static::_generateData($model),
                    'table'     => $model->getTable(),
                    'type'      => $type,
                    'created_at'=> now(),
                ]);
            }
        } catch (\Exception $e) {
            \Log::info(get_class($this));
            \Log::info('Error: ' . $e->getMessage());
        }
    }

    private static function _generateData($model)
    {
        $data = [];
        foreach ($model->getDirty() as $field => $new_value) {
            $data[$field] = [$model->getOriginal($field), $new_value];
        }
        return json_encode($data);
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
