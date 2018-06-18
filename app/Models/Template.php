<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'text',
        'type',
        'name',
        'who',
        'number'
    ];

    public static function get($number)
    {
        return self::where('number', $number)->value('text');
    }
}
