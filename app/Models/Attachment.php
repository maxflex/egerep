<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
     "subject_id",
     "user_id",
     "client_id",
     "tutor_id",
     "attachment_date",
     "grade",
     "subjects",
     "attachment_comment",
     "archive_date",
     "total_lessons_missing",
     "archive_comment",
     "archive_user_id",
     "archive_status",
     "review_date",
     "review_user_id",
     "score",
     "signature",
     "review_comment",
     "review_status",
     "created_at",
   ];


    public function getSubjectsAttribute($value)
    {
        return empty($value) ? null : explode(',', $value);
    }

    public function setSubjectsAttribute($value)
    {
        $this->attributes['subjects'] = implode(',', $value);
    }
}
