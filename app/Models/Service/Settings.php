<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    public $timestamps = false;
    public $loggable = false;

    public static function set($key, $value)
    {
        static::where('key', $key)->update([
            'value' => $value
        ]);
    }

    public static function get($key)
    {
        return static::where('key', $key)->value('value');
    }
}
