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
        'attachment_date',
        'grade',
        'subjects',
        'attachment_comment',
        'archive_date',
        'total_lessons_missing',
        'archive_comment',
        'archive_user_id',
        'archive_status',
        'review_date',
        'review_user_id',
        'score',
        'signature',
        'review_comment',
        'review_status',
        'review_date_saved',
        'archive_date_saved',
    ];
    protected $casts = [
        'review_on'     => 'boolean',
        'archive_on'    => 'boolean',
        'grade'         => 'int',
    ];
    protected $dates = ['review_date_saved', 'archive_date_saved'];
    protected static $commaSeparated = ['subjects'];
    protected static $dotDates = ['attachment_date', 'archive_date', 'review_date'];

    // ------------------------------------------------------------------------

    public function requestList()
    {
        return $this->belongsTo('App\Models\RequestList');
    }

    // ------------------------------------------------------------------------

    public function getReviewUserLoginAttribute()
    {
        return User::where('id', $this->review_user_id)->select('login')->login;
    }

    public function getArchiveUserLoginAttribute()
    {
        return User::where('id', $this->archive_user_id)->select('login')->login;
    }

    // ------------------------------------------------------------------------

    protected static function boot()
    {
        static::saving(function ($model) {
            if (!$model->exists) {
                $model->attachment_date = date('Y-m-d');
                $model->user_id = User::fromSession()->id;
            }
        });
    }
}
