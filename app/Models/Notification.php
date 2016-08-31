<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected static $dotDates = ['date'];

    protected $fillable = [
        'entity_id',
        'entity_type',
        'comment',
        'user_id',
        'approved',
        'date'
    ];
}
