<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
//    protected $with = ['user'];
    protected $fillable = ['comment', 'user_id', 'entity_id', 'entity_type'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
