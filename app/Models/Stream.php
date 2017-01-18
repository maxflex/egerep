<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    public $table = 'stream';
    public $timestamps = false;
    public static $commaSeparated = ['subjects'];

    public function tutor()
    {
        return $this->belongsTo('App\Models\Tutor')->select(['id', 'first_name','last_name', 'middle_name']);
    }
}
