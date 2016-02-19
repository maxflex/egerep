<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestList extends Model
{
    protected $fillable = ['request_id', 'tutor_ids', 'subjects'];
    protected $with = ['attachments'];
    public $timestamps = false;

    public function request()
    {
        return $this->belongsTo('App\Models\Request');
    }

    public function attachments()
    {
        return $this->hasMany('App\Models\Attachment');
    }

    public function getSubjectsAttribute($value)
    {
        return empty($value) ? [] : explode(',', $value);
    }
    public function setSubjectsAttribute($value)
    {
        $this->attributes['subjects'] = implode(',', $value);
    }

    public function getTutorIdsAttribute($value='')
    {
        return empty($value) ? [] : explode(',', $value);
    }
    public function setTutorIdsAttribute($value)
    {
        $this->attributes['tutor_ids'] = implode(',', $value);
    }
}
