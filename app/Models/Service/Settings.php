<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    public $timestamps = false;

    public static function set($key, $value)
    {
        $query = static::where('key', $key);

        if ($query->exists()) {
            $query->update([
                'value' => $value
            ]);
        } else {
            static::insert(['key' => $key, 'value' => $value]);
        }
    }

    public static function get($key)
    {
        return static::where('key', $key)->value('value');
    }
}
