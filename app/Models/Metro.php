<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Metro extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'minutes',
        'meters',
        'station_id',
    ];
    protected $with = ['station'];
    
    // ------------------------------------------------------------------------

    public function station()
    {
        return $this->belongsTo('App\Models\Station');
    }

    // ------------------------------------------------------------------------

}
