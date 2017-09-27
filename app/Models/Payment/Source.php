<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $table = 'payment_sources';
    public $timestamps = false;

    protected $fillable = ['name'];
}
