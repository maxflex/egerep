<?php

namespace App\Models;

use Log;
use App\Traits\Markerable;
use App\Traits\Person;
use App\Events\PhoneChanged;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use Markerable;
    use Person;

    const ENTITY_TYPE = 'client';

    public $timestamps = false;

    protected $with = ['requests', 'markers'];

    protected $fillable = [
        'name',
        'grade',
        'address',
        'requests',
        'markers',
        'id_a_pers', // @temp
    ];

    public function requests()
    {
        return $this->hasMany('App\Models\Request');
    }

    public function setRequestsAttribute($value)
    {
        foreach ($value as $request) {
            Request::find($request['id'])->update($request);
        }
    }

    /**
     * Удалить клиентов без заявок
     */
    public static function removeWithoutRequests()
    {
        // нужно, чтобы запускалось событие удаления клиента
        Client::doesntHave('requests')->get()->each(function($model) {
            $model->destroy();
        });
    }

    protected static function boot()
    {
        static::saving(function($client) {
            cleanNumbers($client);

            // запускаем функцию проверки дубликатов на измененные номера
            foreach($client->changedPhones() as $phone_field) {
                event(new PhoneChanged($client->getOriginal($phone_field), $client->{$phone_field}));
            }
        });

        static::deleted(function($client) {
            // запускаем функцию проверки дубликатов на измененные номера
            foreach($client->getPhones() as $phone) {
                event(new PhoneChanged($phone));
            }
        });
    }
}
