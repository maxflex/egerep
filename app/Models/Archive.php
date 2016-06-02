<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    protected $fillable = [
        'attachment_id',
        'total_lessons_missing',
        'date',
        'comment',
        'state',
    ];
    protected $appends = ['user_login'];
    protected static $dotDates = ['date'];

    // ------------------------------------------------------------------------

    public function getUserLoginAttribute()
    {
        if (! $this->user_id) {
            return 'system';
        } else {
            return User::where('id', $this->user_id)->pluck('login')->first();
        }
    }

    // ------------------------------------------------------------------------

    protected static function boot()
    {
        static::saving(function ($model) {
            if (!$model->exists) {
                $model->date = date('Y-m-d');
                $model->user_id = User::fromSession()->id;
            }
        });
    }
}
