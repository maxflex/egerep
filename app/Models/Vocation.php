<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vocation extends Model
{
    protected $attributes = [
        'data' => []
    ];

    protected $fillable = [
        'work_off',
        'data',
        'comment',
        'approved_by'
    ];

    protected static $commaSeparated = ['approved_by'];

    public function getDataAttribute($value)
    {
        if (count($value)) {
            // all data to be displayed
            return static::allEvents();
        } else {
            return $value;
        }
    }

    public function setDataAttribute($value)
    {
        $this->attributes['data'] = json_encode($value);
    }

    protected static function boot()
    {
        static::creating(function($model) {
            $model->user_id = User::fromSession()->id;
        });
    }

    public static function allEvents()
    {
        $data = \DB::table('vocations')->pluck('data');
        $return = [];
        foreach($data as $d) {
            $json = json_decode($d);
            foreach($json as $j) {
                $return[] = $j;
            }
        }
        return $return;
    }

    public static function emptyObject()
    {
        return (object)[
            'data'  => Vocation::allEvents(),
        ];
    }
}
