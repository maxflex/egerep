<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $attributes = [
        'state' => 'new',
    ];

    protected $with = ['user'];
    protected $fillable = ['comment', 'state', 'client_id', 'user_id', 'user_id_created'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function userCreated()
    {
        return $this->belongsTo('App\Models\User', 'user_id_created');
    }

    protected static function boot()
    {
        static::saving(function ($model) {
            if (!$model->exists) {
                $model->user_id_created = User::fromSession()->id;
            }
        });
    }
}
