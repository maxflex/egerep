<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Service\Person
{
    const ENTITY_TYPE = 'client';

    public $timestamps = false;

    protected $with = ['requests', 'markers'];

    protected $appends = ['requests_count'];

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

    public function getRequestsCountAttribute()
    {
        return $this->requests()->count();
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
            $model->delete();
        });
    }
}
