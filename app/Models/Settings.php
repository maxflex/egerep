<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
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
