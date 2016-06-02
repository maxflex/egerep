<?php

namespace App\Models;

use Log;
use App\Traits\Markerable;
use App\Traits\Person;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use Markerable;
    use Person;

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

    public function scopeSearchByPhone($query, $searchText)
    {
        if ($searchText) {
            // @todo: цикл по номерам телефона
            return $query->where("phone", "like", "%{$searchText}%")
                         ->orWhere("phone2", "like", "%{$searchText}%")
                         ->orWhere("phone3", "like", "%{$searchText}%")
                         ->orWhere("phone4", "like", "%{$searchText}%");

        }
    }

    /**
     * Удалить клиентов без заявок
     */
    public static function removeWithoutRequests()
    {
        Client::doesntHave('requests')->delete();
    }

    protected static function boot()
    {
        static::saving(function($client) {
            cleanNumbers($client);
            if ($client->changed(static::$phone_fields)) {
                foreach($client->phones as $phone) {
                    if (Client::searchByPhone($phone)->where('id', '<>', $client->exists ? $client->id : 0)->exists()) {
                        $client->duplicate = true;
                        break;
                    }
                }
            }
        });
    }
}
