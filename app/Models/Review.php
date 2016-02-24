<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'attachment_id',
        'score',
        'comment',
        'state',
        'signature',
    ];
    protected $appends = ['user_login'];

    // ------------------------------------------------------------------------

    public function getUserLoginAttribute()
    {
        return User::where('id', $this->user_id)->pluck('login')->first();
    }

    // ------------------------------------------------------------------------

    protected static function boot()
    {
        static::saving(function ($model) {
            if (!$model->exists) {
                $model->user_id = User::fromSession()->id;
            }
        });
    }
}
