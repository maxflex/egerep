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
        $query = $entity_type == 'client' ? Client::query() : Tutor::query();

        // условие удаления из таблицы дублей:
        // таких номеров <= 2 И номер находится в таблице дублей
        // ВАЖНО: цифра 2 превратится в цифру 1 после сохранения
        if (! empty($old_phone) && PhoneDuplicate::countByPhone($query, $old_phone) <= 2 && PhoneDuplicate::exists($old_phone, $entity_type)) {
            PhoneDuplicate::remove($old_phone, $entity_type);
        }

        if (! $new_phone) {
            return;
        }

        // условие добавления в таблицу дублей:
        // номера $new_phone есть в таблице дублей (т.к. запуск идет в static::saving,
        // то нового номера еще нет в базе, поэтому если есть хотя бы один, то дубль)
        if (PhoneDuplicate::countByPhone($query, $new_phone)) {
            PhoneDuplicate::add($new_phone, $entity_type);
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
