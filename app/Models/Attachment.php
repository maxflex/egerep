<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'created_at',
    ];

    // @todo: rename list() to something else
    public function requestList()
    {
        return $this->belongsTo('App\Models\RequestList');
    }

    public function getSubjectsAttribute($value)
    {
        return empty($value) ? [] : explode(',', $value);
    }

    public function setSubjectsAttribute($value)
    {
        $this->attributes['subjects'] = implode(',', $value);
    }

    public function getAttachmentDateAttribute($value)
    {
        return date('d.m.Y', strtotime($value));
    }

    protected static function boot()
    {
        static::saving(function ($model) {
            if (!$model->exists) {
                $model->attachment_date = date('Y-m-d');
            }
        });
    }
}
