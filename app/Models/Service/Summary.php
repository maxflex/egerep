<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    protected $fillable = [
        'forecast',
        'debt',
        'date',
    ];

    public $timestamps = false;
}
