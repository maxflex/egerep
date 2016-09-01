<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'entity_id',
        'entity_type',
        'comment',
        'user_id',
        'approved',
        'date'
    ];
    protected $with = ['user'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function getDateAttribute($value)
    {
        return date('d.m.y', strtotime($value));
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = fromDotDate($value, '20');
    }

    public static function countUnapproved()
    {
        $existing = static::where('a.user_id', User::fromSession()->id)
                        ->join('attachments as a', function($join) {
                            $join->on('a.id', '=', 'notifications.entity_id')
                                ->where('notifications.entity_type', '=', 'attachments');
                        })
                        ->whereNullOrZero('a.forecast')
                        ->whereRaw('NOT EXISTS (SELECT id FROM archives WHERE archives.attachment_id = a.id)')
                        ->where('notifications.approved', 0)
                        ->whereRaw('notifications.date <= DATE(NOW())')
                        ->count();
        $virtual = Attachment::newest()->where('attachments.user_id', User::fromSession()->id)
                        ->leftJoin('notifications as n', function($join) {
                            $join->on('n.entity_id', '=', 'attachments.id')
                                 ->where('n.entity_type', '=','attachment');
                        })
                        ->whereRaw('attachments.date <= DATE(NOW())')
                        ->whereNull('n.id')
                        ->count();
        return $existing + $virtual;
    }
}
