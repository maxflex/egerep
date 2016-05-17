<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestList extends Model
{
    protected $with = ['attachments'];
    protected $fillable = [
        'request_id',
        'tutor_ids',
        'subjects',
        'attachments',
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
}
