<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DelayedJob extends Model
{
    public $timestamps = false;
    public $loggable = false;

    public function getParamsAttribute($val)
    {
        return json_decode($val);
    }

    public static function dispatch($class, $params, $delay_in_minutes)
    {
        // сначала удаляем похожие
        self::where('class', $class)->where('params', json_encode($params))->delete();

        self::insert([
            'class'     => '\\' . $class,
            'params'    => json_encode($params),
            'run_at'    => (new \DateTime)->modify("+{$delay_in_minutes} minutes")->format('Y-m-d H:i:00')
        ]);
    }
}
