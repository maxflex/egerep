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
        return Attachment::newest()
                    ->leftJoin('notifications as n', function($join) {
                        $join->on('n.entity_id', '=', 'attachments.id')
                             ->where('n.entity_type', '=','attachment');
                    })
                    ->whereRaw("((n.id IS NULL AND DATE_ADD(attachments.date, INTERVAL 2 DAY) <= DATE(NOW())) OR (n.id > 0 AND n.approved = 0 AND n.date <= DATE(NOW())))")
                    ->count();
    }
}
