<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlannedAccount extends Model
{
    protected $fillable = [
        'tutor_id',
        'date',
        'payment_method',
        'user_id',
    ];

    public $timestamps = false;

    public function getDateAttribute($value)
    {
        return date('d.m.Y', strtotime($value));
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = fromDotDate($value);
    }
}
