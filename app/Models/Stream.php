<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    public $table = 'stream';
    public $timestamps = false;
    public static $commaSeparated = ['subjects'];
}
