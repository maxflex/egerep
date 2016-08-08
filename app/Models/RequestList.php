<?php

namespace App\Models;

use App\Models\Tutor;
use Illuminate\Database\Eloquent\Model;

class RequestList extends Model
{
    protected $with = ['attachments'];
    protected $appends = [
        'tutors'
    ];
    protected $fillable = [
        'request_id',
        'tutor_ids',
        'subjects',
        'attachments',
        'user_id',
    ];
    protected static $commaSeparated = ['tutor_ids', 'subjects'];

    // ------------------------------------------------------------------------

    public function request()
    {
        return $this->belongsTo('App\Models\Request');
    }

    public function attachments()
    {
        return $this->hasMany('App\Models\Attachment');
    }

    // ------------------------------------------------------------------------

    public function setAttachmentsAttribute($value)
    {
        foreach ($value as $attachment) {
            Attachment::find($attachment['id'])->update($attachment);
        }
    }

    public function getTutorsAttribute()
    {
        return Tutor::with(['markers'])->whereIn('id', $this->tutor_ids)->get([
            'id',
            'first_name',
            'last_name',
            'middle_name',
            'birth_year',
            'subjects',
            'tb',
            'lk',
            'js',
            'debt_calc',
            'debt_comment',
            'photo_extension',
            'margin',
        ])->append(['clients_count', 'meeting_count', 'active_clients_count', 'last_account_info']);
    }

    // ------------------------------------------------------------------------

    protected static function boot()
    {
        static::saving(function ($model) {
            if (! $model->exists) {
                $model->user_id = User::fromSession()->id;
            } else {
                $model->tutor_ids = array_unique($model->tutor_ids);
            }
        });
    }
}
