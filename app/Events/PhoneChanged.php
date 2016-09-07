<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Tutor;
use App\Models\Client;
use App\Models\Service\PhoneDuplicate;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PhoneChanged extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($old_phone, $new_phone = null, $entity_type = 'client')
    {
        $this->_updateDuplicatesTable($old_phone, $new_phone, $entity_type);
    }

    private function _updateDuplicatesTable($old_phone, $new_phone, $entity_type)
    {
        // условие удаления из таблицы дублей:
        // таких номеров <= 2 И номер находится в таблице дублей
        // ВАЖНО: цифра 2 превратится в цифру 1 после сохранения
        if (! empty($old_phone) && PhoneDuplicate::countByPhone(static::_getQuery($entity_type), $old_phone) <= 2 && PhoneDuplicate::exists($old_phone, $entity_type)) {
            PhoneDuplicate::remove($old_phone, $entity_type);
        }

        if (! $new_phone) {
            return;
        }

        // условие добавления в таблицу дублей:
        // номеров $new_phone после сохранения модели >= 2
        if (PhoneDuplicate::countByPhone(static::_getQuery($entity_type), $new_phone) >= 2) {
            PhoneDuplicate::add($new_phone, $entity_type);
        }
    }

    private static function _getQuery($entity_type)
    {
	    return $entity_type == 'client' ? Client::query() : Tutor::query();
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
