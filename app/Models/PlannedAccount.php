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
    protected static $dotDates = [
       'date'
    ];

    public $timestamps = false;

    public function tutor()
    {
        return $this->belongsTo('App\Models\Tutor');
    }
}
