<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Marker;

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
    public $loggable = false;

    // ------------------------------------------------------------------------

    public function markerable()
    {
        return $this->morphTo();
    }

    public function metros()
    {
        return $this->hasMany('App\Models\Metro');
    }

    // ------------------------------------------------------------------------

    /**
     * Создать ближайшие метро к маркеру
     */
    public function createMetros()
    {
        $metros = Metro::getClosest($this->lat, $this->lng);
        foreach ($metros as $metro) {
            $metro['station_id'] = $metro['id'];
            $this->metros()->create($metro);
        }
    }
}
