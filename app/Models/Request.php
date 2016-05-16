<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    public $timestamps = false;

    protected $attributes = [
        'state' => 'new',
    ];
    protected $with = ['user', 'lists'];
    protected $fillable = [
        'comment',
        'state',
        'client_id',
        'user_id',
        'user_id_created',
        'lists',
        'id_a_pers',
    ];

    // ------------------------------------------------------------------------

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function userCreated()
    {
        return $this->belongsTo('App\Models\User', 'user_id_created');
    }

    public function lists()
    {
        return $this->hasMany('App\Models\RequestList');
    }

    // ------------------------------------------------------------------------

    public function setListsAttribute($value)
    {
        foreach ($value as $list) {
            RequestList::find($list['id'])->update($list);
        }
    }

    // ------------------------------------------------------------------------

    protected static function boot()
    {
        static::saving(function ($model) {
            if (!$model->exists) {
                $model->user_id_created = User::fromSession()->id;
            }
        });
    }
}
