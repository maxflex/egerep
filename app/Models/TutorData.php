<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class TutorData extends Model
{
    public $table = 'tutor_data';
    public $timestamps = false;
    public static $commaSeparated = ['svg_map'];
}