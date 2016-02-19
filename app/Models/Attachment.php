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

    public function list()
    {
        return $this->belongsTo('App\Models\List');
    }

    public function getSubjectsAttribute($value)
    {
        return empty($value) ? [] : explode(',', $value);
    }

    public function setSubjectsAttribute($value)
    {
        $this->attributes['subjects'] = implode(',', $value);
    }
}
