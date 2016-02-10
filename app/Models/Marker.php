<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marker extends Model
{
    protected $fillable = ['lat', 'lng', 'type', 'markerable_id', 'markerable_type'];
    public $timestamps = false;

    public function markerable()
    {
        return $this->morphTo();
    }
}
