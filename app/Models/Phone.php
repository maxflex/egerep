<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $fillable = ['tutor_id', 'phone'];
    public $timestamps = false;
}
