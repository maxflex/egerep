<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;
use App\Events\PhoneChanged;

/**
 *
 */
class Person extends Model
{
    use \App\Traits\Markerable;
    use \App\Traits\Person;

    public function save(array $options = [])
    {
        // сохраняем маркеры
        $this->saveMarkers();

        // запускаем функцию проверки дубликатов на измененные номера
        $events = [];
        foreach($this->changedPhones() as $phone_field) {
            $events[] = [$this->getOriginal($phone_field), $this->{$phone_field}];
        }

        parent::save($options);

        // отключаем дубликаты для преподавателей – будет в "ошибках"
        if (static::ENTITY_TYPE != 'tutor') {
            foreach($events as $event) {
                event(new PhoneChanged(cleanNumber($event[0]), cleanNumber($event[1]), static::ENTITY_TYPE));
            }
        }
    }

    protected static function boot()
    {
        static::saving(function($client) {
            cleanNumbers($client);
        });

        static::deleted(function($client) {
            // запускаем функцию проверки дубликатов на измененные номера
            foreach($client->phones as $phone) {
                event(new PhoneChanged($phone, null, static::ENTITY_TYPE));
            }
        });
    }
}
