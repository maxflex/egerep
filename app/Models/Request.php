<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $with = ['user'];
    protected $fillable = ['comment', 'status', 'client_id', 'user_id', 'user_id_created'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function userCreated()
    {
        return $this->belongsTo('App\Models\User', 'user_id_created');
    }
}
