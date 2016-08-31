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
        $existing = static::where('user_id', User::fromSession()->id)
                        ->where('approved', 0)
                        ->whereRaw('date <= DATE(NOW())')
                        ->count();
        $virtual = Attachment::where('user_id', User::fromSession()->id)
                        ->whereRaw('date <= DATE(NOW())')
                        ->count();
        return $existing + $virtual;
    }
}
