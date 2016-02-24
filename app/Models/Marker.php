<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marker extends Model
{
    protected $fillable = [
        'lat',
        'lng',
        'type',
        'markerable_id',
        'markerable_type',
    ];
    protected $with = ['metros'];
    public $timestamps = false;

    // ------------------------------------------------------------------------

    public function markerable()
    {
        return $this->morphTo();
    }

    public function metros()
    {
        return $this->hasMany('App\Models\Metro');
    }

}
