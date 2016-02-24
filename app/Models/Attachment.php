<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attachment extends Model
{
    protected $fillable = [
        'request_list_id',
        'user_id',
        'tutor_id',
        'date',
        'grade',
        'subjects',
        'archive',
        'review',
        'comment',
        'forecast',
    ];
    protected $casts = [
        'grade' => 'int',
    ];
    protected $appends = ['user_login'];
    protected $with = ['archive', 'review'];
    protected static $commaSeparated = ['subjects'];
    protected static $dotDates = ['date'];

    // ------------------------------------------------------------------------

    public function requestList()
    {
        return $this->belongsTo('App\Models\RequestList');
    }

    public function archive()
    {
        return $this->hasOne('App\Models\Archive');
    }

    public function review()
    {
        return $this->hasOne('App\Models\Review');
    }

    // ------------------------------------------------------------------------

    public function getUserLoginAttribute()
    {
        return User::where('id', $this->user_id)->pluck('login')->first();
    }

    public function setArchiveAttribute($archive)
    {
        if ($archive !== null) {
            Archive::find($archive['id'])->update($archive);
        }
    }

    public function setReviewAttribute($review)
    {
        if ($review !== null) {
            Review::find($review['id'])->update($review);
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
